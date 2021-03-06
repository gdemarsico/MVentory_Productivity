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
 * RSS helper
 *
 * @package MVentory/Productivity
 * @author Anatoly A. Kazantsev <anatoly@mventory.com>
 */
class MVentory_Productivity_Helper_Rss
  extends Mage_Core_Helper_Abstract {

  const MEDIA_NS = 'http://search.yahoo.com/mrss/';
  const ATOM_NS = 'http://www.w3.org/2005/Atom';

  const IMAGE_WIDTH = 300;
  const IMAGE_HEIGHT = 300;

  const THUMB_WIDTH = 75;
  const THUMB_HEIGHT = 75;

  public function generateFeedForProducts ($products, $params) {
    $params = $this->_prepareParams($params);

    $currency = Mage::app()
                  ->getStore(null)
                  ->getCurrentCurrency();

    $formatParams = array('display'=>Zend_Currency::NO_SYMBOL);

    $helper = Mage::helper('catalog/image');

    $xml = new SimpleXMLElement('<rss xmlns:media="' . self::MEDIA_NS . '"'
                                   . 'xmlns:atom="' . self::ATOM_NS . '"/>');

    $xml->registerXPathNamespace('media', self::MEDIA_NS);
    $xml->registerXPathNamespace('atom', self::ATOM_NS);
    $xml->addAttribute('version', '2.0');

    $date = date('r', time());

    $channel = $xml->addChild('channel');

    $channel->title = $params['title'];
    $channel->link = $params['link'];
    $channel->addChild('description');
    $channel->pubDate = $date;
    $channel->lastBuildDate = $date;
    $channel->generator = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);

    $link = $channel->addChild('link', null, self::ATOM_NS);
    $link->addAttribute('href', Mage::helper('core/url')->getCurrentUrl());
    $link->addAttribute('rel', 'self');
    $link->addAttribute('type', 'application/rss+xml');

    $author = Mage::getStoreConfig('trans_email/ident_general/email')
              . ' ('
              . Mage::getStoreConfig('trans_email/ident_general/name')
              . ')';

    foreach($products as $product) {
      $name = $product->getName();

      $item = $channel->addChild('item');

      $item->title = $name;
      $item->link = $product->getProductUrl();
      $item->description = $this->_getProductAttributes($product);
      $item->pubDate = date('r', strtotime($product->getUpdatedAt()));
      $item->author = $author;
      $item->guid = $item->link;

      $item->addChild('title', htmlspecialchars($name), self::MEDIA_NS);

      foreach ($params['images'] as $tag => $data) {
        $child = $item->addChild($tag, '', self::MEDIA_NS);

        $url = $helper->init($product, 'small_image');

        if (count($data) == 2) {
          $url->resize($data['width'], $data['height']);

          foreach (array('width', 'height') as $p)
            if ($data[$p])
              $child->addAttribute($p, $data[$p]);
        }

        $child->addAttribute('url', $url->__toString());
      }

      //We assume that current currency and base currency are same.
      //I.e. no currency convertion in the store
      $price = $currency->format($product->getPrice(), $formatParams, false);

      $item
        ->addChild('price', '', self::MEDIA_NS)
        ->addAttribute('price', $price);
    }

    return $this->formatXml($xml->asXML());
  }

  /**
   * Returns RSS data in JSON format
   *
   * @param array|Traversable $products List of products
   * @param array $params Parameters
   * @return string
   */
  public function asJson ($products, $params) {
    $params = $this->_prepareParams($params);

    $helper = Mage::helper('catalog/image');
    $currency = Mage::app()
      ->getStore(null)
      ->getCurrentCurrency();

    foreach($products as $product) {
      $image = $helper->init($product, 'small_image');

      if (count($params['images']['content']) == 2)
        $image->resize(
          $params['images']['content']['width'],
          $params['images']['content']['height']
        );

      $_products[] = array(
        'title' => $product->getName(),
        'description' => $this->_getProductAttributes($product),
        'url' => $product->getProductUrl(),
        'image' => $image->__toString(),
        'price' => $currency->format($product->getPrice(), array(), false)
      );
    }

    return Mage::helper('core')->jsonEncode(
      isset($_products) ? $_products : array()
    );
  }

  public function formatXml ($xml) {
    $dom = new DOMDocument('1.0');

    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->loadXML($xml);

    return $dom->saveXML();
  }

  public function addFeedToHeader ($url, $title = null) {
    $head = $this
              ->getLayout()
              ->getBlock('head');

    if (!$head)
      return;

    if (!$title)
      $title = $head->getTitle();

    $head->addItem('rss', $url, 'title="' . $title . '"');
  }

  public function getLayout () {
    $layout = parent::getLayout();

    if ($layout)
      return $layout;

    $layout = Mage::app()
                ->getFrontController()
                ->getAction()
                ->getLayout();

    parent::setLayout($layout);

    return $layout;
  }

  protected function _getProductAttributes ($product) {
    Mage::register('product', $product->load($product->getId()));

    $_attrs = $this
                ->getLayout()
                ->createBlock('catalog/product_view_attributes')
                ->getAdditionalData(array(), false);

    Mage::unregister('product');

    $attrs = '';

    foreach ($_attrs as $_attr)
      $attrs .= $_attr['label'] . ': ' . $_attr['value'] . '<br />';

    return substr($attrs, 0, -6);
  }

  protected function _prepareParams ($params) {
    $defaults = array(
      'images' => array(
        'main' => array(
          'width' => self::IMAGE_WIDTH,
          'height' => self::IMAGE_HEIGHT
        ),
        'thumb' => array(
          'width' => self::THUMB_WIDTH,
          'height' => self::THUMB_HEIGHT
        )
      )
    );

    $params['images'] = array_merge($defaults['images'], $params['images']);
    $params = array_merge($defaults, $params);

    $params['images']['content'] = $params['images']['main'];
    $params['images']['thumbnail'] = $params['images']['thumb'];

    unset($params['images']['main']);
    unset($params['images']['thumb']);

    return $params;
  }
}
