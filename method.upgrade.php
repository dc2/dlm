<?php
if (!isset($gCms)) exit;

$current_version = $oldversion;
$db =& $this->GetDb();

switch($current_version) {
	case '0.5.2b':
	case '0.5.1b':
	case '0.5b':
		$dict = NewDataDictionary($db);

		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_dlm_mirrors', 'position I UNSIGNED DEFAULT 0');
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = "0.6.1";
	break;
	default:
		$current_version = "0.6.1";
	break;
}

?>
