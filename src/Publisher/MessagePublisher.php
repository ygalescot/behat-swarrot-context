<?php

namespace Ygalescot\BehatSwarrotContext\Publisher;

use Swarrot\Broker\Message;
use Swarrot\Broker\MessagePublisher\MessagePublisherInterface;
use Swarrot\Broker\MessagePublisher\PeclPackageMessagePublisher;

class MessagePublisher
{
    /**
     * @var array
     */
    private $messageProperties = [];

    /**
     * @var string|null
     */
    private $messageBody;

    /**
     * @var MessagePublisherInterface
     */
    private $publisher;

    /**
     * @param \AMQPExchange $exchange
     */
    public function __construct(\AMQPExchange $exchange)
    {
        $this->publisher = new PeclPackageMessagePublisher($exchange);
    }

    /**
     * @param array $messageProperties
     */
    public function setMessageProperties(array $messageProperties)
    {
        $this->messageProperties = $messageProperties;
    }

    /**
     * @param string|null $messageBody
     */
    public function setMessageBody($messageBody = null)
    {
        $this->messageBody = $messageBody;
    }

    /**
     * @param $routingKey
     */
    public function publish($routingKey)
    {
        $this->publisher->publish(new Message($this->messageBody, $this->messageProperties), $routingKey);
        $this->messageProperties = [];
        $this->messageBody = null;
    }
}