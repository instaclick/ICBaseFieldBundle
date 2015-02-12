<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Field Selection Model
 *
 * This model is specially designated as a virtual entity/model, NOT a form model by architects.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class FieldSelection
{
    /**
     * @var \IC\Bundle\Core\FieldBundle\Entity\Field
     */
    protected $field;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $choiceList;

    /**
     * @var string|integer|float
     */
    protected $value;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->choiceList = new ArrayCollection();
    }

    /**
     * Retrieve the field
     *
     * @return \IC\Bundle\Core\FieldBundle\Entity\Field
     */
    final public function getField()
    {
        return $this->field;
    }

    /**
     * Define the field
     *
     * @param \IC\Bundle\Core\FieldBundle\Entity\Field $field the field entity
     */
    final public function setField(Field $field)
    {
        $this->field = $field;
    }

    /**
     * Retrieve the list of choices for this field
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    final public function getChoiceList()
    {
        return $this->choiceList;
    }

    /**
     * Define the list of choices for this field
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $choiceList the list of choices
     */
    final public function setChoiceList(ArrayCollection $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * Retrieve the value of this field
     *
     * @return string
     */
    final public function getValue()
    {
        return $this->value;
    }

    /**
     * Define the value of this field
     *
     * @param string $value the new value
     */
    final public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Check if the selection is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->value) && $this->choiceList->count() === 0;
    }
}
