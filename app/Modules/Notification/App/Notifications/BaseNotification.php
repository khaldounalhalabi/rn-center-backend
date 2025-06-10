<?php

namespace App\Modules\Notification\App\Notifications;

use App\Enums\NotificationResourceEnum;
use App\Modules\Notification\App\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use NotificationChannels\Fcm\FcmMessage;

class BaseNotification extends Notification
{
    use Queueable;

    private array $data;
    private string $messageEn;
    private string $messageAR;
    private string $title;
    private string $resource;
    private mixed $resourceId;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct(array $data = [])
    {
        $this->data = [];
        $this->messageEn = '';
        $this->messageAR = '';
        $this->title = "";
        $this->resource = "";
        $this->resourceId = 0;
    }

    public function resource(string|NotificationResourceEnum $resource): static
    {
        $this->resource = $resource instanceof NotificationResourceEnum
            ? $resource->value
            : $resource;
        return $this;
    }

    public function resourceId(mixed $resourceId): static
    {
        $this->resourceId = $resourceId;
        return $this;
    }

    public function title(string $title = "New notification"): self
    {
        $this->title = $title;
        return $this;
    }

    public function via(mixed $notifiable): array
    {
        return Config::baseNotificationVia($notifiable);
    }

    public function data(array $data): static
    {
        $this->data = [
            ...$this->data,
            ...$data,
        ];
        return $this;
    }

    public function messageEn(string $message): static
    {
        $this->messageEn = $message;
        return $this;
    }

    public function messageAr(string $message): static
    {
        $this->messageAR = $message;
        return $this;
    }

    public function toDatabase(): array
    {
        return $this->data;
    }

    /**
     * @throws CouldNotSendNotification
     */
    public function toFcm(): FcmMessage
    {
        return
            FcmMessage::create()
                ->setData([
//                    'data' => json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'title' => $this->title,
                    'message_en' => $this->messageEn,
                    'message_ar' => $this->messageAR,
                    'type' => str_replace("App\\Notifications\\", "", static::class),
                    'resource' => "$this->resource",
                    'resource_id' => "$this->resourceId",
                    ...$this->data,
                ]);
    }
}
