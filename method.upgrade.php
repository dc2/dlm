<?php
if (!isset($gCms)) exit;

$current_version = $oldversion;
$db =& $this->GetDb();

$this->RemovePermission('Manage Downloads');
$this->RemovePermission('Use DlM');
$this->RemovePermission('Set DlM Prefs');

$this->CreatePermission('Use DlM', 'Use DlM');
$this->CreatePermission('Set DlM Prefs', 'Set DlM Prefs');

switch($current_version) {
	case '0.5.2b':
	case '0.5.1b':
	case '0.5b':
		$dict = NewDataDictionary($db);

		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_dlm_mirrors', 'position I UNSIGNED DEFAULT 0');
		$dict->ExecuteSQLArray($sqlarray);
	break;
	default:
		#ALTER TABLE `cms_module_dlm_items` CHANGE `type` `type` VARCHAR( 10 ) NOT NULL DEFAULT '0'
	break;
}

?>
