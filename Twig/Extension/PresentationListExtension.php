<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Base\ComponentBundle\Entity\Entity;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;

/**
 * Presentation list twig extension
 *
 * @author Yuan Xie <yuanxie@live.ca>
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class PresentationListExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $config;

    /**
     * @var IdToLabelExtension
     */
    private $idToLabelExtension;

    /**
     * Define the name of the extension.
     *
     * @param string $name Name of this extension
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Define the config.
     *
     * @param array $config Configuration
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * Define the ID-to-Label Extension
     *
     * @param \IC\Bundle\Core\FieldBundle\Twig\Extension\IdToLabelExtension $idToLabelExtension the extension
     */
    public function setIdToLabelExtension(IdToLabelExtension $idToLabelExtension)
    {
        $this->idToLabelExtension = $idToLabelExtension;
    }

    /**
     * Returns an array of filters which are available
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('inPresentationList', array($this, 'inPresentationList')),
        );
    }

    /**
     * Return the presentation list.
     *
     * This code is modified to anticipate all varieties of data entities, including array, Entity object, and data
     * structure with plain old PHP objects.
     *
     * @param mixed  $entity   Data entity
     * @param string $configId Presentation Configuration ID
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function inPresentationList($entity, $configId)
    {
        if ( ! $this->config) {
            throw new \InvalidArgumentException('The configuration map is undefined');
        }

        if ( ! in_array($configId, array_keys($this->config))) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid configuration ID. Knew [%s]. Requested for [%s]',
                implode(', ', array_keys($this->config)),
                $configId
            ));
        }

        $presentationPathList = $this->config[$configId];
        $contextVariableMap   = new ArrayCollection();

        $propertyAccessor = PropertyAccess::getPropertyAccessor();

        foreach ($presentationPathList as $variableName => $selector) {
            $field        = null;
            $accessedData = null;

            try {
                $accessedData = $propertyAccessor->getValue($entity, $selector);
            } catch (NoSuchPropertyException $exception) {
                // NOP
            }

            $propertyNameList = explode('.', $selector);
            $lastNode         = array_pop($propertyNameList);
            $isCustomField    = (bool) preg_match('/^fieldList\[[^\]]+\]$/', $lastNode);

            // If the accessed data is not from a field list, move on to the next one.
            if ( ! $isCustomField) {
                $contextVariableMap->set($variableName, $accessedData);

                continue;
            }

            $field = new \stdClass();

            // Make assumption that the access key (for the field list) is the label.
            $field->label = preg_replace('/^fieldList\[([^\]]+)\]$/', '$1', $lastNode);
            $field->value = $accessedData;

            // TEMPORARY SOLUTION
            $field->label = preg_replace('/-/', '_', $field->label);
            $field->label = preg_replace('/^(ic_[^_]+_[^_]+)_/', '$1.', $field->label);
            $field->label = preg_replace('/(_field)_/', '$1.', $field->label);

            // If the value of the custom field is not string, then apply the extension.
            if ( ! is_string($accessedData)) {
                // Now, convert the accessed data with idToLabelExtension.
                $parentSelector = empty($propertyNameList)
                    ? null
                    : implode('.', $propertyNameList);

                $accessedEntity = $parentSelector === null
                    ? $entity
                    : $propertyAccessor->getValue($entity, $parentSelector);

                $field->value = $this->idToLabelExtension
                                     ->idToLabel($accessedData, $accessedEntity);
            }

            $contextVariableMap->set($variableName, $field);
        }

        return $contextVariableMap;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
