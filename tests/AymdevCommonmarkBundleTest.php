<?php

namespace Tests\AymDev\CommonMarkBundle;

use Aymdev\CommonmarkBundle\AymdevCommonmarkBundle;
use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonMarkExtension;
use PHPUnit\Framework\TestCase;

class AymdevCommonmarkBundleTest extends TestCase
{
    /**
     * Ensure the extension will be loaded
     */
    public function testExtensionNameConsistency()
    {
        $bundle = new AymdevCommonmarkBundle();
        $extension = $bundle->getContainerExtension();

        self::assertInstanceOf(AymdevCommonMarkExtension::class, $extension);
    }
}