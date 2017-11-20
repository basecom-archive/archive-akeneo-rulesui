<?php

namespace Basecom\Bundle\RulesEngineBundle\DTO;

use Akeneo\Bundle\RuleEngineBundle\Model\RuleDefinition as BaseRuleDefinition;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class RuleDefinition
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var string
     */
    public $type = 'product';

    /**
     * @var Action[]
     */
    public $actions = [];

    /**
     * @var Condition[]
     */
    public $conditions = [];

    /**
     * @var BaseRuleDefinition
     */
    private $entity;

    /**
     * @param bool $createNew
     *
     * @return BaseRuleDefinition
     */
    public function getEntity(bool $createNew = false): BaseRuleDefinition
    {
        if ($createNew || null === $this->entity) {
            return new BaseRuleDefinition();
        }

        $this->entity->setCode($this->code);
        $this->entity->setPriority($this->priority);
        $this->entity->setType($this->type);

        $conditionsData = [];
        foreach ($this->conditions as $condition) {
            $conditionsData[] = $condition->getData();
        }

        $actionsData = [];
        foreach ($this->actions as $action) {
            $actionsData[] = $action->getData();
        }

        $content = [
            'conditions' => $conditionsData,
            'actions'    => $actionsData,
        ];

        $this->entity->setContent($content);

        return $this->entity;
    }

    /**
     * @param BaseRuleDefinition $entity
     *
     * @return RuleDefinition
     */
    public static function fromEntity(BaseRuleDefinition $entity): self
    {
        $definition           = new self();
        $definition->entity   = $entity;
        $definition->code     = $entity->getCode();
        $definition->priority = $entity->getPriority();
        $definition->type     = $entity->getType();
        $content              = $entity->getContent();

        if (is_array($content)) {
            if (array_key_exists('actions', $content) && is_array($content['actions'])) {
                foreach ($content['actions'] as $actionData) {
                    $definition->actions[] = Action::fromData($actionData);
                }
            }

            if (array_key_exists('conditions', $content) && is_array($content['conditions'])) {
                foreach ($content['conditions'] as $conditionData) {
                    $definition->conditions[] = Condition::fromData($conditionData);
                }
            }
        }
        if (0 === count($definition->conditions)) {
            $definition->conditions[] = new Condition();
        }
        if (0 === count($definition->actions)) {
            $definition->actions[] = new Action();
        }

        return $definition;
    }
}
