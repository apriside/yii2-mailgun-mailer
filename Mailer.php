<?php

namespace apriside\mailgunmailer;

use Http\Client\Exception\RequestException;
use Mailgun\Connection\Exceptions\MissingRequiredParameters;
use Mailgun\HttpClientConfigurator;
use Mailgun\Hydrator\ArrayHydrator;
use Yii;
use yii\base\InvalidArgumentException;
use yii\mail\BaseMailer;
use Mailgun\Mailgun;

/**
 * Mailer implements a mailer based on Mailgun.
 *
 * To use Mailer, you should configure it in the application configuration like the following,
 *
 * ~~~
 * 'components' => [
 *     ...
 *     'mailer' => [
 *         'class' => 'e96\mailgunmailer\Mailer',
 *         'domain' => 'example.com',
 *         'key' => 'key-somekey',
 *         'tags' => ['yii'],
 *         'clickTracking' => false,
 *         'opensTracking' => false
 *     ],
 *     ...
 * ],
 * ~~~
 */
class Mailer extends BaseMailer
{

    /**
     * [$messageClass description]
     * @var string message default class name.
     */
    public $messageClass = 'apriside\mailgunmailer\Message';

    public $domain;
    public $key;
    public $from;

    public $tags = [];
    public $campaignId;
    public $dkim;
    public $testMode;
    public $clickTracking;
    public $opensTracking;

    private $_mailgunMailer;

    /**
     * @return Mailgun Mailgun mailer instance.
     */
    public function getMailgunMailer()
    {
        if (!is_object($this->_mailgunMailer)) {
            $this->_mailgunMailer = $this->createMailgunMailer();
        }

        return $this->_mailgunMailer;
    }

    /**
     * @param \apriside\mailgunmailer\Message $message
     * @return bool
     * @throws MissingRequiredParameters
     */
    protected function sendMessage($message)
    {
        if (!$this->domain) {
            throw new MissingRequiredParameters('Domain property is required');
        }

        if ($this->from && $message->from === null) {
            $message->from = $this->from;
        }
        if ($this->tags && $message->tags === null) {
            $message->tags = $this->tags;
        }
        if ($this->campaignId !== null && $message->campaignId === null) {
            $message->campaignId = $this->campaignId;
        }
        if ($this->dkim !== null && $message->dkim === null) {
            $message->dkim = $this->dkim;
        }
        if ($this->testMode !== null && $message->testMode === null) {
            $message->testMode = $this->testMode;
        }
        if ($this->clickTracking !== null && $message->clickTracking === null) {
            $message->clickTracking = $this->clickTracking;
        }
        if ($this->opensTracking !== null && $message->opensTracking === null) {
            $message->opensTracking = $this->opensTracking;
        }

        Yii::info('Sending email', __METHOD__);

        try {
            $response = $this->getMailgunMailer()->post(
                "{$this->domain}/messages",
                $message->getMessage(),
                $message->getFiles()
            );

            Yii::info('Response : '.print_r($response, true), __METHOD__);

        } catch (\Exception $e) {
            Yii::info('Error sending email', __METHOD__);
        }

        return true;
    }

    /**
     * Creates Mailgun mailer instance.
     * @return \Mailgun\Mailgun
     */
    protected function createMailgunMailer()
    {
        if (!$this->key) {
            throw new InvalidArgumentException('API key property is required');
        }

        return new Mailgun($this->key);
    }
}
