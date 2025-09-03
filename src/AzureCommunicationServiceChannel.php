<?php

namespace NotificationChannels\ProdevelUK;

use NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class AzureCommunicationServiceChannel
{
    protected $client;

    public function __construct(AzureCommunicationServiceClient $client = null)
    {
        $this->client = $client ?: app(AzureCommunicationServiceClient::class);
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @throws \NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toAzureCommunication($notifiable);

        if (isset($message['email'])) {
            $this->sendEmail($notifiable, $message['email']);
        }

        if (isset($message['sms'])) {
            $this->sendSms($notifiable, $message['sms']);
        }
    }

    /**
     * Send email notification via Azure Communication Service
     *
     * @param mixed $notifiable
     * @param array $emailData
     * @throws CouldNotSendNotification
     */
    protected function sendEmail($notifiable, array $emailData)
    {
        $senderAddress = Config::get('azure-communication.email_sender');
        $recipientAddress = $notifiable->routeNotificationFor('mail', $notifiable);

        if (!$recipientAddress) {
            throw CouldNotSendNotification::serviceRespondedWithAnError('No email address found for notifiable');
        }

        return $this->client->sendEmail($senderAddress, $recipientAddress, $emailData);
    }

    /**
     * Send SMS notification via Azure Communication Service
     *
     * @param mixed $notifiable
     * @param array $smsData
     * @throws CouldNotSendNotification
     */
    protected function sendSms($notifiable, array $smsData)
    {
        $senderNumber = Config::get('azure-communication.sms_sender');
        $recipientNumber = $notifiable->routeNotificationFor('sms', $notifiable);

        if (!$recipientNumber) {
            throw CouldNotSendNotification::serviceRespondedWithAnError('No phone number found for notifiable');
        }

        $message = $smsData['message'] ?? '';
        
        if (empty($message)) {
            throw CouldNotSendNotification::serviceRespondedWithAnError('SMS message content is empty');
        }

        return $this->client->sendSms($senderNumber, $recipientNumber, $smsData);
    }
}
