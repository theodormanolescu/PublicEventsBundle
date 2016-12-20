<?php

namespace Elefant\PublicEventsBundle\PublicEvents\Handler;

use Elefant\PublicEventsBundle\PublicEvents\Filter\FilterInterface;
use Elefant\PublicEventsBundle\PublicEvents\Formatter\FormatterInterface;
use Elefant\PublicEventsBundle\PublicEvents\PublicEvent;

abstract class Handler implements HandlerInterface
{
    /** @var  FilterInterface[] */
    private $filters = [];
    /** @var  FormatterInterface */
    private $formatter;

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
        $this->doHandle($this->formatter->format($event));
    }

    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    abstract protected function doHandle($formattedEvent);

}
