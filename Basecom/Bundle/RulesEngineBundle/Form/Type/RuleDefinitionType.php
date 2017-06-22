<?php

namespace Basecom\Bundle\RulesEngineBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Basecom\Bundle\RulesEngineBundle\DTO\RuleDefinition as RuleDefinitionDTO;

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
        $this
            ->addDefinition($builder)
            ->addConditions($builder)
            ->addActions($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addDefinition(FormBuilderInterface $builder)
    {
        $builder->add('code', 'text', ['required' => true]);
        $builder->add('priority', 'integer', ['required' => true]);
        $builder->add('type', 'hidden', ['data' => 'product']);

        return $this;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addActions(FormBuilderInterface $builder)
    {
        $builder->add(
            'actions',
            'collection',
            [
                'type'         => 'basecom_rule_action',
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'attr'         => [
                    'class' => 'rule-action-container',
                ],
                'options'      => [
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
    protected function addConditions(FormBuilderInterface $builder)
    {
        $builder->add(
            'conditions',
            'collection',
            [
                'type'         => 'basecom_rule_condition',
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'attr'         => [
                    'class' => 'rule-condition-container',
                ],
                'options'      => [
                    'attr' => [
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
    public function getName()
    {
        return 'basecom_rule';
    }
}
