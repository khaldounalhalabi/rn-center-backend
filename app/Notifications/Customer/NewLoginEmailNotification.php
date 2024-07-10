<?php

namespace App\Notifications\Customer;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLoginEmailNotification extends Notification
{
    use Queueable;

    public string $ip;
    public string $deviceType;
    public string $browserType;

    /**
     * @param string $ip
     * @param string $deviceType
     * @param string $browserType
     */
    public function __construct(string $ip, string $deviceType, string $browserType)
    {
        $this->ip = $ip;
        $this->deviceType = $deviceType;
        $this->browserType = $browserType;
    }


    /**
     * Get the notification's delivery channels.
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("POM - New Login To Your Account")
            ->view('emails.new-login', [
                'ip'          => $this->ip,
                'deviceType'  => $this->deviceType,
                'browserType' => $this->browserType,
            ]);
    }

    /**
     * Get the array representation of the notification.
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
