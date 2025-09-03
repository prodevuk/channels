<?php

namespace NotificationChannels\ProdevelUK;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification;

class AzureCommunicationServiceClient
{
    protected $endpoint;
    protected $accessKey;

    public function __construct()
    {
        $connectionString = Config::get('azure-communication.connection_string');
        
        if (!$connectionString) {
            throw new \InvalidArgumentException('Azure Communication Service connection string is not configured.');
        }

        $this->endpoint = $this->extractEndpoint($connectionString);
        $this->accessKey = $this->extractAccessKey($connectionString);
    }

    protected function extractEndpoint($connectionString)
    {
        // Parse connection string to extract endpoint
        // Format: endpoint=https://your-resource.communication.azure.com/;accesskey=your-access-key
        if (preg_match('/endpoint=([^;]+)/', $connectionString, $matches)) {
            return rtrim($matches[1], '/');
        }
        throw new \InvalidArgumentException('Invalid connection string format.');
    }

    protected function extractAccessKey($connectionString)
    {
        // Extract access key from connection string
        if (preg_match('/accesskey=([^;]+)/', $connectionString, $matches)) {
            return $matches[1];
        }
        throw new \InvalidArgumentException('Access key not found in connection string.');
    }

    /**
     * Send email via Azure Communication Service
     *
     * @param string $senderAddress
     * @param string $recipientAddress
     * @param array $emailData
     * @throws CouldNotSendNotification
     */
    public function sendEmail($senderAddress, $recipientAddress, array $emailData)
    {
        $payload = [
            'senderAddress' => $senderAddress,
            'content' => [
                'subject' => $emailData['subject'] ?? '',
            ],
            'recipients' => [
                'to' => [
                    ['address' => $recipientAddress]
                ]
            ]
        ];

        if (isset($emailData['html'])) {
            $payload['content']['html'] = $emailData['html'];
        }
        
        if (isset($emailData['text'])) {
            $payload['content']['plainText'] = $emailData['text'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint . '/emails:send?api-version=2023-03-31', $payload);

            if (!$response->accepted()) {
                throw CouldNotSendNotification::serviceRespondedWithAnError(
                    'Email sending failed: ' . $response->body()
                );
            }

            return $response;

        } catch (\Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e->getMessage());
        }
    }

    /**
     * Send SMS via Azure Communication Service
     *
     * @param string $senderNumber
     * @param string $recipientNumber
     * @param array $smsData
     * @throws CouldNotSendNotification
     */
    public function sendSms($senderNumber, $recipientNumber, array $smsData)
    {
        $payload = [
            'from' => $senderNumber,
            'smsRecipients' => [
                ['to' => $recipientNumber]
            ],
            'message' => $smsData['message'] ?? '',
            'smsSendOptions' => [
                'enableDeliveryReport' => true
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accessKey,
                'Content-Type' => 'application/json',
            ])->post($this->endpoint . '/sms/send?api-version=2021-03-07', $payload);

            if (!$response->accepted()) {
                throw CouldNotSendNotification::serviceRespondedWithAnError(
                    'SMS sending failed: ' . $response->body()
                );
            }

            return $response;

        } catch (\Exception $e) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($e->getMessage());
        }
    }

    /**
     * Get the Azure Communication Service endpoint
     *
     * @return string
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }
}
