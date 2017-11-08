<?php

namespace Basecom\Bundle\RulesEngineBundle\Form\Type;

use Basecom\Bundle\RulesEngineBundle\DTO\RuleDefinition as RuleDefinitionDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class RuleDefinitionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->addDefinition($builder)
             ->addConditions($builder)
             ->addActions($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDefinition(FormBuilderInterface $builder): self
    {
        $builder
            ->add('code', TextType::class, [
                'required' => true,
            ])
            ->add('priority', IntegerType::class, [
                'required' => true,
            ])
            ->add('type', HiddenType::class, [
                'data' => 'product',
            ]);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addActions(FormBuilderInterface $builder): self
    {
        $builder->add(
            'actions',
            CollectionType::class,
            [
                'entry_type'    => ActionType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'attr'          => [
                    'class' => 'rule-action-container',
                ],
                'entry_options' => [
                    'attr'  => [
                        'class' => 'rule-action',
                    ],
                    'label' => 'Action',
                ],
            ]
        );

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addConditions(FormBuilderInterface $builder): self
    {
        $builder->add(
            'conditions',
            CollectionType::class,
            [
                'entry_type'    => ConditionType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'attr'          => [
                    'class' => 'rule-condition-container',
                ],
                'entry_options' => [
                    'attr'  => [
                        'class' => 'rule-condition',
                    ],
                    'label' => 'Condition',
                ],
            ]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => RuleDefinitionDTO::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'basecom_rule';
    }
}
