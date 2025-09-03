<?php

namespace NotificationChannels\ProdevelUK;

class AzureCommunicationServiceSmsMessage
{
    protected $message;
    protected $enableDeliveryReport = true;

    /**
     * Create a new SMS message instance.
     *
     * @param string $message
     */
    public function __construct($message = '')
    {
        $this->message = $message;
    }

    /**
     * Create a new SMS message instance.
     *
     * @param string $message
     * @return static
     */
    public static function create($message = '')
    {
        return new static($message);
    }

    /**
     * Set the SMS message content.
     *
     * @param string $message
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Enable or disable delivery reports.
     *
     * @param bool $enable
     * @return $this
     */
    public function enableDeliveryReport($enable = true)
    {
        $this->enableDeliveryReport = $enable;

        return $this;
    }

    /**
     * Get the message content.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Check if delivery reports are enabled.
     *
     * @return bool
     */
    public function isDeliveryReportEnabled()
    {
        return $this->enableDeliveryReport;
    }

    /**
     * Convert the message to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->message,
            'enableDeliveryReport' => $this->enableDeliveryReport,
        ];
    }
}
