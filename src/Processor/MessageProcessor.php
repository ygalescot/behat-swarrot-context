<?php

namespace Ygalescot\BehatSwarrotContext\Processor;

use Swarrot\Broker\Message;
use Swarrot\Processor\ProcessorInterface;

class MessageProcessor implements ProcessorInterface
{
    /**
     * @var Message
     */
    private $message;

    /**
     * @param Message $message
     * @param array $options
     *
     * @return bool|null
     */
    public function process(Message $message, array $options)
    {
        $this->message = $message;
        return true;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed|null|string
     */
    public function getMessageBody()
    {
        return $this->message->getBody();
    }

    /**
     * @return array|null
     */
    public function getDecodedMessageBody()
    {
        return \json_decode($this->message->getBody(), true);
    }
}