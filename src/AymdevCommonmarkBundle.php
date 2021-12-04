<?php

namespace Aymdev\CommonmarkBundle;

use Aymdev\CommonmarkBundle\DependencyInjection\Compiler\ConvertersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @internal
 */
class AymdevCommonmarkBundle extends Bundle
{
    /**
     * @codeCoverageIgnore
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConvertersPass());
    }
}
