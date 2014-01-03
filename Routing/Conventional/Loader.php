<?php

namespace Knp\RadBundle\Routing\Conventional;

use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Parser;
use Knp\RadBundle\Routing\Conventional\Factory;

class Loader extends FileLoader
{
    private $yamlParser;
    private $factory;

    public function __construct(FileLocatorInterface $locator, Parser $yamlParser = null, Factory $factory = null)
    {
        parent::__construct($locator);
        $this->yamlParser = $yamlParser ?: new Parser;
        $this->factory = $factory ?: new Factory;
    }

    public function supports($resource, $type = null)
    {
        return 'rad' === $type;
    }

    public function load($file, $type = null)
    {
        $path   = $this->locator->locate($file);
        $collection = new RouteCollection;
        $collection->addResource(new FileResource($file));

        $rawConfigs = $this->yamlParser->parse($path);
        if (empty($rawConfigs)) {
            return $collection;
        }

        foreach ($rawConfigs as $name => $rawConfig) {
            $config = new Config($name, $rawConfig);

            foreach ($config->getElements() as $element) {
                list ($name, $route) = $this->factory->create($element);
                $collection->add($name, $route);
            }
        }

        return $collection;
    }
}
