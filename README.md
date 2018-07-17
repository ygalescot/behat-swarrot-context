# ygalescot/behat-swarrot-context

This project is an extension built for Behat that allows you to test
AMQP messages.

It is based on Swarrot library and PECL AMQP php extension.

## How to use

Simply add the SwarrotContext to your behat.yml config:

```
default:
    suites:
        your_suite:
            ...
            contexts:
                - ...
                - Ygalescot\BehatSwarrotContext\Context\SwarrotContext
    
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
                - Ygalescot\BehatSwarrotContext\Context\SwarrotContext
                    host: your_custom_host
                    ...
``` 

Then in your Behat test scenarios you can use these steps:

- `Given I purge queue :queue_name`
- `Then I consume a message from queue :queue_name`
- `Then the message should have property :property equal to :value`
- `Then the message should have header :header equal to :value`
- `Then the message body should contain :body`
- `Then the message body should have JSON node :node equal to :value`
- `Then print the message body` (to display the content of your message in the console)

## Licence

MIT