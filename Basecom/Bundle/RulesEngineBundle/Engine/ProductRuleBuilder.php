<?php

namespace Basecom\Bundle\RulesEngineBundle\Engine;

use Akeneo\Tool\Bundle\RuleEngineBundle\Exception\BuilderException;
use Akeneo\Tool\Bundle\RuleEngineBundle\Model\RuleInterface;
use Akeneo\Tool\Bundle\RuleEngineBundle\Model\RuleDefinitionInterface;
use Akeneo\Pim\Automation\RuleEngine\Component\Engine\ProductRuleBuilder as BaseProductRuleBuilder;
use LogicException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Justus Klein <klein@basecom.de>
 * @author Jordan Kniest <j.kniest@basecom.de>
 * @author Christopher Steinke <c.steinke@basecom.de>
 */
class ProductRuleBuilder extends BaseProductRuleBuilder
{
    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * ProductRuleBuilder constructor.
     * @param DenormalizerInterface $chainedDenormalizer
     * @param EventDispatcherInterface $eventDispatcher
     * @param $ruleClass
     * @param ValidatorInterface $validator
     */
    public function __construct(
        DenormalizerInterface $chainedDenormalizer,
        EventDispatcherInterface $eventDispatcher,
        $ruleClass,
        ValidatorInterface $validator
    ) {
        parent::__construct($chainedDenormalizer, $eventDispatcher, $ruleClass);
        $this->validator = $validator;
    }

    /**
     * @param RuleInterface $rule
     *
     * @return ConstraintViolationListInterface
     */
    public function validateRule(RuleInterface $rule): ConstraintViolationListInterface
    {
        return $this->validator->validate($rule);
    }

    /**
     * @param RuleDefinitionInterface $definition
     *
     * @return RuleInterface
     */
    public function getRuleByRuleDefinition(RuleDefinitionInterface $definition): RuleInterface
    {
        /** @var RuleInterface $rule */
        $rule = new $this->ruleClass($definition);

        try {
            $content = $this->chainedDenormalizer->denormalize(
                $definition->getContent(),
                $this->ruleClass,
                'rule_content'
            );
        } catch (LogicException $e) {
            throw new BuilderException(
                sprintf('Impossible to build the rule "%s". %s', $definition->getCode(), $e->getMessage())
            );
        }

        $rule->setConditions($content['conditions']);
        $rule->setActions($content['actions']);

        return $rule;
    }
}
