<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Twig\Extension;

/**
 * Alias to label twig extension
 *
 * @author David Maignan <davidm@gmail.com>
 */
class AliasToLabelExtension extends \Twig_Extension
{
    /**
     * Returns an array of filters which are being are available
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('aliasToLabel', array($this, 'aliasToLabel')),
        );
    }

    /**
     * Return the label from an alias
     *
     * @param string $alias alias
     *
     * @return string
     */
    public function aliasToLabel($alias)
    {
        $alias = preg_replace('/-/', '_', $alias);
        $alias = preg_replace('/^(ic_[^_]+_[^_]+)_/', '$1.', $alias);
        $alias = preg_replace('/(_field)_/', '$1.', $alias);

        return $alias;
    }

    /**
     * Returns the name of the extension
     *
     * @return string
     */
    public function getName()
    {
        return 'ic_core_field.twig.extension.aliasToLabel';
    }
}
