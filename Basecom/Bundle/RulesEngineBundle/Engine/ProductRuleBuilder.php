<?php

namespace Basecom\Bundle\RulesEngineBundle\Engine;

use Akeneo\Bundle\RuleEngineBundle\Event\RuleEvent;
use Akeneo\Bundle\RuleEngineBundle\Event\RuleEvents;
use Akeneo\Bundle\RuleEngineBundle\Exception\BuilderException;
use Akeneo\Bundle\RuleEngineBundle\Model\RuleDefinitionInterface;
use Akeneo\Bundle\RuleEngineBundle\Model\RuleInterface;
use PimEnterprise\Component\CatalogRule\Engine\ProductRuleBuilder as BaseProductRuleBuilder;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @author Justus Klein <klein@basecom.de>
 * @author Jordan Kniest <j.kniest@basecom.de>
 */
class ProductRuleBuilder extends BaseProductRuleBuilder
{
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
        } catch (\LogicException $e) {
            throw new BuilderException(
                sprintf('Impossible to build the rule "%s". %s', $definition->getCode(), $e->getMessage())
            );
        }

        $rule->setConditions($content['conditions']);
        $rule->setActions($content['actions']);

        return $rule;
    }
}
