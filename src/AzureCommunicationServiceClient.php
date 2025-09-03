<?php

namespace NotificationChannels\ProdevelUK;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use NotificationChannels\ProdevelUK\Exceptions\CouldNotSendNotification;

class AzureCommunicationServiceClient
{
    protected $endpoint;
    protected $accessKey;

    public function __construct($connectionString = null)
    {
        $connectionString = $connectionString ?: Config::get('azure-communication.connection_string');
        
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

        // Add attachments if provided
        if (isset($emailData['attachments']) && is_array($emailData['attachments'])) {
            $payload['attachments'] = array_map(function($attachment) {
                return [
                    'name' => $attachment['name'],
                    'contentType' => $attachment['contentType'],
                    'contentInBase64' => $attachment['content']
                ];
            }, $emailData['attachments']);
        }

        try {
            $url = $this->endpoint . '/emails:send?api-version=2023-03-31';
            $headers = $this->generateAuthHeaders('POST', $url, json_encode($payload));
            $headers['Content-Type'] = 'application/json';

            $response = Http::withHeaders($headers)->post($url, $payload);

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
            $url = $this->endpoint . '/sms/send?api-version=2021-03-07';
            $headers = $this->generateAuthHeaders('POST', $url, json_encode($payload));
            $headers['Content-Type'] = 'application/json';

            $response = Http::withHeaders($headers)->post($url, $payload);

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
     * Generate authentication headers for Azure Communication Service
     *
     * @param string $method
     * @param string $url
     * @param string $body
     * @return array
     */
    protected function generateAuthHeaders($method, $url, $body)
    {
        $uri = parse_url($url);
        $host = $uri['host'];
        $path = $uri['path'] . (isset($uri['query']) ? '?' . $uri['query'] : '');
        
        $date = gmdate('D, d M Y H:i:s') . ' GMT';
        $contentHash = base64_encode(hash('sha256', $body, true));
        
        $stringToSign = $method . "\n" . $path . "\n" . $date . ";" . $host . ";" . $contentHash;
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->accessKey), true));
        
        return [
            'Authorization' => 'HMAC-SHA256 SignedHeaders=date;host;x-ms-content-sha256&Signature=' . $signature,
            'x-ms-date' => $date,
            'x-ms-content-sha256' => $contentHash,
        ];
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
