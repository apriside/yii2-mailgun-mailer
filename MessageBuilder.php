<?php

namespace apriside\mailgunmailer;

use Mailgun\Messages\MessageBuilder as MailgunMessageBuilder;

class MessageBuilder extends MailgunMessageBuilder
{
    /**
     * @param string      $attachmentContent
     * @param string|null $attachmentName
     *
     * @return bool
     */
    public function addContent($attachmentContent, $attachmentName = null)
    {
        if (!isset($this->files['attachment'])) {
            $this->files['attachment'] = [];
        }

        $attachment = [
            'fileContent' => $attachmentContent,
            'filename' => $attachmentName
        ];
        array_push($this->files['attachment'], $attachment);

        return true;
    }
}