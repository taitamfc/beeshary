<?php 

class MarketPlaceCategoryMarketModuleFrontController extends ModuleFrontController
{
	  public function setMedia()
    {
        parent::setMedia();

        $this->context->controller->addCSS('\themes\beeshary\assets\css\theme.css', 'all');
		    $this->context->controller->addCSS('\themes\beeshary\assets\css\custom.css', 'all');

    }
	
    public function initContent()
    {
        parent::initContent();

    	  $this->setTemplate('module:MarketPlaceCategory/views/templates/front/market.tpl');	
	 }
}
