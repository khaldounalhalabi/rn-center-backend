<?php

namespace App\Modules\Notification\App;

use App\Modules\Notification\App\Enums\NotifyMethod;
use App\Modules\Notification\App\Jobs\SendNotificationJob;
use App\Modules\Notification\App\Notifications\BaseNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class NotificationBuilder
{
    private string $method;
    private string $notification;
    private array $data;
    private array|Collection|int|Model|string|Relation|Builder|QueryBuilder $to;
    private array|Collection|Model|Builder|QueryBuilder|Relation $target;

    private static mixed $instance = null;

    private function __construct()
    {
    }

    public static function make(): static
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function send(): void
    {
        $this->{$this->method}();
        if ($this->target instanceof Builder
            || $this->target instanceof QueryBuilder
            || $this->target instanceof Relation
        ) {
            $this->target->chunk(25, fn($users) => $this->sendNotification($users));
            return;
        }

        $this->sendNotification($this->target);
    }

    public function data(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param class-string<BaseNotification> $notification
     * @return $this
     */
    public function notification(string $notification): static
    {
        $this->notification = $notification;
        return $this;
    }

    /**
     * @param Collection|array|Builder|Model|Relation|QueryBuilder|string $to
     * @return static
     */
    public function to(Collection|array|Builder|Model|Relation|QueryBuilder|string $to): static
    {
        $this->to = $to;
        return $this;
    }

    public function method(NotifyMethod $method): static
    {
        $this->method = match ($method) {
            NotifyMethod::ONE => 'one',
            NotifyMethod::MANY => 'many',
            NotifyMethod::BY_ROLE => 'byRole',
            NotifyMethod::TO_QUERY => 'byQuery'
        };
        return $this;
    }

    private function one(): static
    {
        if (is_numeric($this->to)) {
            $user = Config::userQuery()->where('id', $this->to)->firstOrFail();
        } else {
            $user = $this->to;
        }

        $this->target = $user;
        return $this;
    }

    private function many(): static
    {
        if (empty($this->to)) {
            return $this;
        }

        if ($this->to instanceof Collection) {
            $first = $this->to->first();
        } else {
            $first = $this->to[0];
        }

        if (is_numeric($first)) {
            $this->target = Config::userQuery()->whereIn(
                'id',
                $this->to instanceof Collection
                    ? $this->to->toArray()
                    : $this->to
            );
        } else {
            $this->target = $this->to;
        }

        return $this;
    }

    private function byRole(): static
    {
        $this->target = Config::userQuery()->whereHas('roles', function (Builder $query) {
            $query->where('name', $this->to);
        });
        return $this;
    }

    private function byQuery(): static
    {
        $this->target = $this->to;
        return $this;
    }

    private function sendNotification($users): void
    {
        SendNotificationJob::dispatch($users, $this->notification, $this->data);
    }
}
