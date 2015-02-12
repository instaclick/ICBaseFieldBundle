<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\EventListener\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\Proxy;
use Doctrine\ORM\Event\LifecycleEventArgs;
use IC\Bundle\Base\ComponentBundle\EventListener\EntityListenerInterface;
use IC\Bundle\Core\FieldBundle\Entity\Field;
use IC\Bundle\Core\FieldBundle\Entity\FieldChoice;
use IC\Bundle\Core\FieldBundle\Entity\FieldContentAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field Content Aware Listener
 *
 * @author Guilherme Blanco <guilhermeblanco@gmail.com>
 */
class FieldContentAwareListener implements EntityListenerInterface, ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    private $container;

    /**
     * @var array
     */
    private $fieldMapping = array(
        // 'IC\Bundle\Csr\PitBundle\Entity\Biography' => '\IC\Bundle\Core\UserBundle\Entity\Biography',
    );

    /**
     * @param string $fromEntity
     * @param string $toEntity
     */
    public function addFieldMapping($fromEntity, $toEntity)
    {
        $this->fieldMapping[$fromEntity] = $toEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function isListenedTo($entity)
    {
        if ($entity instanceof Field || $entity instanceof FieldChoice) {
            return false;
        }

        return $entity instanceof FieldContentAwareInterface;
    }

    /**
     * Execute the postLoad lifecycle event after FieldContentAware entity being load.
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $event
     */
    public function postLoad(LifecycleEventArgs $event)
    {
        $entity = $event->getEntity();

        if ( ! $this->isListenedTo($entity)) {
            return;
        }

        $this->loadFieldList($entity);
    }

    /**
     * Load the internal field content list.
     *
     * @param \IC\Bundle\Core\FieldBundle\Entity\FieldContentAwareInterface $entity
     */
    private function loadFieldList(FieldContentAwareInterface $entity)
    {
        $entityClassName     = get_class($entity);
        $fieldRepositoryName = $this->getFieldRepositoryName($entityClassName);
        $fieldRepository     = $this->container->get($fieldRepositoryName);

        if ( ! isset($this->repositoryToFieldListMap[$fieldRepositoryName])) {
            $this->repositoryToFieldListMap[$fieldRepositoryName] = $fieldRepository->findAll();
        }

        $availableFieldList     = $this->repositoryToFieldListMap[$fieldRepositoryName];
        $dataTransformerFactory = $this->container->get('ic_core_field.service.data_transformer_factory.model');

        $transformer = $dataTransformerFactory->create($availableFieldList);
        $fieldList   = $transformer->transform($entity->getContent());

        $entity->setFieldList($fieldList);
    }

    /**
     * Get the field repository service and field choice repository service for a given class name
     *
     * @param string $name Fully qualified namespaced class name
     *
     * @return string
     */
    private function getFieldRepositoryName($name)
    {
        foreach ($this->fieldMapping as $old => $new) {
            if (strpos($name, $old) !== false) {
                $name = $new;

                break;
            }
        }

        if (class_exists($name . 'FieldChoice')
            && preg_match('/IC\\\\Bundle\\\\([^\\\\]+)\\\\(.+?)Bundle\\\\Entity\\\\(.+)$/D', $name, $matches)
        ) {
            $name = preg_replace_callback(
                '/([A-Z])/',
                function ($matches) {
                    return '_' . strtolower($matches[1]);
                },
                lcfirst($matches[3])
            );

            return 'ic_' . strtolower($matches[1]) . '_' . strtolower($matches[2]) . '.repository.' . $name . '_field';
        }

        return null;
    }
}
