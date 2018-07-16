<?php

namespace Ygalescot\BehatSwarrotContext\Context;

use Behat\Behat\Context\Context;
use Swarrot\Processor\ProcessorInterface;
use Ygalescot\BehatSwarrotContext\Factory\SwarrotFactory;
use Ygalescot\BehatSwarrotContext\Processor\MessageProcessor;

class SwarrotContext implements Context
{
    /**
     * @var SwarrotFactory
     */
    protected $factory;

    /**
     * @var ProcessorInterface
     */
    protected $processor;

    /**
     * @param string $host
     * @param int $port
     * @param string $vhost
     * @param string $login
     * @param string $password
     */
    public function __construct($host = 'localhost', $port = 5672, $vhost = '/', $login = 'guest', $password = 'guest')
    {
        $this->factory = new SwarrotFactory($host, $port, $vhost, $login, $password);
        $this->processor = new MessageProcessor();
    }

    /**
     * @Then I purge queue :queueName
     *
     * @param string $queueName
     * @throws \Exception
     */
    public function iPurgeQueue($queueName)
    {
        $purged = $this->factory->getQueue($queueName)->purge();

        if (false === $purged) {
            throw new \Exception("Could not purge queue $queueName");
        }
    }

    /**
     * @Then I consume a message form queue :queueName
     *
     * @param string $queueName
     * @throws \Exception
     */
    public function iConsumeAMessageFromQueue($queueName)
    {
        $messageProvider = $this->factory->getMessageProvider($queueName);
        $stackedProcessor = $this->factory->createStackedProcessor($messageProvider, $this->processor);

        $consumer = $this->factory->createConsumer($messageProvider, $stackedProcessor);
        $consumer->consume([
            'max_messages' => 1,
            'max_execution_time' => 3,
        ]);

        if (empty($this->processor->getMessage())) {
            throw new \Exception("Could not consume message from queue $queueName");
        }
    }

    /**
     * @Then the message should have property :property equal to :value
     *
     * @param string $property
     * @param string $value
     * @throws \Exception
     */
    public function theMessageShouldHavePropertyEqualTo($property, $value)
    {
        $this->assertArrayHasKey($property, $this->processor->getMessage()->getProperties());
        $this->assertEquals($value, $this->processor->getMessage()->getProperties()[$property]);
    }

    /**
     * @Then the message should have header :header equal to :value
     *
     * @param string $header
     * @param string $value
     * @throws \Exception
     */
    public function theMessageShouldHaveHeaderEqualTo($header, $value)
    {
        $this->assertArrayHasKey('headers', $this->processor->getMessage()->getProperties());
        $this->assertArrayHasKey($header, $this->processor->getMessage()->getProperties()['headers']);
        $this->assertEquals($value, $this->processor->getMessage()->getProperties()['headers'][$header]);
    }

    /**
     * @Then the message body should contain :body
     *
     * @param string $body
     * @throws \Exception
     */
    public function theMessageBodyShouldContain($body)
    {
        $this->assertContains($body, $this->processor->getMessageBody());
    }

    /**
     * @Then the message body should have JSON node :node equal to :value
     *
     * @param string $node
     * @param string $value
     * @throws \Exception
     */
    public function theMessageBodyShouldHaveJSONNodeEqualTo($node, $value)
    {
        $decodedBody = $this->processor->getDecodedMessageBody();
        $this->assertArrayHasKey($node, $decodedBody);
        $this->assertEquals($value, $decodedBody[$node]);
    }

    /**
     * @Then print the message body
     */
    public function printTheMessageBody()
    {
        print_r($this->processor->getMessageBody());
    }

    /**
     * @param string $key
     * @param array $array
     *
     * @return bool
     * @throws \Exception
     */
    protected function assertArrayHasKey($key, array $array)
    {
        if (!array_key_exists($key, $array)) {
            return true;
        }

        throw new \Exception("$key not found");
    }

    /**
     * @param string $expected
     * @param string $actual
     *
     * @return bool
     * @throws \Exception
     */
    protected function assertEquals($expected, $actual)
    {
        if ($expected === $actual) {
            return true;
        }

        throw new \Exception("$actual does not match expected $expected");
    }

    /**
     * @param string $item
     * @param string $content
     *
     * @return bool
     * @throws \Exception
     */
    protected function assertContains($item, $content)
    {
        if (preg_match("/$item/", $content)) {
            return true;
        }

        throw new \Exception("$item not found in $content");
    }
}
