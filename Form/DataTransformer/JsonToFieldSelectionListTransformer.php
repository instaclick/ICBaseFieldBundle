<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use IC\Bundle\Core\FieldBundle\Collections\Collection;
use IC\Bundle\Core\FieldBundle\Entity;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Data Transformer between a JSON string and a collection of field selections
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class JsonToFieldSelectionListTransformer implements DataTransformerInterface
{
    /**
     * @var int the default choice ID
     */
    const DEFAULT_CHOICE_ID = 0;

    /**
     * @var array
     */
    private static $textFieldTypeList = array(
        Entity\Type::TEXT,
        Entity\Type::TEXTAREA,
    );

    /**
     * Constructor
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $fieldList
     */
    public function __construct(ArrayCollection $fieldList)
    {
        $this->fieldList = $fieldList;
    }

    /**
     * Transforms a JSON string to a collection of field selections
     *
     * @param string $value
     *
     * @return \IC\Bundle\Core\FieldBundle\Collections\Collection
     */
    public function transform($value)
    {
        $rawFieldList = json_decode($value, true); // mapped field from JSON string
        $collection   = new Collection();

        if (empty($rawFieldList)) {
            return $collection;
        }

        foreach ($rawFieldList as $rawField) {
            $field = $this->getField($rawField['field']);

            if ($field === null) {
                continue;
            }

            $fieldSelection = new Entity\FieldSelection();
            $fieldSelection->setField($field);

            // Deal with text fields.
            if (in_array($field->getType(), self::$textFieldTypeList)) {
                $fieldSelection->setValue($rawField['value']);
                $collection->set($field->getAlias(), $fieldSelection);

                continue;
            }

            // Deal with single/multiple choice fields.
            $selectedChoiceIdList = $rawField['choiceList'];
            $selectedChoiceList   = $field->getChoiceList()->filter(
                function ($entry) use ($selectedChoiceIdList) {
                    return in_array($entry->getId(), $selectedChoiceIdList);
                }
            );

            $fieldSelection->setChoiceList($selectedChoiceList);

            $collection->set($field->getAlias(), $fieldSelection);
        }

        return $collection;
    }

    /**
     * Transforms a collection of field selections to a JSON string.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $value the collection of field selections
     *
     * @return string
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return '[]';
        }

        $simpleSelectionList = array();

        foreach ($value as $alias => $fieldSelection) {
            $field = $fieldSelection->getField();

            $simpleSelectionList[] = array(
                'field'      => $field->getId(),
                'choiceList' => $this->getChoiceIdList($fieldSelection->getChoiceList()),
                'value'      => $fieldSelection->getValue(),
            );
        }

        return json_encode($simpleSelectionList);
    }

    /**
     * Retrieve the list of choice IDs.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $choiceList
     *
     * @return array
     */
    private function getChoiceIdList(ArrayCollection $choiceList)
    {
        $choiceIdList = array();

        foreach ($choiceList as $choice) {
            $choiceIdList[] = $choice->getId();
        }

        return $choiceIdList;
    }

    /**
     * Retrieve the field by ID.
     *
     * @param mixed $id the identifier
     *
     * @return \IC\Bundle\Core\FieldBundle\Entity\Field|null
     */
    private function getField($id)
    {
        $filteredList = $this->fieldList->filter(
            function ($field) use ($id) {
                return $field->getId() === $id;
            }
        );

        return $filteredList->count() > 0
            ? $filteredList->first()
            : null;
    }
}
