<?php

namespace Aymdev\CommonmarkBundle;

use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AymdevCommonmarkBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConvertersPass());
    }
}