<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from SAS Comptoir du Code
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the SAS Comptoir du Code is strictly forbidden.
 * In order to obtain a license, please contact us: contact@comptoirducode.com
 *
 * @author    Vincent - Comptoir du Code
 * @copyright Copyright(c) 2015-2016 SAS Comptoir du Code
 * @license   Commercial license
 * @package   cdc_googletagmanager
 */

class cdc_googletagmanagerAsyncModuleFrontController extends ModuleFrontController
{
	private $dataLayer = null;
	private $cdc_gtm = null;

	public function __construct()
	{
		// if page is called in https, force ssl
		if (Tools::usingSecureMode()) {
			$this->ssl = true;
		}
		return parent::__construct();
	}


	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
	    $object = Tools::getValue('obj');
	    if(!empty($object)) {
            $this->dataLayer = new Gtm_DataLayer();
            $this->cdc_gtm = new cdc_googletagmanager();

	        switch ($object) {
                case 'user':
                    $this->dataLayer = $this->cdc_gtm->addUserInfosToDatalayer($this->dataLayer);
                    break;
                case 'cart-action':

                    $this->dataLayer = $this->cdc_gtm->getDataLayerCartAction(
                        (int) Tools::getValue('id'),
                        (int) Tools::getValue('id_attribute'),
                        Tools::getValue('action'),
                        (int) Tools::getValue('qtity')
                    );
                    break;
            }
        }
	}


	public function display()
	{
		echo $this->cdc_gtm->dataLayerToJson($this->dataLayer);
	}

}