<?php

namespace Basecom\Bundle\RulesEngineBundle\DTO;

use Akeneo\Bundle\RuleEngineBundle\Model\ActionInterface;
use Akeneo\Bundle\RuleEngineBundle\Model\RuleDefinition as BaseRuleDefinition;
use PimEnterprise\Component\CatalogRule\Model\ProductCopyAction;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class Action
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $value;

    /**
     * @var []
     */
    public $items;

    /**
     * @var string
     */
    public $locale;

    /**
     * @var string
     */
    public $scope;

    /**
     * @var string
     */
    public $fromField;

    /**
     * @var string
     */
    public $fromLocale;

    /**
     * @var string
     */
    public $fromScope;

    /**
     * @var string
     */
    public $toField;

    /**
     * @var string
     */
    public $toLocale;

    /**
     * @var string
     */
    public $unit;

    /**
     * @var string
     */
    public $toScope;

    /**
     * @return array
     */
    public function getData()
    {
        $data = ['type' => $this->type];

        switch ($this->type) {

            case 'copy':
                $data['from_field'] = $this->fromField;
                $data['to_field']   = $this->toField;
                if ('' != $this->fromLocale) {
                    $data['from_locale'] = $this->fromLocale;
                }
                if ('' != $this->toLocale) {
                    $data['to_locale'] = $this->toLocale;
                }
                if ('' != $this->fromScope) {
                    $data['from_scope'] = $this->fromScope;
                }
                if ('' != $this->toScope) {
                    $data['to_scope'] = $this->toScope;
                }
                break;
            case 'add':
                $data['field'] = $this->field;
                $data['items'] = $this->items;
                if ('' != $this->locale) {
                    $data['locale'] = $this->locale;
                }
                if ('' != $this->scope) {
                    $data['scope'] = $this->scope;
                }
                break;
            case 'set':
                $data['field'] = $this->field;
                $data['value'] = $this->value;
                if ('' != $this->locale) {
                    $data['locale'] = $this->locale;
                }
                if ('' != $this->scope) {
                    $data['scope'] = $this->scope;
                }
                break;
            case 'remove':
                $data['field'] = $this->field;
                $data['items'] = $this->items;
                if ('' != $this->locale) {
                    $data['locale'] = $this->locale;
                }
                if ('' != $this->scope) {
                    $data['scope'] = $this->scope;
                }
                break;
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @return Action
     */
    public static function fromData($data)
    {
        $action       = new self();
        $action->type = $data['type'];
        if (array_key_exists('field', $data)) {
            $action->field = $data['field'];
        }
        if (array_key_exists('value', $data)) {
            $action->value = $data['value'];
        }
        if (array_key_exists('items', $data)) {
            $action->items = $data['items'];
        }
        if (array_key_exists('locale', $data)) {
            $action->locale = $data['locale'];
        }
        if (array_key_exists('scope', $data)) {
            $action->scope = $data['scope'];
        }
        if (array_key_exists('from_field', $data)) {
            $action->fromField = $data['from_field'];
        }
        if (array_key_exists('from_locale', $data)) {
            $action->fromLocale = $data['from_locale'];
        }
        if (array_key_exists('from_scope', $data)) {
            $action->fromScope = $data['from_scope'];
        }
        if (array_key_exists('to_field', $data)) {
            $action->toField = $data['to_field'];
        }
        if (array_key_exists('to_locale', $data)) {
            $action->toLocale = $data['to_locale'];
        }
        if (array_key_exists('to_scope', $data)) {
            $action->toScope = $data['to_scope'];
        }

        return $action;
    }
}
