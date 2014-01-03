<?php

namespace Knp\RadBundle\Routing\Conventional;

class Config
{
    public $name;
    private $parent;
    private $config;

    public function __construct($name, $config = null, Config $parent = null)
    {
        $this->name = $name;
        if (!is_array($config)) {
            $config = array('pattern' => $config);
        }
        $this->config = $config;
        $this->parent = $parent;
    }

    public function getController()
    {
        if ($this->get('controller')) {
            return $this->get('controller');
        }
        if ($this->parent) {
            return $this->parent->getController();
        }

        return $this->name;
    }

    public function getPattern()
    {
        if ($this->get('pattern')) {
            return $this->get('pattern');
        }

        return $this->name;
    }

    public function getPrefix()
    {
        if ($this->get('prefix')) {
            return $this->get('prefix');
        }
        if ($this->parent) {
            return $this->parent->getPrefix();
        }

        return $this->name;
    }

    public function getDefaults()
    {
        $defaults = $this->get('defaults', array());
        if ($this->parent) {
            $defaults = array_merge($this->parent->getDefaults(), $defaults);
        }

        return $defaults;
    }

    public function getRequirements()
    {
        $requirements = $this->get('requirements', array());
        if ($this->parent) {
            $requirements = array_merge($this->parent->getRequirements(), $requirements);
        }

        return $requirements;
    }

    public function getMethods()
    {
        return array('GET');
    }

    public function getElements()
    {
        $elements = $this->getDefaultElements();

        foreach ($elements as $key => $element) {
            if (!in_array($key, $this->get('elements', array_keys($elements)))) {
                unset($elements[$key]); // TODO improve this filter, couldn't get it work with array_intersect_*
            }
        }

        $ignore = array_flip(array(
            'elements',
            'pattern',
            'prefix',
            'controller',
            'methods',
            'defaults',
            'requirements',
        ));

        $others = array_keys(array_diff_key($this->config, $ignore, $elements));
        foreach ($others as $name) {
            $elements[$name] = new static($name, $this->config[$name], $this);
        }

        return $elements;
    }

    private function merge($path, array $config)
    {
        return array_merge($this->get($path, array()), $config);
    }

    private function get($path, $default = null)
    {
        if (isset($this->config[$path])) {
            return $this->config[$path];
        }

        return $default;
    }

    private function getDefaultElements()
    {
        return array(
            'index' => new static('index', $this->merge('index', array(
                'methods'      => array('GET'),
            )), $this),
            'new' => new static('new', $this->merge('new', array(
                'methods'      => array('GET'),
            )), $this),
            'create' => new static('create', $this->merge('create', array(
                'methods'      => array('POST'),
            )), $this),
            'edit' => new static('edit', $this->merge('edit', array(
                'methods'      => array('GET'),
                'requirements' => array('id' => '\d+'),
            )), $this),
            'update' => new static('update', $this->merge('update', array(
                'methods'      => array('PUT'),
                'requirements' => array('id' => '\d+'),
            )), $this),
            'show' => new static('show', $this->merge('show', array(
                'methods'      => array('GET'),
                'requirements' => array('id' => '\d+'),
            )), $this),
            'delete' => new static('delete', $this->merge('delete', array(
                'methods'      => array('DELETE'),
                'requirements' => array('id' => '\d+'),
            )), $this),
        );
    }
}
