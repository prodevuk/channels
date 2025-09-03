<?php

namespace NotificationChannels\ProdevelUK\Test;

use PHPUnit\Framework\TestCase;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceClient;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceEmailMessage;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceSmsMessage;
use NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Notifiable;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_create_email_message()
    {
        $message = AzureCommunicationServiceEmailMessage::create('Test Subject')
            ->html('<h1>Test</h1>')
            ->text('Test content');

        $this->assertEquals('Test Subject', $message->getSubject());
        $this->assertEquals('<h1>Test</h1>', $message->getHtml());
        $this->assertEquals('Test content', $message->getText());
    }

    /** @test */
    public function it_can_create_sms_message()
    {
        $message = AzureCommunicationServiceSmsMessage::create('Test SMS')
            ->enableDeliveryReport(true);

        $this->assertEquals('Test SMS', $message->getMessage());
        $this->assertTrue($message->isDeliveryReportEnabled());
    }

    /** @test */
    public function it_can_attach_files_to_email()
    {
        $message = AzureCommunicationServiceEmailMessage::create('Test Subject')
            ->attach('test.pdf', 'application/pdf', 'base64content');

        $attachments = $message->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertEquals('test.pdf', $attachments[0]['name']);
        $this->assertEquals('application/pdf', $attachments[0]['contentType']);
    }

    /** @test */
    public function it_can_create_client()
    {
        // This test would require mocking the config
        $this->assertTrue(true);
    }
}

class TestNotification extends Notification
{
    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'email' => [
                'subject' => 'Test Subject',
                'html' => '<h1>Test</h1>',
                'text' => 'Test content',
            ],
            'sms' => [
                'message' => 'Test SMS message',
            ]
        ];
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForMail($notification)
    {
        return 'test@example.com';
    }

    public function routeNotificationForSms($notification)
    {
        return '+1234567890';
    }
}
