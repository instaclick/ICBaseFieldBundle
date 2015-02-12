<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

use DMS\Filter\Rules as Filter;

use JMS\Serializer\Annotation as Rest;

use IC\Bundle\Base\SerializerBundle\Annotation as BaseRest;

use IC\Bundle\Base\ComponentBundle\Entity\Entity;

/**
 * Field Choice Entity
 *
 * @ORM\MappedSuperclass
 *
 * {@internal FieldChoice contains a many to one relationship with Field.
 *            There can be multiple default in case you have a select multiple. }}
 *
 * Example:
 *
 * Field:
 *     "Do you smoke?"
 * FieldChoice:
 *     "Yes I do."
 *     "No I don't."
 *     "Tell you later."
 *
 * @author Yuan Xie <yuanxie@live.ca>
 * @author Oleksandr Kovalov <oleksandrk@gmail.com>
 */
class FieldChoice extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={ "comment"="primary key, autoincrement, counter" })
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Rest\Type("integer")
     * @Rest\ReadOnly()
     *
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100, options={ "comment"="label of the custom field" })
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=100)
     *
     * @Filter\StripTags()
     * @Filter\Trim()
     * @Filter\StripNewlines()
     *
     * @Rest\Type("string")
     *
     * @BaseRest\Translatable()
     *
     * @var string
     */
    protected $label;

    /**
     * @ORM\Column(type="boolean", name="is_default", options={ "comment"="custom field default value" })
     *
     * @Assert\NotNull()
     *
     * @Rest\Type("boolean")
     *
     * @var boolean
     */
    protected $default = false;

    /**
     * {@internal This field needs to be overwritten by actual entities.
     *            It is the owning side, so it also appends itself in Field when defined. }}
     *
     * @var \IC\Bundle\Core\FieldBundle\Entity\Field
     */
    protected $field;

    /**
     * Get the ID.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label.
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Retrieve the field.
     *
     * @return \IC\Bundle\Core\FieldBundle\Entity\Field
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Define the field.
     *
     * @param \IC\Bundle\Core\FieldBundle\Entity\Field $field
     */
    public function setField(Field $field)
    {
        $this->field = $field;
        $field->addChoice($this);
    }

    /**
     * Checks whether this choice is by default enabled.
     *
     * @return boolean true if the choice is by default enabled, false otherwise
     */
    public function isDefault()
    {
        return $this->default;
    }

    /**
     * Set the choice's state whether it is by default enabled.
     *
     * @param boolean $default true if the choice is by default enabled, false otherwise
     */
    public function setDefault($default = true)
    {
        $this->default = $default;
    }
}
