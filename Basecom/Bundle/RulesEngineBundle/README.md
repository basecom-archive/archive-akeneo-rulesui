# Akeneo rules engine graphical interface
## Requirements

Akeneo PIM Enterprise Edition 3.0.~

## Installation
Enable the bundle in the `app/AppKernel.php` file in the `registerBundles()` method:

```php
    $bundles = [
        // ...
        new \Basecom\Bundle\RulesEngineBundle\BasecomRulesEngine(),
    ]
```
Enable the route in the 'app/config/routing.yml' file 

```php
    basecom_rules_routing:
        resource: "@BasecomRulesEngine/Resources/config/routing/rules.yml"
```

Clear you cache:

```bash
    php bin/console cache:clear --env=prod
    rm -rf var/cache/* ./web/bundles/* ./web/css/* ./web/js/*
    bin/console --env=prod pim:installer:assets
    bin/console --env=prod cache:warmup
```


## Documentation

- OverwriteRuleController.php overwrites the standard Akeneo RuleController to extend the view with a edit button in the rule overview.
  
- Operator Between and not Between is disabled 

https://docs.akeneo.com/master/cookbook/rule/general_information_on_rule_format.html#enrichment-rule-structure

### Available Operators Conditions List
- STARTS WITH
- ENDS WITH
- CONTAINS
- DOES NOT CONTAIN
- EMPTY
- NOT EMPTY
- EQUAL ( = )
- NOT EQUAL ( != )
- IN
- NOT IN
- UNCLASSIFIED
- IN OR UNCLASSIFIED
- IN CHILDREN
- NOT IN CHILDREN
- GREATER ( > )
- GREATER OR EQUAL ( >= )
- SMALLER ( < )
- SMALLER OR EQUAL ( <= )

#### Operator
STARTS WITH

##### Requirements 
- Attribute (no Simple or Multliselect) 
- Locale (optional)
- Scope (optional)
- Value

#### Operator
ENDS WITH

##### Requirements 
- Attribute (no Simple or Multliselect) 
- Locale (optional)
- Scope (optional)
- Value

#### Operator
CONTAINS

##### Requirements 
- Attribute (no Simple or Multliselect) 
- Locale (optional)
- Scope (optional)
- Value

#### Operator
DOES NOT CONTAIN

##### Requirements 
- Attribute (no Simple or Multliselect) 
- Locale (optional)
- Scope (optional)
- Value

#### Operator
EMPTY

##### Requirements 
- Attribute, Family (family.code), Groups (groups.code)
- Locale (optional)
- Scope (optional)

#### Operator
NOT EMPTY

##### Requirements 
- Attribute, Family (family.code), Groups (groups.code)
- Locale (optional)
- Scope (optional)

#### Operator
EQUAL ( = )

##### Requirements 
- Attribute, created, updated, enabled, completeness
- Value (dates format: yyyy-mm-dd) (enabled and yes/no format = true or false)
- Locale (optional)
- Scope (optional)
- Unit (optional, only if a metric Attribute is selected)

#### Operator
NOT EQUAL ( != )

##### Requirements 
- Attribute (Number or Metric), created, updated, enabled, completeness
- Value (created, updated dates format: yyyy-mm-dd)(enabled format = true or false)
- Locale (optional)
- Scope (optional)
- Unit (optional, only if a metric Attribute is selected)

#### Operator
IN

##### Requirements 
- Simple, Multiselect Attribute, Category (categories.code), Family (family.code), Groups (groups.code)
- Locale (optional)
- Scope (optional)
- One or more value


#### Operator
NOT IN

##### Requirements 
- Simple, Multiselect Attribute, Category (categories.code), Family (family.code), Groups (groups.code)
- Locale (optional)
- Scope (optional)
- One or more value

#### Operator
UNCLASSIFIED

##### Requirements 
Only available on Categories
- Field = categories.code
- No Attributes have to be selected

#### Operator
IN OR UNCLASSIFIED

##### Requirements 
Only available on Categories
- Field = categories.code
- Category code 

#### Operator
IN CHILDREN 

##### Requirements 
Only available on Categories
- Field = categories.code
- Category code 

#### Operator
NOT IN CHILDREN

##### Requirements 
Only available on Categories
- Field = categories.code
- Category code

#### Operator
GREATER ( > )

##### Requirements 
- Number, Price, Metric, Date Attribute, completeness
- Value (dates format: yyyy-mm-dd)
- Locale (optional)
- Scope (optional)
- Unit (optional, only if a metric Attribute is selected)

#### Operator
GREATER OR EQUAL ( >= )

##### Requirements 
- Number, Price, Metric, Date Attribute
- Value (dates format: yyyy-mm-dd)
- Locale (optional)
- Scope (optional)
- Unit (optional, only if a metric Attribute is selected)

#### Operator
SMALLER ( < )

##### Requirements 
- Number, Price, Metric, Date Attribute, completeness
- Value (dates format: yyyy-mm-dd)
- Locale (optional)
- Scope (optional)
- Unit (optional, only if a metric Attribute is selected)

#### Operator
SMALLER OR EQUAL ( <= )

##### Requirements 
- Number, Price, Metric, Date Attribute
- Value (dates format: yyyy-mm-dd)
- Locale (optional)
- Scope (optional)
- Unit (optional, only if a metric Attribute is selected)


### Available Operators Actions List
- add
- set
- copy 
- remove 

#### Operator
add

##### Requirements 
- field: attribute code.
- locale: local code for which value is assigned (optional).
- scope: channel code for which value is assigned (optional).
- values: attribute values to add.

#### Operator
set

##### Requirements 
- field: attribute code.
- locale (optional)
- scope (optional)
- value: attribute value.

#### Operator
copy

##### Requirements 
- from_field: code of the attribute to be copied.
- from_locale: locale code of the value to be copied (optional).
- from_scope: channel code of the value to be copied (optional).
- to_field: attribute code the value will be copied into.
- to_locale: locale code the value will be copied into (optional).
- to_scope: channel code the value will be copied into (optional).

#### Operator
remove

##### Requirements 
- field: attribute code.
- locale: local code for which value is assigned (optional).
- scope: channel code for which value is assigned (optional).
- values: attribute values to remove.