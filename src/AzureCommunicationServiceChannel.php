<?php

namespace NotificationChannels\ProdevelUK;

use NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class AzureCommunicationServiceChannel
{
    protected $client;

    public function __construct(AzureCommunicationServiceClient $client)
    {
        $this->client = $client;
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
        // Check if notification has toAzureCommunication method (combined email/SMS)
        if (method_exists($notification, 'toAzureCommunication')) {
            $message = $notification->toAzureCommunication($notifiable);

            if (isset($message['email'])) {
                $this->sendEmail($notifiable, $message['email']);
            }

            if (isset($message['sms'])) {
                $this->sendSms($notifiable, $message['sms']);
            }
        }
        // Check if notification has toEmail method (email only)
        elseif (method_exists($notification, 'toEmail')) {
            $emailData = $notification->toEmail($notifiable);
            $this->sendEmail($notifiable, $emailData);
        }
        // Check if notification has toSms method (SMS only)
        elseif (method_exists($notification, 'toSms')) {
            $smsData = $notification->toSms($notifiable);
            $this->sendSms($notifiable, $smsData);
        }
        else {
            throw CouldNotSendNotification::invalidMessage(
                'Notification must implement toAzureCommunication(), toEmail(), or toSms() method.'
            );
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
            throw CouldNotSendNotification::invalidRecipient('email');
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
            throw CouldNotSendNotification::invalidRecipient('SMS');
        }

        $message = $smsData['message'] ?? '';
        
        if (empty($message)) {
            throw CouldNotSendNotification::invalidMessage('SMS message content is empty');
        }

        return $this->client->sendSms($senderNumber, $recipientNumber, $smsData);
    }
}
