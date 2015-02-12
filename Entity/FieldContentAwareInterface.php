<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity;

use Doctrine\Common\Collections\Collection;

/**
 * Field Content Aware Interface
 *
 * @author Guilherme Blanco <guilhermeblanco@gmail.com>
 */
interface FieldContentAwareInterface
{
    /**
     * Define the field content.
     *
     * @param string $content
     */
    public function setContent($content);

    /**
     * Retrieve the field content.
     *
     * @return string
     */
    public function getContent();

    /**
     * Define the internal field content list.
     *
     * {@internal Should not be used directly in code. }}
     *
     * @param \Doctrine\Common\Collections\Collection $fieldList
     */
    public function setFieldList(Collection $fieldList);

    /**
     * Retrieve the internal field content list.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFieldList();

    /**
     * Retrieve one specific field content.
     *
     * @param string $field
     *
     * @return mixed
     */
    public function getField($field);
}
