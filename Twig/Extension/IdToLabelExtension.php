<?php
/**
 * @copyright 2013 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Twig\Extension;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use IC\Bundle\Base\ComponentBundle\Renderer\Renderer;

/**
 * IdToLabelExtension converts field choice ID to corresponding label
 */
class IdToLabelExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    private $container;

    /**
     * Constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            'id_to_label' => new \Twig_Filter_Method($this, 'idToLabel'),
        );
    }

    /**
     * Convert ID to label
     *
     * @param array|integer $idList
     * @param mixed         $entity
     *
     * @return array|string
     */
    public function idToLabel($idList, $entity)
    {
        if (empty($idList)) {
            return null;
        }

        $className   = $this->getEntityName($entity);
        $serviceName = $this->getRepositoryName($className);
        $repository  = $this->container->get($serviceName);

        if (is_scalar($idList)) {
            $entity = $repository->get((int) $idList);

            return $entity ? $entity->getLabel() : $idList;
        }

        $criteria = $repository->newCriteria('e');
        $criteria->andWhere('e.id IN (:idList)');
        $criteria->setParameter('idList', $idList, Connection::PARAM_INT_ARRAY);

        $labelList = array_map(
            function ($entity) {
                return $entity->getLabel();
            },
            $repository->filter($criteria)->toArray()
        );

        return $labelList;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'id_to_label_extension';
    }

    /**
     * Get the class name for a given entity
     *
     * @param mixed $entity
     *
     * @return string|null
     */
    private function getEntityName($entity)
    {
        if ($entity instanceof Renderer) {
            $entity = $entity->getValue();
        }

        if ($entity instanceof ArrayCollection) {
            $entity = $entity->first();
        }

        if ($entity instanceof \stdClass) {
            $fieldList = $entity->fieldList;

            foreach ($fieldList as $key => $value) {
                return $key;
            }
        }

        return $entity ? get_class($entity) : null;
    }

    /**
     * Get the entity repository service name for a given class name
     *
     * @param string $name Fully qualified namespaced class name
     *
     * @return string|null
     */
    private function getRepositoryName($name)
    {
        if (class_exists($name . 'FieldChoice')
            && preg_match('/IC\\\\Bundle\\\\([^\\\\]+)\\\\(.+?)Bundle\\\\Entity\\\\(.+)$/D', $name, $matches)
        ) {
            $name = preg_replace_callback(
                '/([A-Z])/',
                function ($match) {
                    return '_' . strtolower($match[1]);
                },
                lcfirst($matches[3])
            );

            return 'ic_' . strtolower($matches[1]) . '_' . strtolower($matches[2]) . '.repository.' . $name . '_field_choice';
        }

        // Handle " ic_personal_profile.profile_field.hair_color" situation
        $fieldRegEx = "/^ic_([^\.]*)_([^\.]*)\.([^\.]*_field)\.(.*)/";

        if (preg_match($fieldRegEx, $name)) {
            return preg_replace($fieldRegEx, 'ic_\1_\2.repository.\3_choice', $name);
        }

        // Handle " ic_personal_profile_profile_field_hair_color" situation
        $fieldRegEx = "/^ic_([^_]*)_([^_]*)_([^_]*_field)_(.*)/";

        if (preg_match($fieldRegEx, $name)) {
            return preg_replace($fieldRegEx, 'ic_\1_\2.repository.\3_choice', $name);
        }

        // Assume it is a biography field if it starts with "ic_"
        if ( ! preg_match("#^ic_(.*?)#", $name)) {
            return 'ic_core_user.repository.biography_field_choice';
        }

        return null;
    }
}
