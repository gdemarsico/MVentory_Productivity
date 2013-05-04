<?php

class ZetaPrints_MvProductivityPack_Helper_Data
  extends Mage_Core_Helper_Abstract {

  const ATTRIBUTE_CODE = 'media_gallery';
  const REVIEWER_GROUP_CODE = 'REVIEWER';

  private $_mediaBackend = array();

  public function rotate ($file, $angle) {
    $image = Mage::getModel('catalog/product_image');

    $image
      ->setBaseFile($file)
      ->setQuality(100)
      ->setKeepFrame(false)
      ->rotate($angle)
      ->saveFile();

    return $image->getNewFile();
  }

  public function remove ($file, $productId) {
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    $product = Mage::getModel('catalog/product')->load($productId);

    if (!$product->getId())
      return;

    $attributes = $product
                    ->getTypeInstance(true)
                    ->getSetAttributes($product);

    if (!isset($attributes[self::ATTRIBUTE_CODE]))
      return;

    $gallery = $attributes[self::ATTRIBUTE_CODE];

    if (!$gallery->getBackend()->getImage($product, $file))
      return;

    $gallery
      ->getBackend()
      ->removeImage($product, $file);

    try {
      $product->save();
    } catch (Mage_Core_Exception $e) {
      return;
    }

    return true;
  }

  public function setMainImage ($file, $productId) {
    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    $product = Mage::getModel('catalog/product')->load($productId);

    if (!$product->getId())
      return;

    $attributes = $product
                    ->getTypeInstance(true)
                    ->getSetAttributes($product);

    if (!isset($attributes[self::ATTRIBUTE_CODE]))
      return;

    $gallery = $attributes[self::ATTRIBUTE_CODE];

    if (!$gallery->getBackend()->getImage($product, $file))
      return;

    $currentImage = $product->getImage();

    if ($currentImage)
      $gallery
        ->getBackend()
        ->updateImage($product, $currentImage, array('exclude' => false));

    $gallery
      ->getBackend()
      ->updateImage($product, $file, array('exclude' => true));

    $gallery
      ->getBackend()
      ->setMediaAttribute($product,
                array('image', 'small_image', 'thumbnail'),
                $file);

    try {
      $product->save();
    } catch (Mage_Core_Exception $e) {
      Mage::log($product);
      return;
    }

    return true;
  }

  public function updateImageInGallery ($oldFile, $newFile, $productId,
                                        $mediaAttribute = null, $move = true,
                                        $exclude = false) {

    Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

    $product = Mage::getModel('catalog/product')->load($productId);

    if (!$product->getId())
      return;

    $attributes = $product
                    ->getTypeInstance(true)
                    ->getSetAttributes($product);

    if (!isset($attributes[self::ATTRIBUTE_CODE]))
      return;

    $mediaGalleryAttribute = $attributes[self::ATTRIBUTE_CODE];

    $mediaGalleryAttribute
      ->getBackend()
      ->removeImage($product, $oldFile);

    $file = $mediaGalleryAttribute
              ->getBackend()
              ->addImage($product, $newFile, $mediaAttribute, $move, $exclude);

    $product->save();

    return $file;
  }

  protected function _getMediaBackend ($product) {
    $id = $product->getId();

    if (isset($this->_mediaBackend[$id]))
      return $this->_mediaBackend[$id];

    $attributes = $product
                    ->getTypeInstance(true)
                    ->getSetAttributes($product);

    if (!isset($attributes[self::ATTRIBUTE_CODE]))
      return;

    $mediaGalleryAttribute = $attributes[self::ATTRIBUTE_CODE];
    $backend = $mediaGalleryAttribute->getBackend();

    $this->_mediaBackend[$id] = $backend;

    return $backend;
  }

  public function isAdminLogged () {
    return Mage::registry('is_admin_logged') === true;
  }

  public function isReviewerLogged () {
    if ($this->isAdminLogged())
      return true;

    $groupId = Mage::getSingleton('customer/session')
                 ->getCustomerGroupId();

    if ($groupId == Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
      return false;

    $group = Mage::getModel('customer/group')->load($groupId);

    if (!$group->getId())
      return false;

    return strcasecmp($group->getCode(), self::REVIEWER_GROUP_CODE) == 0;
  }

  /**
   * Sends email to stores's general contant address
   *
   * @param string $subject
   * @param string $message
   * @param int|string|Mage_Core_Model_Store $store Store, its ID or code
   */
  public function sendEmail ($subject,
                             $message,
                             $store = Mage_Core_Model_App::ADMIN_STORE_ID) {

    $store = Mage::app()->getStore($store);

    $email = $store->getConfig('trans_email/ident_general/email');
    $name = $store->getConfig('trans_email/ident_general/name');

    Mage::getModel('core/email')
      ->setFromEmail($email)
      ->setFromName($name)
      ->setToName($name)
      ->setToEmail($email)
      ->setBody($message)
      ->setSubject($subject)
      ->send();
  }
}
