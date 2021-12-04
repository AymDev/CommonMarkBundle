<?php

namespace Tests\AymDev\CommonMarkBundle;

use Aymdev\CommonmarkBundle\AymdevCommonmarkBundle;
use Aymdev\CommonmarkBundle\DependencyInjection\AymdevCommonmarkExtension;
use PHPUnit\Framework\TestCase;

class AymdevCommonmarkBundleTest extends TestCase
{
    /**
     * Ensure the extension will be loaded
     */
    public function testExtensionNameConsistency(): void
    {
        $bundle = new AymdevCommonmarkBundle();
        $extension = $bundle->getContainerExtension();

        self::assertInstanceOf(AymdevCommonmarkExtension::class, $extension);
    }
}
