<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

interface HandlerInterface
{

    /**
     * @param PublicEvent $event
     * @return mixed
     */
    public function handle(PublicEvent $event);

    /**
     * @param FormatterInterface $formatter
     * @return FormatterInterface
     */
    public function addFormatter(FormatterInterface $formatter);

    /**
     * @param FilterInterface $filter
     * @return HandlerInterface
     */
    public function addFilter(FilterInterface $filter);
}
