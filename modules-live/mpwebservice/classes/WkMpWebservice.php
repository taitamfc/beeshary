<?php
/**
* 2010-2021 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2021 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkMpWebservice
{
    protected $objOutput;
    protected $output;
    protected $wsObject;
    // Made public for image specific management class, need to improve
    public $idSeller;

    public function __construct($objOutput = null, $wsObject = null, $output = null)
    {
        $this->objOutput = $objOutput;
        $this->wsObject = $wsObject;
        $this->output = $output;

        $this->setContext();
        $this->setIdSeller();
    }

    /**
     * Get the xml output by message and custom node
     *
     * @deprecated from v6.0.0
     *
     * @param  string $message Message to display
     * @param  boolean $status status can be 0/1
     * @param  array   $args custom node in the array
     * @param  boolean $xml Is basix xml or in $this->output
     * @return xml
     */
    public function getResult($message, $status = null, $args = array(), $xml = false)
    {
        $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('response', array());
        if ($status !== null) {
            $this->output .= $this->renderField('status', $status);
        }

        $this->output .= $this->renderField('message', $message);

        if (is_array($args) && !empty($args)) {
            foreach ($args as $key => $value) {
                $this->output .= $this->renderField($key, $value);
            }
        }

        if ($xml) {
            $this->output .= $xml;
        }

        $this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('response', array());

        return $this->output;
    }

    public function setIdSeller()
    {
        $this->idSeller = false;
        $seller = WkMpSeller::getSellerDetailByCustomerId($this->context->customer->id);
        if ($seller) {
            $this->idSeller = $seller['id_seller'];
        } else {
            $this->output['success'] = false;
            $this->output['message'] = 'Invalid seller';
            return $this->output;
        }
    }


    /**
     * Set context for mobikul so that urlFragments can be get in $this->context var
     */
    public function setContext()
    {
        $this->context = Context::getContext();
        $this->context->controller = new FrontController();

        // Set Customer
        if (isset($this->wsObject->urlFragments['id_customer'])) {
            $this->context->customer = new Customer($this->wsObject->urlFragments['id_customer']);
            $this->context->customer->logged = 1; // Assuming that when getting id_customer, customer is logged in.
        } elseif (Context::getContext()->customer->id) {
            $this->context->customer = new Customer(Context::getContext()->customer->id);
        }

        // Set Cart
        if (isset($this->wsObject->urlFragments['id_cart'])) {
            $this->context->cart = new Cart($this->wsObject->urlFragments['id_cart']);
        }

        // Set language
        if (isset($this->wsObject->urlFragments['id_lang'])) {
            $language = new Language($this->wsObject->urlFragments['id_lang']);
            if (!Validate::isLoadedObject($language)) {
                $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
            }
        } else {
            $language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        }
        $this->context->language = $language;

        // Set Currency
        if (isset($this->wsObject->urlFragments['id_currency'])) {
            $currency = new Currency($this->wsObject->urlFragments['id_currency']);
            if (!Validate::isLoadedObject($currency)) {
                $language = new Language((int) Configuration::get('PS_CURRENCY_DEFAULT'));
            }
        } else {
            $currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        }
        $this->context->currency = $currency;
    }

    /**
     * Create just like PS renderField function in WebServiceOutputXML class.
     *
     * @todo to be mode functional with attributes and others
     * @param string/array $nodeName
     * @param varchar $value
     * @return xml xml node
     */
    public function renderField($nodeName, $value = null)
    {
        $output = '';
        if (is_array($nodeName)) {
            foreach ($nodeName as $name => $value) {
                $output .= '<'.$name.'><![CDATA['.$value.']]></'.$name.'>'."\n";
            }
        } else {
            $node_content = '<![CDATA['.$value.']]>';
            $output = '<'.$nodeName.'>'.$node_content.'</'.$nodeName.'>'."\n";
        }
        return $output;
    }
}
