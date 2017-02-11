[![Build Status](https://travis-ci.org/ElefantLabs/PublicEventsBundle.svg?branch=master)](https://travis-ci.org/ElefantLabs/PublicEventsBundle)
[![Coverage Status](https://coveralls.io/repos/github/ElefantLabs/PublicEventsBundle/badge.svg?branch=master)](https://coveralls.io/github/ElefantLabs/PublicEventsBundle?branch=master)


|  |  | |
| ------------- |-------------| -----|
| You choose how you want to make your events public|you choose which events to make public | and how to transform them|
| you define a [Handler](#handlers)      | You define [filters](#filters)      |   You define a [foramtter](#filters) |


# Installation

`composer require elefantlabs/public-events-bundle`

add to `AppKernel.php`

````php
public function registerBundles()
{
    $bundles = array(
        //...
        new Elefant\PublicEventsBundle\ElefantPublicEventsBundle(),
        //...
    );
}
````

# Configuration reference
````yml
elefant_public_events:
    formatter: array|json #or a service id for a custom formatter
    enabled: #default true
    handlers:
        logger_test: #You need a logger service
            type: logger
            filters:
                - {name: regex}
                - {class: MyEventType}
                - my_custom_filter # the service Id of your custom filter.
            formatter: array|json|my_custom_formatter # the service Id of your custom formatter.
        guzzle_test: #You need a GuzzleClient service
            type: guzzle
            config:
                client: 'guzzle_client' #Guzzle client service ID
                method: test_method #Http method, default: get
                uri: /test_uri #default: /
                headers: ['extra headers'] #default: []
            formatter: array
        rabbit_test: #You need rabbitmq bundle
             type: rabbitmq
             config:
                 connection: default# default: default
                 exchange_options: {}
                 queue_options: {}
                 callback: 'your_bundle.service_definition' #must implement ConsumerInterface
             formatter: array
````

# Handlers

Handlers process public events. Supported handlers:

- LoggerHandler uses [Monolog](https://github.com/Seldaek/monolog) (supports a [psr-log](https://github.com/php-fig/log) `LoggerInterface`)
- GuzzleHandler uses [Guzzle](https://github.com/guzzle/guzzle)
- RabbitmqHandler uses [RabbitMqBundle](https://github.com/php-amqplib/RabbitMqBundle)
- Custom handlers should implement `Elefant\PublicEventsBundle\PublicEvents\Handler\HandlerInterface`

> RabbitmqHandler will automatically create one consumer and one producer for each handler of type rabbitmq

For **rabbit_test** handler,  `old_sound_rabbit_mq.public_events_rabbit_test_consumer` and `old_sound_rabbit_mq.public_events_rabbit_test_producer` will be created.

# Filters
Filters which event you want to make public.
Filters can be stacked and the first one that returns `true` on `isPublic` will mark the event as public.
Currently there are **name**, **class** and **custom** filters.

If no filters are specified, the handler will handle **all** events. This is the equivalent to
````yml
filters:
    - {name: '/.*/'}
````

A filter should implement `Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface`.

# Formatters
A formatter transforms a `Elefant\PublicEventsBundle\PublicEvents\PublicEvent` object befor handing it to `Handler::doHandle`. 
There are an **array** and **json** formatters and you can define your own formatters implementing `Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface`.