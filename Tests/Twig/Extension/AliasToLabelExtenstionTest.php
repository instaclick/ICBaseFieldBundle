<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Tests\Twig\Extension;

use IC\Bundle\Base\TestBundle\Test\TestCase;
use IC\Bundle\Core\FieldBundle\Twig\Extension\AliasToLabelExtension;

/**
 * Alias to Label extension test
 *
 * @group IC
 * @group Unit
 * @group Twig
 *
 * @author David Maignan <davidm@gmail.com>
 */
class AliasToLabelExtensionTest extends TestCase
{
    /**
     * @var \IC\Bundle\Core\FieldBundle\Twig\Extension\AliasToLabelExtension
     */
    private $aliasToLabelExtension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->aliasToLabelExtension = new AliasToLabelExtension();
    }

    /**
     * Test getName
     */
    public function testGetName()
    {
        $this->assertEquals('ic_core_field.twig.extension.aliasToLabel', $this->aliasToLabelExtension->getName());
    }

    /**
     * Test aliasToLabel
     *
     * @param string $alias alias
     * @param string $key   key
     *
     * @dataProvider provideAliasKeyData
     */
    public function testAliasToLabel($alias, $key)
    {
        $this->assertEquals($this->aliasToLabelExtension->aliasToLabel($alias), $key, 'Expected key is wrong for ' . $alias);
    }

     /**
     * Creates mock data for a alias and key to test aliasToLabel method
     *
     * @return array
     */
    public function provideAliasKeyData()
    {
        $baseTestList = array(
            array('ic_personal_intimacy_intimacy_field_i_love', 'ic_personal_intimacy.intimacy_field.i_love'),
            array('ic_personal_intimacy_intimacy_field_sex_partners_count', 'ic_personal_intimacy.intimacy_field.sex_partners_count'),
            array('ic_personal_intimacy_intimacy_field_how_far_am_i_willing_to_travel', 'ic_personal_intimacy.intimacy_field.how_far_am_i_willing_to_travel'),
            array('ic_personal_intimacy_intimacy_field_what_i_ll_do', 'ic_personal_intimacy.intimacy_field.what_i_ll_do'),
            array('ic_personal_intimacy_intimacy_field_most_attractive_age_group', 'ic_personal_intimacy.intimacy_field.most_attractive_age_group')
        );

        return $baseTestList;
    }

    /**
     * Test getFilters
     */
    public function testGetFilters()
    {
        $filterList = $this->aliasToLabelExtension->getFilters();

        $this->assertTrue(is_array($filterList));

        $filter = reset($filterList);

        $this->assertEquals('Twig_SimpleFilter', get_class($filter));
        $this->assertEquals('aliasToLabel', $filter->getName());
        $this->assertEquals(array($this->aliasToLabelExtension, 'aliasToLabel'), $filter->getCallable());
    }
}
