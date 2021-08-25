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
*  @copyright 2010-2016 Webkul IN
*  @license   https://store.webkul.com/license.html
*/

class AdminMpAddNewBadgeController extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'mp_badges';
        $this->className = 'MpBadge';
        $this->lang = false;
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->context = Context::getContext();
        $this->_select = 'a.id as id_badge';
        $this->identifier = 'id';
        parent::__construct();
        $this->fields_list = [
            'id' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'id_badge' => [
                'title' => $this->l('Badge Image'),
                'align' => 'center',
                'callback' => 'displayBadgeImage',
                'orderby' => false,
                'search' => false,
            ],
            'badge_name' => [
                'title' => $this->l('Badge Name'),
                'align' => 'center',
            ],
            'badge_desc' => [
                'title' => $this->l('Badge Description'),
                'align' => 'center',
            ],
            'badge_is_partner' => [
                'title' => $this->l('Is partner'),
                'active' => 'badge_is_partner',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => true,
            ],
            'active' => [
                'title' => $this->l('Status'),
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => true,
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
            ],
        ];
    }

    public function displayBadgeImage($id_badge)
    {
        $this->context->smarty->assign(
            [
                'id_badge' => $id_badge,
                'modules_dir' => _MODULE_DIR_,
            ]
        );

        return $this->createTemplate('_display_badge_image.tpl')->fetch();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        $this->page_header_toolbar_btn['new'] = [
            'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
            'desc' => $this->l('Add new badge'),
        ];
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        return parent::renderList();
    }

    public function renderForm()
    {
        $id = Tools::getValue('id');

        $image = _PS_ROOT_DIR_.'/modules/mpbadgesystem/views/img/badge_img/'.$id.'.jpg';
        $image_url = ImageManager::thumbnail($image, $this->table.'_'.(int) $id.'.jpg', 350, 'jpg', true, true);
        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

		/*Badge Banner Profile*/
		$image_file = _PS_ROOT_DIR_.'/modules/mpbadgesystem/views/img/badge_img/'.$id.'_banner_user_detail.png';
		$badge_banner_url = ImageManager::thumbnail($image_file, $this->table.'_'.(int) $id.'_banner_user_detail.png', 350, 'png', true, true);
		$badge_banner_size = file_exists($image_file) ? filesize($image_file) / 1000 : false;
		
		/*Badge Banner Category*/
		$image_file = _PS_ROOT_DIR_.'/modules/mpbadgesystem/views/img/badge_img/'.$id.'_banner_cat_detail.png';
		$badge_banner_category_url = ImageManager::thumbnail($image_file, $this->table.'_'.(int) $id.'_banner_cat_detail.png', 350, 'png', true, true);
		$badge_banner_category_size = file_exists($image_file) ? filesize($image_file) / 1000 : false;
		
		/*Badge Watermark Product Detail*/
		$image_file = _PS_ROOT_DIR_.'/modules/mpbadgesystem/views/img/badge_img/'.$id.'_watermark_p_detail.png';
		$badge_watermark_url = ImageManager::thumbnail($image_file, $this->table.'_'.(int) $id.'_watermark_p_detail.png', 350, 'png', true, true);
		$badge_watermark_size = file_exists($image_file) ? filesize($image_file) / 1000 : false;
		
		/*Badge Watermark Product List*/
		$image_file = _PS_ROOT_DIR_.'/modules/mpbadgesystem/views/img/badge_img/'.$id.'_watermark_p_list.png';
		$badge_watermark_product_list_url = ImageManager::thumbnail($image_file, $this->table.'_'.(int) $id.'_watermark_p_list.png', 350, 'png', true, true);
		$badge_watermark_product_list_size = file_exists($image_file) ? filesize($image_file) / 1000 : false;
		

        $this->fields_form = [
            'legend' => [
                'title' => $this->l('Add new Badge'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'name' => 'badge_name',
                    'label' => $this->l('Badge Name'),
                    'required' => true,
                ],
                [
                    'type' => 'textarea',
                    'name' => 'badge_desc',
                    'label' => $this->l('Badge Description'),
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'name' => 'badge_link',
                    'label' => $this->l('Badge Link'),
                    'required' => false,
                ],
                [
                    'type' => 'file',
                    'name' => 'badge_image',
                    'label' => $this->l('Badge Image'),
                    'required' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                    'display_image' => true,
                ],
                [
                    'type' => 'file',
                    'name' => 'badge_banner',
                    'label' => $this->l('Badge Banner Profile'),
                    'required' => false,
					'image' => $badge_banner_url ? $badge_banner_url : false,
                    'size' => $badge_banner_size,
                    'display_image' => true,
                ],
                [
                    'type' => 'file',
                    'name' => 'badge_banner_category',
                    'label' => $this->l('Badge Banner Category'),
                    'required' => false,
					'image' => $badge_banner_category_url ? $badge_banner_category_url : false,
                    'size' => $badge_banner_category_size,
                    'display_image' => true,
                ],
                [
                    'type' => 'file',
                    'name' => 'badge_watermark',
                    'label' => $this->l('Badge Watermark Product Detail'),
                    'required' => false,
					'image' => $badge_watermark_url ? $badge_watermark_url : false,
                    'size' 	=> $badge_watermark_size,
                    'display_image' => true,
                ],
                [
                    'type' => 'file',
                    'name' => 'badge_watermark_product_list',
                    'label' => $this->l('Badge Watermark Product List'),
                    'required' => false,
					'image' => $badge_watermark_product_list_url ? $badge_watermark_product_list_url : false,
                    'size' 	=> $badge_watermark_product_list_size,
                    'display_image' => true,
                ],
                [
                    'type' => 'select',
                    'name' => 'badge_is_partner',
                    'label' => $this->l('Is partner'),
                    'required' => false,
                    'options' => array(
						'query' => [
							array(
								'id_option' => 0,
								'name' => 'No'
							),
							array(
							   'id_option' => 1,
								'name' => 'Yes'
							)
						],
						'id' => 'id_option',
						'name' => 'name'
					)
					
                ],
                [
                    'type' => 'color',
                    'name' => 'badge_color',
                    'label' => $this->l('Border Color'),
                    'required' => false,
                    
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
            ],
        ];

        return parent::renderForm();
    }

    public function processSave()
    {
        $id = Tools::getValue('id');
        $badgeName = Tools::getValue('badge_name');
        $badgeDesc = Tools::getValue('badge_desc');
        $badgeLink = Tools::getValue('badge_link');
        $badgeBanner = Tools::getValue('badge_banner');
        $badgeWatermark = Tools::getValue('badge_watermark');
        if ($badgeName == '') {
            $this->errors[] = Tools::displayError('Badge name is required');
        } else {
            if (!Validate::isGenericName($badgeName)) {
                $this->errors[] = Tools::displayError($this->l('Badge name must not have Invalid characters <>;=#{}'));
            }
        }

        if ($badgeDesc == '') {
            $this->errors[] = Tools::displayError('Badge description is required');
        } else {
            if (!Validate::isGenericName($badgeDesc)) {
                $this->errors[] = Tools::displayError($this->l('Badge description must not have Invalid characters <>;=#{}'));
            }
        }
		

        if (!$id) {
            if ($_FILES['badge_image']['name'] == '') {
                $this->errors[] = Tools::displayError('Badge image is required');
            }
        }
        if (empty($this->errors)) {
            $objMpBadge = new MpBadge();
            $objMpBadge->badge_name 		= $badgeName;
            $objMpBadge->badge_desc 		= $badgeDesc;
            $objMpBadge->badge_link 		= $badgeLink;
            $objMpBadge->badge_is_partner 	= Tools::getValue('badge_is_partner');
            $objMpBadge->badge_color 		= Tools::getValue('badge_color');
			

			if ($id) {
				
				$mpBadgeInfo = $objMpBadge->getBadgeInfo($id);
				$objMpBadge->badge_banner 		= $mpBadgeInfo[0]['badge_banner'];
				$objMpBadge->badge_watermark 	= $mpBadgeInfo[0]['badge_watermark'];
				$objMpBadge->badge_banner_category 	= $mpBadgeInfo[0]['badge_banner_category'];
				$objMpBadge->badge_watermark_product_list 	= $mpBadgeInfo[0]['badge_watermark_product_list'];
				$objMpBadge->active 	= $mpBadgeInfo[0]['active'];
	
				
				if ( ImageManager::isCorrectImageFileExt($_FILES['badge_image']['name']) ) {
                    $imagesize = getimagesize($_FILES['badge_image']['tmp_name']);
                    $imgWidth = $imagesize[0];
                    $imgHeight = $imagesize[1];
                    if ($imgWidth < 50 || $imgWidth > 500 || $imgHeight < 50 || $imgHeight > 500) {
                        $this->errors[] = Tools::displayError('Please upload image with dimensions between 50*50 to 500*500 px');
                    } else {
                        $objMpBadge->id = $id;
                        $objMpBadge->save();
                        $badge_id = $objMpBadge->id;
                        $image_name = $badge_id.'.jpg';
                        $dir = _PS_MODULE_DIR_.'mpbadgesystem/views/img/badge_img/';
                        ImageManager::resize($_FILES['badge_image']['tmp_name'], $dir.$image_name);
                    }
                } else {
                    $this->errors[] = Tools::displayError('Please upload image with extensions jpg, jpeg, gif or png.');
                }
				
				$badge_id = $id;
				
				/*Badge Banner Profile*/
				if ( $_FILES['badge_banner']['name'] != '' ) {
					$image_name = $badge_id.'_banner_user_detail.png';
					$dir = _PS_MODULE_DIR_.'mpbadgesystem/views/img/badge_img/';
					ImageManager::resize($_FILES['badge_banner']['tmp_name'], $dir.$image_name,null,null,'png');
					
					$objMpBadge->badge_banner = '/modules/mpbadgesystem/views/img/badge_img/'.$image_name;
				}
				
				/*Badge Banner Category*/
				if ( $_FILES['badge_banner_category']['name'] != ''  ) {
					$image_name = $badge_id.'_banner_cat_detail.png';
					$dir = _PS_MODULE_DIR_.'mpbadgesystem/views/img/badge_img/';
					ImageManager::resize($_FILES['badge_banner_category']['tmp_name'], $dir.$image_name,null,null,'png');
					
					$objMpBadge->badge_banner_category = '/modules/mpbadgesystem/views/img/badge_img/'.$image_name;
				}
				
				/*Badge Watermark Product Detail*/
				if ( $_FILES['badge_watermark']['name'] != ''  ) {
					$image_name = $badge_id.'_watermark_p_detail.png';
					$dir = _PS_MODULE_DIR_.'mpbadgesystem/views/img/badge_img/';
					ImageManager::resize($_FILES['badge_watermark']['tmp_name'], $dir.$image_name,null,null,'png');
					
					$objMpBadge->badge_watermark = '/modules/mpbadgesystem/views/img/badge_img/'.$image_name;
				}
				
				/*Badge Watermark Product List*/
				if ( $_FILES['badge_watermark_product_list']['name'] != ''  ) {
					$image_name = $badge_id.'_watermark_p_list.png';
					$dir = _PS_MODULE_DIR_.'mpbadgesystem/views/img/badge_img/';
					ImageManager::resize($_FILES['badge_watermark_product_list']['tmp_name'], $dir.$image_name,null,null,'png');
					
					$objMpBadge->badge_watermark_product_list = '/modules/mpbadgesystem/views/img/badge_img/'.$image_name;
				}
				
				$objMpBadge->id = $id;

				$objMpBadge->save();
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token.'&conf=4');
			} else {
				$this->errors[] = Tools::displayError('Cannot upload image.');
			}

			
            if (!empty($this->errors)) {
                $this->display = 'add';
            }
        } else {
            $this->display = 'add';
        }
    }

    public function postProcess()
    {
        if ($this->display == 'view') {
            $badgeId = Tools::getValue('id');
            $objMpBadge = new MpBadge();
            $mpBadgeInfo = $objMpBadge->getBadgeInfo($badgeId);
            $this->context->smarty->assign('mp_badge_info', $mpBadgeInfo[0]);
        }
        parent::postProcess();
    }

    public function ajaxProcessAddSellerBadge()
    {
        $mpIdSeller = Tools::getValue('mp_id_seller');
        $badgeIds = Tools::getValue('badge_id');
        $objSellerBadge = new MpSellerBadges();
        $objSellerBadge->deletePrevSellerBadges($mpIdSeller);
        $error = [];
        foreach ($badgeIds as $badge) {
            $objSellerBadge->badge_id = $badge;
            $objSellerBadge->mp_seller_id = $mpIdSeller;
            $result = $objSellerBadge->add();
            if (!$result) {
                $error[] = $badge;
            }
        }
        if (empty($error)) {
            die('1');
        } else {
            die('0');
        }
    }

    public function ajaxProcessRemoveSellerBadge()
    {
        $mpIdSeller = Tools::getValue('mp_id_seller');
        $badgeId = Tools::getValue('badge_id');
        $objSellerBadge = new MpSellerBadges();
        $result = $objSellerBadge->deleteSellerBadge($mpIdSeller, $badgeId);
        if ($result) {
            die('1');
        } else {
            die('0');
        }
    }
}

