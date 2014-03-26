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
 * @copyright Copyright (c) 2014 mVentory Ltd. (http://mventory.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Block for displaying related product by specified attribute
 *
 * @package MVentory/Productivity
 * @author Anatoly A. Kazantsev <anatoly@mventory.com>
 */
class MVentory_Productivity_Block_Product_Related
  extends Mage_Catalog_Block_Product_Abstract {

  public function getCacheKeyInfo () {
    return array_merge(
      parent::getCacheKeyInfo(),
      array(
        Mage::getSingleton('customer/session')->getCustomerGroupId(),
        ($product = $this->getProduct()) ? $product->getId() : null,
        $this->getData('attribute_code'),
        $this->getData('product_count')
      )
    );
  }

  public function getProductCollection () {
    $collection = $this->getData('product_collection');

    if ($collection)
      return $collection;

    $this->setData(
      'product_collection',
      $collection = new Varien_Data_Collection()
    );

    if (!(($product = $this->getProduct())
          && ($productId = $product->getId())))
      return $collection;

    if (!$code = trim($this->getData('attribute_code')))
      return $collection;

    if (($value = $product->getData($code)) === null)
      return $collection;

    $visibility = Mage::getSingleton('catalog/product_visibility')
                    ->getVisibleInCatalogIds();

    $imageFilter = array('nin' => array('no_selection', ''));

    $collection = Mage::getResourceModel('catalog/product_collection')
                    ->addAttributeToSelect(array(
                        'name',
                        'special_price',
                        'special_from_date',
                        'special_to_date'
                      ))
                    ->addPriceData()
                    ->addUrlRewrite()
                    ->addIdFilter($productId, true)
                    ->addAttributeToFilter($code, $value)
                    ->addAttributeToFilter('small_image', $imageFilter)
                    ->setVisibility($visibility)
                    ->addStoreFilter()
                    ->setPageSize($this->getProductsCount())
                    ->setCurPage(1);

    $collection
      ->getSelect()
      ->order(new Zend_Db_Expr('RAND()'));

    return $collection;
  }
}
