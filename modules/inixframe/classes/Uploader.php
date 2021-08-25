<?php

/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Class UploaderCore
 */
class UploaderCore
{
    /**
     *
     */
    const DEFAULT_MAX_SIZE = 10485760;

    /**
     * @var
     */
    private $_accept_types;
    /**
     * @var
     */
    private $_files;
    /**
     * @var
     */
    private $_max_size;
    /**
     * @var
     */
    private $_name;
    /**
     * @var
     */
    private $_save_path;

    /**
     * @param null $name
     */
    public function __construct($name = null)
    {
        $this->setName($name);
        $this->files = array();
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setAcceptTypes($value)
    {
        $this->_accept_types = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAcceptTypes()
    {
        return $this->_accept_types;
    }

    /**
     * @param null $file_name
     *
     * @return string
     */
    public function getFilePath($file_name = null)
    {
        if (!isset($file_name)) {
            return tempnam($this->getSavePath(), $this->getUniqueFileName());
        }

        return $this->getSavePath() . $file_name;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        if (!isset($this->_files)) {
            $this->_files = array();
        }

        return $this->_files;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setMaxSize($value)
    {
        $this->_max_size = intval($value);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxSize()
    {
        if (!isset($this->_max_size)) {
            $this->setMaxSize(self::DEFAULT_MAX_SIZE);
        }

        return $this->_max_size;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setName($value)
    {
        $this->_name = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setSavePath($value)
    {
        $this->_save_path = $value;

        return $this;
    }

    /**
     * @return int|null|string
     */
    public function getPostMaxSizeBytes()
    {
        $post_max_size = ini_get('post_max_size');
        $bytes         = trim($post_max_size);
        $last          = strtolower($post_max_size[strlen($post_max_size) - 1]);

        switch ($last) {
            case 'g':
                $bytes *= 1024;
            case 'm':
                $bytes *= 1024;
            case 'k':
                $bytes *= 1024;
        }

        if ($bytes == '') {
            $bytes = null;
        }

        return $bytes;
    }

    /**
     * @return string
     */
    public function getSavePath()
    {
        if (!isset($this->_save_path)) {
            $this->setSavePath(_PS_UPLOAD_DIR_);
        }

        return $this->_normalizeDirectory($this->_save_path);
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function getUniqueFileName($prefix = 'PS')
    {
        return uniqid($prefix, true);
    }

    /**
     * @param null $dest
     *
     * @return array
     */
    public function process($dest = null)
    {
        $upload = isset($_FILES[$this->getName()]) ? $_FILES[$this->getName()] : null;

        if ($upload && is_array($upload['tmp_name'])) {
            $tmp = array();

            foreach ($upload['tmp_name'] as $index => $value) {
                $tmp[$index] = array(
                    'tmp_name' => $upload['tmp_name'][$index],
                    'name'     => $upload['name'][$index],
                    'size'     => $upload['size'][$index],
                    'type'     => $upload['type'][$index],
                    'error'    => $upload['error'][$index]
                );

                $this->files[] = $this->upload($tmp[$index], $dest);
            }
        } elseif ($upload) {
            $this->files[] = $this->upload($upload, $dest);
        }

        return $this->files;
    }

    /**
     * @param      $file
     * @param null $dest
     *
     * @return mixed
     */
    public function upload($file, $dest = null)
    {
        if ($this->validate($file)) {
            if (isset($dest) && is_dir($dest)) {
                $file_path = $dest;
            } else {
                $file_path = $this->getFilePath(isset($dest) ? $dest : $file['name']);
            }

            if ($file['tmp_name'] && is_uploaded_file($file['tmp_name'])) {
                move_uploaded_file($file['tmp_name'], $file_path);
            } else // Non-multipart uploads (PUT method support)
            {
                file_put_contents($file_path, fopen('php://input', 'r'));
            }

            $file_size = $this->_getFileSize($file_path, true);

            if ($file_size === $file['size']) {
                $file['save_path'] = $file_path;
            } else {
                $file['size'] = $file_size;
                unlink($file_path);
                $file['error'] = Tools::displayError('Server file size is different from local file size');
            }
        }

        return $file;
    }

    /**
     * @param $error_code
     *
     * @return array|int|string
     */
    protected function checkUploadError($error_code)
    {
        $error = 0;
        switch ($error_code) {
            case 1:
                $error = Tools::displayError(sprintf('The uploaded file exceeds %s', ini_get('post_max_size')));
                break;
            case 2:
                $error = Tools::displayError(sprintf(
                    'The uploaded file exceeds %s',
                    Tools::formatBytes((int) $_POST['MAX_FILE_SIZE'])
                ));
                break;
            case 3:
                $error = Tools::displayError('The uploaded file was only partially uploaded');
                break;
            case 4:
                $error = Tools::displayError('No file was uploaded');
                break;
            case 6:
                $error = Tools::displayError('Missing temporary folder');
                break;
            case 7:
                $error = Tools::displayError('Failed to write file to disk');
                break;
            case 8:
                $error = Tools::displayError('A PHP extension stopped the file upload');
                break;
            default;
                break;
        }

        return $error;
    }

    /**
     * @param $file
     *
     * @return bool
     */
    protected function validate(&$file)
    {
        $file['error'] = $this->checkUploadError($file['error']);

        $post_max_size = $this->getPostMaxSizeBytes();

        if ($post_max_size && ($this->_getServerVars('CONTENT_LENGTH') > $post_max_size)) {
            $file['error'] = Tools::displayError('The uploaded file exceeds the post_max_size directive in php.ini');

            return false;
        }

        if (preg_match('/\%00/', $file['name'])) {
            $file['error'] = Tools::displayError('Invalid file name');

            return false;
        }

        $types = $this->getAcceptTypes();

        //TODO check mime type.
        if (isset($types) && !in_array(pathinfo($file['name'], PATHINFO_EXTENSION), $types)) {
            $file['error'] = Tools::displayError('Filetype not allowed');

            return false;
        }

        if ($file['size'] > $this->getMaxSize()) {
            $file['error'] = Tools::displayError('File is too big');

            return false;
        }

        return true;
    }

    /**
     * @param            $file_path
     * @param bool|false $clear_stat_cache
     *
     * @return int
     */
    protected function _getFileSize($file_path, $clear_stat_cache = false)
    {
        if ($clear_stat_cache) {
            clearstatcache(true, $file_path);
        }

        return filesize($file_path);
    }

    /**
     * @param $var
     *
     * @return string
     */
    protected function _getServerVars($var)
    {
        return (isset($_SERVER[$var]) ? $_SERVER[$var] : '');
    }

    /**
     * @param $directory
     *
     * @return string
     */
    protected function _normalizeDirectory($directory)
    {
        $last = $directory[strlen($directory) - 1];

        if (in_array($last, array('/', '\\'))) {
            $directory[strlen($directory) - 1] = DIRECTORY_SEPARATOR;

            return $directory;
        }

        $directory .= DIRECTORY_SEPARATOR;

        return $directory;
    }
}
