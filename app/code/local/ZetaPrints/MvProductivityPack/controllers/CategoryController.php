<?php
/**
 * Rewrite the CategoryController to check the admin login on store front
 *
 * @category   Mage_Catalog_CategoryController
 * @package    ZetaPrints_MvProductivityPack
 * @author     ZetaPrints <anemets1@gmail.com>
 */
require_once("Mage/Catalog/controllers/CategoryController.php");
class ZetaPrints_MvProductivityPack_CategoryController extends Mage_Catalog_CategoryController
{
	/*
	 * Below preDispatch method will check whether admin is logged in or not on admin side and add the value in 
	 * registry 
	 */
	public function preDispatch () {
		Mage::getSingleton('core/session', array('name' => 'adminhtml'))
		  ->start();

		Mage::register('is_admin_logged',
					   Mage::getSingleton('admin/session')->isLoggedIn());

		parent::preDispatch();

		return $this;
	}
}

