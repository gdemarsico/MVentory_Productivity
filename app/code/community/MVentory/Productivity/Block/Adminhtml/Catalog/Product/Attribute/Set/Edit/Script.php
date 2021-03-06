<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE-OSL.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @package MVentory/Productivity
 * @copyright Copyright (c) 2014-2015 mVentory Ltd. (http://mventory.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Block Attribute Set edit template script
 *
 * @package MVentory/Productivity
 * @author <anemets1@gmail.com>
 */
class MVentory_Productivity_Block_Adminhtml_Catalog_Product_Attribute_Set_Edit_Script
  extends Mage_Adminhtml_Block_Template {

  public function getJson() {
    $urls = [];
    $attrs = Mage::getResourceModel('catalog/product_attribute_collection');

    foreach ($attrs as $attr)
      $urls[$attr->getAttributeCode()] = $this->getUrl(
        'adminhtml/catalog_product_attribute/edit',
        ['attribute_id' => $attr->getAttributeId()]
      );

    return Mage::helper('core')->jsonEncode($urls);
  }
}
