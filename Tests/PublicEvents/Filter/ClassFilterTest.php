<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Filter;

use Elefant\PublicEventsBundle\PublicEvents\Filter\ClassFilter;
use PHPUnit\Framework\TestCase;

class ClassFilterTest extends TestCase
{
    public function testNameFilter()
    {
        $classFilter = new ClassFilter(FilteredTestEvent::class);

        $classFilter->isPublic('event', new FilteredTestEvent());
    }
}
