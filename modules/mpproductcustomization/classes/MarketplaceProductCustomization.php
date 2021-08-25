<?php

class MarketplaceProductCustomization extends ObjectModel
{
	public $id;
	public $id_mp_product;
	public $type;
	public $required;

	public $id_ps_shop;
	public $name;

	public static $definition = array(
		    'table' => 'mp_product_customization',
		    'primary' => 'id',
		    'multilang' => true,
		    'fields' => array(
		        'id_mp_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt' ,'required' => true),				
				'type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'required' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
                /*Lang field*/
				'name' => array('type' => self::TYPE_STRING, 'lang' => true, 'size' => 255),	
		    ),
		);

	public function getCustomizationFieldIds($mp_product_id)
    {
        return Db::getInstance()->executeS('
			SELECT `id`, `type`, `required`
			FROM `'._DB_PREFIX_.'mp_product_customization`
			WHERE `id_mp_product` = '.(int)$mp_product_id);
    }

    protected function defineLabel($languages, $type, $mp_product_id)
    {
        // Label insertion
        if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'mp_product_customization` (`id_mp_product`, `type`, `required`)
			VALUES ('.(int)$mp_product_id.', '.(int)$type.', 0)') ||
            !$id = (int)Db::getInstance()->Insert_ID()) {
            return false;
        }

        // Multilingual label name creation
        $values = '';

        foreach ($languages as $language) {
            foreach (Shop::getContextListShopID() as $id_shop) {
                $values .= '('.(int)$id.', '.(int)$language['id_lang'].', '.$id_shop .',\'\'), ';
            }
        }

        $values = rtrim($values, ', ');
        if (!Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'mp_product_customization_lang` (`id`, `id_lang`, `id_ps_shop`, `name`)
			VALUES '.$values)) {
            return false;
        }

        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', '1');
        
        return true;
    }

    public function createLabels($uploadable_files, $text_fields, $mp_product_id)
    {
        $languages = Language::getLanguages();
        if ((int)$uploadable_files > 0) {
            for ($i = 0; $i < (int)$uploadable_files; $i++) {
                if (!$this->defineLabel($languages, Product::CUSTOMIZE_FILE, $mp_product_id)) {
                    return false;
                }
            }
        }

        if ((int)$text_fields > 0) {
            for ($i = 0; $i < (int)$text_fields; $i++) {
                if (!$this->defineLabel($languages, Product::CUSTOMIZE_TEXTFIELD, $mp_product_id)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function updateLebel($id, $lebel, $lang, $required)
    {   
        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'mp_product_customization_lang`
            SET `name` ="'.pSQL($lebel).'"
            WHERE `id` ='.(int)$id.'
            AND `id_lang` ='.(int)$lang);
        
        Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'mp_product_customization`
            SET `required` ='.(int)$required.'
            WHERE `id` ='.(int)$id);
        
    }

    public function getLangFieldValue($id,$id_lang = false) 
    {
        if (!$result = Db::getInstance()->executeS('
            SELECT mpc.`id` AS id, mpc.`type` AS type , mpc.`required` AS required, 
            mpcl.`name` AS name, mpcl.`id_lang` AS id_lang
            FROM `'._DB_PREFIX_.'mp_product_customization` mpc
            NATURAL JOIN `'._DB_PREFIX_.'mp_product_customization_lang` mpcl
            WHERE mpc.`id_mp_product` = '.(int)$id.($id_lang ? ' AND mpcl.`id_lang` = '.(int)$id_lang : '').'
            ORDER BY mpc.`id`')) {
            return false;
        }

     
        $customization_fields = array();
        foreach ($result as $row) {
            $customization_fields[(int)$row['type']][(int)$row['id']][(int)$row['id_lang']] = $row;
        }

        return $customization_fields;
    }

    public function updatefields($mp_product_id, $file, $text)
    {

        /* Get customization field ids */
        if (($result = Db::getInstance()->executeS(
            'SELECT `id`, `type`
            FROM `'._DB_PREFIX_.'mp_product_customization`
            WHERE `id_mp_product` = '.(int)$mp_product_id.'
            ORDER BY `id`')
        ) === false) {
            return false;
        }

        if (empty($result)) {
            return true;
        }

        $customization_fields = array(
            Product::CUSTOMIZE_FILE => array(),
            Product::CUSTOMIZE_TEXTFIELD => array()
        );

        foreach ($result as $row) {
            $customization_fields[(int)$row['type']][] = (int)$row['id'];
        }

        
        /* If too much inside the database, deletion */
        if ($file > 0 && count($customization_fields[Product::CUSTOMIZE_FILE]) - $file >= 0 &&
        (!Db::getInstance()->execute('
            DELETE `'._DB_PREFIX_.'mp_product_customization`,`'._DB_PREFIX_.'mp_product_customization_lang`
            FROM `'._DB_PREFIX_.'mp_product_customization` JOIN `'._DB_PREFIX_.'mp_product_customization_lang`
            WHERE `'._DB_PREFIX_.'mp_product_customization`.`id_mp_product` = '.(int)$mp_product_id.'
            AND `'._DB_PREFIX_.'mp_product_customization`.`type` = '.Product::CUSTOMIZE_FILE.'
            AND `'._DB_PREFIX_.'mp_product_customization_lang`.`id` = `'._DB_PREFIX_.'mp_product_customization`.`id`
            AND `'._DB_PREFIX_.'mp_product_customization`.`id` >= '.(int)$customization_fields
            [Product::CUSTOMIZE_FILE][count($customization_fields[Product::CUSTOMIZE_FILE]) - $file]
        ))) {
            return false;
        }

        if ($text > 0 && count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $text >= 0 &&
        (!Db::getInstance()->execute(
            'DELETE `'._DB_PREFIX_.'mp_product_customization`,`'._DB_PREFIX_.'mp_product_customization_lang`
            FROM `'._DB_PREFIX_.'mp_product_customization` JOIN `'._DB_PREFIX_.'mp_product_customization_lang`
            WHERE `'._DB_PREFIX_.'mp_product_customization`.`id_mp_product` = '.(int)$mp_product_id.'
            AND `'._DB_PREFIX_.'mp_product_customization`.`type` = '.Product::CUSTOMIZE_TEXTFIELD.'
            AND `'._DB_PREFIX_.'mp_product_customization_lang`.`id` = `'._DB_PREFIX_.'mp_product_customization`.`id`
            AND `'._DB_PREFIX_.'mp_product_customization`.`id` >= '.(int)$customization_fields
            [Product::CUSTOMIZE_TEXTFIELD][count($customization_fields[Product::CUSTOMIZE_TEXTFIELD]) - $text]
        ))) {
            return false;
        }

        // Refresh cache of feature detachable
        Configuration::updateGlobalValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', Customization::isCurrentlyUsed());

        return true;
    }

    public function deleteMpProductCustomization($mp_product_id)
    {
        return (
            Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'mp_product_customization`
                WHERE `id_mp_product` = '.(int)$mp_product_id
            )
            &&
            Db::getInstance()->execute(
                'DELETE `'._DB_PREFIX_.'mp_product_customization_lang` 
                FROM `'._DB_PREFIX_.'mp_product_customization_lang` 
                LEFT JOIN `'._DB_PREFIX_.'mp_product_customization`
                ON ('._DB_PREFIX_.'mp_product_customization.id = '._DB_PREFIX_.'mp_product_customization_lang.id)
                WHERE '._DB_PREFIX_.'mp_product_customization.id IS NULL'
            )
        );
    }

    public function updatePsNameField($mp_product_id, $ps_product_id) 
    {
        if ( !$result = Db::getInstance()->executeS(
            'SELECT mpcl.`name` AS name, mpcl.`id_lang` AS id_lang ,mpc.`id` AS id ,mpc.`required` AS required
            FROM `'._DB_PREFIX_.'mp_product_customization` mpc 
            LEFT JOIN `'._DB_PREFIX_.'mp_product_customization_lang` mpcl ON (mpc.`id` = mpcl.`id`)
            WHERE mpc.`id_mp_product` = '.(int)$mp_product_id.'
            ORDER BY mpc.`id`,mpcl.`id_lang`')) {
            return false;
        }

        if (!$ps_result = Db::getInstance()->executeS(
            'SELECT cfl.`id_customization_field` AS id , cf.`id_customization_field` AS id_ps
            FROM `'._DB_PREFIX_.'customization_field` cf 
            LEFT JOIN `'._DB_PREFIX_.'customization_field_lang` cfl 
            ON (cf.`id_customization_field` = cfl.`id_customization_field`)
            WHERE cf.`id_product` = '.(int)$ps_product_id.'
            ORDER BY cf.`id_customization_field`,cfl.`id_lang`')) {
            return flase;
        }

        $ps_name = 0;
        foreach ($result as $value) {
          if(!Db::getInstance()->execute('
            UPDATE `'._DB_PREFIX_.'customization_field_lang`
            SET `name` ="'.pSQL($value['name']).'"
            WHERE `id_customization_field` ='.(int)$ps_result[$ps_name++]['id'].'
            AND `id_lang` ='.(int)$value['id_lang'])) {
            return false;
          }
        }
        $ps_require = 0; 
        foreach ($result as  $value) {
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'customization_field`
                SET `required` ='.(int)$value['required'].'
                WHERE `id_customization_field` ='.(int)$ps_result[$ps_require++]['id_ps']);
        }
     return true;
    }
}