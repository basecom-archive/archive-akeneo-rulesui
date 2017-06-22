<?php

namespace Basecom\Bundle\RulesEngineBundle\Form\Type;

use Basecom\Bundle\RulesEngineBundle\DTO\Condition;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Repository\ChannelRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class ConditionType extends AbstractType
{
    /** @var string */
    protected $dataClass;

    /** @var LocaleRepositoryInterface */
    protected $localeRepository;

    /** @var ChannelRepositoryInterface */
    protected $channelRepository;

    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var array */
    protected $operators;

    /**
     * @param string $dataClass
     * @param        $localeRepository
     * @param        $channelRepository
     * @param        $attributeRepository
     * @param array  $operators
     */
    public function __construct($dataClass, $localeRepository, $channelRepository, $attributeRepository, array $operators)
    {
        $this->dataClass           = $dataClass;
        $this->localeRepository    = $localeRepository;
        $this->channelRepository   = $channelRepository;
        $this->attributeRepository = $attributeRepository;
        $this->operators           = $operators;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addFieldOperator($builder);
        $this->addFieldField($builder);
        $this->addFieldLocale($builder);
        $this->addFieldScope($builder);
        $this->addFieldValue($builder);
        $this->addFieldUnit($builder);
    }


    /**
     * Add field field to form builder
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldField(FormBuilderInterface $builder)
    {
        $builder->add('field', 'choice', [
            'choices'  => $this->getFieldCodes(),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'condition-field',
            ],
        ]);

    }

    /**
     * Add field locale to form builder
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldLocale(FormBuilderInterface $builder)
    {
        $builder->add('locale', 'choice', [
            'choices'  => $this->getValuesAsArray($this->localeRepository->getActivatedLocaleCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'condition-field-locale',
            ],
        ]);
    }

    /**
     * Add field scope to form builder
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldScope(FormBuilderInterface $builder)
    {
        $builder->add('scope', 'choice', [
            'choices'  => $this->getValuesAsArray($this->channelRepository->getChannelCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'condition-field-scope',
            ],
        ]);
    }

    /**
     * Add field operator to form builder
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldOperator(FormBuilderInterface $builder)
    {
        $builder->add('operator', 'choice', [
            'choices'  => $this->operators,
            'required' => true,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'condition-type-select',
            ],
        ]);
    }

    /**
     * Add field value to form builder
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldValue(FormBuilderInterface $builder)
    {
        $builder->add('values', 'collection', [
                'type'         => 'text',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required'     => false,
                'label'        => 'Value',
                'attr'         => [
                    'class' => 'condition-field-values-container',
                ],
                'options'      => [
                    'attr'     => [
                        'class' => 'condition-field-values-value',
                    ],
                    'required' => false,
                    'label'    => false,
                ],
            ]
        );
    }

    /**
     * Add field unit to form builder
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldUnit(FormBuilderInterface $builder)
    {
        $builder->add('unit', 'text', [
            'required' => false,
            'attr'     => [
                'class' => 'condition-field-unit',
            ],
        ]);

    }

    /**
     * Get all possible entries for field value
     * @return array
     */
    protected function getFieldCodes()
    {
        $attributes = $this->attributeRepository->findAll();
        $newArray   = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            if ($attribute->getAttributeType() === 'pim_catalog_simpleselect' || $attribute->getAttributeType() === 'pim_catalog_multiselect') {
                $newArray[$attribute->getCode().'.code'] = $attribute->getCode().'.code';
            } else {
                $newArray[$attribute->getCode()] = $attribute->getCode();
            }
        }

        $newArray['enabled']         = 'enabled';
        $newArray['completeness']    = 'completeness';
        $newArray['updated']         = 'updated';
        $newArray['created']         = 'created';
        $newArray['groups.code']     = 'groups.code';
        $newArray['family.code']     = 'family.code';
        $newArray['categories.code'] = 'categories.code';


        return $newArray;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Condition::class,
            ]
        );
    }

    /**
     * @param $array
     *
     * @return array
     */
    protected function getValuesAsArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value] = $value;
        }

        return $newArray;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'basecom_rule_condition';
    }
}
