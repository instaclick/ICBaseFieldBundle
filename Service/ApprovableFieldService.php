<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Approvable Field Service is responsible for manipulating fieldList and content.
 *
 * @author Yuan Xie <yuanxie@live.ca>
 * @author David Maignan <davidm@gmail.com>
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class ApprovableFieldService
{
    /**
     * Merge content.
     *
     * @param string $content          Content
     * @param string $overwriteContent Overwrite content
     *
     * @return string
     */
    public function mergeContent($content, $overwriteContent)
    {
        if (empty($overwriteContent)) {
            return $content;
        }

        if (empty($content)) {
            return $overwriteContent;
        }

        $originalList = json_decode($content, true);
        $updatedList  = json_decode($overwriteContent, true);
        $analysisMap  = $this->createFieldIdMap($originalList, $updatedList);

        // Override the value.
        foreach ($updatedList as $updatedFieldSelection) {
            $fieldId = $updatedFieldSelection['field'];

            if (array_key_exists($fieldId, $analysisMap) && $analysisMap[$fieldId]['index'] === null) {
                $originalList[] = $updatedFieldSelection;

                continue;
            }

            $targetIndex                         = $analysisMap[$fieldId]['index'];
            $originalList[$targetIndex]['value'] = $updatedFieldSelection['value'];
        }

        return json_encode($originalList);
    }

    /**
     * Absorb persistable content.
     *
     * @param string $content         The content that is going to absorb
     * @param string $incomingContent The content that is going to be absorbed from
     *
     * @return string
     */
    public function absorbPersistableContent($content, $incomingContent)
    {
        if (empty($incomingContent)) {
            return $content;
        }

        if (empty($content)) {
            $content = '[]';
        }

        $fieldList         = json_decode($content, true);
        $incomingFieldList = json_decode($incomingContent, true);
        $analysisMap       = $this->createFieldIdMap($fieldList, $incomingFieldList);

        foreach ($incomingFieldList as $fieldSelection) {
            $fieldId     = $fieldSelection['field'];
            $targetIndex = $analysisMap[$fieldId]['index'];

            // If field is approvable, skip absorbing field.
            if ($this->isApprovable($fieldSelection)) {
                continue;
            }

            if ($targetIndex === null) {
                $fieldList[] = $fieldSelection;

                continue;
            }

            // Otherwise it is persistable, absorb field.
            $fieldList[$targetIndex] = $fieldSelection;
        }

        return json_encode($fieldList);
    }

    /**
     * Determine if a field is approvable or not. The default approvable fields, as defined natively here, are:
     *     1) not an array (which implies choice fields), and;
     *     2) not an empty string.
     *
     * @param array $fieldSelection FieldSelection in array
     *
     * @return boolean
     */
    protected function isApprovable(array $fieldSelection)
    {
        return ( ! empty($fieldSelection['value']));
    }

    /**
     * Cancel content.
     *
     * @param string $content          The original content
     * @param string $cancelledContent The content to be deleted
     *
     * @return string
     */
    public function cancelContent($content, $cancelledContent)
    {
        $fieldList = empty($content)
            ? array()
            : json_decode($content, true);

        $cancelledFieldList = empty($cancelledContent)
            ? array()
            : json_decode($cancelledContent, true);

        $analysisMap = $this->createFieldIdMap($fieldList, $cancelledFieldList);

        foreach ($cancelledFieldList as $fieldSelection) {
            $fieldId = $fieldSelection['field'];

            $targetIndex = $analysisMap[$fieldId]['index'];

            // Skip the field if the original index is not found.
            if ($targetIndex !== null && $fieldList[$targetIndex]['value'] !== $fieldSelection['value']) {
                continue;
            }

            unset($fieldList[$targetIndex]);
        }

        // as the index numbers are not reset and we only care about the values, array_values is required before the serialization.
        return json_encode(array_values($fieldList));
    }

    /**
     * Create the analyzed map
     *
     * @param array $originalList  the original list
     * @param array $referenceList the reference list
     *
     * @return array integer-to-map map
     */
    private function createFieldIdMap(array $originalList, array $referenceList)
    {
        $map = array();

        // Get the list of the identifiers of the updated fields
        foreach ($referenceList as $fieldSelection) {
            $fieldId = $fieldSelection['field'];

            $map[$fieldId] = array(
                'index' => null,
            );
        }


        // Search for the position of the target field selection in the original list.
        foreach ($originalList as $index => $fieldSelection) {
            $fieldId = $fieldSelection['field'];

            if (array_key_exists($fieldId, $map)) {
                $map[$fieldId]['index'] = $index;
            }
        }

        return $map;
    }
}
