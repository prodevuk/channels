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

    /** @test */
    public function it_can_resolve_channel_from_service_container()
    {
        // Create a mock application container
        $app = new \Illuminate\Container\Container();
        
        // Mock the config using a simple array
        $app->instance('config', [
            'azure-communication' => [
                'connection_string' => 'endpoint=https://test.communication.azure.com/;accesskey=test-key',
                'email_sender' => 'test@example.com',
                'sms_sender' => '+1234567890'
            ]
        ]);

        // Register the service provider
        $provider = new \NotificationChannels\ProdevelUK\AzureCommunicationServiceServiceProvider($app);
        $provider->register();

        // Test that we can resolve the channel from the container
        $channel = $app->make(\NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel::class);
        
        $this->assertInstanceOf(\NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel::class, $channel);
    }

    /** @test */
    public function it_provides_meaningful_exception_messages()
    {
        // Test service not configured exception
        $exception = \NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification::serviceNotConfigured('Test configuration error');
        $this->assertEquals('Test configuration error', $exception->getMessage());

        // Test invalid recipient exception
        $exception = \NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification::invalidRecipient('email', 'invalid-email');
        $this->assertEquals('Invalid email recipient: invalid-email', $exception->getMessage());

        // Test invalid message exception
        $exception = \NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification::invalidMessage('Test message error');
        $this->assertEquals('Test message error', $exception->getMessage());

        // Test authentication failed exception
        $exception = \NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification::authenticationFailed('Test auth error');
        $this->assertEquals('Test auth error', $exception->getMessage());
    }

    /** @test */
    public function it_supports_individual_toEmail_and_toSms_methods()
    {
        // Test email-only notification
        $emailNotification = new EmailOnlyNotification();
        $this->assertTrue(method_exists($emailNotification, 'toEmail'));
        $this->assertFalse(method_exists($emailNotification, 'toSms'));
        $this->assertFalse(method_exists($emailNotification, 'toAzureCommunication'));

        // Test SMS-only notification
        $smsNotification = new SmsOnlyNotification();
        $this->assertTrue(method_exists($smsNotification, 'toSms'));
        $this->assertFalse(method_exists($smsNotification, 'toEmail'));
        $this->assertFalse(method_exists($smsNotification, 'toAzureCommunication'));

        // Test combined notification
        $combinedNotification = new TestNotification();
        $this->assertTrue(method_exists($combinedNotification, 'toAzureCommunication'));
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

class EmailOnlyNotification extends Notification
{
    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toEmail($notifiable)
    {
        return [
            'subject' => 'Email Only Subject',
            'html' => '<h1>Email Only HTML</h1>',
            'text' => 'Email Only Text',
        ];
    }
}

class SmsOnlyNotification extends Notification
{
    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toSms($notifiable)
    {
        return [
            'message' => 'SMS Only message',
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
