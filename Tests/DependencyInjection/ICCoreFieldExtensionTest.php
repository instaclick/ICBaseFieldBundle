<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Tests\DependencyInjection;

use IC\Bundle\Base\TestBundle\Test\DependencyInjection\ExtensionTestCase;
use IC\Bundle\Core\FieldBundle\DependencyInjection\ICCoreFieldExtension;

/**
 * Test for ICCoreFieldExtension
 *
 * @group ICCoreFieldBundle
 * @group Unit
 * @group DependencyInjection
 */
class ICCoreFieldExtensionTest extends ExtensionTestCase
{
    /**
     * Test configuration
     */
    public function testConfiguration()
    {
        $loader = new ICCoreFieldExtension();

        $this->load($loader, array());
    }
}
