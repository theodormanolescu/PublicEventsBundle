<?php

namespace Elefant\PublicEventsBundle;

use Elefant\PublicEventsBundle\DependencyInjection\Compiler\ServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ElefantPublicEventsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ServicePass());
    }
}
