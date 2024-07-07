<?php

namespace App\Traits;

use App\Jobs\SendNotificationJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait NotificationSender
{
    /**
     * This function send to single user if user have fcm token
     * @param       $notification
     * @param array $data
     * @param User  $user
     * @return void
     */
    public function sendToSingleUser($notification, array $data, User $user): void
    {
        $this->sendNotification($notification, $data, $user);
    }

    /**
     * @param       $notification
     * @param array $data
     * @param       $users
     * @return void
     */
    private function sendNotification($notification, array $data, $users): void
    {
        SendNotificationJob::dispatch($users, $notification, $data);
    }

    /**
     * This function send to array of users ids if they have fcm tokens
     * @param       $notification
     * @param array $data
     * @param array $users_ids
     * @return void
     */
    public function sendToManyUsers($notification, array $data, array $users_ids): void
    {
        User::query()
            ->whereIn('id', $users_ids)
            ->chunk(25,
                fn($users) => $this->sendNotification($notification, $data, $users)
            );
    }

    /**
     * This function send to array of users ids if they have fcm tokens
     * @param        $notification
     * @param array  $data
     * @param string $role
     * @return void
     */
    public function sendToUsersByRole($notification, array $data, string $role = 'admin'): void
    {
        User::whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        })->chunk(25,
            fn($users) => $this->sendNotification($notification, $data, $users)
        );
    }

    public function sendByQuery(Builder $query, array $data, $notification): void
    {
        $query->chunk(25,
            fn($users) => $this->sendNotification($notification, $data, $users)
        );
    }
}
