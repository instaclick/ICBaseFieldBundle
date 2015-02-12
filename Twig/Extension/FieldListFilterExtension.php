<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Order and Filter a fieldlist twig extension
 *
 * @author David Maignan <davidm@gmail.com>
 */
class FieldListFilterExtension extends \Twig_Extension
{
    /**
     * Returns an array of filters which are being are available
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('filter', array($this, 'filter')),
        );
    }

    /**
     * Return a ordered and filtered field list from a list
     *
     * @param \Doctrine\Common\Collections\ArrayCollection       $fieldList   fieldList
     * @param \Doctrine\Common\Collections\ArrayCollection|array $sortingList list for sorting the fieldList
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function filter(ArrayCollection $fieldList, $sortingList)
    {
        $commonList = new ArrayCollection();

        foreach ($sortingList as $alias) {
            if ( ! $fieldList->containsKey($alias)) {
                continue;
            }

            $fieldSelection = $fieldList->get($alias);

            if ($fieldSelection->isEmpty()) {
                continue;
            }

            $commonList->set($alias, $fieldSelection);
        }

        return $commonList;
    }

    /**
     * Returns the name of the extension
     *
     * @return string
     */
    public function getName()
    {
        return 'ic_core_field.twig.extension.fieldListFilter';
    }
}
