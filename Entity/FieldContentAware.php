<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use IC\Bundle\Base\ComponentBundle\Entity\Entity;
use JMS\Serializer\Annotation as Rest;

/**
 * Field Content Aware
 *
 * @ORM\MappedSuperclass
 *
 * {@internal Mapped superclasses in Doctrine are single level only.
 *            We are forced to re-declare the properties from Entity here too. }}
 *
 * @author Guilherme Blanco <guilhermeblanco@gmail.com>
 * @author Oleksandr Kovalov <oleksandrk@gmail.com>
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
abstract class FieldContentAware extends Entity implements FieldContentAwareInterface
{
    /**
     * @ORM\Column(type="datetime", options={ "comment"="creation date and time" })
     *
     * @Gedmo\Timestampable(on="create")
     * @Rest\Type("DateTime")
     *
     * @var \DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", options={ "comment"="date and time of last update" })
     *
     * @Gedmo\Timestampable(on="update")
     * @Rest\Type("DateTime")
     *
     * @var \DateTime
     */
    protected $updated;

    /**
     * @ORM\Column(type="text", nullable=true, options={ "comment"="field content" })
     *
     * @Rest\Type("string")
     *
     * @var string
     */
    protected $content;

    /**
     * @Rest\Type("ArrayCollection")
     *
     * {@internal Volatile collection holding the de-serialized content. }}
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $fieldList;

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content ?: '';
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldList(Collection $fieldList)
    {
        $this->fieldList = $fieldList;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldList()
    {
        return $this->fieldList;
    }

    /**
     * {@inheritdoc}
     */
    public function getField($field)
    {
        return $this->fieldList->get($field);
    }
}
