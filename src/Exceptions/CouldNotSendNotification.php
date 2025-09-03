<?php

namespace NotificationChannels\ProdevelUK\Exceptions;

class CouldNotSendNotification extends \Exception
{
    /**
     * Create a new exception instance when the service responds with an error.
     *
     * @param mixed $response
     * @return static
     */
    public static function serviceRespondedWithAnError($response)
    {
        $message = 'Azure Communication Service responded with an error.';
        
        if (is_string($response)) {
            $message = $response;
        } elseif (is_object($response) && method_exists($response, 'body')) {
            $body = $response->body();
            $message = "Azure Communication Service error: {$body}";
        } elseif (is_array($response)) {
            $message = 'Azure Communication Service error: ' . json_encode($response);
        }
        
        return new static($message);
    }

    /**
     * Create a new exception instance when the service is not configured.
     *
     * @param string $reason
     * @return static
     */
    public static function serviceNotConfigured($reason = 'Azure Communication Service is not properly configured.')
    {
        return new static($reason);
    }

    /**
     * Create a new exception instance when the recipient is invalid.
     *
     * @param string $type
     * @param string $recipient
     * @return static
     */
    public static function invalidRecipient($type, $recipient = null)
    {
        $message = "Invalid {$type} recipient";
        if ($recipient) {
            $message .= ": {$recipient}";
        }
        
        return new static($message);
    }

    /**
     * Create a new exception instance when the message content is invalid.
     *
     * @param string $reason
     * @return static
     */
    public static function invalidMessage($reason = 'Invalid message content provided.')
    {
        return new static($reason);
    }

    /**
     * Create a new exception instance when authentication fails.
     *
     * @param string $reason
     * @return static
     */
    public static function authenticationFailed($reason = 'Azure Communication Service authentication failed.')
    {
        return new static($reason);
    }
}
