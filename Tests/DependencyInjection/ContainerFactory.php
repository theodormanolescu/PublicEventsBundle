<?php

namespace Elefant\PublicEventsBundle\Tests\DependencyInjection;

use Elefant\PublicEventsBundle\DependencyInjection\ElefantPublicEventsExtension;
use Elefant\PublicEventsBundle\ElefantPublicEventsBundle;
use OldSound\RabbitMqBundle\OldSoundRabbitMqBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class ContainerFactory
{
    public static function createContainer($file = null, $extensions = [])
    {
        $container = new ContainerBuilder(new ParameterBag(array('kernel.debug' => false)));

        $container->registerExtension(new ElefantPublicEventsExtension());


        foreach ($extensions as $extension) {
            $container->registerExtension($extension);
        }

        if ($file) {
            $locator = new FileLocator(__DIR__ . '/Fixtures');
            $loader = new YamlFileLoader($container, $locator);
            $loader->load($file);
        }

        $bundle = new ElefantPublicEventsBundle();
        $bundle->build($container);
        
        $container->compile();

        return $container;
    }
}