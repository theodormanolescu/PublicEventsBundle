<?php

namespace Elefant\PublicEventsBundle\Tests\PublicEvents\Filter;

use Elefant\PublicEventsBundle\PublicEvents\Filter\NameFilter;
use PHPUnit\Framework\TestCase;

class NameFilterTest extends TestCase
{
    public function testNameFilter()
    {
        $this->assertEquals(new NameFilter('regex'), new NameFilter('/regex'));
        $this->assertEquals(new NameFilter('regex'), new NameFilter('regex/'));
        $this->assertEquals(new NameFilter('regex'), new NameFilter('/regex/'));
    }
}
