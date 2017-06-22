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

use Akeneo\Bundle\BatchBundle\Launcher\JobLauncherInterface;
use Akeneo\Bundle\RuleEngineBundle\Repository\RuleDefinitionRepositoryInterface;
use Akeneo\Component\Console\CommandLauncher;
use Akeneo\Component\StorageUtils\Remover\RemoverInterface;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use PimEnterprise\Bundle\DataGridBundle\Adapter\OroToPimGridFilterAdapter;
use PimEnterprise\Bundle\ImportExportBundle\Entity\Repository\JobInstanceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use PimEnterprise\Bundle\CatalogRuleBundle\Controller\RuleController as BaseRuleController;

/**
 * OverwriteRule controller extends AkeneoRuleController to manipulate the Twig template
 *
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class OverwriteRuleController extends BaseRuleController
{
    /** @var RuleDefinitionRepositoryInterface */
    protected $repository;

    /** @var RemoverInterface */
    protected $remover;

    /** @var TokenStorageInterface */
    protected $tokenStorage;

    /** @var JobLauncherInterface */
    protected $simpleJobLauncher;

    /** @var JobInstanceRepository */
    protected $jobInstanceRepo;

    /** @var OroToPimGridFilterAdapter */
    protected $gridFilterAdapter;

    /** @var CommandLauncher */
    protected $commandLauncher;

    /**
     * @param RuleDefinitionRepositoryInterface $repository
     * @param RemoverInterface                  $remover
     * @param TokenStorageInterface             $tokenStorage
     * @param JobLauncherInterface              $simpleJobLauncher
     * @param JobInstanceRepository             $jobInstanceRepo
     * @param OroToPimGridFilterAdapter         $gridFilterAdapter
     * @param CommandLauncher                   $commandLauncher
     */
    public function __construct(
        RuleDefinitionRepositoryInterface $repository,
        RemoverInterface $remover,
        TokenStorageInterface $tokenStorage,
        JobLauncherInterface $simpleJobLauncher,
        JobInstanceRepository $jobInstanceRepo,
        OroToPimGridFilterAdapter $gridFilterAdapter,
        CommandLauncher $commandLauncher
    )
    {
        parent::__construct($repository, $remover, $tokenStorage, $simpleJobLauncher, $jobInstanceRepo, $gridFilterAdapter, $commandLauncher);
    }

    /**
     * List all rules
     *
     * @Template
     *
     * @AclAncestor("pimee_catalog_rule_rule_view_permissions")
     *
     * @return array
     */
    public function indexAction()
    {
        return [];
    }
}
