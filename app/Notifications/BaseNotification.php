<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\Exceptions\CouldNotSendNotification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;

class BaseNotification extends Notification
{
    use Queueable;

    private array $data;

    private string $message;

    private string $messageAR;

    /** @var class-string */
    private string $type = BaseNotification::class;

    /**
     * Create a new notification instance.
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = [];
        $this->message = '';
        $this->messageAR = '';
    }

    /**
     * Get the notification's delivery channels.
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        if ($notifiable->fcm_token) {
            return [FcmChannel::class, 'database'];
        }

        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * @param mixed $notifiable
     * @return FcmMessage
     * @throws CouldNotSendNotification
     */
    public function toFcm(mixed $notifiable): FcmMessage
    {
        return FcmMessage::create()
            ->setData([
                'data'       => json_encode($this->data),
                'title'      => env('APP_NAME', 'Rakeen Jawaher'),
                'body'       => $this->message,
                'message'    => $this->message,
                'body_ar'    => $this->messageAR,
                'message_ar' => $this->messageAR,
                "type"       => $this->type,
            ]);
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @param class-string $type
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = str_replace("App\\Notifications\\", "", $type);
    }

    public function setMessage($message): void
    {
        $this->message = $message;
        $this->data = array_merge(['message_en' => $this->message], $this->data);
    }

    public function setMessageAR($message): void
    {
        $this->messageAR = $message;
        $this->data = array_merge(['message_ar' => $this->messageAR], $this->data);
    }

    /**
     * Get the array representation of the notification.
     * @return array
     */
    public function toDatabase(): array
    {
        return $this->data;
    }

    public function fcmProject($notifiable, $message): string
    {
        return 'app'; // name of the firebase project to use
    }

    public function getFrontUrl(): string
    {
        return "http://localhost:3000/" . app()->getLocale() . "/";
    }
}
