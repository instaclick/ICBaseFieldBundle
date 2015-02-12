<?php

namespace IC\Bundle\Core\FieldBundle\Tests\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Core\FieldBundle\Entity\FieldSelection;
use IC\Bundle\Core\FieldBundle\Entity\Type;
use IC\Bundle\Core\FieldBundle\Form\DataTransformer\JsonToFieldSelectionListTransformer;
use IC\Bundle\Core\FieldBundle\Tests\MockObject;

/**
 * Test for the data transformer between a JSON string to a collection of FieldSelection.
 *
 * @group ICCoreFieldBundle
 * @group Unit
 * @group Form
 * @group DataTransformer
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class JsonToFieldSelectionListTransformerTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Test how the transformer reverse-transforms the empty data.
     *
     * @param mixed $sampleData the sample data
     *
     * @dataProvider emptyCollectionDataProvider
     */
    public function testReverseTransformWithEmptyCollectionReturnJSONStringForEmptyList($sampleData)
    {
        $transformer = new JsonToFieldSelectionListTransformer(new ArrayCollection());
        $content     = $transformer->reverseTransform($sampleData);

        $this->assertEquals('[]', $content);
    }

    /**
     * Data provider for empty collections.
     *
     * @return array
     */
    public function emptyCollectionDataProvider()
    {
        return array(
            array(new ArrayCollection()),
            array(null)
        );
    }

    /**
     * Test how the transformer reverse-transforms a collection of field selections.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $sampleData      the sample data
     * @param string                                       $expectedContent the expected content
     *
     * @dataProvider randomCollectionDataProvider
     */
    public function testReverseTransformWithSampleDataReturnIdenticalContent(ArrayCollection $sampleData, $expectedContent)
    {
        $transformer = new JsonToFieldSelectionListTransformer(new ArrayCollection());
        $content     = $transformer->reverseTransform($sampleData);

        $this->assertEquals($expectedContent, $content);
    }

    /**
     * Data provider for random non-empty collection.
     *
     * @return array
     */
    public function randomCollectionDataProvider()
    {
        $entityHelper = $this->getHelper('Unit\Entity');

        $sampleTextField = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 8);
        $sampleTextField->setType(Type::TEXT);

        $sampleChoiceField = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 9);
        $sampleChoiceField->setType(Type::SELECT);

        $fieldSelectionAlpha = new FieldSelection();
        $fieldSelectionAlpha->setField($sampleTextField);
        $fieldSelectionAlpha->setValue('monkey!');

        $fieldSelectionBravo = new FieldSelection();
        $fieldSelectionBravo->setField($sampleChoiceField);
        $fieldSelectionBravo->getChoiceList()->add($entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\FieldChoice', 1));

        $expectedContent = json_encode(array(
            array(
                'field'      => 8,
                'choiceList' => array(),
                'value'      => 'monkey!',
            ),
            array(
                'field'      => 9,
                'choiceList' => array(1),
                'value'      => null,
            ),
        ));

        return array(
            array(
                new ArrayCollection(array($fieldSelectionAlpha, $fieldSelectionBravo)),
                $expectedContent
            ),
        );
    }

    /**
     * Test how the transformer transforms the empty data.
     *
     * @param string $sampleContent the sample content
     *
     * @dataProvider emptyJsonDataProvider
     */
    public function testTransformWithEmptyJsonReturnEmptyCollection($sampleContent)
    {
        $transformer = new JsonToFieldSelectionListTransformer(new ArrayCollection());
        $fieldList   = $transformer->transform($sampleContent);

        $this->assertEquals(0, $fieldList->count());
    }

    /**
     * Data Transformer for testTransformEmptyJson
     *
     * @return array
     */
    public function emptyJsonDataProvider()
    {
        return array(
            array(''),
            array('[]')
        );
    }

    /**
     * Test how the transformer transforms the given empty data.
     *
     * @param string                                       $sampleContent   the sample content
     * @param \Doctrine\Common\Collections\ArrayCollection $sampleFieldList the list of sample fields
     *
     * @dataProvider sharedInputDataProvider
     */
    public function testTransformWithProperContentReturnAliasToSelectionMap($sampleContent, ArrayCollection $sampleFieldList)
    {
        $transformer = new JsonToFieldSelectionListTransformer($sampleFieldList);
        $fieldList   = $transformer->transform($sampleContent);

        $this->assertEquals(4, $fieldList->count());
        $this->assertTrue($fieldList->get('python') !== null);
    }

    /**
     * Test how the transformer transforms a single choice field.
     *
     * @param string                                       $sampleContent   the sample content
     * @param \Doctrine\Common\Collections\ArrayCollection $sampleFieldList the list of sample fields
     *
     * @dataProvider sharedInputDataProvider
     */
    public function testTransformWithProperContentExtractSingleChoiceFieldSelection($sampleContent, ArrayCollection $sampleFieldList)
    {
        $transformer = new JsonToFieldSelectionListTransformer($sampleFieldList);
        $fieldList   = $transformer->transform($sampleContent);

        $this->assertEquals(1, $fieldList->get('elephant')->getChoiceList()->count());
        $this->assertEquals(0, strlen($fieldList->get('elephant')->getValue()));
    }

    /**
     * Test how the transformer transforms a multiple choice field.
     *
     * @param string                                       $sampleContent   the sample content
     * @param \Doctrine\Common\Collections\ArrayCollection $sampleFieldList the list of sample fields
     *
     * @dataProvider sharedInputDataProvider
     */
    public function testTransformWithProperContentExtractMultipleChoiceFieldSelectionAndIgnoreInvalidChoices($sampleContent, ArrayCollection $sampleFieldList)
    {
        $transformer = new JsonToFieldSelectionListTransformer($sampleFieldList);
        $fieldList   = $transformer->transform($sampleContent);

        $this->assertEquals(2, $fieldList->get('ruby')->getChoiceList()->count());
        $this->assertEquals(0, strlen($fieldList->get('ruby')->getValue()));
    }

    /**
     * Test how the transformer transforms a text field.
     *
     * @param string                                       $sampleContent   the sample content
     * @param \Doctrine\Common\Collections\ArrayCollection $sampleFieldList the list of sample fields
     *
     * @dataProvider sharedInputDataProvider
     */
    public function testTransformWithProperContentExtractTextFieldSelection($sampleContent, ArrayCollection $sampleFieldList)
    {
        $transformer = new JsonToFieldSelectionListTransformer($sampleFieldList);
        $fieldList   = $transformer->transform($sampleContent);

        $this->assertEquals(0, $fieldList->get('python')->getChoiceList()->count());
        $this->assertEquals('hebi ha kawaikunai', $fieldList->get('python')->getValue());
    }

    /**
     * Test how the transformer transforms a text field from corruptted data.
     *
     * @param string                                       $sampleContent   the sample content
     * @param \Doctrine\Common\Collections\ArrayCollection $sampleFieldList the list of sample fields
     *
     * @dataProvider sharedInputDataProvider
     */
    public function testTransformWithProperContentExtractTextFieldSelectionFromCorruptedData($sampleContent, ArrayCollection $sampleFieldList)
    {
        $transformer = new JsonToFieldSelectionListTransformer($sampleFieldList);
        $fieldList   = $transformer->transform($sampleContent);

        $this->assertEquals(0, $fieldList->get('cat')->getChoiceList()->count());
        $this->assertEquals('your cat is plotting to kill you', $fieldList->get('cat')->getValue());
    }

    /**
     * Data provider for testTransformWithProperContent.
     *
     * @return array
     */
    public function sharedInputDataProvider()
    {
        $entityHelper = $this->getHelper('Unit\Entity');

        // Mix non-existent fields, string fields and collection fields.
        // - assert that the transformer extract a single-choice field properly (field #123).
        // - assert that the transformer extract a multiple-choice field properly (field #567).
        // - assert that the transformer extract a text field properly (field #345).
        // - assert that invalid choices are ignored (choice #6).
        // - assert that invalid fields are ignored (field #789).
        // - assert that only the value is extracted as field #901 is a text field.

        $sampleContent = <<<JSON
            [
                {
                    "field": 123,
                    "choiceList": [1],
                    "value": null
                },
                {
                    "field": 345,
                    "choiceList": [],
                    "value": "hebi ha kawaikunai"
                },
                {
                    "field": 567,
                    "choiceList": [1,2,6],
                    "value": null
                },
                {
                    "field": 789,
                    "choiceList": [1,2,3],
                    "value": null
                },
                {
                    "field": 901,
                    "choiceList": [1,2,3],
                    "value": "your cat is plotting to kill you"
                }
            ]
JSON;

        $sampleFieldList = new ArrayCollection();

        // Common choices. Please note that this might not happen in reality.
        $choice1 = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\FieldChoice', 1);
        $choice2 = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\FieldChoice', 2);

        $field123 = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 123);
        $field123->setAlias('elephant');
        $field123->setType(Type::SELECT);
        $field123->getChoiceList()->add($choice1);
        $field123->getChoiceList()->add($choice2);

        $field345 = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 345);
        $field345->setAlias('python');
        $field345->setType(Type::TEXT);

        $field567 = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 567);
        $field567->setAlias('ruby');
        $field567->setType(Type::CHECKBOX_MULTIPLE);
        $field567->getChoiceList()->add($choice1);
        $field567->getChoiceList()->add($choice2);

        $field901 = $entityHelper->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 901);
        $field901->setAlias('cat');
        $field901->setType(Type::TEXTAREA);

        $sampleFieldList->add($field123);
        $sampleFieldList->add($field345);
        $sampleFieldList->add($field567);
        $sampleFieldList->add($field901);

        return array(
            array($sampleContent, $sampleFieldList),
        );
    }
}
