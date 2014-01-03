<?php

namespace Knp\RadBundle\Routing\Conventional\Generator;

use Knp\RadBundle\Routing\Conventional\Config;

class RouteName
{
    public function generate(Config $config)
    {
        return strtolower(str_replace(array('/', '\\', ':', ' '), '_', sprintf(
            '%s_%s', $config->getPrefix(), $config->name
        )));
    }
}
