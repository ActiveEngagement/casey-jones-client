<?php

namespace Actengage\CaseyJones\Models;

use Actengage\CaseyJones\Database\Factories\SendJobFactory;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Throwable;

/**
 * @property bool|null $failed
 * @property int|null $status_code
 * @property int|null $mailingid
 * @property mixed $response
 * @property string|null $error_message
 * @property-read Send $send
 */
class SendJob extends Model
{
    /** @use HasFactory<SendJobFactory> */
    use BroadcastsEvents, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'status_code',
        'failed',
        'mailingid',
        'response',
        'error_message',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * The primary key stays an auto-incrementing integer; the UUID is stored
     * in the separate "uuid" column.
     *
     * @return array<int, string>
     */
    #[\Override]
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): SendJobFactory
    {
        return SendJobFactory::new();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[\Override]
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
     * @return BelongsTo<Send, $this>
     */
    public function send(): BelongsTo
    {
        return $this->belongsTo(Send::class);
    }

    /**
     * Scope the query for failed jobs.
     *
     * @param  Builder<static>  $query
     */
    public function scopeFailed(Builder $query): void
    {
        $query->where('failed', true);
    }

    /**
     * Scope the query for successful jobs.
     *
     * @param  Builder<static>  $query
     */
    public function scopeSuccess(Builder $query): void
    {
        $query->where('failed', false);
    }

    /**
     * Scope the query for pending jobs.
     *
     * @param  Builder<static>  $query
     */
    public function scopePending(Builder $query): void
    {
        $query->whereNull('failed');
    }

    /**
     * Scope the query for mailingid.
     *
     * @param  Builder<static>  $query
     */
    public function scopeMailingid(Builder $query, int $mailingid): void
    {
        $query->where('mailingid', $mailingid);
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @return array<int, Channel|Model>
     */
    public function broadcastOn(string $event): array
    {
        return [$this, $this->send];
    }

    /**
     * Mark the job as failed.
     */
    public function fail(Throwable $e): void
    {
        $this->fill([
            'failed' => true,
            'mailingid' => $this->send->mailingid,
        ]);

        if ($e instanceof BadResponseException) {
            $response = json_decode($e->getResponse()->getBody(), true);

            $this->update([
                'response' => $response,
                'status_code' => $e->getResponse()->getStatusCode(),
                'error_message' => is_array($response)
                    ? Arr::get($response, 'errorMessage', $e->getMessage())
                    : $e->getMessage(),
            ]);
        } else {
            $this->update([
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Bootstrap the model and its traits.
     */
    #[\Override]
    public static function booted(): void
    {
        static::saving(function (SendJob $model) {
            if ($model->failed === null && $model->status_code) {
                $model->failed = ! ($model->status_code >= 200 && $model->status_code < 300);
            }
        });
    }
}
