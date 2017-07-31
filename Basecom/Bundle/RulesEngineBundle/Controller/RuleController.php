<?php
namespace Basecom\Bundle\RulesEngineBundle\Controller;

use Akeneo\Bundle\RuleEngineBundle\Doctrine\ORM\Repository\RuleDefinitionRepository;
use Basecom\Bundle\RulesEngineBundle\Form\Type\RuleDefinitionType;
use Pim\Bundle\CatalogBundle\Entity\Attribute;
use Pim\Bundle\EnrichBundle\Flash\Message;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use PimEnterprise\Component\CatalogRule\Engine\ProductRuleBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Akeneo\Bundle\RuleEngineBundle\Doctrine\Common\Saver\RuleDefinitionSaver;
use Akeneo\Bundle\RuleEngineBundle\Model\RuleDefinition;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Basecom\Bundle\RulesEngineBundle\DTO\RuleDefinition as RuleDefinitionDTO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

/**
 * Rulecontroller
 *
 * @author Peter van der Zwaag <vanderzwaag@basecom.de>
 */
class RuleController extends Controller
{
    /** @var Request */
    protected $request;

    /** @var RouterInterface */
    protected $router;

    /** @var Form */
    protected $ruleForm;
    /**
     * @var RuleDefinitionSaver
     */
    private $ruleDefinitionSaver;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var DenormalizerInterface
     */
    protected $denormalizer;
    /**
     * @var string rule class
     */
    protected $ruleClass;
    /**
     * @var string
     */
    protected $class;
    /**
     * @var FormFactory formFactory
     */
    protected $formFactory;
    /**
     * @var RuleDefinitionRepository ruleDefinitionRepository
     */
    protected $ruleDefinitionRepository;
    /**
     * @var AttributeRepositoryInterface attributeRepository
     */
    protected $attributeRepository;
    /**
     * @var ProductRuleBuilder $productRuleBuilder
     */
    protected $productRuleBuilder;

    public function __construct(
        Request $request,
        RouterInterface $router,
        Form $ruleForm,
        RuleDefinitionSaver $ruleDefinitionSaver,
        ValidatorInterface $validator,
        DenormalizerInterface $denormalizer,
        $ruleClass,
        $class,
        $formFactory,
        RuleDefinitionRepository $ruleDefinitionRepository,
        AttributeRepositoryInterface $attributeRepository,
        ProductRuleBuilder $productRuleBuilder
    )
    {
        $this->request                  = $request;
        $this->router                   = $router;
        $this->ruleForm                 = $ruleForm;
        $this->ruleDefinitionSaver      = $ruleDefinitionSaver;
        $this->validator                = $validator;
        $this->denormalizer             = $denormalizer;
        $this->ruleClass                = $ruleClass;
        $this->class                    = $class;
        $this->formFactory              = $formFactory;
        $this->ruleDefinitionRepository = $ruleDefinitionRepository;
        $this->attributeRepository      = $attributeRepository;
        $this->productRuleBuilder       = $productRuleBuilder;

    }

    /**
     * Create rule
     *
     * @Template("BasecomRulesEngine:Rule:edit.html.twig")
     * @AclAncestor("pimee_catalog_rule_rule_delete_permissions")
     *
     * @return array
     */
    public function createAction(Request $request)
    {
        $ruleDefinition = new RuleDefinition();

        return $this->editAction($request, $ruleDefinition);
    }

