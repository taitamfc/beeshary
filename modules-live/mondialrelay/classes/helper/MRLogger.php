<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

//namespace MRHelper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MRLogger
{
    /**
     * Errors level
     */
    const DEBUG = 1;
    const INFO  = 2;
    const WARN  = 3;
    const ERROR = 4;

    /**
     * Errors mod
     */
    const DEV_MOD  = 1;
    const PROD_MOD = 3;

    /**
     * Current instance of logger
     * @var MRLogger
     */
    private static $_instance;

    /**
     * Path to file
     * @var mixed
     */
    private $file;

    /**
     * Add time to message
     * @var boolean
     */
    private $time_msg;

    /**
     * If object want to save in file
     * @var boolean
     */
    private $save_to_file;

    /**
     * Error level
     * @var integer
     */
    private $error_level = MRLogger::PROD_MOD;

    /**
     * Constructor
     */
    public function __construct()
    {
        # Settings
        $this->time_msg     = true;
        $this->save_to_file = true;

        # If want to save in file, set the file log
        if ($this->save_to_file) {
            $this->file = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'logs'.DIRECTORY_SEPARATOR.'modules-log.txt';
        }
    }

    /**
     * Create blank line
     */
    public function blankLine()
    {
        $time_msg       = $this->time_msg;
        $this->time_msg = false;
        $this->writeLine('');
        $this->time_msg = $time_msg;
    }

    /**
     * Save line
     * @param     $message
     * @param int $level
     * @return bool|int|void
     */
    private function writeLine($message, $level = MRLogger::DEBUG)
    {
        # If module is not in debug mod, or level is not
        if (!MondialRelay::LOG || $level < $this->error_level) {
            return true;
        }

        # Get backtrace
        $backtrace = debug_backtrace();
        $message   = basename($backtrace[1]['file']).':'.$backtrace[1]['line'].' '.$message;

        # Get time
        $date      = strftime('%Y-%m-%d %H:%M:%S');
        $microtime = (float)microtime();

        # Add prefix message
        if ($this->time_msg && $this->save_to_file) {
            $message = '['.$date.Tools::substr($microtime, 1).'] : '.$message;
        }

        # Switch action
        if ($this->save_to_file) {
            return file_put_contents($this->file, $message."\n", FILE_APPEND);
        } else {
            return Db::getInstance()->insert(
                'totlogger',
                array(
                    'level'     => $level,
                    'message'   => $message,
                    'date'      => $date,
                    'microtime' => $microtime,
                    'module'    => "MondialRelay",
                )
            );
        }
    }

    /**
     * Get logs
     * @param  integer $level Error level
     * @return array          Errors
     */
    public static function getLogs($level = null)
    {
        $query = new DbQuery();
        $query->from('totlogger');
        $query->where('module = "MondialRelay"');

        if (!is_null($level)) {
            $query->where('level = '.(int)$level);
        }

        return Db::getInstance()->executeS($query);
    }

    /**
     * Add warning log
     * @param  string $message Error message
     * @return boolean         If process is ok
     */
    public function warn($message)
    {
        return $this->writeLine($message, MRLogger::WARN);
    }

    /**
     * Add error log
     * @param  string $message Error message
     * @return boolean         If process is ok
     */
    public function error($message)
    {
        return $this->writeLine($message, MRLogger::ERROR);
    }

    /**
     * Add debug log
     * @param  string $message Error message
     * @return boolean         If process is ok
     */
    public function debug($message)
    {
        return $this->writeLine($message, MRLogger::DEBUG);
    }

    /**
     * Add info log
     * @param  string $message Error message
     * @return boolean         If process is ok
     */
    public function info($message)
    {
        return $this->writeLine($message, MRLogger::INFO);
    }

    /**
     * Create an instance of this object
     * @return MRLogger Instance
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new MRLogger();
        }

        return self::$_instance;
    }
}
