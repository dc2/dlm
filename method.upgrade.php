<?php
	if (!isset($gCms)) exit;

	$current_version = $oldversion;
	$db = &$this->GetDb();

	$this->CreatePermission('Use DLM', 'Use DLM');
	$this->CreatePermission('Set DLM Prefs', 'Set DLM Prefs');

	if(version_compare($current_version, '0.7', '<')) {
		$query = "UPDATE ".cms_db_prefix()."module_dlm_items SET description = ? WHERE dl_id = ?";
		$db->Execute($query, array('default.tpl', 0));
	}

	switch($current_version) {
		case '0.5.2b':
		case '0.5.1b':
		case '0.5b':
			$dict = NewDataDictionary($db);

			$dict = NewDataDictionary($db);
			$sqlarray = $dict->AddColumnSQL(cms_db_prefix().'module_dlm_mirrors', 'position I UNSIGNED DEFAULT 0');
			$dict->ExecuteSQLArray($sqlarray);
			
		case '0.7.7':
			$query = 'UPDATE '.cms_db_prefix().'module_templates SET content = REPLACE(REPLACE(content, ?, ?), ?, ?) WHERE module_name = ? ';
			
			$db->Execute($query, array('$entry->downloads', '$entry->files', '$entry->', '$entry.', 'DLM'));
		break;
		default:
			#$query = "ALTER TABLE `".cms_db_prefix()."module_dlm_items` CHANGE `type` `type` VARCHAR( 10 ) NOT NULL DEFAULT '0'";
		break;
	}

?>