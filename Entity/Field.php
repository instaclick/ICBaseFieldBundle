<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;

use DMS\Filter\Rules as Filter;

use JMS\Serializer\Annotation as Rest;

use IC\Bundle\Base\SerializerBundle\Annotation as BaseRest;

use Gedmo\Mapping\Annotation as Gedmo;

use IC\Bundle\Base\ComponentBundle\Entity\Entity;

use IC\Bundle\Core\FieldBundle\Entity\Type;

use IC\Bundle\Core\SiteBundle\Entity\Site;

/**
 * Field Entity
 *
 * @ORM\MappedSuperclass
 *
 * @DoctrineAssert\UniqueEntity("alias")
 *
 * @author Yuan Xie <yuanxie@live.ca>
 * @author Oleksii Strutsynskyi <oleksiis@gmail.com>
 * @author Oleksandr Kovalov <oleksandrk@gmail.com>
 */
class Field extends Entity
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
     * @ORM\Column(type="string", unique=true, length=100, options={ "comment"="alias of the custom field" })
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
     * @Gedmo\Slug(separator="_", fields={"label"})
     *
     * @var string
     */
    protected $alias;

    /**
     * @ORM\Column(type="string", unique=true, length=100, options={ "comment"="custom field label" })
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
     * @ORM\Column(type="string", length=500, nullable=true, options={ "comment"="custom field description" })
     *
     * @Assert\Length(max=500)
     *
     * @Filter\StripTags()
     * @Filter\Trim()
     * @Filter\StripNewlines()
     *
     * @Rest\Type("string")
     *
     * @var string
     */
    protected $description;

    /**
     * @ORM\Column(type="string", length=20, options={ "comment"="type of custom field" })
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=20)
     *
     * @Filter\StripTags()
     * @Filter\Trim()
     * @Filter\StripNewlines()
     *
     * @Rest\Type("string")
     *
     * @var integer
     */
    protected $type = Type::TEXT;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $siteList;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $choiceList;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->siteList   = new ArrayCollection();
        $this->choiceList = new ArrayCollection();
    }

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
     * Get the alias.
     *
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Define the alias.
     *
     * @param string $alias the new alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
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
     * Get the description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the description.
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the render type.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the render type.
     *
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get the site list.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSiteList()
    {
        return $this->siteList;
    }

    /**
     * Set the site list.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $siteList
     */
    public function setSiteList(ArrayCollection $siteList)
    {
        $this->siteList = $siteList;
    }

    /**
     * Add an Site to the site list.
     *
     * @param Site $site
     */
    public function addSite(Site $site)
    {
        $this->siteList->add($site);
    }

    /**
     * Get the choice list.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChoiceList()
    {
        return $this->choiceList;
    }

    /**
     * Set the choice list.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $choiceList
     */
    public function setChoiceList(ArrayCollection $choiceList)
    {
        $this->choiceList = $choiceList;
    }

    /**
     * Add an choice to the choice list.
     *
     * @param FieldChoice $choice
     */
    public function addChoice(FieldChoice $choice)
    {
        $this->choiceList->add($choice);
    }
}
