<?php

namespace IC\Bundle\Core\FieldBundle\Tests\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Core\FieldBundle\Twig\Extension\PresentationListExtension;
use IC\Bundle\Core\FieldBundle\Tests\MockObject;

/**
 * Presentation List Extension Test
 *
 * @group ICCoreFieldBundle
 * @group Unit
 * @group Twig
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class PresentationListExtensionTest extends TestCase
{
    /**
     * @var \IC\Bundle\Core\FieldBundle\Twig\Extension\PresentationListExtension
     */
    private $presentationListExtension;

    /**
     * @var \IC\Bundle\Core\FieldBundle\Twig\Extension\IdToLabelExtension
     */
    private $idToLabelExtension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->presentationListExtension = new PresentationListExtension();
        $this->idToLabelExtension        = $this->createMock('IC\Bundle\Core\FieldBundle\Twig\Extension\IdToLabelExtension');

        $this->presentationListExtension->setIdToLabelExtension($this->idToLabelExtension);
    }

    /**
     * Test if the invalid argument exception is thrown when the configuration is undefined.
     *
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentExceptionWithoutConfig()
    {
        // Setup
        $mockEntity = new MockObject\MockStaticEntity();

        $this->presentationListExtension->setConfig(array('abc' => array()));

        // Test
        $this->presentationListExtension->inPresentationList($mockEntity, 'def');
    }

    /**
     * Test if the invalid argument exception is thrown when the extension cannot find the configuration set.
     *
     * @expectedException InvalidArgumentException
     */
    public function testInvalidArgumentExceptionWithInvalidId()
    {
        // Setup
        $mockEntity = new MockObject\MockStaticEntity();

        // Test
        $this->presentationListExtension->inPresentationList($mockEntity, 'def');
    }

    /**
     * Test the data retrieval on a nested object
     */
    public function testDataRetrievalOnNestedObject()
    {
        // Setup
        //$this->idToLabelExtension->expects($this->never());

        $mockEntity    = new MockObject\MockStaticEntity();
        $barMockEntity = new MockObject\MockStaticEntity();

        $mockEntity->setFoo('wonderfoo');
        $mockEntity->setBar($barMockEntity);

        $barMockEntity->setFoo('foto');
        $barMockEntity->setBar(array(1, 2, 3));

        $this->presentationListExtension->setConfig(
            array(
                'sample' => array(
                    'xa' => 'foo',
                    'db' => 'bar',
                    'ac' => 'bar.foo',
                    'jo' => 'bar.bar[2]',
                    'ja' => 'bar.bar[3]',
                ),
            )
        );

        // Test
        $contextMap = $this->presentationListExtension->inPresentationList($mockEntity, 'sample');

        // Ensure the order of variables.
        $this->assertEquals(
            array('xa', 'db', 'ac', 'jo', 'ja'),
            array_keys($contextMap->toArray())
        );

        $this->assertEquals('wonderfoo', $contextMap['xa']);
        $this->assertEquals($barMockEntity, $contextMap['db']);
        $this->assertEquals('foto', $contextMap['ac']);
        $this->assertEquals(3, $contextMap['jo']);
        $this->assertEquals(null, $contextMap['ja']);
    }

    /**
     * Test the data retrieval on a nested object
     */
    public function testDataRetrievalOnNestedObjectWithUnknownPath()
    {
        // Setup
        //$this->idToLabelExtension->expects($this->never());

        $mockEntity = new MockObject\MockStaticEntity();

        $mockEntity->setFoo('wonderfoo');
        $mockEntity->setBar(array(1, 2, 3));

        $this->presentationListExtension->setConfig(
            array(
                'sample' => array(
                    'beer'       => 'beer',
                    'outOfBound' => 'bar[3]',
                ),
            )
        );

        // Test
        $contextMap = $this->presentationListExtension->inPresentationList($mockEntity, 'sample');

        $this->assertEquals(null, $contextMap['beer']);
        $this->assertEquals(null, $contextMap['outOfBound']);
    }

    /**
     * Test the data retrieval on a content-aware object
     *
     * This is a temporary test.
     */
    public function testDataRetrievalOnContentAwareObject()
    {
        // Setup
        // Mock the id-to-label extension. Please note that this is just to make sure that the extension is used.
        $this->idToLabelExtension
            ->expects($this->exactly(2))
            ->method('idToLabel')
            ->with($this->anything())
            ->will($this->returnValue('snowman'));

        // Mock an entity and a collection
        $mockEntity     = new MockObject\MockDynamicEntity();
        $mockCollection = new ArrayCollection();

        $mockEntity->setFoo(890);
        $mockEntity->setBar(array(1, 2, 3));
        $mockEntity->setFieldList($mockCollection);

        $mockCollection->set('abc', 123);
        $mockCollection->set('def', 456);
        $mockCollection->set('ghi', 'poppy');

        $this->presentationListExtension->setConfig(
            array(
                'sample' => array(
                    'foo' => 'foo',
                    'def' => 'fieldList[def]',
                    'abc' => 'fieldList[abc]',
                    'xyz' => 'fieldList[ghi]',
                ),
            )
        );

        // Test
        $contextMap = $this->presentationListExtension->inPresentationList($mockEntity, 'sample');

        // Ensure the order of variables.
        $this->assertEquals(
            array('foo', 'def', 'abc', 'xyz'),
            array_keys($contextMap->toArray())
        );

        $this->assertEquals(890, $contextMap['foo']);
        $this->assertEquals('snowman', $contextMap['def']->value);
        $this->assertEquals('snowman', $contextMap['abc']->value);
        $this->assertEquals('poppy', $contextMap['xyz']->value);
    }
}
