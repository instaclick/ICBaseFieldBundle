<?php
/**
 * @copyright 2013 Instaclick Inc.
 */
namespace IC\Bundle\Core\FieldBundle\DataFixtures\ORM;

use IC\Bundle\Core\SecurityBundle\DataFixtures\ORM\FullAccessAclDataFixture;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Acl data fixture for FieldBundle entities.
 *
 * @author Martin Llano <martinl@gmail.com>
 */
class Acl extends FullAccessAclDataFixture
{
    /**
     * Build Entity Name List.
     *
     * @return ArrayCollection
     */
    private function buildEntityNameList()
    {
        $classNameList = array(
            'IC\Bundle\Core\FieldBundle\Entity\Field',
            'IC\Bundle\Core\FieldBundle\Entity\FieldChoice',
        );

        $entityNameList = new ArrayCollection($classNameList);

        return $entityNameList;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityNameList()
    {
        return $this->buildEntityNameList();
    }
}
