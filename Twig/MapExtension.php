<?php

namespace Nyxis\GoogleMapBundle\Twig;

use Nyxis\GoogleMapBundle\Map\MapBuilder;

use Symfony\Component\DependencyInjection\Container;

/**
 * Twig extension created to support map rendering
 *
 * Functions :
 *      - {{ google_map("map_name", { "width": "500" .... }) }}
 *      - {{ google_map({ "place": "My very good place" .... }) }}
 *
 * @see Nyxis/GoogleMapBundle/Resources/services.xml
 */
class MapExtension extends \Twig_Extension
{
    protected $builder;

    /**
     * {@inherit_doc}
     */
    public function getName()
    {
        return 'nyxis_google_map_extension';
    }

    /**
     * construct
     * @param Container $container because circular ref between templating, extensions and builder
     */
    public function __construct(Container $container)
    {
        $this->builder = $container->get('google_map_builder');
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'google_map' => new \Twig_Function_Method($this, 'renderMap', array('is_safe' => array('html'))),
        );
    }

    /**
     * @see Nyxis\GoogleMapBundle\Map\MapBuilder::renderMap()
     */
    public function renderMap($name, $configs = array())
    {
        return $this->builder->renderMap($name, $configs);
    }
}