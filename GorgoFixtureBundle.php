<?php

namespace Gorgo\Bundle\FixtureBundle;

use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GorgoFixtureBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        if ('test' !== $container->getParameter('kernel.environment')) {
            $nelmioBundle = new NelmioAliceBundle();
            $nelmioBundle->build($container);
        }
    }
}
