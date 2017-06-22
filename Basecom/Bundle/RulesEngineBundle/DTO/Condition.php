<?php

namespace Basecom\Bundle\RulesEngineBundle\DTO;

use Akeneo\Bundle\RuleEngineBundle\Model\RuleDefinition as BaseRuleDefinition;
use PimEnterprise\Component\CatalogRule\Model\ProductCondition;

/**
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class Condition
{
    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $operator;

    /**
     * @var string[]
     */
    public $values;

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
    public $unit;

    /**
     * @return array
     */
    public function getData()
    {
        $data['field']    = $this->field;
        $data['operator'] = $this->operator;
        switch ($this->operator) {
            case 'starts_with':
                $data['operator'] = 'STARTS WITH';
                break;
            case 'ends_with':
                $data['operator'] = 'ENDS WITH';
                break;
            case 'contains':
                $data['operator'] = 'CONTAINS';
                break;
            case 'does_not_contain':
                $data['operator'] = 'DOES NOT CONTAIN';
                break;
            case 'equal':
                $data['operator'] = '=';
                break;
            case 'empty':
                $data['operator'] = 'EMPTY';
                break;
            case 'not_empty':
                $data['operator'] = 'NOT EMPTY';
                break;
            case 'not_equal':
                $data['operator'] = '!=';
                break;
            case 'between':
                $data['operator'] = 'BETWEEN';
                break;
            case 'not_between':
                $data['operator'] = 'NOT BETWEEN';
                break;
            case 'in':
                $data['operator'] = 'IN';
                break;
            case 'not_in':
                $data['operator'] = 'NOT IN';
                break;
            case 'unclassified':
                $data['operator'] = 'UNCLASSIFIED';
                break;
            case 'in_or_unclassified':
                $data['operator'] = 'IN OR UNCLASSIFIED';
                break;
            case 'in_children':
                $data['operator'] = 'IN CHILDREN';
                break;
            case 'not_in_children':
                $data['operator'] = 'NOT IN CHILDREN';
                break;
            case 'greater':
                $data['operator'] = '>';
                break;
            case 'greater_or_equal':
                $data['operator'] = '>=';
                break;
            case 'smaller':
                $data['operator'] = '<';
                break;
            case 'smaller_or_equal':
                $data['operator'] = '<=';
                break;
            default:
                $data['operator'] = $this->operator;
        }
        /**
         * if the operator is IN or NOT IN there is an option that it can be multiple values
         * if the field is a metric field there has to be an extra field for unit
         */
        if ($data['operator'] === 'IN' || $data['operator'] === 'NOT IN' || $data['operator'] === 'NOT BETWEEN' || $data['operator'] === 'BETWEEN') {
            $data['value'] = $this->values;
        } else if ('' != $this->unit && null != $this->unit) {
            $data['value']['data'] = $this->values[0];
            $data['value']['unit']  = $this->unit;

        } else {
            $data['value'] = array_shift($this->values);
        }
        if ('' != $this->locale) {
            $data['locale'] = $this->locale;
        }
        if ('' != $this->scope) {
            $data['scope'] = $this->scope;
        }

        return $data;
    }

    /**
     * @param $data
     *
     * @return Condition
     */
    public static function fromData($data)
    {
        $condition        = new self();
        $condition->field = $data['field'];
        switch ($data['operator']) {
            case 'STARTS WITH':
                $condition->operator = 'starts_with';
                break;
            case 'ENDS WITH':
                $condition->operator = 'ends_with';
                break;
            case 'CONTAINS':
                $condition->operator = 'contains';
                break;
            case 'DOES NOT CONTAIN':
                $condition->operator = 'does_not_contain';
                break;
            case '=':
                $condition->operator = 'equal';
                break;
            case 'EMPTY':
                $condition->operator = 'empty';
                break;
            case 'NOT EMPTY':
                $condition->operator = 'not_empty';
                break;
            case '!=':
                $condition->operator = 'not_equal';
                break;
            case 'IN':
                $condition->operator = 'in';
                break;
            case 'NOT IN':
                $condition->operator = 'not_in';
                break;
            case 'NOT BETWEEN':
                $condition->operator = 'not_between';
                break;
            case 'BETWEEN':
                $condition->operator = 'between';
                break;
            case 'UNCLASSIFIED':
                $condition->operator = 'unclassified';
                break;
            case 'IN OR UNCLASSIFIED':
                $condition->operator = 'in_or_unclassified';
                break;
            case 'IN CHILDREN':
                $condition->operator = 'in_children';
                break;
            case 'NOT IN CHILDREN':
                $condition->operator = 'not_in_children';
                break;
            case '>':
                $condition->operator = 'greater';
                break;
            case '>=':
                $condition->operator = 'greater_or_equal';
                break;
            case '<':
                $condition->operator = 'smaller';
                break;
            case '<=':
                $condition->operator = 'smaller_or_equal';
                break;
            default:
                $condition->operator = $data['operator'];
        }
        if (array_key_exists('value', $data)) {
            if (is_array($data['value'])) {
                if (key_exists('data', $data['value'])) {
                    $condition->values[] = $data['value']['data'];
                    $condition->unit   = $data['value']['unit'];
                } else {
                    $condition->values = $data['value'];
                }
            } else {
                $condition->values[] = $data['value'];
            }
        }

        if (array_key_exists('locale', $data)) {
            $condition->locale = $data['locale'];
        }
        if (array_key_exists('scope', $data)) {
            $condition->scope = $data['scope'];
        }

        return $condition;
    }
}
