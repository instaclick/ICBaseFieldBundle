<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity\Repository;

use IC\Bundle\Core\SiteBundle\Entity\Repository\SiteAwareRepository;

/**
 * Field Repository class
 *
 * @author Guilherme Blanco <guilhermeblanco@gmail.com>
 * @author Oleksii Strutsynskyi <oleksiis@gmail.com>
 * @author Yuan Xie <yuanxie@live.ca>
 * @author Oleksandr Kovalov <oleksandrk@gmail.com>
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class FieldRepository extends SiteAwareRepository
{
    /**
     * {@inheritdoc}
     */
    public function newCriteria($alias = 'e')
    {
        $currentSite = $this->siteRepository->getCurrent();
        $criteria    = parent::newCriteria($alias);

        $criteria->addSelect('fcl')
                 ->addSelect('sl')
                 ->leftJoin($alias . '.choiceList', 'fcl')
                 ->innerJoin($alias . '.siteList', 'sl')
                 ->where(':site MEMBER OF ' . $alias . '.siteList')
                 ->setParameter('site', $currentSite);

        return $criteria;
    }

    /**
     * Get all fields from all sites.
     *
     * @internal for admin use only
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function findAll()
    {
        $criteria = $this->newBlankCriteria('f');

        $criteria->addSelect('fcl')
                 ->addSelect('sl')
                 ->leftJoin('f.choiceList', 'fcl')
                 ->innerJoin('f.siteList', 'sl')
                 ->where('f.siteList IS NOT EMPTY');

        return $this->filter($criteria);
    }

    /**
     * Retrieve the list of entities by label.
     *
     * @param string $label the label of the entity
     *
     * @return \IC\Bundle\Base\ComponentBundle\Entity\Entity|null
     */
    public function findByLabel($label)
    {
        $criteria = $this->newCriteria('f');

        $criteria->andWhere('f.label = :label');
        $criteria->setParameter('label', $label);

        return $this->filter($criteria)->first();
    }
}
