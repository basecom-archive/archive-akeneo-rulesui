<?php

namespace Basecom\Bundle\RulesEngineBundle\Form\Type;

use Akeneo\Tool\Bundle\MeasureBundle\Manager\MeasureManager;
use Basecom\Bundle\RulesEngineBundle\DTO\Condition;
use Basecom\Bundle\RulesEngineBundle\Form\DataTransformer\AttributeCodeToAttributeTransformer;
use Akeneo\Pim\Structure\Component\Model\Attribute;
use Akeneo\Pim\Structure\Component\Repository\AttributeRepositoryInterface;
use Akeneo\Channel\Component\Repository\ChannelRepositoryInterface;
use Akeneo\Channel\Component\Repository\CurrencyRepositoryInterface;
use Akeneo\Channel\Component\Repository\LocaleRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class ConditionType extends AbstractType
{
    /**
     * @var string
     */
    protected $dataClass;

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
     * @var MeasureManager
     */
    protected $measureManager;

    /**
     * @var CurrencyRepositoryInterface
     */
    private $currencyRepository;

    /**
     * Constructor of ConditionType.
     *
     * @param string                       $dataClass
     * @param LocaleRepositoryInterface    $localeRepository
     * @param ChannelRepositoryInterface   $channelRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param MeasureManager               $measureManager
     * @param CurrencyRepositoryInterface  $currencyRepository
     */
    public function __construct(
        string $dataClass,
        LocaleRepositoryInterface $localeRepository,
        ChannelRepositoryInterface $channelRepository,
        AttributeRepositoryInterface $attributeRepository,
        MeasureManager $measureManager,
        CurrencyRepositoryInterface $currencyRepository
    ) {
        $this->dataClass           = $dataClass;
        $this->localeRepository    = $localeRepository;
        $this->channelRepository   = $channelRepository;
        $this->attributeRepository = $attributeRepository;
        $this->measureManager      = $measureManager;
        $this->currencyRepository  = $currencyRepository;
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
        $this->addFieldCurrency($builder);
    }

    /**
     * Add field field to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldField(FormBuilderInterface $builder)
    {
        $builder->add('field', ChoiceType::class, [
            'choices'  => $this->getFieldCodes(),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'condition-field',
            ],
        ]);

        $transformer = new AttributeCodeToAttributeTransformer($this->attributeRepository);
        $builder->get('field')->addModelTransformer($transformer);
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
                'class' => 'condition-field-locale',
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
                'class' => 'condition-field-scope',
            ],
        ]);
    }

    /**
     * Add field operator to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldOperator(FormBuilderInterface $builder)
    {
        $builder->add('operator', ChoiceType::class, [
            'choices'      => array_keys(Condition::OPERATORS),
            'choice_label' => function (string $value, string $key, string $index) {
                return sprintf('pimee_catalog_rule.condition.operators.%s', Condition::OPERATORS[$value]);
            },
            'required'     => true,
            'multiple'     => false,
            'select2'      => true,
            'attr'         => [
                'class' => 'condition-type-select',
            ],
        ]);
    }

    /**
     * Add field value to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldValue(FormBuilderInterface $builder)
    {
        $builder->add(
            'values',
            CollectionType::class,
            [
                'entry_type'    => TextType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'by_reference'  => false,
                'required'      => false,
                'label'         => 'Value',
                'attr'          => [
                    'class' => 'condition-field-values-container',
                ],
                'entry_options' => [
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
     * Add field unit to form builder.
     *
     * @param FormBuilderInterface $builder
     */
    protected function addFieldUnit(FormBuilderInterface $builder)
    {
        $attributes     = $this->attributeRepository->findAll();
        $metricFamilies = [];

        foreach ($attributes as $attribute) {
            /** @var Attribute $attribute */
            $metricFamily = $attribute->getMetricFamily();

            if (null === $metricFamily || 0 >= strlen($metricFamily)) {
                continue;
            }

            if (!in_array($metricFamily, $metricFamilies, true)) {
                $metricFamilies[] = $metricFamily;
            }
        }

        $units = [];
        foreach ($metricFamilies as $metricFamily) {
            $familyUnits = $this->measureManager->getUnitSymbolsForFamily($metricFamily);

            foreach ($familyUnits as $key => $unitSymbol) {
                $units[$key] = $key;
            }
        }

        $builder->add('unit', ChoiceType::class, [
            'choices'  => $units,
            'required' => false,
            'attr'     => [
                'class' => 'condition-field-unit',
            ],
        ]);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function addFieldCurrency(FormBuilderInterface $builder)
    {
        $builder->add('currency', ChoiceType::class, [
            'choices'  => $this->getValuesAsArray($this->currencyRepository->getActivatedCurrencyCodes()),
            'required' => false,
            'multiple' => false,
            'select2'  => true,
            'attr'     => [
                'class' => 'condition-field-currency',
            ],
        ]);
    }

    /**
     * Get all possible entries for field value.
     *
     * @return array
     */
    protected function getFieldCodes(): array
    {
        $attributes = $this->attributeRepository->findAll();
        $fieldCodes = [];

        /** @var Attribute $attribute */
        foreach ($attributes as $attribute) {
            $fieldCodes[$attribute->getCode()] = $attribute->getCode();
        }

        return array_merge($fieldCodes, self::getAdditionalFieldCodes());
    }

    /**
     * @return array
     */
    public static function getAdditionalFieldCodes(): array
    {
        return [
            'enabled'      => 'enabled',
            'completeness' => 'completeness',
            'updated'      => 'updated',
            'created'      => 'created',
            'groups'       => 'groups',
            'family'       => 'family',
            'categories'   => 'categories',
        ];
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
    protected function getValuesAsArray(array $array): array
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
    public function getName(): string
    {
        return 'basecom_rule_condition';
    }
}
