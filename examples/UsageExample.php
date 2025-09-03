<?php

/**
 * Example usage of the Azure Communication Service notification channel
 */

use Examples\OrderShippedNotification;

// Example order data
$order = (object) [
    'id' => '12345',
    'customer_name' => 'John Doe',
    'tracking_number' => '1Z999AA1234567890',
    'estimated_delivery' => '2024-01-15',
    'tracking_url' => 'https://example.com/track/1Z999AA1234567890'
];

// Example user/notifiable
$user = new class {
    use \Illuminate\Notifications\Notifiable;
    
    public function routeNotificationForMail($notification)
    {
        return 'john.doe@example.com';
    }
    
    public function routeNotificationForSms($notification)
    {
        return '+1234567890';
    }
};

// Send the notification
$user->notify(new OrderShippedNotification($order));

// Or send only email
class EmailOnlyNotification extends \Illuminate\Notifications\Notification
{
    public function via($notifiable)
    {
        return [\NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'email' => [
                'subject' => 'Welcome to our service!',
                'html' => '<h1>Welcome!</h1><p>Thank you for joining us.</p>',
                'text' => 'Welcome! Thank you for joining us.'
            ]
        ];
    }
}

// Or send only SMS
class SmsOnlyNotification extends \Illuminate\Notifications\Notification
{
    public function via($notifiable)
    {
        return [\NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'sms' => [
                'message' => 'Your verification code is: 123456'
            ]
        ];
    }
}
