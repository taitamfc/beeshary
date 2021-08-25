<?php
/**
  *  @author    Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2009-2015 Inveo s.r.o.
  *  @license   EULA
  */

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'InveoLoader2.php');
InveoLoader2::loadPackage(
				substr(basename(__FILE__), 0, -4),
				dirname(__FILE__).DIRECTORY_SEPARATOR.'pkgs',
				'Inveo Theme Provider'
);

?>