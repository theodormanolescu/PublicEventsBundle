# Installation

`composer require elefantlabs/public-events-bundle`

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

# Configuration
````yml
elefant_public_events:
    enabled: #default true
    handlers:
        name_logger:
            type: logger
            filters:
                - {name: regex}
        class_logger:
            type: logger
            filters:
                - {class: ClassName}
````

# Filters
Filters implement will decide if an event is public or not.
Filters can be stacked and the first one that returns `true` on `isPublic` will mark the event as public.
Currently there are **name**, **class** and **custom** filters.

If no filters are specified, the handler will handle **all** events. This is the equivalent to
````yml
    filters:
        - {name: '/.*/'}
````

custom filter example:
````
elefant_public_events:
    handlers:
        logger_test:
            type: logger
            filters:
                - custom_filter
````

Where **custom_filter** is the service Id of your custom filter.
A filter should implement `Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface`.

# Handlers

## Logger handler
Logger handler logs filtered event if you have a registered **logger** service.
````yml
elefant_public_events:
    enabled: #default true
    handlers:
        logger1:
            type: logger
````

## Guzzle handler
Guzzle handler will request configured client and uri with filtered events.
````yml
elefant_public_events:
    handlers:
        guzzle_test:
            type: guzzle
            config:
                client: 'guzzle_client' #Guzzle client service ID
                method: test_method #Http method, default: get
                uri: /test_uri #default: /
                headers: ['extra headers'] #default: []
````

## RabbitMq producer handler
RabbitMq producer handler will publish filtered events to a RabbitMq exchange with the configured *routing_key*
 ````yml
 elefant_public_events:
     handlers:
         producer_test:
             type: rabbitmq_producer
             config:
                 producer: 'test_producer'
                 routing_key: test_routing_key # default: current handler name (producer_test in this example)
 ````

 **producer** is the producer name you configured in `old_sound_rabbit_mq.producers`