<?php
namespace Nyxis\GoogleMapBundle\Tests\Map;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Nyxis\GoogleMapBundle\Map\MapBuilder;

class MapBuilderTest extends WebTestCase
{
    public function testRenderMap()
    {
        $client        = static::createClient(array('environment' => 'test'));
        $container     = $client->getContainer();

        $mapBuilder = new MapBuilderMock($container);
        $mapBuilder->addDefaultConfiguration(array(
            'locale'   => 'en',
            'template' => 'NyxisGoogleMapBundle:Test/Map:test-template.html.twig'
        ));

        $this->assertEquals(
            "425-350-test-8-en-UTF8-m\n",
            $mapBuilder->renderMap(array('place' => 'test')),
            'Renders with default config'
        );

        $this->assertEquals(
            "425-350-test-15-en-UTF8-m\n",
            $mapBuilder->renderMap(array('place' => 'test', 'zoom' => 15)),
            'Renders with custom config at param'
        );

        $mapBuilder->addMapConfiguration('test', array(
            'width'  => '800',
            'height' => '600',
            'place'  => 'test place'
        ));

        $this->assertEquals(
            "800-600-test place-8-en-UTF8-m\n",
            $mapBuilder->renderMap('test'),
            'Renders a map config'
        );

        $this->assertEquals(
            "800-600-test place-5-en-UTF8-m\n",
            $mapBuilder->renderMap('test', array('zoom' => 5)),
            'Renders a map config with config at param'
        );

        // clear a config to test exception throwing
        $defaultConfig = $container->getParameter('nyxis_map_bundle.default_map_config');
        unset($defaultConfig['width']);
        $mapBuilder->setDefaultConfig($defaultConfig);

        try  {
            $message = 'Exception has been thrown if config is missing with good message';
            $mapBuilder->renderMap(array('place' => 'test', 'zoom' => 15));
            $this->assertTrue(false, $message);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals(
                'Nyxis\GoogleMapBundle\Map\MapBuilder::validateConfig() : given map config is invalid, "width" fields are missing',
                $e->getMessage(),
                $message)
            ;
        }

        try  {
            $message = 'Exception has been thrown if request map is unknown with good message';
            $mapBuilder->renderMap('unknow', array('place' => 'test', 'width' => 800));
            $this->assertTrue(false, $message);
        } catch(\InvalidArgumentException $e) {
            $this->assertEquals(
                'Nyxis\GoogleMapBundle\Map\MapBuilder::normalizeWithMap() : unknown given map, "unknow" given, forgot to add it in configs ?',
                $e->getMessage(),
                $message);
        }
    }
}

class MapBuilderMock extends MapBuilder
{
    public function setDefaultConfig($config)
    {
        $this->defaultConfigs = $config;

        return $this;
    }
}