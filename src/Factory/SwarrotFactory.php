<?php

namespace Ygalescot\BehatSwarrotContext\Factory;

use Swarrot\Broker\MessageProvider\MessageProviderInterface;
use Swarrot\Broker\MessageProvider\PeclPackageMessageProvider;
use Swarrot\Consumer;
use Swarrot\Processor\ProcessorInterface;

class SwarrotFactory
{
    /**
     * @var \AMQPConnection
     */
    protected $connection;

    /**
     * @var \AMQPChannel
     */
    protected $channel;

    /**
     * @var \AMQPQueue[]
     */
    protected $queues;

    /**
     * @var MessageProviderInterface[]
     */
    protected $messageProviders;

    /**
     * @param string $host
     * @param int $port
     * @param string $vhost
     * @param string $login
     * @param string $password
     */
    public function __construct($host = 'localhost', $port = 5672, $vhost = '/', $login = 'guest', $password = 'guest')
    {
        $this->connection = new \AMQPConnection([
            'host'  => $host,
            'port'  => $port,
            'vhost' => $vhost,
            'login' => $login,
            'password' => $password,
        ]);
    }

    /**
     * @return \AMQPChannel
     */
    public function getChannel()
    {
        $this->connection->reconnect();

        if (null === $this->channel) {
            $this->channel = new \AMQPChannel($this->connection);
        }

        return $this->channel;
    }

    /**
     * @param string $queueName
     *
     * @return \AMQPQueue
     */
    public function getQueue($queueName)
    {
        if (null === $this->queues[$queueName]) {
            $this->queues[$queueName] = (new \AMQPQueue($this->getChannel()))->setName($queueName);
        }

        return $this->queues[$queueName];
    }

    /**
     * @param string $queueName
     *
     * @return MessageProviderInterface
     */
    public function getMessageProvider($queueName)
    {
        if (null === $this->messageProviders[$queueName]) {
            $this->messageProviders[$queueName] = new PeclPackageMessageProvider($this->getQueue($queueName));
        }

        return $this->messageProviders[$queueName];
    }

    /**
     * @param MessageProviderInterface $messageProvider
     * @param ProcessorInterface $processor
     *
     * @return \Swarrot\Processor\Stack\StackedProcessor
     */
    public function createStackedProcessor(MessageProviderInterface $messageProvider, ProcessorInterface $processor)
    {
        $stack = (new \Swarrot\Processor\Stack\Builder())
            ->push('Swarrot\Processor\MaxExecutionTime\MaxExecutionTimeProcessor')
            ->push('Swarrot\Processor\MaxMessages\MaxMessagesProcessor')
            ->push('Swarrot\Processor\Ack\AckProcessor', $messageProvider)
        ;

        return $stack->resolve($processor);
    }

    /**
     * @param MessageProviderInterface $messageProvider
     * @param ProcessorInterface $processor
     *
     * @return Consumer
     */
    public function createConsumer(MessageProviderInterface $messageProvider, ProcessorInterface $processor)
    {
        return new Consumer($messageProvider, $processor);
    }
}