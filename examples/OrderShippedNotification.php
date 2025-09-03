<?php

namespace Examples;

use Illuminate\Notifications\Notification;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel;

class OrderShippedNotification extends Notification
{
    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'email' => [
                'subject' => 'Your Order #' . $this->order->id . ' Has Shipped',
                'html' => $this->getEmailHtml(),
                'text' => $this->getEmailText(),
            ],
            'sms' => [
                'message' => 'Your order #' . $this->order->id . ' has been shipped! Track it at: ' . $this->order->tracking_url
            ]
        ];
    }

    protected function getEmailHtml()
    {
        return '
            <h1>Order Shipped!</h1>
            <p>Hello ' . $this->order->customer_name . ',</p>
            <p>Great news! Your order #' . $this->order->id . ' has been shipped and is on its way to you.</p>
            <p><strong>Tracking Number:</strong> ' . $this->order->tracking_number . '</p>
            <p><strong>Estimated Delivery:</strong> ' . $this->order->estimated_delivery . '</p>
            <p>You can track your package <a href="' . $this->order->tracking_url . '">here</a>.</p>
            <p>Thank you for your business!</p>
        ';
    }

    protected function getEmailText()
    {
        return "
            Order Shipped!
            
            Hello {$this->order->customer_name},
            
            Great news! Your order #{$this->order->id} has been shipped and is on its way to you.
            
            Tracking Number: {$this->order->tracking_number}
            Estimated Delivery: {$this->order->estimated_delivery}
            
            Track your package: {$this->order->tracking_url}
            
            Thank you for your business!
        ";
    }
}
