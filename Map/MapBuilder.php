<?php

namespace Nyxis\GoogleMapBundle\Map;

use Symfony\Component\DependencyInjection\Container;

/**
 * map builder class
 *
 * @see Nyxis/GoogleMapBundle/Resources/services.xml
 */
class MapBuilder
{
    protected $mapType = array(
        'plan'     => 'm',
        'satelite' => 'h',
        'terrain'  => 'p',
        'earth'    => 'f'
    );

    protected $container;
    protected $defaultConfigs;
    protected $mapsConfigs;

    /**
     * construct
     * @param Container $container because circular ref between templating, extensions and builder
     */
    public function __construct(Container $container)
    {
        $this->container      = $container;
        $this->defaultConfigs = $container->getParameter('nyxis_map_bundle.default_map_config');
        $this->mapsConfigs    = $container->getParameter('nyxis_map_bundle.maps_configs');
    }

    /**
     * defines default config
     * @param array $config
     */
    public function addDefaultConfiguration($config)
    {
        $this->defaultConfigs = array_replace_recursive($this->defaultConfigs, $config);
    }

    /**
     * defines a map config
     * @param string $mapName
     * @param array  $config
     */
    public function addMapConfiguration($mapName, $config)
    {
        $this->mapsConfigs[$mapName] = $config;
    }

    /**
     * Normalize given config with defaults
     * @param  array $config
     * @return array
     */
    protected function normalizeWithDefault($config)
    {
        return array_replace_recursive($this->defaultConfigs, $config);
    }

    /**
     * Normalize given config with map params
     * @param  string $map    map name
     * @param  array  $config
     * @return array
     */
    protected function normalizeWithMap($map, $config)
    {
        if (!isset($this->mapsConfigs[$map])) {
            throw new \InvalidArgumentException(sprintf('%s() : unknown given map, "%s" given, forgot to add it in configs ?',
                __METHOD__, $map
            ));
        }

        return array_replace_recursive(
            $this->normalizeWithDefault($this->mapsConfigs[$map]), $config
        );
    }

    /**
     * validate given config
     * @param  array $config
     * @throws InvalidArgumentException
     */
    protected function validateConfig($config)
    {
        $neededConfigs = array('place', 'locale', 'zoom', 'charset', 'width', 'height', 'type', 'template');
        $diff = array_diff_key(array_flip($neededConfigs), $config);

        if (!empty($diff)) {
            throw new \InvalidArgumentException(sprintf('%s() : given map config is invalid, "%s" fields are missing',
                __METHOD__, implode('", "', array_keys($diff))
            ));
        }

        if (empty($this->mapType[$config['type']])) {
            throw new \InvalidArgumentException(sprintf('%s() : given map type is invalid, "%s" given, has to be one of "%s"',
                __METHOD__, $config['type'], implode('", "', array_keys($this->mapType))
            ));
        }

        // convert config type to gmap type
        $config['type'] = $this->mapType[$config['type']];

        return $config;
    }

    /**
     * render a map from its config name, or a config
     * @param  string|array $map    if string, render a map from it's config,
     *                              then a map based on given parameters, merged with defaults
     * @param  array        $config override config
     * @return string
     */
    public function renderMap($map, $configs = array())
    {
        if (is_string($map)) {
            $configs = $this->normalizeWithMap($map, $configs);
        }
        elseif (is_array($map)) {
            $configs = $this->normalizeWithDefault($map);
        }
        else {
            throw new \InvalidArgumentException(sprintf('%s only support string or array as first arg, "%s" given',
                __METHOD__, is_object($map) ? get_class($map) : empty($map) ? 'none' : $map
            ));
        }

        $configs = $this->validateConfig($configs);

        // extract template (any needs to give it to view)
        $template = $configs['template'];
        unset($configs['template']);

        return $this->container->get('templating')->render($template, $configs);
    }

}