<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php $
 */
$extensionPath = t3lib_extMgm::extPath('static_info_tables');
return array(
	'ext_update' => $extensionPath . 'class.ext_update.php',
	'tx_staticinfotables_div' => $extensionPath . 'class.tx_staticinfotables_div.php',
	'tx_staticinfotables_encoding' => $extensionPath . 'class.tx_staticinfotables_encoding.php',
	'tx_staticinfotables_syslanguage' => $extensionPath . 'class.tx_staticinfotables_syslanguage.php',
	'tx_staticinfotables_emconfhelper' => $extensionPath . 'classes/class.tx_staticinfotables_emconfhelper.php',
	'tx_staticinfotables_pi1' => $extensionPath . 'pi1/class.tx_staticinfotables_pi1.php',
);
unset($extensionPath);
?>