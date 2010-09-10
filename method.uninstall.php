<?php
	if (!isset($gCms)) exit;
	
	/**
	 * Recursively delete a directory
	 *
	 * @param string $dir Directory name
	 * @param boolean $deleteRoot Delete specified top-level directory as well
	 */
	function unlinkRecursive($dir, $deleteRoot)
	{
		if(!$dh = @opendir($dir)){return;}
		
		while (false !== ($obj = readdir($dh))){
			if($obj == '.' || $obj == '..') {
				continue;
			}

			if (!@unlink($dir . '/' . $obj)) {
				unlinkRecursive($dir.'/'.$obj, true);
			}
		}

		closedir($dh);
	   
		if ($deleteRoot){@rmdir($dir);}
	   
		return;
	} 
	
	
	/** 
	 * After this, the code is identical to the code that would otherwise be
	 * wrapped in the Uninstall() method in the module body.
	 */
	
	$db =& $gCms->GetDb();
	
	// remove the database table
	$dict = NewDataDictionary($db);
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_dlm_downloads");
	$dict->ExecuteSQLArray($sqlarray);
	
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_dlm_mirrors");
	$dict->ExecuteSQLArray($sqlarray);
	
	$sqlarray = $dict->DropTableSQL(cms_db_prefix()."module_dlm_items");
	$dict->ExecuteSQLArray($sqlarray);
	$db->DropSequence( cms_db_prefix()."module_dlm_items_seq" );
	
	// remove files if possible
	@unlinkRecursive(cms_join_path(dirname(__FILE__), '..', '..', 'downloads', ''), true);
	@unlinkRecursive(cms_join_path(dirname(__FILE__), '..', '..', 'tmp', 'downloads', ''), true);
	
	// remove the permissions
	$this->RemovePermission('Use DLM');
	$this->RemovePermission('Set DLM Prefs');
	
	$this->RemoveEvent('DownloadAdded');
	$this->RemoveEvent('DownloadEdited');
	//$this->RemoveEvent('DownloadMoved');
	//$this->RemoveEvent('DownloadDeleted');
	
	$this->RemoveEvent('CategoryAdded');
	$this->RemoveEvent('CategoryEdited');
	//$this->RemoveEvent('CategoryMoved');
	//$this->RemoveEvent('CategoryDeleted');
	
	$this->RemoveEvent('ItemActivated');
	$this->RemoveEvent('ItemMoved');
	$this->RemoveEvent('ItemDeleted');
	
	// put mention into the admin log
	$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('uninstalled'));
?>