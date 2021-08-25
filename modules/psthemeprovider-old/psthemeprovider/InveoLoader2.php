<?php
/**
  *  @name      Inveo Loader 2
  *  @author    Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2009-2017 Inveo s.r.o.
  *  @license   EULA
  */

if(!defined('INVEOLOADER2_LOADED'))
{

define('INVEOLOADER2_LOADED', true);

/** @class: InveoLoader2
  * @project: Inveo Framework, branch: lightweight
  * @date: 2015-03-10
  * @compatibility: PHP 5 >= 5.0.0
  * @version: 2.2.4
  */
class InveoLoader2 {

	/**
	  * Component name
	  * @const String
	  */
	const COMPONENT_NAME = 'Inveo Loader2';
	
	/**
	  * File identificator (prefix)
	  * @var String
	  *
	  * @access private
	  */
	private $_fileId = '';

	/**
	  * Absolute path to directory with pkgs
	  * @var String
	  *
	  * @access private
	  */
	private $_dirAbsPath = '';

	/**
	  * Product name we are loading a package
	  * @var String
	  *
	  * @access private
	  */
	private $_productName = '';
	
	/**
	  * Array of loaders and their files
	  * @var Array
	  *
	  * @access private
	  */
	private $_pkgFilesAr = array();

	/**
	  * PHP branch we run
	  * @var String
	  *
	  * @access private
	  */
	private $_phpBranch = '';

	/**
	  * Constructor for InveoLoader2 class.
	  * @access constructor
	  * @param String $fileId - file identificator
	  * @param String $dirAbsPath - absolute path to directory with pkgs
	  * @param String $productName - product name
	  */
	public function __construct($fileId, $dirAbsPath, $productName = 'Anonymous component')
	{
		$this->_fileId = $fileId;
		$this->_dirAbsPath = $dirAbsPath;
		$this->_productName = $productName;
		$this->_phpBranch = substr(PHP_VERSION, 0, 3);
		
		$this->_loadFiles();
	}
	
	private function _loadFiles() {
		if (is_dir($this->_dirAbsPath))
		{
			if ($dh = opendir($this->_dirAbsPath))
			{
				while(($file = readdir($dh)) !== false)
				{
					if(preg_match('/^'.preg_quote($this->_fileId).'-(guard|ioncube)-([0-9]{1})([0-9]{0,1})\.php(5|)$/', $file, $matches))
					{
						$this->_pkgsFileAr[$matches[1]][$matches[2].'.'.(($matches[3] == '') ? $matches[3] = 0 : $matches[3])] = $file;
					}
				}
				closedir($dh);
				return true;
			}
		}
		else
		{
			trigger_error(self::COMPONENT_NAME.': directory does not exist in '.__METHOD__, E_USER_ERROR);
		}
		return false;
	}

	/**
	  * Method to execute the loader
	  * @access public
	  */
	public function execute()
	{
		if(empty($this->_pkgsFileAr))
		{
			trigger_error(self::COMPONENT_NAME.': no '.$this->_productName.' package to load in '.__METHOD__, E_USER_WARNING);
			return false;
		}

		// pkgs only for guard
		if(array_key_exists('guard', $this->_pkgsFileAr) && !array_key_exists('ioncube', $this->_pkgsFileAr))
		{
			if(!self::_guardLoaded())
			{
				self::_displayWarning(
						$this->_generateMessage('guard')
					);
				return false;
			}
			if(!self::_guardLoadedVersion())
			{
				self::_displayWarning(
						$this->_generateMessage('guardUpdate')
					);
				return false;
			}
			if(!self::_checkOpcache())
			{
				self::_displayWarning(
						$this->_generateMessage('opcache')
					);
				return false;
			}
		}

		// pkgs only for ioncube
		if(!array_key_exists('guard', $this->_pkgsFileAr) && array_key_exists('ioncube', $this->_pkgsFileAr))
		{
			if(!self::_ioncubeLoaded())
			{
				self::_displayWarning(
						$this->_generateMessage('ioncube')
					);
				return false;
			}
			if(!self::_ioncubeLoadedVersion())
			{
				self::_displayWarning(
						$this->_generateMessage('ioncubeUpdate')
					);
				return false;
			}
		}
		// pkgs available for both
		if(array_key_exists('guard', $this->_pkgsFileAr) && array_key_exists('ioncube', $this->_pkgsFileAr))
		{
			if(!self::_guardLoaded() && !self::_ioncubeLoaded()) // no loaders
			{
				self::_displayWarning(
						$this->_generateMessage('both')
					);
				return false;
			}

			if(
				(self::_guardLoaded() && (!self::_guardLoadedVersion() || !self::_checkOpcache())) // guard loaded with issues
					&&
				(!self::_ioncubeLoaded() || !self::_ioncubeLoadedVersion())) // no ioncube
			{
				if(!self::_guardLoadedVersion())
				{
					self::_displayWarning(
							$this->_generateMessage('guardUpdate')
						);
					return false;
				}
				if(!self::_checkOpcache())
				{
					self::_displayWarning(
							$this->_generateMessage('opcache')
						);
					return false;
				}
			}
			if(
				(self::_ioncubeLoaded() && !self::_ioncubeLoadedVersion()) // ioncube loaded with issues
					&&
				(!self::_guardLoaded() || !self::_guardLoadedVersion() || !self::_checkOpcache()) // no guard
			)
			{
				if(!self::_ioncubeLoadedVersion())
				{
					self::_displayWarning(
							$this->_generateMessage('ioncubeUpdate')
						);
					return false;
				}
			}
		}

		$pkgToLoad = false;
		// load guard pkg, if any
		if(array_key_exists('guard', $this->_pkgsFileAr) && self::_guardLoaded() && self::_guardLoadedVersion() && self::_checkOpcache())
		{
			$pkgToLoad = self::_getMatchingGuardPkg($this->_pkgsFileAr['guard'], $this->_dirAbsPath, $this->_phpBranch);
		}

		// load ioncube pkg otherwise, if any
		if(!$pkgToLoad && array_key_exists('ioncube', $this->_pkgsFileAr) && self::_ioncubeLoaded() && self::_ioncubeLoadedVersion())
		{
			$pkgToLoad = self::_getMatchingIoncubePkg($this->_pkgsFileAr['ioncube'], $this->_dirAbsPath, $this->_phpBranch);
		}

		if(!$pkgToLoad)
		{
			self::_displayWarning(
						'The current version of '.$this->_productName.' does not support the installed PHP version.<br />Please download an upgrade at <a href="http://www.inveostore.com" style="text-decoration: underline !important">www.inveostore.com</a>.'
					);
		}
		elseif($this->_checkPackage($pkgToLoad)) // pkg found and ok
		{
			require_once($pkgToLoad);
			return true;
		}
		return false;
	}

	/**
	  * Method to load a matching package
	  * @access public static
	  * @param String $productName - product name
	  * @param String $pkgAbsPath - absolute path to directory with pkgs
	  * @param Array $pkgFileAr - array of loaders and their files
	  */
	public static function loadPackage($fileId, $dirAbsPath, $productName = null) {
		$loader = new InveoLoader2($fileId, $dirAbsPath, $productName);
		return $loader->execute();
	}

	/**
	  * Method to generate a message
	  * @access private
	  * @param String $loader - type of loader
	  */
	private function _generateMessage($loader)
	{
		$message = array();
	
		switch($loader)
		{
			case 'both':
				$message[] = '<b>The '.$this->_productName.' module requires at least one of the following PHP loaders:</b><br />';
				$message[] = self::_loaderDownloadlink('guard');
				$message[] = '<br />'.self::_loaderDownloadlink('ioncube');
			break;

			case 'guard':
			case 'ioncube':
				$message[] = '<b>The '.$this->_productName.' module requires the following PHP loader:</b><br />';
				$message[] = self::_loaderDownloadlink($loader);
			break;
			
			case 'guardUpdate':
			case 'ioncubeUpdate':
				$message[] = '<b>The '.$this->_productName.' module requires to update the following PHP loader:</b><br />';
				$message[] = self::_loaderDownloadlink(str_replace('Update', '', $loader));
			break;
			
			case 'opcache':
				$message[] = '<b>The '.$this->_productName.' module requires OP Cache extension shipped with Zend Guard Loader:</b><br />';
				$message[] = self::_loaderDownloadlink($loader);
			break;
		}
		
		$message[] = '<br />Inveo can assist you with loader installation on shared, VPS or dedicated servers. Should you need any help, feel free to contact us!';
	
		return implode('<br />', $message);
	}

	/**
	* Method to check the package integrity
	* @access private
	* @param String $package - path to encoded package
	*/
	private function _checkPackage($package)
	{
		$md5File = substr($package, 0, -3).'md5';
		
		if(!file_exists($md5File))
		{
			self::_displayWarning($this->_productName.' was not initialized because the MD5 signature is missing.<br />('.basename(dirname($md5File)).DIRECTORY_SEPARATOR.basename($md5File).' file was not found)');
			return false;
		}
	
		if(md5_file($package) != trim(file_get_contents($md5File)))
		{
			self::_displayWarning($this->_productName.' was not initialized because the file to load is corrupted.<br />(wrong md5 checksum)');
			return false;
		}
		return true;
	}

	/**
	  * Method to find a matching guard package
	  * @access private static
	  * @param Array $pkgFileAr - array of loaders and their files
	  * @param String $pkgAbsPath - absolute path to directory with pkgs
	  * @param String $phpBranch - PHP branch
	  */
	private static function _getMatchingGuardPkg($pkgsFileAr, $pkgAbsPath, $phpBranch)
	{
		krsort($pkgsFileAr);
		foreach($pkgsFileAr as $fileVer => $file)
		{
			$file = self::_makeAbsolutePath($file, $pkgAbsPath);
			if(
				(
					// later than 5.3 => packages are backward in-compatible and have to (not always in case of ioncube) match the major PHP version
					(
						version_compare(PHP_VERSION, '5.3.0', '>=') && substr($fileVer, 0, 3) === $phpBranch
					)
						||
					// older than 5.3 => packages are backward compatible
					(
						version_compare(PHP_VERSION, '5.3.0', '<') && version_compare(PHP_VERSION, $fileVer, '>=')
					)
				)
					&&
				file_exists($file)
			)
			{
				return $file;
			}
		}
		return false;
	}

	/**
	  * Method to find a matching ioncube package
	  * @access private static
	  * @param Array $pkgFileAr - array of loaders and their files
	  * @param String $pkgAbsPath - absolute path to directory with pkgs
	  * @param String $phpBranch - PHP branch
	  */
	private static function _getMatchingIoncubePkg($pkgsFileAr, $pkgAbsPath, $phpBranch)
	{
		// we use rules similar to guard's (since we will always provide a file for the exact PHP branch)
		return self::_getMatchingGuardPkg($pkgsFileAr, $pkgAbsPath, $phpBranch);
	}

	/**
	  * Method to make an absolute path to a file
	  * @access private static
	  * @param String $file - PHP file name
	  */
	private static function _makeAbsolutePath($file, $path = null)
	{
		if(is_null($path))
		{
			$path = dirname(__FILE__);
		}

		return $path.DIRECTORY_SEPARATOR.$file;
	}
	
	/**
	  * Method to generate download link message
	  * @access private static
	  * @param String $loader - type of loader
	  */
	private static function _loaderDownloadlink($loader)
	{
		$os = '32 bit';
		if(PHP_INT_SIZE === 8)
		{
			$os = '64 bit';
		}
	
		switch($loader)
		{
		
			case 'guard':
			case 'opcache':
				if($loader == 'guard')
				{
					$name = 'Zend Optimizer';
					if(version_compare(PHP_VERSION, '5.3.0', '>='))
					{
						$name = 'Zend Guard Loader';
					}
				}
				else
				{
					$name = 'OP Cache';
				}
				$name .= ' for PHP '.substr(PHP_VERSION, 0, 3).' ('.$os.')';
				$link = 'www.zend.com/products/loader/downloads';
			break;
			
			case 'ioncube':
				$name = 'ionCube Loader';
				$name .= ' ('.$os.')';
				$link = 'www.ioncube.com/loaders.php';
			break;

			default:
				trigger_error(self::COMPONENT_NAME.': unknown loader passed to '.__METHOD__, E_USER_ERROR);
				exit();
			break;
		
		}
		
		return $name.' is available without charge at <a href="http://'.$link.'" style="text-decoration: underline !important">'.$link.'</a>.';
	}

	/**
	  * Method to display a warning message
	  * @access private static
	  * @param String $msg - message to display
	  */
	private static function _displayWarning($msg)
	{
		echo '<div style="width: 100% !important; position: fixed !important; left: 10px !important; bottom: 10px !important; z-index: 999 !important"><div style="margin: 0 auto !important; width: 600px !important; font-family: Verdana, Arial, Helvetica, sans-serif !important; font-size: 12px !important; background-color: #FFFF00 !important; color: black !important; margin: 1em auto 1em auto !important; padding: 1em !important; text-align: center !important; border: 1px solid red !important">'.$msg.'</div></div>';
	}

	/**
	  * Method to find out whether ionCube loader is installed
	  * @access private static
	  */
	private static function _ioncubeLoaded()
	{
		return function_exists('ioncube_loader_version');
	}
	
	/**
	  * Method to find out whether ionCube loader has required version
	  * @access private static
	  */
	private static function _ioncubeLoadedVersion()
	{
		if(!function_exists('ioncube_loader_version'))
		{
			return false;
		}
	
		return self::_checkLoaderVersion(
					array( // php => loader
						'7.2' => '10.1',
						'7.1' => '10.0',
						'7.0' => '6.0',
						'5.6' => '5.0',
						'5.5' => '4.6',
						'5.4' => '4.4',
						'5.3' => '4.0',
						'5.0' => '3.1'
					),
					ioncube_loader_version()
			);
	}
	
	/**
	  * Method to find out whether ZO/ZGL loader is installed
	  * @access private static
	  */
	private static function _guardLoaded()
	{
		return (function_exists('zend_loader_enabled') && zend_loader_enabled());
	}

	/**
	  * Method to find out whether Zend Guard loader has required version
	  * @access private static
	  */
	private static function _guardLoadedVersion()
	{
		if(!function_exists('zend_loader_version'))
		{
			return false;
		}
	
		return self::_checkLoaderVersion(
					array( // php => loader
						'5.6' => '3.3',
						'5.5' => '3.3',
						'5.4' => '3.3',
						'5.3' => '3.3',
						'5.0' => '3.3'
					),
					zend_loader_version()
			);
	}
	
	/**
	  * Method to find out whether loader has required version
	  * @access private static
	  */
	private static function _checkLoaderVersion($phpToLoaderAr, $loaderVersion)
	{
		foreach($phpToLoaderAr as $phpVer => $loaderVer)
		{
			if(version_compare(PHP_VERSION, $phpVer, '>='))
			{
				if(version_compare($loaderVersion, $loaderVer, '>='))
				{
					return true;
				}
				return false;
			}
		}
		return false;
	}

	/**
	  * Method to find out whether OP Cache shipped with ZGL is installed
	  * @access private static
	  */
	private static function _checkOpcache()
	{
		if(!function_exists('opcache_get_configuration') || version_compare(PHP_VERSION, '5.5.0', '<'))
		{
			return true;
		}

		$conf = @opcache_get_configuration();
		if(isset($conf['version']['version']) && version_compare($conf['version']['version'], '7.0.4-dev', '=='))
		{
			return true;
		}
		return false;
	}
}

}
?>
