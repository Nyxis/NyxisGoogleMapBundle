<?php

namespace Nyxis\GoogleMapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NyxisGoogleMapExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('nyxis_map_bundle.default_map_config', array(
            'locale'   => empty($config['default']['locale']) ? $container->getParameter('locale') : $config['default']['locale'],
            'zoom'     => $config['default']['zoom'],
            'charset'  => $config['default']['charset'],
            'type'     => $config['default']['type'],
            'width'    => $config['default']['width'],
            'height'   => $config['default']['height'],
            'template' => $config['default']['template']
        ));

        $container->setParameter('nyxis_map_bundle.maps_configs', $config['maps']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
