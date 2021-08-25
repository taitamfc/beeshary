<?php
/*
* 2010-2019 Webkul
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
*  @author Webkul IN <support@webkul.com>
*  @copyright  2010-2019 Webkul IN
*/

class MpSellerInvoiceConfig extends ObjectModel
{
    public $invoice_number;
    public $invoice_vat;
    public $invoice_based;
    public $value;
    public $time_interval;
    public $last_generated;
    public $invoice_prefix;
    public $invoice_legal_text;
    public $invoice_footer_text;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'mp_seller_invoice_config',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id_seller' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'invoice_number' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'invoice_vat' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'invoice_based' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'value' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'time_interval' => array('type' => self::TYPE_STRING, 'shop' => true, 'validate' => 'isString'),
            'last_generated' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),

             /* Lang fields */
            'invoice_prefix' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 20
            ),
            'invoice_legal_text' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml'
            ),
            'invoice_footer_text' => array(
                'type' => self::TYPE_HTML,
                'lang' => true,
                'validate' => 'isCleanHtml'
            ),
        )
    );

    public function isSellerInvoiceConfigExist($idSeller)
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM '._DB_PREFIX_.'mp_seller_invoice_config WHERE `id_seller` = '. (int) $idSeller
        );
    }

    public function getSellerInvoiceConfig($idSeller, $idLang = false)
    {
        if ($idLang) {
            return Db::getInstance()->getRow(
                'SELECT * FROM '._DB_PREFIX_.'mp_seller_invoice_config si
                    JOIN '._DB_PREFIX_.'mp_seller_invoice_config_lang sil on (si.`id` = sil.`id`)
                    WHERE si.`id_seller` = '. (int) $idSeller.' AND sil.`id_lang` = '.(int) $idLang
            );
        } else {
            return Db::getInstance()->executeS(
                'SELECT * FROM '._DB_PREFIX_.'mp_seller_invoice_config si
                    JOIN '._DB_PREFIX_.'mp_seller_invoice_config_lang sil on (si.`id` = sil.`id`)
                    WHERE si.`id_seller` = '. (int) $idSeller
            );
        }
    }
}
