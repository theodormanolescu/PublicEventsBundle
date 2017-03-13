<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;
use Psr\Log\LoggerInterface;

abstract class Handler implements HandlerInterface
{
    /** @var  FilterInterface[] */
    private $filters = [];
    /** @var  FormatterInterface[] */
    private $formatters = [];
    /** @var LoggerInterface $logger */
    protected $logger;

    /**
     * @param FilterInterface $filter
     * @return Handler
     */
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[] = $filter;
        return $this;
    }

    private function canHandle(PublicEvent $event)
    {
        foreach ($this->filters as $filter) {
            if ($filter->isPublic($event->getOriginalEventName(), $event->getOriginalEvent())) {
                return true;
            }
        }
        return false;
    }

    public function handle(PublicEvent $event)
    {
        if (!$this->canHandle($event)) {
            return;
        }
        $serialized = $this->format($event);
        $this->doHandle($serialized);
    }
    
    abstract protected function doHandle($formattedEvent);

    public function addFormatter(FormatterInterface $formatter)
    {
        $this->formatters[] = $formatter;
        return $this;
    }

    protected function format(PublicEvent $event)
    {
        $formatted = [];

        foreach ($this->formatters as $formatter) {
            $formatted = array_merge($formatted, $formatter->format($event));
        }

        return $formatted;
    }


    /**
     * @param LoggerInterface $logger
     * @return Handler
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
