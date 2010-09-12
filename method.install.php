<?php
	if (!isset($gCms)) exit;

	#error_reporting(E_ALL);
	$dl_path = cms_join_path(dirname(__FILE__), '..', '..', 'downloads', '');
	$tmp_path = cms_join_path(dirname(__FILE__), '..', '..', 'tmp', 'downloads', '');
	clearstatcache();

	if(((is_dir($dl_path) && (is_writable($dl_path) || chmod($dl_path, 0777))) || (!is_dir($dl_path) && mkdir($dl_path))) && ((is_dir($tmp_dir) && (is_writable($tmp_dir) || chmod($tmp_dir, 0777))) || (!is_dir($tmp_path) && mkdir($tmp_path)))) {
		$db =& $gCms->GetDb();

		$taboptarray = array('mysql' => 'TYPE=MyISAM');

		$dict = NewDataDictionary($db);

		#----downlaods-table-------------------------------------------------------------------------------#
		$flds = "dl_parent_id I, location X, created_date DT, modified_date DT, size I UNSIGNED, downloads I UNSIGNED DEFAULT 0";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_dlm_downloads", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		#----mirrors-table-----------------------------------------------------------------------------#
		$flds = "dl_mirror_id I AUTOINCREMENT KEY, dl_parent_id I UNSIGNED, position I UNSIGNED DEFAULT 0, name C(255), location X, downloads I UNSIGNED DEFAULT 0";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_dlm_mirrors", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		#----items-table--------------------------------------------------------------------------#
		$flds = "dl_id I KEY, dl_left I UNSIGNED NOTNULL, dl_right I UNSIGNED NOTNULL, dl_level I UNSIGNED NOTNULL, expand L UNSIGNED DEFAULT 0, active L UNSIGNED DEFAULT 1, downloads I UNSIGNED DEFAULT 0, parent I DEFAULT 0, type L DEFAULT 0, name C(255), description X";
		// type 0 = category, type 1 = download

		$sqlarray = $dict->CreateTableSQL(cms_db_prefix()."module_dlm_items", $flds, $taboptarray);
		$dict->ExecuteSQLArray($sqlarray);

		$db->CreateSequence(cms_db_prefix()."module_dlm_items_seq", 0);


		// Init the tree-strcuture and create root-node
		$this->tree->Clear(array('name' => 'root', 'parent' => -1, 'description' => 'default.tpl'));

		$this->CreatePermission('Use DLM', 'Use DownloadManager (DLM)');
		$this->CreatePermission('Set DLM Prefs', 'Set Download Manager Preferences and edit Templates');

		$this->CreateEvent('DownloadAdded');
		$this->CreateEvent('DownloadEdited');
		//$this->CreateEvent('DownloadMoved');
		//$this->CreateEvent('DownloadDeleted');

		$this->CreateEvent('CategoryAdded');
		$this->CreateEvent('CategoryEdited');
		//$this->CreateEvent('CategoryMoved');
		//$this->CreateEvent('CategoryDeleted');

		$this->CreateEvent('ItemActivated');
		$this->CreateEvent('ItemMoved');
		$this->CreateEvent('ItemDeleted');

		$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
	} else {
		$tmp = 'An error occurred during the installation of DLM. Possibly the folders /downloads/ and /tmp/downloads/ could not be created. Make sure they exist and are writable than retry.';//$this->Lang('installerror');
		return $tmp;
	}
?>