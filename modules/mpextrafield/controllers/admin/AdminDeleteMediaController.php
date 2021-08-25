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

class AdminDeleteMediaController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        if (Tools::getValue('id_attachment')) {
            $download_id = Tools::getValue('id_attachment');
            $this->downloadContent($download_id);
        } elseif (Tools::getValue('id_delete')) {
            $id = Tools::getValue('id_delete');
            $this->deleteAdminContent($id);
        }
    }
    /**
     * [downloadContent -> download media content].
     *
     * @param [type] $download_id [file id]
     *
     * @return [type] [description]
     */
    public function downloadContent($download_id)
    {
        $link = new Link();
        $custom_values = new MarketplaceExtrafieldValue($download_id);
        if ($custom_values) {
            $file_path = _PS_MODULE_DIR_.'mpextrafield/views/img/'.$custom_values->field_value;
            if (!file_exists($file_path)) {
                $this->context->controller->errors[] = Tools::displayError('Image Not exist');
                Tools::redirect($link->getModuleLink('marketplace', 'productupdate', array(
                    'id' => $custom_values->marketplace_product_id,
                    'edited_conf' => 1,
                    )));
            } else {
                set_time_limit(0);
                $this->downloadFile($file_path, ''.$custom_values->field_value.'', 'text/plain');
            }
        }
    }

    /**
     * [downloadFile -> php download code].
     *
     * @param [type] $file      [file]
     * @param [type] $name      [name of file]
     * @param string $mime_type [file type (format)]
     *
     * @return [type] [description]
     */
    public function downloadFile($file, $name, $mime_type = '')
    {
        if (!is_readable($file)) {
            die('File not found or inaccessible!');
        }
        $size = filesize($file);
        $name = rawurldecode($name);
        $known_mime_types = array(
            'pdf' => 'application/pdf',
            'txt' => 'text/plain',
            'html' => 'text/html',
            'htm' => 'text/html',
            'exe' => 'application/octet-stream',
            'zip' => 'application/zip',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'jpeg' => 'image/jpg',
            'jpg' => 'image/jpg',
            'php' => 'text/plain',
            );
        if ($mime_type == '') {
            $file_extension = Tools::strtolower(Tools::substr(strrchr($file, '.'), 1));
            if (array_key_exists($file_extension, $known_mime_types)) {
                $mime_type = $known_mime_types[$file_extension];
            } else {
                $mime_type = 'application/force-download';
            }
        }
        @ob_end_clean();
        if (ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }
        header('Content-Type: '.$mime_type);
        header('Content-Disposition: attachment; filename="'.$name.'"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        header('Cache-control: private');
        header('Pragma: private');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        if (isset($_SERVER['HTTP_RANGE'])) {
            list($a, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            list($range) = explode(',', $range, 2);
            list($range, $range_end) = explode('-', $range);
            $range = (int) $range;
            if (!$range_end) {
                $range_end = $size - 1;
            } else {
                $range_end = (int) $range_end;
            }
            $new_length = $range_end - $range + 1;
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: $new_length");
            header("Content-Range: bytes $range-$range_end/$size");
        } else {
            $new_length = $size;
            header('Content-Length: '.$size);
        }
        $chunksize = 1 * (1024 * 1024);
        $bytes_send = 0;
        if ($file = fopen($file, 'r')) {
            if (isset($_SERVER['HTTP_RANGE'])) {
                fseek($file, $range);
            }
            while (!feof($file) && (!connection_aborted()) && ($bytes_send < $new_length)) {
                $buffer = fread($file, $chunksize);
            }
            print($buffer);
            flush();
            $bytes_send += Tools::strlen($buffer);
        }
        if ($a) {
            fclose($file);
        } else {
            fclose($file);
        }
    }

    public function deleteAdminContent($delete_id)
    {
        $link = new Link();
        $custom_values = new MarketplaceExtrafieldValue($delete_id);
        if ($custom_values) {
            //$mp_prod_id = $custom_values->marketplace_product_id;
            $file_path = _PS_MODULE_DIR_.'mpextrafield/views/img/'.$custom_values->field_value;
            if (!file_exists($file_path)) {
                $this->context->controller->errors[] = Tools::displayError('Image Not exist');
            } else {
                unlink($file_path);
            }
            $custom_values->field_value = 0;
            $custom_values->update();
            Tools::redirectAdmin();
        } else {
            Tools::redirectAdmin();
        }
    }
}
