# Codelegacy_Utils_M2
Magento 2 Module with some tools to make dev life and debugging easier

###Cron Runner ###

how to use :
bin/magento codelegacy:utils:cronrunner --cronClass="\My\CronClass\toRun"

### Custom Product Attribute Creator ###
The class `ProductUtils` is useful to create simply and quickly your custom Product Attributes.
In your InstallData/ UpgradeData , call \Codelegacy\Utils\Model\ProductUtils as DI,
and use public function inside. 
To create an custom product attribute, pass an array of your data as argument :
Example :
<pre><code>
$attributes = [
   'another_custom_attribute' => [
       'type' => 'varchar',
       'label' => 'Another Custom Label',
       'input' => 'text',
       'source' => '',
       'filterable' => false,
       'visible_on_front' => true,
       'used_in_product_listing' => false,
       'attribute_group_name' => 'My Custom Attribute Group Name',
       'backend' => ''
   ],
   'another_custom_attribute_with_option' => [
       'type' => 'int',
       'label' => 'Type',
       'input' => 'select',
       'source' => '',
       'filterable' => true,
       'visible_on_front' => true,
       'used_in_product_listing' => true,
       'backend' => '',
       'options' => [
           'Option A',
           'Option B',
           'Option C',
           'Option D',
           'Option E',
           'Option F'
       ]
   ],
   'another_custom_attribute_boolean' =>[
       'type' => 'int',
       'label' => 'Shown In List',
       'input' => 'select',
       'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
       'filterable' => false,
       'visible_on_front' => false,
       'used_in_product_listing' => false,
       'attribute_group_name' => 'Another Custom Attribute Group Name',
       'attribute_set_name' => 'my custom Attribute Set Name',
       'backend' => ''
   ],
   'another_custom_attribute_multiselect' => [
       'type' => 'varchar',
       'label' => 'Collection',
       'input' => 'multiselect',
       'source' => '',
       'filterable' => true,
       'visible_on_front' => true,
       'used_in_product_listing' => true,
       'options' => [
           'Collection A',
           'Collection B',
           'Collection C',
           'Collection D',
       ],
       'attribute_group_name' => 'Another Custom Attribute Group Name',
       'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend'
   ],
];
</code></pre>

