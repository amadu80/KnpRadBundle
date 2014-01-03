<?php

namespace spec\Knp\RadBundle\Routing\Conventional;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Yaml\Parser;

class LoaderSpec extends ObjectBehavior
{
    public function let(FileLocatorInterface $locator, Parser $yaml)
    {
        $this->beConstructedWith($locator, $yaml);
        $locator->locate('routing.yml')->willReturn('yaml/file/path');
    }

    function it_generates_7_conventional_routes_by_default($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => null
        ));
        $collection = $this->load('routing.yml');

        $collection->shouldHaveCount(7);
    }

    function it_generates_conventional_patterns($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => null
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getPattern()->shouldReturn('/cheese.{_format}');
        $collection->get('app_cheese_new')->getPattern()->shouldReturn('/cheese/new.{_format}');
        $collection->get('app_cheese_create')->getPattern()->shouldReturn('/cheese/new.{_format}');
        $collection->get('app_cheese_edit')->getPattern()->shouldReturn('/cheese/{id}/edit.{_format}');
        $collection->get('app_cheese_update')->getPattern()->shouldReturn('/cheese/{id}/edit.{_format}');
        $collection->get('app_cheese_show')->getPattern()->shouldReturn('/cheese/{id}.{_format}');
        $collection->get('app_cheese_delete')->getPattern()->shouldReturn('/cheese/{id}.{_format}');
    }

    function it_generates_conventional_controller_names($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => null
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:indexAction'));
        $collection->get('app_cheese_new')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:newAction'));
        $collection->get('app_cheese_create')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:newAction'));
        $collection->get('app_cheese_edit')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:editAction'));
        $collection->get('app_cheese_update')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:editAction'));
        $collection->get('app_cheese_show')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:showAction'));
        $collection->get('app_cheese_delete')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:deleteAction'));
    }

    function it_cascades_default_config($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'defaults' => array(
                    '_resources' => array(
                        'object' => array('expr' => "request.get('id')"),
                    ),
                ),
                'requirements' => array('_format' => 'html'),
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:indexAction',
            '_resources' => array(
                'object' => array('expr' => "request.get('id')"),
            ),
        ));
    }

    function it_overrides_default_config_explicitly($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'defaults' => array(
                    '_resources' => array(
                        'object' => array('expr' => "request.get('id')"),
                    ),
                ),
                'index' => array(
                    'defaults' => array('_resources' => array()),
                ),
                'requirements' => array('_format' => 'html'),
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array(
            '_controller' => 'App:Cheese:indexAction',
            '_resources' => array(),
        ));
    }

    function its_controller_can_be_a_service($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'controller' => 'knp_rad.controller.crud_controller',
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_index')->getDefaults()->shouldReturn(array('_controller' => 'knp_rad.controller.crud_controller:indexAction'));
    }

    function it_allows_to_add_new_routes($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => null,
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:customAction'));
        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/cheese/custom');
    }

    function it_allows_to_limit_number_of_generated_routes($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => null,
                'elements' => ['index', 'show'],
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->shouldHaveCount(3);
    }

    function it_allows_to_disable_all_routes($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'elements' => [],
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->shouldHaveCount(0);
    }

    function it_allows_new_routes_to_be_configured($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => array('controller' => 'a different one'),
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array('_controller' => 'a different one:customAction'));
        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/cheese/custom');
    }

    function it_uses_pattern_as_default_string_value($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => '/patt{ern}/de/ouf'
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('app_cheese_custom')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:customAction'));
        $collection->get('app_cheese_custom')->getPattern()->shouldReturn('/cheese/patt{ern}/de/ouf');
    }

    public function it_can_be_prefixed($yaml)
    {
        $yaml->parse('yaml/file/path')->willReturn(array(
            'App:Cheese' => array(
                'custom' => array(
                    'pattern' => '/patt{ern}/de/ouf',
                    'prefix' => 'test:sub',
                ),
            ),
        ));
        $collection = $this->load('routing.yml');

        $collection->get('test_sub_custom')->getPattern()->shouldReturn('/sub/patt{ern}/de/ouf');
        $collection->get('test_sub_custom')->getDefaults()->shouldReturn(array('_controller' => 'App:Cheese:customAction'));
    }
}
