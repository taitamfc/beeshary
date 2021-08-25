<?php
/**
* 2010-2019 Webkul.
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
*  @copyright 2010-2019 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class WkGdprAgreementData extends ObjectModel
{
    public $id_agreement_data;
    // for modules id module is id_module for perticular module but for ps forms id_module will be a negative number
    // @@See $this->psCoreGdprForms
    public $id_module;
    public $active;
    public $date_add;
    public $date_upd;

    public static $definition = array(
        'table' => 'wk_gdpr_agreement_data',
        'primary' => 'id_agreement_data',
        'multilang' => true,
        'fields' => array(
            'id_module' => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
            'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),

            // Lang fields
            'agreement_content' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'lang' => true),
        )
    );

    const WK_CUSTOMER_REGISTER_FORM = -1;
    const WK_CUSTOMER_IDENTITY_FORM = -2;
    const WK_CUSTOMER_CONTACT_FORM = -3;

    const WK_ONLY_MODULE_AGREEMENT_DATA = 1;
    const WK_ONLY_PS_CORE_AGREEMENT_DATA = 2;

    public function __construct($id = null)
    {
        parent::__construct($id);
        // valiable for prestashop core forms
        $this->objModule = Module::getInstanceByName('wkgdpr');
        $this->psCoreGdprForms[self::WK_CUSTOMER_REGISTER_FORM] = array(
            'name' => $this->objModule->l('Customer Registration Form', 'WkGdprAgreementData')
        );
    }

    // get modules gdpr agreement data -- active = 2 for all the active/inactive . Other wise send 0 or 1
    public function getModulesGdprAgreementData(
        $idModule = 0,
        $active = 2,
        $idLang = 0,
        $type = 0,
        $multiLang = 0,
        $orderBy = 'id_agreement_data',
        $orderWay = 'ASC'
    ) {
        $sql = 'SELECT gad.*';
        if (!$multiLang) {
            $sql .= ' , gadl.`agreement_content`';
        }
        $sql .= ' FROM `'._DB_PREFIX_.'wk_gdpr_agreement_data` gad';
        if (!$multiLang) {
            $sql .= ' INNER JOIN `'._DB_PREFIX_.'wk_gdpr_agreement_data_lang` gadl
            ON (gadl.`id_agreement_data` = gad.`id_agreement_data`)';
        }
        $sql .= ' WHERE 1';
        if (!$multiLang) {
            if (!$idLang) {
                $idLang = Context::getContext()->language->id;
            }
            $sql .= ' AND gadl.`id_lang` = '.(int)$idLang;
        }
        if ($idModule) {
            $sql .= ' AND `id_module` = '.(int)$idModule;
        }
        if ($type == self::WK_ONLY_MODULE_AGREEMENT_DATA) {
            $sql .= ' AND `id_module` > 0';
        } elseif ($type == self::WK_ONLY_PS_CORE_AGREEMENT_DATA) {
            $sql .= ' AND `id_module` < 0';
        }
        if ($active == 1 || $active == 0) {
            $sql .= ' AND `active` = '.(int)$active;
        }
        if (Validate::isOrderBy($orderBy) && Validate::isOrderBy($orderWay)) {
            $sql .= ' ORDER BY '.pSQL($orderBy).' '.pSQL($orderWay);
        }
        if ($idModule) {
            if ($result = Db::getInstance()->getRow($sql)) {
                if ($result['id_module'] > 0) {
                    if (Validate::isLoadedObject(
                        $objModule = Module::getInstanceById($result['id_module'])
                    )) {
                        $result['name'] = $objModule->name;
                    }
                } else {
                    if (isset($this->psCoreGdprForms[$result['id_module']])) {
                        $result['name'] = $this->psCoreGdprForms[$result['id_module']]['name'];
                    }
                }
                if (empty($result['name'])) {
                    $result['name'] = $this->objModule->l(
                        'N/A',
                        'WkGdprAgreementData'
                    );
                }
                if ($multiLang) {
                    $result['agreement_content'] = array();
                    if ($agreementMultilang = Db::getInstance()->executeS(
                        'SELECT `agreement_content`, `id_lang` FROM `'._DB_PREFIX_.'wk_gdpr_agreement_data_lang`
                        WHERE `id_agreement_data` = '.(int)$result['id_agreement_data']
                    )) {
                        foreach ($agreementMultilang as $dataLang) {
                            $result['agreement_content'][$dataLang['id_lang']] = $dataLang['agreement_content'];
                        }
                    }
                }
            }
        } else {
            if ($result = Db::getInstance()->executeS($sql)) {
                foreach ($result as &$agreementData) {
                    if ($agreementData['id_module'] > 0) {
                        if (Validate::isLoadedObject(
                            $objModule = Module::getInstanceById($agreementData['id_module'])
                        )) {
                            $agreementData['name'] = $objModule->name;
                        }
                    } else {
                        if (isset($this->psCoreGdprForms[$agreementData['id_module']])) {
                            $agreementData['name'] = $this->psCoreGdprForms[$agreementData['id_module']]['name'];
                        }
                    }
                    if (empty($agreementData['name'])) {
                        $agreementData['name'] = $this->objModule->l(
                            'N/A',
                            'WkGdprAgreementData'
                        );
                    }
                    if ($multiLang) {
                        $agreementData['agreement_content'] = array();
                        if ($agreementMultilang = Db::getInstance()->executeS(
                            'SELECT `agreement_content`, `id_lang` FROM `'._DB_PREFIX_.'wk_gdpr_agreement_data_lang`
                            WHERE `id_agreement_data` = '.(int)$agreementData['id_agreement_data']
                        )) {
                            foreach ($agreementMultilang as $dataLang) {
                                $agreementContent = $dataLang['agreement_content'];
                                $agreementData['agreement_content'][$dataLang['id_lang']] = $agreementContent;
                            }
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function insertInstalledModulesGdprAgreementData()
    {
        if (Configuration::get('WK_GDPR_DEFAULT_AGREEMENT_CONTENT')) {
            $defaultAgreementContent = Configuration::get('WK_GDPR_DEFAULT_AGREEMENT_CONTENT');
        } else {
            $defaultAgreementContent = $this->objModule->l(
                'You must agree with terms and conditions as per GDPR rules.',
                'WkGdprAgreementData'
            );
        }
        $languages = Language::getLanguages(true);

        // // insert Prestashop core Gdpr Agreement Data
        $psGDPRForms = $this->psCoreGdprForms;
        $psGDPRForms = array_keys($psGDPRForms);
        foreach ($psGDPRForms as $psForm) {
            $objAgreementData = new WkGdprAgreementData();
            if (!$objAgreementData->getModulesGdprAgreementData($psForm)) {
                $objAgreementData->id_module = $psForm;
                $objAgreementData->active = 1;
                foreach ($languages as $lang) {
                    $objAgreementData->agreement_content[$lang['id_lang']] = $defaultAgreementContent;
                }
                $objAgreementData->save();
            }
        }
        // // insert Installed Modules Gdpr Agreement Data
        if ($regGDPRModules = Hook::getHookModuleExecList('registerGDPRConsent')) {
            foreach ($regGDPRModules as $module) {
                $objAgreementData = new WkGdprAgreementData();
                if (!$objAgreementData->getModulesGdprAgreementData($module['id_module'])) {
                    $objAgreementData->id_module = $module['id_module'];
                    $objAgreementData->active = 1;
                    foreach ($languages as $lang) {
                        $objAgreementData->agreement_content[$lang['id_lang']] = $defaultAgreementContent;
                    }
                    $objAgreementData->save();
                }
            }
        }
        return true;
    }
}
