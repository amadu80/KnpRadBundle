<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;

class Pattern
{
    private static $map = array(
        'index'  => '.{_format}',
        'new'    => '/new.{_format}',
        'create' => '/new.{_format}',
        'edit'   => '/{id}/edit.{_format}',
        'update' => '/{id}/edit.{_format}',
        'show'   => '/{id}.{_format}',
        'delete' => '/{id}.{_format}',
    );

    public function generate(Config $config)
    {
        $pattern = isset(self::$map[$config->getPattern()]) ? self::$map[$config->getPattern()] : $config->getPattern();

        $parts = explode(':', $config->getPrefix());
        array_shift($parts);
        $prefix = strtolower(str_replace(array('/', '\\'), '_', implode('_', $parts)));

        if ('/' !== $pattern[0] && '.' !== $pattern[0]) {
            return rtrim($prefix, '/').'/'.$pattern;
        }

        return $prefix.$pattern;
    }
}
