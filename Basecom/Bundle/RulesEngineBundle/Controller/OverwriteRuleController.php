<?php

/*
 * This file is part of the Akeneo PIM Enterprise Edition.
 *
 * (c) 2014 Akeneo SAS (http://www.akeneo.com)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Basecom\Bundle\RulesEngineBundle\Controller;

use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use PimEnterprise\Bundle\CatalogRuleBundle\Controller\RuleController as BaseRuleController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * OverwriteRule controller extends AkeneoRuleController to manipulate the Twig template.
 *
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class OverwriteRuleController extends BaseRuleController
{
    /**
     * List all rules.
     *
     * @Template
     *
     * @AclAncestor("pimee_catalog_rule_rule_view_permissions")
     *
     * @return array
     */
    public function indexAction(): array
    {
        return [];
    }
}
