<?php

namespace App\Services;

use App\Enums\RolesPermissionEnum;
use App\Traits\NotificationSender;
use Illuminate\Database\Eloquent\Builder;

class FirebaseServices
{
    use NotificationSender;

    const ONE = "one";
    const MANY = "many";
    const ByRole = "byRole";
    const ToQuery = 'byQuery';

    private static $instance;
    private $notification;
    private array $data;
    private $to;
    private $method;
    private $role;

    private function __construct()
    {
        $this->notification = '';
        $this->data = [];
        $this->to = null;
        $this->role = RolesPermissionEnum::ADMIN['role'];
        $this->method = 'sendForOneDevice';
    }

    public static function make(): static
    {
        if (self::$instance) {
            return self::$instance;
        }

        return new static();
    }

    public function send(): void
    {
        if ($this->method == "sendToQuery" && $this->to) {
            $this->{$this->method}($this->to, $this->notification, $this->data);
        } elseif ($this->method != 'sendToRole') {
            $this->{$this->method}($this->notification, $this->data, $this->to);
        } else {
            $this->{$this->method}($this->notification, $this->data, $this->role);
        }
    }

    public function setData($data): static
    {
        $this->data = $data;

        return $this;
    }

    public function setRole($role): static
    {
        $this->role = $role;

        return $this;
    }

    public function setNotification($notification): static
    {
        $this->notification = $notification;

        return $this;
    }

    public function setTo($to): static
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod(string $method): static
    {
        $this->method = match ($method) {
            'one', null => 'sendForOneDevice',
            'many' => 'sendForMultiDevices',
            'byRole' => 'sendToRole',
            'byQuery' => 'sendToQuery'
        };

        return $this;
    }

    private function sendForOneDevice($notification, array $data, $user): void
    {
        $this->sendToSingleUser($notification, $data, $user);
    }

    private function sendForMultiDevices($notification, array $data, array $users_ids): void
    {
        $this->sendToManyUsers($notification, $data, $users_ids);
    }

    private function sendToRole($notification, array $data, ?string $role = 'admin'): void
    {
        $this->sendToUsersByRole($notification, $data, $role);
    }

    private function sendToQuery(Builder $query, $notification, array $data): void
    {
        $this->sendByQuery($query, $data, $notification);
    }
}
