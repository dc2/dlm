<?php
	/*
	function tree_as_array($dbtree) {
		$menu = array();
		$ref = array();
		foreach($dbtree as $d) {
				$d['children'] = array();
				if(isset($ref[$d['parent']])) { // we have a reference on its parent
						$ref[$d['parent']]['children'][$d['dl_id']] = $d;
						$ref[$d['dl_id']] =& $ref[$d['parent']]['children'][$d['dl_id']];
				} else { // we don't have a reference on its parent => put it a root level
						$menu[$d['dl_id']] = $d;
						$ref[$d['dl_id']] =& $menu[$d['dl_id']];
				}
		}
		return $menu;
	}	
	*/
	
	// tree fucntions
	function RebuildTree(&$dlm, $node = 0, $left = 1, $first = false) {  	
		$right	= $left + 1; 
		
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_items WHERE parent = ? ORDER BY dl_left';
		$result = $dlm->db->Execute($query, array($node));  
		
		while ($row = $result->FetchRow()) {  
			$right = RebuildTree(&$dlm, $row['dl_id'], $right);  
		}  
			
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET dl_left = ?, dl_right = ? WHERE dl_id = ?';
		$result = $dlm->db->Execute($query, array($left, $right, $node));  
		
		if($first === true)
			RecalcDownloadsAll(&$dlm, array($node), array(array($left, $right))); 
		
		return $right + 1;  
	}
	
	function RecalcDownloadsAll(&$dlm, $nodes, $info = NULL) {		
		$count = count($nodes); 	
		for($i = 0; $i < $count; ++$i) {
			$node &= $nodes[$i];
			$info = is_array($info[$i]) ? $info[$i] : $dlm->tree->GetNodeInfo($node);
			
			RecalcDownloadsNode(&$dlm, $node, $info);
			
			$query = 'SELECT dl_id, dl_left, dl_right FROM '.cms_db_prefix().'module_dlm_items WHERE dl_left > ? AND dl_right < ? AND type = 0';
			$result = $dlm->db->Execute($query, array($info[0], $info[1]));
			
			while($row = $result->FetchRow()) {
				RecalcDownloadsNode(&$dlm, $row['dl_id'], array($row['dl_left'], $row['dl_right']));
			}
		}
	}
	
	/* Update download-count of specific node */
	function RecalcDownloadsNode(&$dlm, $node, $info = NULL) {
		if($info === NULL) 
			$info = $this->tree->GetNodeInfo($node);

		$dlm->db->SetFetchMode(ADODB_FETCH_NUM);
		$query = 'SELECT COUNT(*) FROM '.cms_db_prefix().'module_dlm_items WHERE dl_left > ? AND dl_right < ? AND type = 1';
		$result = $dlm->db->Execute($query, array($info[0], $info[1]));
		$count = $result->fields[0];
		$dlm->db->SetFetchMode(ADODB_FETCH_ASSOC);
	
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET downloads = ? WHERE dl_id = ?';
		$dlm->db->Execute($query, array($count, $node));
	}
	
	/* Increment / Decrement download-count on all parent nodes */
	function UpdateDownloadCount(&$dlm, $node, $mode = "+") {
		$info = $dlm->tree->GetNodeInfo($node);
		
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET downloads = downloads '.$mode.' 1 WHERE dl_left < ? AND dl_right > ?';
		
		return $dlm->db->Execute($query, array($info[0], $info[1]));
	}
	
		
	function InsertNode(&$dlm, $node, $data, $condition = '') {	
		$data = array_merge(array('parent' => $node), $data);
		
		return $dlm->tree->Insert($node, $condition, $data);
	}
	
	function MoveNode(&$dlm, $node, $newparent, $oldparent, $condition = ''){		
		$dlm->tree->MoveAll($node, $newparent, $condition);
		RecalcDownloadsAll(&$dlm, array($newparent, $oldparent));
		
		$dlm->SendEvent('ItemMoved', array('dl_item' => array('id' => $node)));
	}
	
	function DeleteDownload(&$dlm, $item_id) {
		$dl = $dlm->GetDownload($item_id);
		if($dl !== false) {
			if(substr($dl['location'], 0, 2) == '$$') {
				$dldir = cms_join_path(dirname(__FILE__), '..', '..', 'downloads', '');
				@unlink($dldir.substr($dl['location'], 2));
			}
			
			return true;
		} else
			return false;		
	}
	
	function DeleteDownloads(&$dlm, $node) {		
		$condition = '';
		$info = $dlm->tree->GetNodeInfo($node);
		
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_items WHERE (dl_left BETWEEN ? AND ?) OR (dl_id = ?)';
		$result = $dlm->db->Execute($query, array($info[0], $info[1], $node));
		
		if($result->NumRows() > 0) {
			while($row = $result->FetchRow()) {
				if($row['type'] == 1) {
					DeleteDownload(&$dlm, $row['dl_id']);
					$condition .= ' dl_parent_id = ' . $row['dl_id'] . ' OR ';
					
					UpdateDownloadCount(&$dlm, $row['dl_id'], '-');
				}
			}
			$condition = substr($condition, 0, strlen($condition) - 3);
			
			$query = 'DELETE FROM '.cms_db_prefix().'module_dlm_downloads WHERE '.$condition;
			$dlm->db->Execute($query, array());
			
			$query = 'DELETE FROM '.cms_db_prefix().'module_dlm_mirrors WHERE '.$condition;
			$dlm->db->Execute($query, array());
			
			return true;
		} else {
			return false;
		}		
	}
	
	function DeleteBranch(&$dlm, $node) {
		DeleteDownloads(&$dlm, $node);
		$dlm->tree->DeleteAll($node);
		
		$dlm->SendEvent('ItemDeleted', array('dl_item' => array('id' => $node)));
		
		return true;
	}
	
	/*
	function DeleteItem($item_id) {
		$dbitem = $this->GetItem($item_id);
		
		if(isset($dbitem)) {
			if($dbitem['type'] == 1) {
				$query = 'DELETE FROM '.cms_db_prefix().'module_dlm_downloads WHERE dl_parent_id=?';
				$this->db->Execute($query, array($item_id));
			}
			
			$query = 'DELETE FROM '.cms_db_prefix().'module_dlm_items WHERE dl_id=?';
			$this->db->Execute($query, array($item_id));
			
			return true;
		} else return false;
	}
	*/
?>