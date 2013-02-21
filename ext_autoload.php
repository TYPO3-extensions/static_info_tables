<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id: ext_autoload.php $
 */
$extensionPath = t3lib_extMgm::extPath('static_info_tables');
return array(
	'tx_staticinfotables_renderElement' => $extensionPath . 'Classes/Hook/Backend/Form/class.tx_staticinfotables_renderElement.php',
	'tx_staticinfotables_processdatamap' => $extensionPath . 'Classes/Hook/Core/DataHandling/class.tx_staticinfotables_processdatamap.php',
	'tx_staticinfotables_div' => $extensionPath . 'class.tx_staticinfotables_div.php',
	'tx_staticinfotables_syslanguage' => $extensionPath . 'class.tx_staticinfotables_syslanguage.php',
	'tx_staticinfotables_pi1' => $extensionPath . 'pi1/class.tx_staticinfotables_pi1.php',
);
unset($extensionPath);
?>