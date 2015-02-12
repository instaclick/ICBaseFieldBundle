<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Core\FieldBundle\Service\DataTransformerFactory;
use IC\Bundle\Core\FieldBundle\Form\DataTransformer\JsonToFieldSelectionListTransformer;

/**
 * Test for the data transformer between a JSON string and a collection of FieldSelection.
 *
 * @group ICCoreFieldBundle
 * @group Unit
 * @group Factory
 * @group Service
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class DataTransformerFactoryTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test create with any collection will return default data transformer
     */
    public function testCreateWithAnyCollectionReturnDefaultDataTransformer()
    {
        $factory     = new DataTransformerFactory();
        $transformer = $factory->create(new ArrayCollection());

        $this->assertInstanceOf(
            'IC\Bundle\Core\FieldBundle\Form\DataTransformer\JsonToFieldSelectionListTransformer',
            $transformer
        );
    }

    /**
     * Test create with any collection to return alternative data transformer
     */
    public function testCreateWithAnyCollectionReturnAlternativeDataTransformer()
    {
        $expectedClassName = 'IC\Bundle\Core\FieldBundle\Form\DataTransformer\FieldSelectionListToArrayTransformer';

        $factory = new DataTransformerFactory();
        $factory->setTransformerClassName($expectedClassName);

        $transformer = $factory->create(new ArrayCollection());

        $this->assertInstanceOf($expectedClassName, $transformer);
    }
}
