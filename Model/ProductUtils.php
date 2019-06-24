<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Alex P <alexander@codelegacy.com> <@>
 * @copyright Copyright (c) 2019 Codelegacy (http://codelegacy.com)
 */

namespace Codelegacy\Utils\Model;

use Magento\Catalog\Api\AttributeSetManagementInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeGroupRepositoryInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Api\Data\AttributeGroupInterfaceFactory;
use Magento\Eav\Api\Data\AttributeSetInterfaceFactory;

class ProductUtils {

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * @var Product
     */
    private $product;
    /**
     * @var AttributeSetInterfaceFactory
     */
    private $attributeSetFactory;
    /**
     * @var AttributeSetManagementInterface
     */
    private $attributeSetManagement;
    /**
     * @var AttributeGroupInterfaceFactory
     */
    private $attributeGroupFactory;
    /**
     * @var AttributeGroupRepositoryInterface
     */
    private $attributeGroupRepository;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        Product $product,
        AttributeSetInterfaceFactory $attributeSetInterfaceFactory,
        AttributeSetManagementInterface $attributeSetManagement,
        AttributeGroupInterfaceFactory $attributeGroupFactory,
        AttributeGroupRepositoryInterface $attributeGroupRepository
    ){
        $this->eavSetupFactory = $eavSetupFactory;
        $this->product = $product;
        $this->attributeSetFactory = $attributeSetInterfaceFactory;
        $this->attributeSetManagement = $attributeSetManagement;
        $this->attributeGroupFactory = $attributeGroupFactory;
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    /**
     * Create Attribute
     * @param $attributes
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createProductAttribute($attributes) {
        foreach ($attributes as $attribute => $data) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create();
            $productEntity = \Magento\Catalog\Model\Product::ENTITY;
            $attrSetName = null;
            $attributeGroupId = null;
            /**
             * Initialise Attribute Set Id
             */
            if (isset($data['attribute_set_name'])) {
                $attributeSetId = $eavSetup->getAttributeSetId($productEntity, $data['attribute_set_name']);
                /**
                 * If our attribute set name does not exist, we create it.
                 * By default if Magento does not find an attribute set Id, it returns the default attribute set Id
                 */
                if($attributeSetId == $eavSetup->getDefaultAttributeSetId($productEntity) && $data['attribute_set_name'] != 'Default') {
                    $attrSetName = $data['attribute_set_name'];
                    $this->createAttributeSet($attrSetName);
                    $attributeSetId = $eavSetup->getAttributeSetId($productEntity, $attrSetName);
                }
            } else {
                $attributeSetId = $this->product->getDefaultAttributeSetId();
            }

            /**
             * Initialise Attribute Group Id
             */
            if (isset($data['attribute_group_name'])) {
                $attributeGroupId = $eavSetup->getAttributeGroupId($productEntity, $attributeSetId, $data['attribute_group_name']);
                /**
                 * If our attribute group name does not exist, we create it
                 */
                if($attributeGroupId == $eavSetup->getDefaultAttributeGroupId($productEntity) && $data['attribute_group_name'] != 'General') {
                    $attributeGroupName = $data['attribute_group_name'];
                    $this->createAttributeGroup($attributeGroupName, $attrSetName);
                    $attributeGroupId = $eavSetup->getAttributeGroupId($productEntity, $attributeSetId, $attributeGroupName);
                }
            }
            /**
             * Add attributes to the eav/attribute
             */
            $eavSetup->addAttribute(
                $productEntity,
                $attribute,
                [
                    'group' => $attributeGroupId ? '' : 'General', // Let empty, if we want to set an attribute group id
                    'type' => $data['type'],
                    'backend' => isset($data['backend']) ? $data['backend'] : '',
                    'frontend' => isset($data['frontend']) ? $data['frontend'] : '',
                    'label' => $data['label'],
                    'input' => $data['input'],
                    'class' => isset($data['class']) ? $data['class'] : '',
                    'source' => $data['source'],
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => isset($data['visible']) ? $data['visible'] : false,
                    'required' => isset($data['required']) ? $data['required'] : false,
                    'user_defined' => isset($data['user_defined']) ? $data['user_defined'] : true,
                    'default' => isset($data['default']) ? $data['default'] : '',
                    'searchable' => isset($data['searchable']) ? $data['searchable'] : false,
                    'filterable' => isset($data['filterable']) ? $data['filterable'] : true,
                    'comparable' => isset($data['comparable']) ? $data['comparable'] : false,
                    'visible_on_front' => isset($data['visible_on_front']) ? $data['visible_on_front'] : true,
                    'used_in_product_listing' => isset($data['used_in_product_listing']) ? $data['used_in_product_listing'] : true,
                    'unique' => false
                ]
            );

            /**
             * Set attribute group Id if needed
             */
            if (!is_null($attributeGroupId)) {
                /**
                 * Set the attribute in the right attribute group in the right attribute set
                 */
                $eavSetup->addAttributeToGroup($productEntity, $attributeSetId, $attributeGroupId, $attribute);
            }

            /**
             * Add options if needed
             */
            if (isset($data['options'])) {
                $options = [
                    'attribute_id' => $eavSetup->getAttributeId($productEntity, $attribute),
                    'values' => $data['options']
                ];
                $eavSetup->addAttributeOption($options);
            }
        }
    }

    /**
     * @param $attrSetName
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createAttributeSet($attrSetName) {
        $defaultAttributeSetId = $this->product->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setAttributeSetName($attrSetName);
        $this->attributeSetManagement->create($attributeSet, $defaultAttributeSetId);
    }

    /**
     * @param $attributeGroupName
     * @param null $attrSetName
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function createAttributeGroup($attributeGroupName, $attrSetName = null) {

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();
        $productEntity = \Magento\Catalog\Model\Product::ENTITY;

        if ($attrSetName) {
            $this->createAttributeSet($attrSetName);
            $attributeSetId = $eavSetup->getAttributeSetId($productEntity, $attrSetName);
        } else {
            $attributeSetId = $this->product->getDefaultAttributeSetId();
        }

        $attributeGroup = $this->attributeGroupFactory->create();
        $attributeGroup->setAttributeSetId($attributeSetId);
        $attributeGroup->setAttributeGroupName($attributeGroupName);
        $this->attributeGroupRepository->save($attributeGroup);
    }
}