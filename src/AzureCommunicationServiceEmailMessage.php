<?php

namespace NotificationChannels\ProdevelUK;

use Illuminate\Support\Arr;

class AzureCommunicationServiceEmailMessage
{
    protected $subject;
    protected $html;
    protected $text;
    protected $attachments = [];

    /**
     * Create a new email message instance.
     *
     * @param string $subject
     */
    public function __construct($subject = '')
    {
        $this->subject = $subject;
    }

    /**
     * Create a new email message instance.
     *
     * @param string $subject
     * @return static
     */
    public static function create($subject = '')
    {
        return new static($subject);
    }

    /**
     * Set the email subject.
     *
     * @param string $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Set the HTML content.
     *
     * @param string $html
     * @return $this
     */
    public function html($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Set the plain text content.
     *
     * @param string $text
     * @return $this
     */
    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Add an attachment to the email.
     *
     * @param string $name
     * @param string $contentType
     * @param string $content
     * @return $this
     */
    public function attach($name, $contentType, $content)
    {
        $this->attachments[] = [
            'name' => $name,
            'contentType' => $contentType,
            'content' => $content,
        ];

        return $this;
    }

    /**
     * Get the email subject.
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Get the HTML content.
     *
     * @return string|null
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Get the plain text content.
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get the attachments.
     *
     * @return array
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * Convert the message to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'subject' => $this->subject,
            'html' => $this->html,
            'text' => $this->text,
            'attachments' => $this->attachments,
        ];
    }
}
