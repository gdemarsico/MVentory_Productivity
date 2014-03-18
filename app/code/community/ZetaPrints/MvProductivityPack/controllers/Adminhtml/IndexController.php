<?php
class ZetaPrints_MvProductivityPack_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
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