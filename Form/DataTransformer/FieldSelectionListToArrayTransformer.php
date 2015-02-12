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
 * Data Transformer between a collection of field selections and a simple alias-to-value map
 *
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class FieldSelectionListToArrayTransformer implements DataTransformerInterface
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
     * @var array
     */
    private static $choiceFieldTypeList = array(
        Entity\Type::CHECKBOX,
        Entity\Type::CHECKBOX_MULTIPLE,
        Entity\Type::SELECT_MULTIPLE
    );

    /**
     * @var array
     */
    private static $radioFieldTypeList = array(
        Entity\Type::SELECT,
        Entity\Type::RADIO
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
     * Transforms a collection of field selections to a simple alias-to-value map
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $value the collection of field selections
     *
     * @return array
     */
    public function transform($value)
    {
        $aliasToValueMap = array();

        if (empty($value)) {
            return $aliasToValueMap;
        }

        foreach ($value as $alias => $fieldSelection) {
            $field = $fieldSelection->getField();

            // Deal with text fields.
            if (in_array($field->getType(), self::$textFieldTypeList)) {
                $aliasToValueMap[$field->getAlias()] = $fieldSelection->getValue();

                continue;
            }

            $choiceIdList = $this->getChoiceIdList($fieldSelection->getChoiceList());

            if (in_array($field->getType(), self::$choiceFieldTypeList)) {
                $aliasToValueMap[$field->getAlias()] = $choiceIdList;
            }

            if (in_array($field->getType(), self::$radioFieldTypeList) && $fieldSelection->getChoiceList()->count() !== 0) {
                $aliasToValueMap[$field->getAlias()] = $fieldSelection->getChoiceList()->first()->getId();
            }
        }

        return $aliasToValueMap;
    }

    /**
     * Transforms a simple alias-to-value map to a collection of field selections
     *
     * @param array $value the alias-to-value map
     *
     * @return \IC\Bundle\Core\FieldBundle\Collections\Collection
     */
    public function reverseTransform($value)
    {
        $collection = new Collection();

        if (empty($value)) {
            return $collection;
        }

        foreach ($value as $alias => $value) {
            $field = $this->getField($alias);

            if ($field === null) {
                continue;
            }

            $fieldSelection = new Entity\FieldSelection();
            $fieldSelection->setField($field);

            // Deal with text fields.
            if (in_array($field->getType(), self::$textFieldTypeList)) {
                $fieldSelection->setValue($value);
                $collection->set($field->getAlias(), $fieldSelection);

                continue;
            }

            // Deal with single/multiple choice fields.
            $selectedChoiceIdList = is_array($value)
                ? $value
                : array($value);

            $selectedChoiceList = $field->getChoiceList()->filter(
                function ($entry) use ($selectedChoiceIdList) {
                    return in_array((string) $entry->getId(), $selectedChoiceIdList);
                }
            );

            $fieldSelection->setChoiceList($selectedChoiceList);

            $collection->set($field->getAlias(), $fieldSelection);
        }

        return $collection;
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
            $choiceIdList[] = (string) $choice->getId();
        }

        return $choiceIdList;
    }

    /**
     * Retrieve the field by alias.
     *
     * @param string $alias the alias
     *
     * @return \IC\Bundle\Core\FieldBundle\Entity\Field|null
     */
    private function getField($alias)
    {
        $filteredList = $this->fieldList->filter(
            function ($field) use ($alias) {
                return $field->getAlias() === $alias;
            }
        );

        return $filteredList->count() > 0
            ? $filteredList->first()
            : null;
    }
}
