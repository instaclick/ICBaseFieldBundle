<?php

/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Tests\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Core\FieldBundle\Entity\FieldSelection;
use IC\Bundle\Core\FieldBundle\Twig\Extension\FieldListFilterExtension;

/**
 * Order field list extension test
 *
 * @group IC
 * @group Unit
 * @group Twig
 *
 * @author David Maignan <davidm@gmail.com>
 */
class FieldListFilterExtensionTest extends TestCase
{
    /**
     * @var \IC\Bundle\Core\FieldBundle\Twig\Extension\FieldListFilterExtension
     */
    private $fieldListFilterExtension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->fieldListFilterExtension = new FieldListFilterExtension();
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertEquals('ic_core_field.twig.extension.fieldListFilter', $this->fieldListFilterExtension->getName());
    }

    /**
     * Test getFilters
     */
    public function testGetFilters()
    {
        $filterList = $this->fieldListFilterExtension->getFilters();

        $this->assertTrue(is_array($filterList));

        $filter = reset($filterList);

        $this->assertEquals('Twig_SimpleFilter', get_class($filter));
        $this->assertEquals('filter', $filter->getName());
        $this->assertEquals(array($this->fieldListFilterExtension, 'filter'), $filter->getCallable());
    }

    /**
     * Test filterList
     *
     * @param array $fieldList   list of fields
     * @param array $sortingList list for sorting the fields
     * @param array $expected    list of fields sorted
     *
     * @dataProvider provideOrderFieldListData
     */
    public function testOrderFieldList($fieldList, $sortingList, $expected)
    {
        $this->assertEquals(
            $this->fieldListFilterExtension->filter(
                $fieldList,
                $sortingList
            ),
            $expected,
            'Expected key is wrong for ' . $fieldList
        );
    }

     /**
     * Creates mock data for ordering a list of field from an array of fields
     *
     * @return array
     */
    public function provideOrderFieldListData()
    {
        $fieldAliasList = array(
            'ic_personal_intimacy_intimacy_field_i_love',
            'ic_personal_intimacy_intimacy_field_i_hate',
            'ic_personal_intimacy_intimacy_field_penis_size_matter',
            'ic_personal_intimacy_intimacy_field_favorite_positions',
        );

        $fieldList = new ArrayCollection();

        foreach ($fieldAliasList as $index => $alias) {
            $fieldList->set($alias, $this->createFieldSelection($index + 1, $alias, $alias));
        }

        $sortingList = array(
            'ic_personal_intimacy_intimacy_field_i_hate',
            'ic_personal_intimacy_intimacy_field_i_love',
            'ic_personal_intimacy_intimacy_field_penis_size_matter',
            'ic_personal_intimacy_intimacy_field_which_hair_color_is_the_sexiest'
        );

        $expected = new ArrayCollection(array(
            'ic_personal_intimacy_intimacy_field_i_hate'            => $fieldList->get('ic_personal_intimacy_intimacy_field_i_hate'),
            'ic_personal_intimacy_intimacy_field_i_love'            => $fieldList->get('ic_personal_intimacy_intimacy_field_i_love'),
            'ic_personal_intimacy_intimacy_field_penis_size_matter' => $fieldList->get('ic_personal_intimacy_intimacy_field_penis_size_matter'),
        ));

        $baseTestList = array(
            array($fieldList, $sortingList, $expected),
        );

        return $baseTestList;
    }

    private function createFieldSelection($id, $alias, $value)
    {
        $field = $this->getHelper('Unit/Entity')->createMock('IC\Bundle\Core\FieldBundle\Entity\Field', 1);
        $field->setAlias($alias);

        $fieldSelection = new FieldSelection();
        $fieldSelection->setField($field);
        $fieldSelection->setValue($value);

        return $fieldSelection;
    }
}
