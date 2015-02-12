<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Core\FieldBundle\Form\Type;

use Doctrine\Common\Collections\Collection;
use IC\Bundle\Core\FieldBundle\Entity\Field;
use IC\Bundle\Core\FieldBundle\Entity\Type;
use IC\Bundle\Core\FieldBundle\Form\DataTransformer\JsonToFieldSelectionListTransformer as ModelDataTransformer;
use IC\Bundle\Core\FieldBundle\Service\DataTransformerFactory;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * XML Form Data Type
 *
 * @author Guilherme Blanco <guilhermeblanco@gmail.com>
 * @author Yuan Xie <yuanxie@live.ca>
 * @author Oleksii Strutsynskyi <oleksiis@gmail.com>
 * @author David Maignan <davidm@gmail.com>
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class FieldContentType extends AbstractType
{
    /**
     * @var array
     */
    private static $choiceTypeList = array(
        Type::CHECKBOX,
        Type::SELECT,
        Type::RADIO,
        Type::CHECKBOX_MULTIPLE,
        Type::SELECT_MULTIPLE,
    );

    /**
     * @var string
     */
    private static $alternativeFormType = 'choice';

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    private $logger;

    /**
     * @var \IC\Bundle\Core\FieldBundle\Service\DataTransformerFactory
     */
    private $modelDataTransformerFactory;

    /**
     * @var \IC\Bundle\Core\FieldBundle\Service\DataTransformerFactory
     */
    private $viewDataTransformerFactory;

    /**
     * @var \IC\Bundle\Base\ComponentBundle\Form\Context\TranslationFormContextInterface
     */
    private $translationFormContext;

    /**
     * Define the logger
     *
     * @param \Symfony\Bridge\Monolog\Logger $logger the logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Define the model transformer factory
     *
     * @param \IC\Bundle\Core\FieldBundle\Service\DataTransformerFactory $dataTransformerFactory the factory
     */
    public function setModelDataTransformerFactory(DataTransformerFactory $dataTransformerFactory)
    {
        $this->modelDataTransformerFactory = $dataTransformerFactory;
    }

    /**
     * Define the view transformer factory
     *
     * @param \IC\Bundle\Core\FieldBundle\Service\DataTransformerFactory $dataTransformerFactory the factory
     */
    public function setViewDataTransformerFactory(DataTransformerFactory $dataTransformerFactory)
    {
        $this->viewDataTransformerFactory = $dataTransformerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'fieldContent';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('field_list'));
        $resolver->setOptional(array('form_context'));

        $resolver->setDefaults(array(
            'form_context' => null,
        ));

        $resolver->setAllowedTypes(array(
            'form_context' => array(
                'null',
                'IC\Bundle\Base\ComponentBundle\Form\Context\TranslationFormContextInterface'
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->translationFormContext = $options['form_context'];

        foreach ($options['field_list'] as $field) {
            $this->addField($builder, $field);
        }

        // Add the model data transformer.
        $builder->addModelTransformer($this->modelDataTransformerFactory->create($options['field_list']));

        // Add the view data transformer.
        // {@internal The condition only exists for code migration.}
        if (isset($this->viewDataTransformerFactory)
            && $this->viewDataTransformerFactory->getTransformerClassName() !== $this->modelDataTransformerFactory->getTransformerClassName()
        ) {
            $builder->addViewTransformer($this->viewDataTransformerFactory->create($options['field_list']));
        }
    }

    /**
     * Add field to form builder
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $builder Form builder
     * @param \IC\Bundle\Core\FieldBundle\Entity\Field     $field   Field
     */
    private function addField(FormBuilderInterface $builder, Field $field)
    {
        $fieldType        = $field->getType();
        $renderingType    = $fieldType;
        $choiceList       = array();
        $extraOptionMap   = array();
        $genericOptionMap = array(
            'label' => $field->getLabel(),
            'attr'  => array(
                'title' => $field->getDescription(),
            ),
        );

        $isRenderingChoiceType = in_array($fieldType, self::$choiceTypeList);

        switch ($renderingType) {
            case Type::TEXT:
            case Type::TEXTAREA:
                $genericOptionMap['attr'] = array(
                    'placeholder' => $field->getDescription(),
                );
                break;
            case Type::CHECKBOX:
                // No need to override type here (reuse Field->getType())
                $extraOptionMap = array(
                    'value' => $field->getId(),
                );
                break;
            case Type::SELECT:
                $extraOptionMap = array(
                    'expanded'    => false,
                    'multiple'    => false,
                    'required'    => false,
                );
                break;
            case Type::RADIO:
                $extraOptionMap = array(
                    'expanded'    => true,
                    'multiple'    => false,
                );
                break;
            case Type::CHECKBOX_MULTIPLE:
                $extraOptionMap = array(
                    'expanded' => true,
                    'multiple' => true,
                );
                break;
            case Type::SELECT_MULTIPLE:
                $extraOptionMap = array(
                    'expanded' => false,
                    'multiple' => true,
                );
                break;
        }

        if ($isRenderingChoiceType) {
            $renderingType = self::$alternativeFormType;
            $choiceList    = $this->convertToChoiceList($field);

            $extraOptionMap['choices'] = $choiceList;
        }

        // Only if it is a <select> field, add the default option.
        if ($fieldType === Type::SELECT) {
            // remove the empty option
            $extraOptionMap['empty_value'] = false;

            $firstChoice  = $field->getChoiceList()->first();
            $optionPrefix = preg_replace('/.[^\.]+$/', '.', $firstChoice->getLabel());

            // Use the plus operator (+) to preserve the order of appearance.
            $extraOptionMap['choices'] = array(
                    ModelDataTransformer::DEFAULT_CHOICE_ID => $optionPrefix . 'default'
                )
                + $extraOptionMap['choices'];
        }

        // Defining possible translation choice
        if ($this->translationFormContext) {
            $pluralization = $this->translationFormContext->getTranslationPluralization();

            $extraOptionMap['label_attr'] = array(
                'transchoice' => $pluralization
            );
        }

        $builder->add(
            $field->getAlias(),
            $renderingType,
            array_merge($genericOptionMap, $extraOptionMap)
        );
    }

    /**
     * Converts a collection of choice list into its array representation.
     *
     * @param \IC\Bundle\Core\FieldBundle\Entity\Field $field
     *
     * @return array
     */
    private function convertToChoiceList(Field $field)
    {
        $convertedChoiceList = array();

        foreach ($field->getChoiceList() as $choice) {
            if ($choice->isDefault() && $field->getType() === Type::SELECT) {
                continue;
            }

            $convertedChoiceList[$choice->getId()] = $choice->getLabel();
        }

        return $convertedChoiceList;
    }
}
