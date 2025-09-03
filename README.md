# Laravel Notification Channel for Azure Communication Service

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravel-notification-channels/azure-communication-services.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/azure-communication-services)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/laravel-notification-channels/azure-communication-services/master.svg?style=flat-square)](https://travis-ci.org/laravel-notification-channels/azure-communication-services)
[![StyleCI](https://styleci.io/repos/:style_ci_id/shield)](https://styleci.io/repos/:style_ci_id)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/:sensio_labs_id.svg?style=flat-square)](https://insight.sensiolabs.com/projects/:sensio_labs_id)
[![Quality Score](https://img.shields.io/scrutinizer/g/laravel-notification-channels/azure-communication-services.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/azure-communication-services)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/laravel-notification-channels/azure-communication-services/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/laravel-notification-channels/azure-communication-services/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-notification-channels/azure-communication-services.svg?style=flat-square)](https://packagist.org/packages/laravel-notification-channels/azure-communication-services)

This package makes it easy to send notifications using [Azure Communication Service](https://azure.microsoft.com/en-us/services/communication-services/) with Laravel 10.x, 11.x, and 12.x. It supports both SMS and email notifications through Azure's reliable communication platform using the official REST API.

```php
use NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceEmailMessage;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceSmsMessage;

// Send both email and SMS
$user->notify(new OrderShipped());
```



## Contents

- [Installation](#installation)
	- [Setting up the Azure Communication Service](#setting-up-the-azure-communication-service)
- [Usage](#usage)
	- [Sending Email Notifications](#sending-email-notifications)
	- [Sending SMS Notifications](#sending-sms-notifications)
	- [Sending Both Email and SMS](#sending-both-email-and-sms)
	- [Available Message methods](#available-message-methods)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

You can install the package via composer:

```bash
composer require laravel-notification-channels/azure-communication-services
```

**Note:** This package uses the Azure Communication Services REST API since Microsoft does not provide an official PHP SDK. The implementation uses Laravel's HTTP client with proper HMAC-SHA256 authentication as specified in the [Azure Communication Services API documentation](https://learn.microsoft.com/en-us/rest/api/communication/email/email/send?view=rest-communication-email-2023-03-31&tabs=HTTP).

The service provider will automatically register itself.

You can publish the config file with:

```bash
php artisan vendor:publish --provider="NotificationChannels\ProdevelUK\AzureCommunicationServiceServiceProvider" --tag="config"
```

### Setting up the Azure Communication Service

1. Create an Azure Communication Service resource in your Azure portal
2. Get your connection string from the resource
3. Set up your email domain and SMS phone number
4. Add the following to your `.env` file:

```env
AZURE_COMMUNICATION_CONNECTION_STRING=your_connection_string_here
AZURE_COMMUNICATION_EMAIL_SENDER=noreply@yourdomain.com
AZURE_COMMUNICATION_SMS_SENDER=+1234567890
```

## Usage

### Sending Email Notifications

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceEmailMessage;

class OrderShipped extends Notification
{
    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'email' => [
                'subject' => 'Your Order Has Shipped',
                'html' => '<h1>Order Shipped!</h1><p>Your order has been shipped and will arrive soon.</p>',
                'text' => 'Order Shipped! Your order has been shipped and will arrive soon.',
            ]
        ];
    }
}
```

### Sending SMS Notifications

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel;

class OrderShipped extends Notification
{
    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'sms' => [
                'message' => 'Your order has been shipped! Track it at: https://example.com/track/12345'
            ]
        ];
    }
}
```

### Sending Both Email and SMS

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\ProdevelUK\AzureCommunicationServiceChannel;

class OrderShipped extends Notification
{
    public function via($notifiable)
    {
        return [AzureCommunicationServiceChannel::class];
    }

    public function toAzureCommunication($notifiable)
    {
        return [
            'email' => [
                'subject' => 'Your Order Has Shipped',
                'html' => '<h1>Order Shipped!</h1><p>Your order has been shipped and will arrive soon.</p>',
                'text' => 'Order Shipped! Your order has been shipped and will arrive soon.',
            ],
            'sms' => [
                'message' => 'Your order has been shipped! Track it at: https://example.com/track/12345'
            ]
        ];
    }
}
```

### Available Message methods

#### Email Message Methods

- `subject($subject)` - Set the email subject
- `html($html)` - Set the HTML content
- `text($text)` - Set the plain text content
- `attach($name, $contentType, $content)` - Add an attachment

#### SMS Message Methods

- `message($message)` - Set the SMS message content
- `enableDeliveryReport($enable)` - Enable or disable delivery reports

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email promise@prodevel.co.uk instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Prodevel](https://github.com/prodevuk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
