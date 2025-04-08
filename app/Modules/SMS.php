<?php

namespace App\Modules;

use AndroidSmsGateway\Client;
use AndroidSmsGateway\Domain\Message;
use AndroidSmsGateway\Domain\MessageState;
use App\Traits\Makable;
use Illuminate\Support\Arr;

class SMS
{
    use Makable;

    private array $phones = [];

    private Client $client;
    private string $content;
    private MessageState $messageStatus;
    private string $messageId;
    private bool $success = false;

    public function init(): void
    {
        $this->client = new Client(config('sms.username'), config('sms.password'));
    }

    public function message(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function to(array|string $phones): static
    {
        $this->phones = Arr::wrap($phones);
        return $this;
    }

    public function send(): static
    {
        try {
            if (app()->environment('production')) {
                $message = new Message($this->content, $this->phones);
                $this->messageStatus = $this->client->send($message);
                $this->messageId = $this->messageStatus->ID();
            }
            $this->success = true;
            return $this;
        } catch (\Exception $exception) {
            logger()->info("----------Error Sending SMS Message----------");
            logger()->info($exception->getMessage());
            logger()->info($exception->getFile());
            logger()->info($exception->getLine());
            logger()->info("---------------------------------------------");
            $this->success = false;
            return $this;
        }
    }

    public function getStatus(): string
    {
        if ($this->success) {
            return (string)$this->client->GetState($this->messageStatus->ID())->State();
        }
        return "Failed to send SMS";
    }

    public function succeed(): bool
    {
        return $this->success;
    }
}
