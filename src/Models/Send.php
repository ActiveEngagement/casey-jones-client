<?php

namespace Actengage\CaseyJones\Models;

use Actengage\CaseyJones\Casts\MessageGearsFolder;
use Actengage\CaseyJones\Enums\SendStatus;
use Carbon\Carbon;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Send extends Model
{
    use BroadcastsEvents, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'app_id',
        'instance_id',
        'campaign_id',
        'name',
        'status',
        'subject',
        'html',
        'text',
        'folder',
        'from_address',
        'from_name',
        'reply_to_address',
        'reply_to_name',
        'data_variables',
        'meta',
        'template_id',
        'mailingid',
        'scheduled_at',
        'failed_at',
        'delivered_at',
    ];

    protected $attributes = [
        'meta' => '{}',
        'data_variables' => '[]'
    ];
    
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SendStatus::class,
            'meta' => 'json',
            'folder' => MessageGearsFolder::class,
            'data_variables' => 'json',
            'scheduled_at' => 'datetime',
            'failed_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /**
     * Get the jobs for the send.
     *
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(SendJob::class);
    }

    /**
     * Get and set the campaign id. This prevents using anything but the test
     * campaign for environments other than production.
     *
     * @return Attribute
     */
    protected function campaignId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => app()->environment('production') ? $value : config('services.mg.campaign_id'),
            set: fn ($value) => app()->environment('production') ? $value : config('services.mg.campaign_id'),
        );
    }

    /**
     * Scope the query for the given statuses.
     *
     * @param Builder $query
     * @param SendStatus ...$statuses
     * @return void
     */
    public function scopeStatus(Builder $query, SendStatus ...$statuses): void
    {
        $query->whereIn('status', $statuses);
    }

    /**
     * Scope the query for draft statuses.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeDraft(Builder $query): void
    {
        $query->where('status', SendStatus::Draft);
    }

    /**
     * Scope the query for scheduled statuses.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeScheduled(Builder $query): void
    {
        $query->where('status', SendStatus::Scheduled);
    }

    /**
     * Scope the query for active statuses.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', SendStatus::Active);
    }

    /**
     * Scope the query for failed statuses.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeFailed(Builder $query): void
    {
        $query->where('status', SendStatus::Failed);
    }

    /**
     * Scope the query for queued statuses.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeQueued(Builder $query): void
    {
        $query->where('status', SendStatus::Queued);
    }

    /**
     * Scope the query to the given instance.
     *
     * @param Builder $query
     * @param Instance|int $campaign
     * @return void
     */
    public function scopeInstance(Builder $query, int $instance): void
    {
        $query->where('instance_id', $instance);
    }

    /**
     * Scope the query to the given campaign.
     *
     * @param Builder $query
     * @param int $campaign
     * @return void
     */
    public function scopeCampaignId(Builder $query, int $campaign_id): void
    {
        $query->where('campaign_id', $campaign_id);
    }

    /**
     * Scope the query for sends scheduled within a date range.
     *
     * @param Builder $query
     * @param Carbon|string $date
     * @return void
     */
    public function scopeScheduledAt(Builder $query, Carbon|string $date): void
    {
        if(is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        $query->whereBetween('scheduled_at', [
            $date->clone()->subMinutes(5), $date->clone()->addMinutes(5)
        ]);
    }

    /**
     * SCope the query for sends that are ready to send.
     *
     * @param Builder $query
     * @return void
     */
    public function scopeReadyToSend(Builder $query): void
    {
        $query
            ->scheduled()
            ->where('scheduled_at', '<=', now());
    }

    /**
     * A scope restricted to sends that have been active for at least an hour.
     * 
     * @param Builder $query
     * @return void
     */
    public function scopeActiveTooLong(Builder $query): void
    {
        $query
            ->where('status', SendStatus::Active)
            ->where('updated_at', '<=', now()->subHour());
    }

    /**
     * Detemines if the send is one of the given statuses.
     *
     * @param SendStatus ...$statuses
     * @return boolean
     */
    public function isStatus(SendStatus ...$statuses): bool
    {
        return in_array($this->status, $statuses);
    }

    /**
     * Get the channels that model events should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel|\Illuminate\Database\Eloquent\Model>
     */
    public function broadcastOn(string $event): array
    {
        return [$this, new PrivateChannel('sends')];
    }

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function booted(): void
    {
        static::saving(function(Send $model) {
            if($model->status === SendStatus::Draft) {
                $model->scheduled_at = null;
            }
            else if($model->status === SendStatus::Scheduled) {
                $model->failed_at = null;
            }
        });
    }
}
