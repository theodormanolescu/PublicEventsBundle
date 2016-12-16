# Installation

`composer require elefant/public-events-bundle`

````

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
````
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

## Filters
Filters implement will decide if an event is public or not.
Filters can be stacked and the first one that returns `true` on `isPublic` will mark the event as public.
Currently there are **name**, **class** and **custom** filters.
A filter should implement `Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface`.

If no filters are specified, the handler will handle **all** events. This is the equivalent to
````
    filters:
        - {name: '/.*/'}
````

## Handlers
### Logger handler
Logger handler logs filtered event if you have a registered **logger** service.
````
elefant_public_events:
    enabled: #default true
    handlers:
        logger1:
            type: logger
````

### Guzzle handler
Guzzle handler will request configured client and uri with filtered events.
 ````
 elefant_public_events:
     handlers:
         producer_test:
             type: rabbitmq_producer
             config:
                 producer: 'test_producer'
                 routing_key: test_routing_key # default: current handler name (producer_test in this example)
 ````

 **producer** is the producer name you configured in `old_sound_rabbit_mq.producers`