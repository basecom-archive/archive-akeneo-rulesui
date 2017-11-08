define(
    ['jquery'],
    function ($) {
        'use strict';
        return function (attributesMap) {
            if ($('.basecom-rule').length) {
                init();
                var select = $('.basecom-rule select');
                select.select2();
                select.trigger('change');
                $('#select2-drop-mask').hide();
            }

            function init () {
                var actionContainers    = $('.AknFieldContainer.rule-action');
                var conditionContainers = $('.AknFieldContainer.rule-condition');

                var actionCounter    = actionContainers.length;
                var conditionCounter = conditionContainers.length;

                function addRemoveEvents () {
                    var actionContainers    = $('.AknFieldContainer.rule-action');
                    var conditionContainers = $('.AknFieldContainer.rule-condition');

                    actionContainers.find('.remove').remove();
                    conditionContainers.find('.remove').remove();

                    actionContainers.append('<div class="remove btn icons-holder-text no-hash btn-delete"><i class="icon-trash"></i>Delete Action</div>');
                    conditionContainers.append('<div class="remove btn icons-holder-text no-hash btn-delete" ><i class="icon-trash"></i>Delete Condition</div>');

                    conditionContainers.each(function (index, conditionContainer) {
                        var removeButton = $(conditionContainer).find('.remove');
                        removeButton.unbind('click');
                        removeButton.on('click', function () {
                            var conditionContainer = removeButton.closest('.AknFieldContainer.rule-condition');
                            conditionContainer.remove();
                        });
                    });

                    actionContainers.each(function (index, actionContainer) {
                        var removeButton = $(actionContainer).find('.remove');
                        removeButton.unbind('click');
                        removeButton.on('click', function () {
                            var actionContainer = removeButton.closest('.AknFieldContainer.rule-action');
                            actionContainer.remove();
                        });
                    });
                }

                conditionContainers.each(function (index, conditionContainer) {
                    addValueEvents(conditionContainer, 'condition');
                });
                actionContainers.each(function (index, actionContainer) {
                    addValueEvents(actionContainer, 'action');
                });

                function addValueEvents (container, type) {

                    var removeButton = $(container).find('.remove');
                    removeButton.unbind('click');
                    removeButton.on('click', function () {
                        var conditionContainer = removeButton.closest('.AknFieldContainer.rule-' + type);
                        conditionContainer.remove();
                    });

                    var valueContainer = $(container).find('.AknFieldContainer.' + type + '-field-values-container');
                    var values         = valueContainer.find('.AknFieldContainer.' + type + '-field-values-value');

                    valueContainer.find('.add-value.btn').remove();
                    valueContainer.append('<div class="add-value btn"><i class="icon-plus"></i>Add Value</div>');

                    values.each(function (index, value) {
                        handleNewValue(value, type);
                    });

                    var addButton  = $(container).find('.btn.add-value');
                    valueContainer = addButton.parent().find('.' + type + '-field-values-container');

                    if (0 >= values.length) {
                        addInitialValue(valueContainer, type);
                    }

                    addButton.unbind('click');
                    addButton.on('click', function (event) {
                        var addButton      = $(event.target);
                        var valueContainer = addButton.parent().find('.' + type + '-field-values-container');
                        var values         = valueContainer.find('.AknFieldContainer.' + type + '-field-values-value');
                        var prototype      = valueContainer.attr('data-prototype');
                        var newValue       = $(prototype.replace(/__name__/g, values.length));
                        valueContainer.append(newValue);
                        handleNewValue(newValue, type);
                    });
                }

                function handleNewValue (valueContainer, type) {
                    var removeButtons = $(valueContainer).find('.btn.remove-value');

                    if (0 < removeButtons.length) {
                        return;
                    }

                    $(valueContainer).append('<div class="remove-value btn"><i class="icon-trash"></i></div>');

                    removeButtons = $(valueContainer).find('.btn.remove-value');
                    removeButtons.unbind('click');
                    removeButtons.on('click', function (event) {
                        var removeButton   = $(event.target);
                        var valueContainer = removeButton.closest('.AknFieldContainer.' + type + '-field-values-value');
                        valueContainer.remove();
                    });
                }

                addRemoveEvents();

                var addConditionButton = $('#create-add-another-condition');
                var addActionButton    = $('#create-add-another-action');

                addConditionButton.unbind('click');
                addConditionButton.click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var conditionList = $('#rule_definition_conditions');

                    var newWidget = conditionList.attr('data-prototype');
                    newWidget     = newWidget.replace(/__name__/g, conditionCounter);
                    newWidget     = newWidget.replace(/label__/g, '');
                    var newLi     = $(newWidget);
                    conditionCounter++;
                    newLi.appendTo(conditionList);

                    addDefinitionItemEventListeners();
                    addRemoveEvents();
                    addValueEvents(newLi, 'condition');
                    $('select').select2();
                });
                addActionButton.unbind('click');
                addActionButton.click(function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var actionList = $('#rule_definition_actions');

                    var newWidget = actionList.attr('data-prototype');
                    newWidget     = newWidget.replace(/__name__/g, actionCounter);
                    newWidget     = newWidget.replace(/label__/g, '');
                    var newLi     = $(newWidget);
                    actionCounter++;
                    newLi.appendTo(actionList);

                    addDefinitionItemEventListeners();
                    addRemoveEvents();
                    $('select').select2();
                });

                var group = $('.group');
                group.unbind('click');
                group.click(function () {
                    $(this).parent().find('tr:not(.group)').toggle();
                    $(this).find('i').toggleClass('icon-expand-alt icon-collapse-alt');
                });

                var editBtn = $('.attribute-requirement:not(.identifier) i');
                editBtn.unbind('click');
                editBtn.on('click', function () {
                    $(this).toggleClass('icon-ok required').toggleClass('icon-circle non-required');

                    var $input  = $(this).siblings('input[type="checkbox"]').eq(0);
                    var checked = $input.is(':checked');
                    $(this).attr('data-original-title', $(this).parent().data((checked ? 'not-' : '') + 'required-title')).tooltip('show');
                    $input.prop('checked', !checked).trigger('change');
                });

                function addInitialValue (valueContainer, type) {
                    var values    = valueContainer.find('.AknFieldContainer.' + type + '-field-values-value');
                    var prototype = valueContainer.attr('data-prototype');
                    var newValue  = $(prototype.replace(/__name__/g, values.length));
                    valueContainer.append(newValue);
                    handleNewValue(newValue, type);
                }

                function addDefinitionItemEventListeners () {
                    function handleTypeSelection (select, ruleType) {
                        select                 = $(select);
                        var selectedType       = select.val();
                        var ruleDefinitionItem = select.closest('.rule-' + ruleType);
                        var isMultiOption;
                        select.children('option').each(function (index, option) {
                            var type = $(option).attr('value');
                            ruleDefinitionItem.removeClass('rule-' + ruleType + '-type-' + type);
                        });

                        ruleDefinitionItem.addClass('rule-' + ruleType + '-type-' + selectedType);
                        var valueContainer = select.closest('.rule-' + ruleType).find('.' + ruleType + '-field-values-container:not(.AknFieldContainer)');

                        if (ruleType === 'condition') {
                            isMultiOption = false;

                            if (selectedType === "in" || selectedType === "not_in" || selectedType === "between" || selectedType === "not_between") {
                                isMultiOption = true;
                            }

                            var values = valueContainer.find('.AknFieldContainer.condition-field-values-value');
                            if (
                                (0 === values.length || (1 < values.length && !isMultiOption)) ||
                                ((selectedType === "between" || selectedType === "not_between") && 2 !== values.length)) {

                                values.remove();
                                addInitialValue(valueContainer, ruleType);
                                if (selectedType === "between" || selectedType === "not_between") {
                                    addInitialValue(valueContainer, ruleType);
                                }
                            }
                        }
                        if (ruleType === 'action') {
                            isMultiOption = false;

                            if (selectedType === "add") {
                                isMultiOption = true;
                            }

                            values = valueContainer.find('.AknFieldContainer.action-field-values-value');

                            if (0 === values.length || (1 < values.length && !isMultiOption)) {
                                values.remove();
                                addInitialValue(valueContainer, ruleType);
                            }
                        }
                    }

                    $('select.condition-type-select').each(function (index, select) {
                        handleTypeSelection(select, 'condition');
                        $(select).unbind('change');
                        $(select).on('change', function () {
                            handleTypeSelection(select, 'condition');
                        });
                    });

                    $('select.action-type-select').each(function (index, select) {
                        handleTypeSelection(select, 'action');
                        $(select).unbind('change');
                        $(select).on('change', function () {
                            handleTypeSelection(select, 'action');
                        });
                    });

                    $('select.condition-field').each(function (index, select) {
                        handleFieldSelection(select, 'condition');
                        $(select).unbind('change');
                        $(select).on('change', function () {
                            handleFieldSelection(select, 'condition');
                        });
                    });
                    $('select.action-field').each(function (index, select) {
                        handleFieldSelection(select, 'action');
                        $(select).unbind('change');
                        $(select).on('change', function () {
                            handleFieldSelection(select, 'action');
                        });
                    });
                    $('select.action-from-field').each(function (index, select) {
                        handleCopyFieldSelection(select);
                        $(select).unbind('change');
                        $(select).on('change', function () {
                            handleCopyFieldSelection(select, 'from');
                        });
                    });
                    $('select.action-to-field').each(function (index, select) {
                        handleCopyFieldSelection(select);
                        $(select).unbind('change');
                        $(select).on('change', function () {
                            handleCopyFieldSelection(select, 'to');
                        });
                    });

                    function handleCopyFieldSelection (select, type) {
                        select            = $(select);
                        var selectedField = select.val();
                        var selectField;

                        if (!attributesMap.hasOwnProperty(selectedField)) {
                            return;
                        }

                        var attributeData      = attributesMap[selectedField];
                        var ruleDefinitionItem = select.closest('.rule-action');

                        if (attributeData.is_localizable) {
                            ruleDefinitionItem.addClass(type + '_is_localizable');
                        } else {
                            ruleDefinitionItem.removeClass(type + '_is_localizable');
                            selectField = $(ruleDefinitionItem).find('.action-' + type + '-field-locale select');
                            $(selectField).select2("val", "");
                        }
                        if (attributeData.is_scopable) {
                            ruleDefinitionItem.addClass(type + '_is_scopable');
                        } else {
                            ruleDefinitionItem.removeClass(type + '_is_scopable');
                            selectField = $(ruleDefinitionItem).find('.action-' + type + '-field-locale select');
                            $(selectField).select2("val", "");
                        }
                    }

                    function handleFieldSelection (select, ruleType) {
                        select            = $(select);
                        var selectedField = select.val();
                        var selectField;

                        var ruleDefinitionItem = select.closest('.rule-' + ruleType);

                        if (selectedField === 'completeness') {
                            ruleDefinitionItem.addClass('is_localizable');
                            ruleDefinitionItem.addClass('is_scopable');
                        } else if (attributesMap.hasOwnProperty(selectedField)) {

                            var attributeData = attributesMap[selectedField];

                            if (attributeData.is_localizable) {
                                ruleDefinitionItem.addClass('is_localizable');
                            } else {
                                ruleDefinitionItem.removeClass('is_localizable');
                                selectField = $(ruleDefinitionItem).find('.' + ruleType + '-field-locale select');
                                $(selectField).select2("val", "");
                            }
                            if (attributeData.is_scopable) {
                                ruleDefinitionItem.addClass('is_scopable');
                            } else {
                                ruleDefinitionItem.removeClass('is_scopable');
                                selectField = $(ruleDefinitionItem).find('.' + ruleType + '-field-scope select');
                                $(selectField).select2("val", "");
                            }
                            if (attributeData.is_metric) {
                                ruleDefinitionItem.addClass('is_metric');
                            } else {
                                ruleDefinitionItem.removeClass('is_metric');
                                selectField = $(ruleDefinitionItem).find('.' + ruleType + '-field-unit input');
                                $(selectField).val('');
                            }
                        } else {
                            ruleDefinitionItem.removeClass('is_localizable');
                            ruleDefinitionItem.removeClass('is_metric');
                            ruleDefinitionItem.removeClass('is_scopable');

                            selectField = $(ruleDefinitionItem).find('.' + ruleType + '-field-locale select');
                            $(selectField).select2("val", "");
                            selectField = $(ruleDefinitionItem).find('.' + ruleType + '-field-unit input');
                            $(selectField).val('');
                            selectField = $(ruleDefinitionItem).find('.' + ruleType + '-field-scope select');
                            $(selectField).select2("val", "");
                        }
                    }
                }

                addDefinitionItemEventListeners();
                addRemoveEvents();
            }

            init();
        };
    }
);
