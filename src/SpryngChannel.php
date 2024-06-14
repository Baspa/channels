<?php

namespace NotificationChannels\Spryng;

use Spryng\SpryngRestApi\Spryng;
use Illuminate\Notifications\Notification;
use NotificationChannels\Spryng\Exceptions\CouldNotSendNotification;

class SpryngChannel
{
    public function __construct(
        private Spryng $spryng
    ) {
        $this->spryng = $spryng;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\Spryng\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->phone;

        if (!$to) {
            return;
        }

        $message = $notification->toSpryng($notifiable);

        $response = $this->spryng->message->create($message);

        if (!$response || $response->serverError()) {
            throw new CouldNotSendNotification(
                message: 'Message could not be send because of a server error...',
                code: $response->getResponseCode()
            );
        }
    }
}
