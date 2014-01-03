<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;

class ControllerName
{
    private static $map = array(
        'create' => 'new',
        'update' => 'edit',
    );

    public function generate(Config $config)
    {
        $name = isset(self::$map[$config->name]) ? self::$map[$config->name] : $config->name;

        return sprintf('%s:%sAction', $config->getController(), $name);
    }
}
