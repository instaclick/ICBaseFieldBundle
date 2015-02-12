<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Core\FieldBundle\Form\DataTransformer\JsonToFieldSelectionListTransformer as DefaultDataTransformer;

/**
 * Data Transformer Factory
 *
 * This is designed to work with model data transformers for this bundle.
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class DataTransformerFactory
{
    /**
     * @var string
     */
    private $transformerClassName = null;

    /**
     * Retrieve the class name of the data transformer
     *
     * @return string|null
     */
    public function getTransformerClassName()
    {
        return $this->transformerClassName;
    }

    /**
     * Define the class name of the data transformer
     *
     * @param string $transformerClassName the name of the transformer
     */
    public function setTransformerClassName($transformerClassName)
    {
        $this->transformerClassName = $transformerClassName;
    }

    /**
     * Create a data transformer
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fieldList the list of available fields
     *
     * @return mixed
     */
    public function create(ArrayCollection $fieldList)
    {
        $className = $this->transformerClassName;

        return ($this->transformerClassName === null)
            ? new DefaultDataTransformer($fieldList)
            : new $className($fieldList);
    }
}
