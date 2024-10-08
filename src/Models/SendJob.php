<?php

namespace Actengage\CaseyJones\Models;

use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Throwable;

class SendJob extends Model
{
    use BroadcastsEvents, HasFactory, SoftDeletes;

    protected $fillable = [
        'status_code',
        'failed',
        'mailingid',
        'response',
        'error_message'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'failed' => 'bool',
            'status_code' => 'int',
            'mailingid' => 'int',
            'response' => 'json',
        ];
    }

    /**
     * Get the parent send.
     *
     * @return BelongsTo
     */
    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class);
    }

    /**
     * Scope the query for failed jobs.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeFailed(Builder $query): void
    {
        $query->where('failed', true);
    }

    /**
     * Scope the query for successful jobs.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeSuccess(Builder $query): void
    {
        $query->where('failed', false);
    }

    /**
     * Scope the query for pending jobs.
     *
     * @param Builder $query
     * @return void
     */
    public function scopePending(Builder $query): void
    {
        $query->whereNull('failed');
    }

    /**
     * Scope the query for mailingid.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeMailingid(Builder $query, int $mailingid): void
    {
        $query->where('mailingid', $mailingid);
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Database\Eloquent\Model>
     */
    public function broadcastOn(string $event): array
    {
        return [$this, $this->send];
    }

    /**
     * Mark the job as failed.
     *
     * @param Throwable $e
     * @return void
     */
    public function fail(Throwable $e): void
    {
        $this->fill([
            'failed' => true,
            'mailingid' => $this->send->mailingid
        ]);
        
        if($e instanceof BadResponseException) {
            $response = json_decode($e->getResponse()->getBody(), true);

            $this->update([
                'response' => $response,
                'status_code' => $e->getResponse()->getStatusCode(),
                'error_message' => Arr::get($response, 'errorMessage', $e->getMessage()),
            ]);
        }
        else {
            $this->update([
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot(): void
    {
        parent::boot();

        static::saving(function(SendJob $model) {
            if($model->failed === null && $model->status_code) {
                $model->failed = !($model->status_code >= 200 && $model->status_code < 300);
            }
        });
    }
}