    /**
     * edit rule
     *
     * @param RuleDefinition $ruleDefinition
     *
     * @Template
     * @AclAncestor("pimee_catalog_rule_rule_delete_permissions")
     *
     * @return array
     */
    public function editAction(Request $request, RuleDefinition $ruleDefinition)
    {
        $ruleDefinitionData = RuleDefinitionDTO::fromEntity($ruleDefinition);
        $form               = $this->formFactory->create(new RuleDefinitionType(), $ruleDefinitionData);
        $form->handleRequest($request);

        /** @var Attribute[] $attributes */
        $attributes     = $this->attributeRepository->findAll();
        $attributesData = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getMetricFamily()) {
                $attributesData[$attribute->getCode()] = [
                    'is_localizable' => $attribute->isLocalizable(),
                    'is_scopable'    => $attribute->isScopable(),
                    'is_metric'      => true,
                ];
            } else {
                $attributesData[$attribute->getCode()] = [
                    'is_localizable' => $attribute->isLocalizable(),
                    'is_scopable'    => $attribute->isScopable(),
                    'is_metric'      => false,
                ];
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $ruleDefinition = $ruleDefinitionData->getEntity();
            $violations     = $this->validator->validate($ruleDefinition);
            foreach ($ruleDefinitionData->actions as $action) {
                if ($action->type == 'copy') {
                    if ($action->fromField == $action->toField) {
                        if (isset($action->fromLocale)) {
                            if ($action->fromLocale == $action->toLocale) {
                                if (isset($action->fromScope)) {
                                    if ($action->fromScope == $action->toScope) {
                                        $this->request->getSession()->getFlashBag()
                                                      ->add('error', 'basecom.flash.rule.error.copy');

                                        return [
                                            'form'           => $form->createView(),
                                            'attributesData' => $attributesData,
                                            'ruleDefinition' => $ruleDefinition,
                                        ];
                                    } else {
                                        break;
                                    }
                                } else {
                                    $this->request->getSession()->getFlashBag()
                                                  ->add('error', 'basecom.flash.rule.error.copy');

                                    return [
                                        'form'           => $form->createView(),
                                        'attributesData' => $attributesData,
                                        'ruleDefinition' => $ruleDefinition,
                                    ];
                                }
                            }

                        } elseif (isset($action->fromScope)) {
                            if ($action->fromScope == $action->toScope) {
                                $this->request->getSession()->getFlashBag()
                                              ->add('error', 'basecom.flash.rule.error.copy');

                                return [
                                    'form'           => $form->createView(),
                                    'attributesData' => $attributesData,
                                    'ruleDefinition' => $ruleDefinition,
                                ];
                            } else {
                                break;
                            }

                        } else {
                            $this->request->getSession()->getFlashBag()
                                          ->add('error', 'basecom.flash.rule.error.copy');

                            return [
                                'form'           => $form->createView(),
                                'attributesData' => $attributesData,
                                'ruleDefinition' => $ruleDefinition,
                            ];
                        }
                    }
                }
            }
            if (0 < count($violations)) {
                $violationString = '';

                /**
                 * @var ConstraintViolation $violation
                 */
                foreach ($violations as $violation) {
                    $violationString = $violationString.$violation->getMessage();
                }
                $this->request->getSession()->getFlashBag()
                              ->add('error', new Message($violationString));

                return [
                    'form'           => $form->createView(),
                    'attributesData' => $attributesData,
                    'ruleDefinition' => $ruleDefinition,
                ];
            } else {
                try {
                    $this->productRuleBuilder->build($ruleDefinition);
                    $this->ruleDefinitionSaver->save($ruleDefinition);
                } catch (\LogicException $e) {

                    $this->request->getSession()->getFlashBag()
                                  ->add('error', 'basecom.flash.rule.error.save');

                    return [
                        'form'           => $form->createView(),
                        'attributesData' => $attributesData,
                        'ruleDefinition' => $ruleDefinition,
                        'error'          => $e->getMessage(),
                    ];
                }

                $ruleDefinition = $this->ruleDefinitionRepository->findOneByIdentifier($ruleDefinition->getCode());
                $this->request->getSession()->getFlashBag()
                              ->add('success', new Message('basecom.flash.rule.saved'));

                return new RedirectResponse(
                    $this->router->generate('basecom_rule_edit', ['id' => $ruleDefinition->getId()])
                );
            }
        }

        return [
            'form'           => $form->createView(),
            'attributesData' => $attributesData,
            'ruleDefinition' => $ruleDefinition,
        ];
    }

    /**
     * Remove rule
     *
     * @param Request $request
     * @param int     $id
     *
     * @AclAncestor("basecom_rule_remove")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function removeAction(Request $request, $id)
    {
        if ($request->isXmlHttpRequest()) {
            return new Response('', 204);
        } else {
            return new RedirectResponse($this->router->generate('pimee_catalog_rule_rule_index'));
        }
    }
}
