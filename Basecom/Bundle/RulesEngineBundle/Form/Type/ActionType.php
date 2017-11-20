<?php

namespace Basecom\Bundle\RulesEngineBundle\Form\Type;

use Basecom\Bundle\RulesEngineBundle\DTO\Action;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Pim\Component\Catalog\Repository\ChannelRepositoryInterface;
use Pim\Component\Catalog\Repository\LocaleRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class ActionType extends AbstractType
{
    /**
     * @var LocaleRepositoryInterface
     */
    protected $localeRepository;

    /**
     * @var ChannelRepositoryInterface
     */
    protected $channelRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $operators;

    /**
     * Constructor of ActionType.
     *
     * @param LocaleRepositoryInterface    $localeRepository
     * @param ChannelRepositoryInterface   $channelRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param array                        $operators
     */
    public function __construct(
        LocaleRepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository,
        AttributeRepositoryInterface $attributeRepository,
        array $operators
    ) {
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
        $this->addFieldType($builder);
        $this->addField($builder);
        $this->addFromFieldField($builder);
        $this->addFromFieldLocale($builder);
        $this->addFromFieldScope($builder);
        $this->addToFieldField($builder);
        $this->addToFieldLocale($builder);
        $this->addToFieldScope($builder);
        $this->addFieldLocale($builder);
        $this->addFieldScope($builder);
        $this->addFieldValue($builder);
        $this->addFieldUnit($builder);
        $this->addFieldItems($builder);
    }

    /**
     * Add field id to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addField(FormBuilderInterface $builder)
    {
        $builder->add('field', ChoiceType::class, [
            'choices'  => $this->getAttributesAsArray($this->attributeRepository->findAll()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-field',
            ],
        ]);
    }

    /**
     * Add field id to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldValue(FormBuilderInterface $builder)
    {
        $builder->add('value', TextType::class, [
            'required' => false,
            'attr'     => [
                'class' => 'action-value',
            ],
        ]);
    }

    /**
     * Add field id to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFromFieldField(FormBuilderInterface $builder)
    {
        $builder->add('fromField', ChoiceType::class, [
            'choices'  => $this->getAttributesAsArray($this->attributeRepository->findAll()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-from-field',
            ],
        ]);
    }

    /**
     * Add field locale to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFromFieldLocale(FormBuilderInterface $builder)
    {
        $builder->add('fromLocale', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->localeRepository->getActivatedLocaleCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-from-field-locale',
            ],
        ]);
    }

    /**
     * Add field locale to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFromFieldScope(FormBuilderInterface $builder)
    {
        $builder->add('fromScope', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->channelRepository->getChannelCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-from-field-scope',
            ],
        ]);
    }

    /**
     * Add field id to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addToFieldField(FormBuilderInterface $builder)
    {
        $builder->add('toField', ChoiceType::class, [
            'choices'  => $this->getAttributesAsArray($this->attributeRepository->findAll()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-to-field',
            ],
        ]);
    }

    /**
     * Add field locale to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addToFieldLocale(FormBuilderInterface $builder)
    {
        $builder->add('toLocale', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->localeRepository->getActivatedLocaleCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-to-field-locale',
            ],
        ]);
    }

    /**
     * Add field toScope to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addToFieldScope(FormBuilderInterface $builder)
    {
        $builder->add('toScope', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->channelRepository->getChannelCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-to-field-scope',
            ],
        ]);
    }

    /**
     * Add field locale to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldLocale(FormBuilderInterface $builder)
    {
        $builder->add('locale', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->localeRepository->getActivatedLocaleCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-field-locale',
            ],
        ]);
    }

    /**
     * Add field scope to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldScope(FormBuilderInterface $builder)
    {
        $builder->add('scope', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->channelRepository->getChannelCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-field-scope',
            ],
        ]);
    }

    /**
     * Add field type to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldType(FormBuilderInterface $builder)
    {
        $builder->add('type', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->operators),
            'required' => true,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'action-type-select',
            ],
        ]);
    }

    /**
     * Add field data to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldData(FormBuilderInterface $builder)
    {
        $builder->add('valueData', TextType::class, [
            'label'    => 'Value',
            'required' => false,
            'attr'     => [
                'class' => 'action-field-data',
            ],
        ]);
    }

    /**
     * Add field unit to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldUnit(FormBuilderInterface $builder)
    {
        $builder->add('unit', TextType::class, [
            'required' => false,
            'attr'     => [
                'class' => 'action-field-unit',
            ],
        ]);
    }

    /**
     * Add field value to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldItems(FormBuilderInterface $builder)
    {
        $builder->add(
            'items',
            CollectionType::class,
            [
                'entry_type'    => TextType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'required'      => false,
                'label'         => 'Value',
                'attr'          => [
                    'class' => 'action-field-values-container',
                ],
                'entry_options' => [
                    'attr'     => [
                        'class' => 'action-field-values-value',
                    ],
                    'required' => false,
                    'label'    => false,
                ],
            ]
        );
    }

    /**
     * @param $array
     *
     * @return array
     */
    protected function getValuesAsArray($array): array
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newArray[$value] = $value;
        }

        return $newArray;
    }

    /**
     * @param Attribute[] $attributes
     *
     * @return array
     */
    protected function getAttributesAsArray($attributes): array
    {
        $newArray = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $newArray[$attribute->getCode()] = $attribute->getCode();
        }
        $newArray['group']    = 'group';
        $newArray['category'] = 'category';
        $newArray['family']   = 'family';

        return $newArray;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Action::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'basecom_rule_action';
    }
}
