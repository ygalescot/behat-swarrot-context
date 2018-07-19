# ygalescot/behat-swarrot-context

This project is an extension built for Behat that allows you to test
AMQP messages.

It is based on Swarrot library and PECL AMQP php extension.

## Setup

Simply add the SwarrotContext to your behat.yml config:

```
default:
    suites:
        your_suite:
            ...
            contexts:
                - ...
                - 'Ygalescot\BehatSwarrotContext\Context\SwarrotContext'
    
``` 

By default the SwarrotContext uses the default connection to RabbitMQ:
```
host: localhost
port: 5672
vhost: /
login: guest
password: guest
```

But you can override this configuration with your own values when you add the SwarrotContext to
your behat.yml file:
```
default:
    suites:
        your_suite:
            ...
            contexts:
                - ...
                - 'Ygalescot\BehatSwarrotContext\Context\SwarrotContext':
                    host: your_custom_host
                    port: 5672
                    vhost: /
                    login: your_custom_login
                    password: your_custom_password
``` 

## How to use

In your Behat test scenarios you can use these steps to test your AMQP Messages:

- `Given I purge queue :queue_name` (will purge all messages in that queue)
- `Then I set message properties:` (with properties described as YAML in a Gherkin PyStringNode)
- `Then I set message body:` (with body as a Gherkin PyStringNode)
- `Then I publish message with routing key :routingKey` (this will publish a message to RabbitMQ with previously set properties and/or body)
- `Then I consume a message from queue :queue_name`
- `Then the message should have property :property equal to :value`
- `Then the message should have header :header equal to :value`
- `Then the message body should contain :body`
- `Then the message body should have JSON node :node equal to :value`
- `Then print the message body` (to display the content of your message in console)
- `Then print the message properties` (to display the message properties in console)

For a fully functional example see our Behat feature file: `features/context.feature`

## Licence

MIT