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
 * Controller to open CMS page editor from the frontend
 *
 * @package MVentory/Productivity
 * @author <anemets1@gmail.com>
 */
class MVentory_Productivity_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function preDispatch()
    {
      if ($this->getRequest()->getActionName() == 'index') Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
      parent::preDispatch();
    }

    public function indexAction()
    {
      $page_id = $this->getRequest()->getParam('page_id');

      $this->getResponse()->setRedirect(Mage::helper("adminhtml")->getUrl("adminhtml/cms_page/edit",array("page_id"=>$page_id)));
      return;
    }

}