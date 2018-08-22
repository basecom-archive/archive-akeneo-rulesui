<?php

namespace Basecom\Bundle\RulesEngineBundle\Controller;

use Akeneo\Bundle\RuleEngineBundle\Doctrine\Common\Saver\RuleDefinitionSaver;
use Akeneo\Bundle\RuleEngineBundle\Doctrine\ORM\Repository\RuleDefinitionRepository;
use Akeneo\Bundle\RuleEngineBundle\Model\RuleDefinition;
use Basecom\Bundle\RulesEngineBundle\DTO\RuleDefinition as RuleDefinitionDTO;
use Basecom\Bundle\RulesEngineBundle\Form\Type\RuleDefinitionType;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Component\Catalog\AttributeTypes;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use PimEnterprise\Component\CatalogRule\Engine\ProductRuleBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * RuleController.
 *
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class RuleController extends Controller
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var RuleDefinitionSaver
     */
    protected $ruleDefinitionSaver;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var RuleDefinitionRepository
     */
    protected $ruleDefinitionRepository;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var ProductRuleBuilder
     */
    protected $productRuleBuilder;

    /**
     * Constructor of RuleController.
     *
     * @param RouterInterface              $router
     * @param RuleDefinitionSaver          $ruleDefinitionSaver
     * @param ValidatorInterface           $validator
     * @param DenormalizerInterface        $denormalizer
     * @param FormFactory                  $formFactory
     * @param RuleDefinitionRepository     $ruleDefinitionRepository
     * @param AttributeRepositoryInterface $attributeRepository
     * @param ProductRuleBuilder           $productRuleBuilder
     */
    public function __construct(
        RouterInterface $router,
        RuleDefinitionSaver $ruleDefinitionSaver,
        ValidatorInterface $validator,
        DenormalizerInterface $denormalizer,
        FormFactory $formFactory,
        RuleDefinitionRepository $ruleDefinitionRepository,
        AttributeRepositoryInterface $attributeRepository,
        ProductRuleBuilder $productRuleBuilder
    ) {
        $this->router                   = $router;
        $this->ruleDefinitionSaver      = $ruleDefinitionSaver;
        $this->validator                = $validator;
        $this->denormalizer             = $denormalizer;
        $this->formFactory              = $formFactory;
        $this->ruleDefinitionRepository = $ruleDefinitionRepository;
        $this->attributeRepository      = $attributeRepository;
        $this->productRuleBuilder       = $productRuleBuilder;
    }

    /**
     * Create rule.
     *
     * @AclAncestor("basecom_rule_create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $ruleDefinition = new RuleDefinition();

        return $this->editAction($request, $ruleDefinition);
    }

    /**
     * Edit rule.
     *
     * @AclAncestor("basecom_rule_edit")
     *
     * @param Request        $request
     * @param RuleDefinition $ruleDefinition
     *
     * @return Response
     */
    public function editAction(Request $request, RuleDefinition $ruleDefinition): Response
    {
        $ruleDefinitionData = RuleDefinitionDTO::fromEntity($ruleDefinition);
        $form               = $this->formFactory->create(RuleDefinitionType::class, $ruleDefinitionData);
        $form->handleRequest($request);

        /** @var Attribute[] $attributes */
        $attributes     = $this->attributeRepository->findAll();
        $attributesData = [];
        foreach ($attributes as $attribute) {
            $attributesData[$attribute->getCode()] = [
                'is_localizable' => $attribute->isLocalizable(),
                'is_scopable'    => $attribute->isScopable(),
                'is_metric'      => $attribute->getMetricFamily() ? true : false,
                'is_price'       => AttributeTypes::PRICE_COLLECTION === $attribute->getType() ? true : false,
            ];
        }

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->render('BasecomRulesEngine:Rule:edit.html.twig', [
                'form'           => $form->createView(),
                'attributesData' => $attributesData,
                'ruleDefinition' => $ruleDefinition,
            ]);
        }

        $ruleDefinition = $ruleDefinitionData->getEntity();
        $violations     = $this->validator->validate($ruleDefinition);

        foreach ($violations as $violation) {
            /** @var ConstraintViolation $violation */
            $violationString = sprintf('%s: %s', $violation->getPropertyPath(), $violation->getMessage());
            $form->addError(new FormError($violationString));
        }

        $ruleViolations = [];
        try {
            $rule           = $this->productRuleBuilder->getRuleByRuleDefinition($ruleDefinition);
            $ruleViolations = $this->productRuleBuilder->validateRule($rule);
        } catch (\LogicException $e) {
            $form->addError(new FormError($e->getMessage()));
        }

        foreach ($ruleViolations as $ruleViolation) {
            /* @var ConstraintViolationInterface $ruleViolation */
            $form->addError(new FormError($ruleViolation->getMessage()));
        }

        if (!$form->isValid()) {
            $this->addFlash('error', 'basecom.flash.rule.error.save');

            return $this->render('BasecomRulesEngine:Rule:edit.html.twig', [
                'form'           => $form->createView(),
                'attributesData' => $attributesData,
                'ruleDefinition' => $ruleDefinition,
            ]);
        }

        $this->ruleDefinitionSaver->save($ruleDefinition);
        $ruleDefinition = $this->ruleDefinitionRepository->findOneByIdentifier($ruleDefinition->getCode());
        $this->addFlash('success', 'basecom.flash.rule.saved');

        return new RedirectResponse(
            $this->router->generate('basecom_rule_edit', ['id' => $ruleDefinition->getId()])
        );
    }

    /**
     * Remove rule.
     *
     * @AclAncestor("basecom_rule_remove")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function removeAction(Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            return new Response('', 204);
        }

        return new RedirectResponse($this->router->generate('pimee_catalog_rule_rule_index'));
    }
}
