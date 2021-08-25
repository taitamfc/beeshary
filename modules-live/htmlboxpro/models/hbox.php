<?php

/**
 * ObjectModel for htmlboxpro module table.
 * @author MyPresta.eu | Milos "VEKIA" Myszczuk <support@mypresta.eu>
 */
class hbox extends ObjectModel
{
    public $id;
    public $position;
    public $hook;
    public $oconfirmation;
    public $active;
    public $logged;
    public $name;
    public $bssl;
    public $shop;
    public $homeonly;
    public $productsonly;
    public $selectedproducts;
    public $cmsonly;
    public $selectedcms;
    public $productscat;
    public $selected_pcats;
    public $productsman;
    public $selected_pmanufs;
    public $catsonly;
    public $selected_cats;
    public $manufsonly;
    public $selected_manufs;
    public $date;
    public $datefrom;
    public $dateto;
    public $urlonly;
    public $url;
    public $cgroup;
    public $hcgroup;
    public $search;
    public $query;
    public $cmscatsonly;
    public $selected_cmscats;
    public $body;
    public $supponly;
    public $selected_supp;

    public static $definition = array(
        'table' => 'hbp_block',
        'primary' => 'id',
        'multilang' => true,
        'fields' => array(
            'id' => array('type' => ObjectModel :: TYPE_INT),
            'position' => array('type' => ObjectModel :: TYPE_INT),
            'hook' => array('type' => ObjectModel :: TYPE_STRING),
            'active' => array('type' => ObjectModel :: TYPE_INT),
            'logged' => array('type' => ObjectModel :: TYPE_INT),
            'name' => array('type' => ObjectModel :: TYPE_STRING),
            'bssl' => array('type' => ObjectModel :: TYPE_INT),
            'shop' => array('type' => ObjectModel :: TYPE_INT),
            'homeonly' => array('type' => ObjectModel :: TYPE_INT),
            'productsonly' => array('type' => ObjectModel :: TYPE_INT),
            'selectedproducts' => array('type' => ObjectModel :: TYPE_STRING),
            'cmsonly' => array('type' => ObjectModel :: TYPE_INT),
            'selectedcms' => array('type' => ObjectModel :: TYPE_STRING),
            'productscat' => array('type' => ObjectModel :: TYPE_INT),
            'selected_pcats' => array('type' => ObjectModel :: TYPE_STRING),
            'productsman' => array('type' => ObjectModel :: TYPE_INT),
            'selected_pmanufs' => array('type' => ObjectModel :: TYPE_STRING),
            'catsonly' => array('type' => ObjectModel :: TYPE_INT),
            'selected_cats' => array('type' => ObjectModel :: TYPE_STRING),
            'manufsonly' => array('type' => ObjectModel :: TYPE_INT),
            'selected_manufs' => array('type' => ObjectModel :: TYPE_STRING),
            'date' => array('type' => ObjectModel :: TYPE_INT),
            'datefrom' => array('type' => ObjectModel :: TYPE_DATE),
            'dateto' => array('type' => ObjectModel :: TYPE_DATE),
            'urlonly' => array('type' => ObjectModel :: TYPE_INT),
            'url' => array('type' => ObjectModel :: TYPE_NOTHING),
            'cgroup' => array('type' => ObjectModel :: TYPE_INT),
            'hcgroup' => array('type' => ObjectModel :: TYPE_INT),
            'search' => array('type' => ObjectModel :: TYPE_INT),
            'query' => array('type' => ObjectModel :: TYPE_STRING),
            'oconfirmation' => array('type' => ObjectModel :: TYPE_INT),
            'cmscatsonly' => array('type' => ObjectModel :: TYPE_INT),
            'selected_cmscats' => array('type' => ObjectModel :: TYPE_STRING),
            'supponly' => array('type' => ObjectModel :: TYPE_INT),
            'selected_supp' => array('type' => ObjectModel :: TYPE_STRING),

            'body' => array('type' => ObjectModel :: TYPE_NOTHING, 'lang' => true),
        ),
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }
}