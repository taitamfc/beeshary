<?php
/**
* 2010-2017 Webkul
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
*  @copyright 2010-2017 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminAddextrafieldController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'marketplace_extrafield';
        $this->className = 'MarketplaceExtrafield';
        $this->context = Context::getContext();
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        // $this->addRowAction('view');
        $this->identifier = 'id';
        $this->explicitSelect = true;
        $this->_select .= "a.id,(CASE a.`page` WHEN 1 THEN 'On add/update product page' ELSE 'On Shop Page' END) as page_lang,mpei.`inputtype_name` as inputtype_lang, mpel.`default_value` as default_value, mpel.`label_name` as label_name";
        $this->_join .= 'Left join `'._DB_PREFIX_.'marketplace_extrafield_inputtype` mpei on (a.`inputtype`=mpei.id)';
        $this->_join .= 'Left join `'._DB_PREFIX_.'marketplace_extrafield_lang` mpel on (a.`id`=mpel.id)';
        $this->_where = 'AND mpel.`id_lang` = '.(int) $this->context->language->id;

        parent::__construct();

        $this->fields_list = array(
                'id' => array(
                    'title' => $this->l('Id') ,
                    'align' => 'center',
                ),
                'page_lang' => array(
                    'title' => $this->l('Page') ,
                    'align' => 'center',
                    'havingFilter' => true,
                ),
                'default_value' => array(
                    'title' => $this->l('Default Value') ,
                    'align' => 'center',
                ),
                'label_name' => array(
                    'title' => $this->l('Label Name') ,
                    'align' => 'center',
                ),
                'inputtype_lang' => array(
                    'title' => $this->l('Input Type') ,
                    'align' => 'center',
                    'havingFilter' => true,
                ),
                'active' => array(
                    'title' => $this->l('Status'),
                    'active' => 'status',
                    'align' => 'center',
                    'type' => 'bool',
                    'orderby' => false,
                ),
            );
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function initToolBar()
    {
        $this->page_header_toolbar_btn['new'] = array(
                'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Add Extra Field'),
            );
        parent::initToolBar();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS(array(_MODULE_DIR_.$this->module->name.'/views/css/style.css'));
        $this->addJS(array(_MODULE_DIR_.$this->module->name.'/views/js/fieldform.js'));

        return true;
    }

    public function renderForm()
    {
        $obj_custom_field_type = new MarketplaceExtrafieldInputtype();
        $input_type_field = $obj_custom_field_type->findExtraFieldInputtype();
        $input_validation = $obj_custom_field_type->findExtraFieldInputtypeValidation();
        $this->context->smarty->assign('extrafieldinputtype', $input_type_field);
        $this->context->smarty->assign('extrafieldinputtypevalidation', $input_validation);

        // Set default lang at every form according to configuration multi-language
        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
            $this->context->smarty->assign('allow_multilang', 1);
        } else {
            $this->context->smarty->assign('allow_multilang', 0);
        }
        $this->context->smarty->assign(array(
            'languages' => Language::getLanguages(),
            'json_languages' => json_encode(Language::getLanguages()),
            'total_languages' => count(Language::getLanguages()),
            'current_lang' => Language::getLanguage((int)Configuration::get('PS_LANG_DEFAULT')),
            'json_current_lang' => json_encode(Language::getLanguage((int)Configuration::get('PS_LANG_DEFAULT'))),
            'multi_lang' => Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE'),
            'multi_def_lang_off' => Configuration::get('WK_MP_MULTILANG_DEFAULT_LANG')
        ));

        if ($this->display == 'add') {
            $this->context->smarty->assign(array(
                'set' => 1,
            ));
        } elseif ($this->display == 'edit') {
            $id = Tools::getValue('id');
            $obj_extrafield = new MarketplaceExtrafield();
            $extrafielddetail = $obj_extrafield->findExtraAttributeDetailById($id);
            $labelAndDefaultDetails = $obj_extrafield->getLabelAndDefaultValueDetailById($id);
            foreach ($labelAndDefaultDetails as $value) {
                $extrafielddetail['label_name'][$value['id_lang']] = $value['label_name'];
                $extrafielddetail['default_value'][$value['id_lang']] = $value['default_value'];
            }
            //d($extrafielddetail);
            $obj_custom_field_option = new MpExtrafieldOptions();
            $obj_custom_field_dropdown = new MarketplaceExtrafieldOptions();
            $values = $obj_custom_field_option->getCustomFieldOptions($id);
            $multiple_values = $obj_custom_field_dropdown->getCustomDropdownOptions($id);

            if ($multiple_values) {
                $i = 1;
                $options = array();
                foreach ($multiple_values as $value) {
                    $options[] = $i;
                    $i++;
                }
                $edit_max_options = implode($options, ',');

                if ($edit_max_options) {
                    $this->context->smarty->assign('edit_max_options', $edit_max_options);
                }
            }

            $this->context->smarty->assign(array(
                'set' => 0,
                'custom_field' => $extrafielddetail,
                'custom_field_values' => $values[0],
                'custom_multiple_field_values' => $multiple_values,
                ));
        }
        $this->fields_form = array(
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        return parent::renderForm();
    }

    public function postProcess()
    {
        if (!$this->loadObject(true)) {
            return;
        }

        parent::postProcess();
    }

    public function processSave()
    {
        if (Tools::isSubmit('submitAddmarketplace_extrafield')) {
            $this->processAdd();
        }
        //parent::processSave();
    }

    public function processAdd()
    {
        $obj_custom = new MarketplaceExtrafield();
        $page = Tools::getValue('page');
        $id = Tools::getValue('id');
        $inputtype = Tools::getValue('inputtype');
        $attribute_name = trim(Tools::getValue('attribute_name'));
        $field_req = Tools::getValue('field_req');
        $status_info = Tools::getValue('status_info');
        $char_limit = Tools::getValue('mp_char_limit');

        if ($inputtype > 2) {
            $default_value = 'N/A';
        }
        $as_placeholder = Tools::getValue('as_placeholder');
        $validationtype = Tools::getValue('validationtype');
        $multiple = Tools::getValue('multiple');
        $file_type = 0;
    
        if ($as_placeholder == 'on') {
            $as_placeholder = '1';
        } else {
            $as_placeholder = '0';
        }

        //If multi-lang is OFF then PS default lang will be default lang
        $default_lang = Configuration::get('PS_LANG_DEFAULT');
        if (!$id) {
            if (empty($inputtype)) {
                $this->errors[] = Tools::displayError($this->l('Please select input type'));
            }
            $exist_attr_name = $obj_custom->isAttributeNameRegister($page, $attribute_name);
            if ($exist_attr_name) {
                $this->errors[] = sprintf($this->l('Attribute name %s already exist.'), $attribute_name);
            }
        }

        //Label Name Validate
        if (!Tools::getValue('label_name_'.$default_lang)) {
            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                $admin_lang = Language::getLanguage((int) $default_lang);
                $this->errors[] = Tools::displayError($this->l('Label name is required in ').$admin_lang['name']);
            } else {
                $this->errors[] = Tools::displayError($this->l('Label name is required'));
            }
        } else {
            $label_name_error = 0;
            $default_value_error = 0;
            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                if (is_numeric(Tools::getValue('label_name_'.$language['id_lang']))) {
                    $label_name_error = 1;
                } elseif (Tools::strlen(Tools::getValue('label_name_'.$language['id_lang'])) > 250) {//David
                    $label_name_error = 2;
                }

                if (Tools::getValue('default_value_'.$language['id_lang'])) {
                    $default_value = Tools::getValue('default_value_'.$language['id_lang']);
                    if (Tools::strlen($default_value) > 50) {
                        $default_value_error = 1;
                    } elseif ($inputtype == 1 && $validationtype == 2 && !Validate::isEmail($default_value)) {
                        $default_value_error = 2;
                    } elseif ($inputtype == 1 && $validationtype == 3 && !is_numeric($default_value)) {
                        $default_value_error = 3;
                    } elseif ($inputtype == 1 && $validationtype == 1 && !Validate::isName($default_value)) {
                        $default_value_error = 4;
                    } elseif ($inputtype == 1 && $validationtype == 2 && Tools::strlen($default_value) > 128) {
                        $default_value_error = 5;
                    }
                }
            }

            if ($label_name_error == 1) {
                $this->errors[] = Tools::displayError($this->l('Label name should be string type'));
            } elseif ($label_name_error == 2) {
                $this->errors[] = Tools::displayError($this->l('Label name can not be more than 25 character'));
            }

            if ($default_value_error == 1) {
                $this->errors[] = Tools::displayError($this->l('Default value can not be more than 50 character'));
            } elseif ($default_value_error == 2) {
                $this->errors[] = Tools::displayError($this->l('Default value must be email type'));
            } elseif ($default_value_error == 3) {
                $this->errors[] = Tools::displayError($this->l('Default value must be numeric type'));
            } elseif ($default_value_error == 4) {
                $this->errors[] = Tools::displayError($this->l('Default value must be string type'));
            } elseif ($default_value_error == 5) {
                $this->errors[] = Tools::displayError($this->l('Default value must be less than 128 characters'));
            }

            if (empty($attribute_name)) {
                $this->errors[] = Tools::displayError($this->l('Provide attribute name for input type'));
            } elseif (!preg_match(Tools::cleanNonUnicodeSupport('/^[^0-9!<>,;?=+()@#"°{}$%:]*$/u'), Tools::stripslashes($attribute_name))) {
                $this->errors[] = Tools::displayError($this->l('Attribute name should be string type'));
            } elseif (Tools::strlen($attribute_name) > 15) {
                $this->errors[] = Tools::displayError($this->l('Attribute name can not be more than 15 character'));
            }
            $attribute_name = str_replace(' ', '_', $attribute_name);
        }

        if ($inputtype == 1) {
            // Textbox input type
            if ($char_limit > 250) {
                $this->errors[] = Tools::displayError($this->l('Maximum character limit is 250'));
            } elseif ($char_limit < 10) {
                $this->errors[] = Tools::displayError($this->l('Character length must be 10 or above'));
            }
            if (empty($validationtype)) {
                $this->errors[] = Tools::displayError($this->l('Provide validation type'));
            }
        }
        if ($inputtype == 2) {
            // Textarea input type
            if (empty($char_limit)) {
                $this->errors[] = Tools::displayError($this->l('Specify character limit'));
            } elseif ($char_limit < 10) {
                $this->errors[] = Tools::displayError($this->l('Character length must be 10 or above'));
            } elseif ($char_limit > 1000) {
                $this->errors[] = Tools::displayError($this->l('Maximum character limit is 1000'));
            }
        }

        if ($inputtype == 3) {
            // Dropdown input type
            $max_options = explode(',', Tools::getValue('max_options'));
            foreach ($max_options as $option) {
                if (!Tools::getValue('display_value_'.$option.'_'.$default_lang)) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $admin_lang = Language::getLanguage((int) $default_lang);
                        $this->errors[] = Tools::displayError($this->l('Drop Down Value is required in ').$admin_lang['name']);
                    } else {
                        $this->errors[] = Tools::displayError($this->l('Drop Down Value is required'));
                    }
                }
            }
        }

        if ($inputtype == 4) {
            // Checkbox input type
            $max_options = explode(',', Tools::getValue('max_options'));
            foreach ($max_options as $option) {
                if (!Tools::getValue('mp_check_val_'.$option.'_'.$default_lang)) {
                    if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                        $admin_lang = Language::getLanguage((int) $default_lang);
                        $this->errors[] = Tools::displayError($this->l('Enter Checkbox label in ').$admin_lang['name']);
                    } else {
                        $this->errors[] = Tools::displayError($this->l('Enter Checkbox label.'));
                    }
                }
            }
        }

        if ($inputtype == 5) {
            // File input type
            $file_image = Tools::getValue('file_image');
            $file_doc = Tools::getValue('file_doc');

            if ($file_image && $file_doc) {
                $file_type = 3;
            } elseif ($file_image && !$file_doc) {
                $file_type = $file_image;
            } elseif (!$file_image && $file_doc) {
                $file_type = $file_doc;
            }

            if (empty($file_image) && empty($file_doc)) {
                $this->errors[] = Tools::displayError($this->l('Select atleast one file type'));
            }
        }
        if ($inputtype == 6) {
            // Radio input type
            if (!Tools::getValue('radio_left_value_'.$default_lang)) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $admin_lang = Language::getLanguage((int) $default_lang);
                    $this->errors[] = Tools::displayError($this->l('Enter radio button value in ').$admin_lang['name']);
                } else {
                    $this->errors[] = Tools::displayError($this->l('Enter radio button value.'));
                }
            } elseif (!Tools::getValue('radio_right_value_'.$default_lang)) {
                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    $admin_lang = Language::getLanguage((int) $default_lang);
                    $this->errors[] = Tools::displayError($this->l('Enter radio button value in ').$admin_lang['name']);
                } else {
                    $this->errors[] = Tools::displayError($this->l('Enter radio button value.'));
                }
            }
        }

        if (empty($this->errors)) {
            if ($id) {
                $obj_custom = new MarketplaceExtrafield($id);
            } else {
                $obj_custom = new MarketplaceExtrafield();
            }
            $obj_custom->page = $page;
            $obj_custom->inputtype = $inputtype;
            $obj_custom->attribute_name = $attribute_name;
            $obj_custom->validation_type = $validationtype;
            $obj_custom->char_limit = $char_limit;
            $obj_custom->field_req = $field_req;
            $obj_custom->file_type = $file_type;
            $obj_custom->multiple = $multiple;
            $obj_custom->asplaceholder = $as_placeholder;
            $obj_custom->active = $status_info;

            foreach (Language::getLanguages(false) as $language) {
                $label_name_lang_id = $language['id_lang'];
                $default_value_lang_id = $language['id_lang'];

                if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                    if (!Tools::getValue('label_name_'.$language['id_lang'])) {
                        $label_name_lang_id = $default_lang;
                    }
                    if (!Tools::getValue('default_value_'.$language['id_lang'])) {
                        $default_value_lang_id = $default_lang;
                    }
                } else {
                    //if multilang is OFF then all fields will be filled as default lang content
                    $label_name_lang_id = $default_lang;
                    $default_value_lang_id = $default_lang;
                }

                $obj_custom->label_name[$language['id_lang']] = Tools::getValue('label_name_'.$label_name_lang_id);
                $obj_custom->default_value[$language['id_lang']] = Tools::getValue('default_value_'.$default_value_lang_id);
            }

            if ($obj_custom->save()) {
                $is_inserted = $obj_custom->id;
                $obj_mp_extra_fields_assoc = new MarketplaceExtrafieldAssociation();
                $is_exist = $obj_mp_extra_fields_assoc->isExistRecord($is_inserted);
                if ($is_exist) {
                    $obj_mp_extra_fields_assoc = new MarketplaceExtrafieldAssociation($is_exist['id']);
                }
                $obj_mp_extra_fields_assoc->extrafield_id = $is_inserted;
                $obj_mp_extra_fields_assoc->attribute_name = pSQL($attribute_name);
                $obj_mp_extra_fields_assoc->save();
                if ($inputtype == 6) {
                    // Radio input type processing
                    $obj_customfield_option = new MpExtrafieldOptions();
                    if ($id) {
                        $obj_customfield_option->deleteExtraFieldRadioOptionsById($is_inserted);
                    } else {
                        $obj_customfield_option = new MpExtrafieldOptions();
                    }
                    $obj_customfield_option->extrafield_id = $is_inserted;

                    foreach (Language::getLanguages(false) as $language) {
                        $radio_left_lang_id = $language['id_lang'];
                        $radio_right_lang_id = $language['id_lang'];

                        if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                            if (!Tools::getValue('radio_left_value_'.$language['id_lang'])) {
                                $radio_left_lang_id = $default_lang;
                            }
                            if (!Tools::getValue('radio_right_value_'.$language['id_lang'])) {
                                $radio_right_lang_id = $default_lang;
                            }
                        } else {
                            //if multilang is OFF then all fields will be filled as default lang content
                            $radio_left_lang_id = $default_lang;
                            $radio_right_lang_id = $default_lang;
                        }

                        $obj_customfield_option->left_value[$language['id_lang']] = Tools::getValue('radio_left_value_'.$radio_left_lang_id);
                        $obj_customfield_option->right_value[$language['id_lang']] = Tools::getValue('radio_right_value_'.$radio_right_lang_id);
                    }
                    $obj_customfield_option->save();
                }

                if ($inputtype == 4) {
                    // Checkbox input type processing
                    if ($id) {
                        $obj_customdropdown_option = new MarketplaceExtrafieldOptions();
                        $obj_customdropdown_option->deleteExtraFieldOptionsById($is_inserted);
                    }

                    $max_options = explode(',', Tools::getValue('max_options'));
                    foreach ($max_options as $option) {
                        $obj_customdropdown_option = new MarketplaceExtrafieldOptions();
                        $obj_customdropdown_option->extrafield_id = $is_inserted;

                        foreach (Language::getLanguages(false) as $language) {
                            $display_value_lang_id = $language['id_lang'];
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                if (!Tools::getValue('mp_check_val_'.$option.'_'.$language['id_lang'])) {
                                    $display_value_lang_id = $default_lang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $display_value_lang_id = $default_lang;
                            }
                            $obj_customdropdown_option->display_value[$language['id_lang']] = Tools::getValue('mp_check_val_'.$option.'_'.$display_value_lang_id);
                        }
                        $obj_customdropdown_option->save();
                    }
                }

                if ($inputtype == 3) {
                    // Dropdown type input processing
                    if ($id) {
                        $obj_customdropdown_option = new MarketplaceExtrafieldOptions();
                        $obj_customdropdown_option->deleteExtraFieldOptionsById($is_inserted);
                    }
                    $max_options = explode(',', Tools::getValue('max_options'));
                    foreach ($max_options as $option) {
                        $obj_customdropdown_option = new MarketplaceExtrafieldOptions();
                        $obj_customdropdown_option->extrafield_id = $is_inserted;

                        foreach (Language::getLanguages(false) as $language) {
                            $display_value_lang_id = $language['id_lang'];
                            if (Configuration::get('WK_MP_MULTILANG_ADMIN_APPROVE')) {
                                if (!Tools::getValue('display_value_'.$option.'_'.$language['id_lang'])) {
                                    $display_value_lang_id = $default_lang;
                                }
                            } else {
                                //if multilang is OFF then all fields will be filled as default lang content
                                $display_value_lang_id = $default_lang;
                            }

                            $obj_customdropdown_option->display_value[$language['id_lang']] = Tools::getValue('display_value_'.$option.'_'.$display_value_lang_id);
                        }
                        $obj_customdropdown_option->save();
                    }
                }
            }
            Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
        } else {
            if ($id) {
                $this->display = 'edit';
            } else {
                $this->display = 'add';
            }
        }
    }
}
