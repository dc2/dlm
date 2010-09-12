<?php

require_once('dbtree.class.php');

class dltree extends dbtree {
	function dltree($table, $prefix, &$db) {
		parent::dbtree($table, $prefix, $db);
	}

	function GetItemsDB($node, $fields = '*', $ignore = false, $condition = '') {
		$this->Branch($node, $fields, $condition);

		if($ignore !== false && (is_numeric($ignore) || is_array($ignore))) {
			$rows = array();
			while ($item = $this->NextRow()) {
				$itemid = (int)$item['dl_id'];

				if(isset($item['parent']) && $item['parent'] != '-1') {
					$parent = (int)$item['parent'];

					if(!is_array($ignore)) {
						if($itemid !== $ignore && $parent !== $ignore) {
							$rows[$itemid] = $item;
						}
					} else {
						$left = (int)$item['dl_left'];
						$right = (int)$item['dl_right'];

						$ignoreid = (int)$ignore['id'];

						if($itemid !== $ignoreid && $parent !== $ignoreid && !($left > (int)$ignore[0] && $right < (int)$ignore[1])) {
							$rows[$itemid] = $item;
						}
					}
				}

			}
		} else {
			$rows = $this->res->GetAssoc();
		}

		return $rows;
	}

	function GetItem($item_id, $fields = '*') {
		$items = $this->GetItemsDB((int)$item_id, $fields, false, array('and' => array('dl_id = '.(int)$item_id)));
		return reset($items);
	}

	// tree functions //
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

	/*function RebuildTree($node = 0, $left = 1, $first = false) {
		$right	= $left + 1;

		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_items WHERE parent = ? ORDER BY dl_left';
		$result = $this->db->Execute($query, array($node));

		while ($row = $result->FetchRow()) {
			$right = $this->RebuildTree($row['dl_id'], $right);
		}

		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET dl_left = ?, dl_right = ? WHERE dl_id = ?';
		$result = $this->db->Execute($query, array($left, $right, $node));

		if($first === true)
			$this->RecalcDownloadsAll(array($node), array(array($left, $right)));

		return $right + 1;
	}*/

	function RecalcDownloadsAll($nodes, $info = NULL) {
		$count = count($nodes);
		for($i = 0; $i < $count; ++$i) {
			$node &= $nodes[$i];
			$info = is_array($info[$i]) ? $info[$i] : $this->GetNodeInfo($node);

			$this->RecalcDownloadsNode($node, $info);

			$query = 'SELECT dl_id, dl_left, dl_right FROM '.cms_db_prefix().'module_dlm_items WHERE dl_left > ? AND dl_right < ? AND type = 0';
			$result = $this->db->Execute($query, array($info[0], $info[1]));

			while($row = $result->FetchRow()) {
				$this->RecalcDownloadsNode($row['dl_id'], array($row['dl_left'], $row['dl_right']));
			}
		}
	}

	/* Update download-count of specific node */
	function RecalcDownloadsNode($node, $info = NULL) {
		if($info === NULL) $info = $this->GetNodeInfo($node);

		$this->db->SetFetchMode(ADODB_FETCH_NUM);
		$query = 'SELECT COUNT(*) FROM '.cms_db_prefix().'module_dlm_items WHERE dl_left > ? AND dl_right < ? AND type = 1';
		$result = $this->db->Execute($query, array($info[0], $info[1]));
		$count = $result->fields[0];
		$this->db->SetFetchMode(ADODB_FETCH_ASSOC);

		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET downloads = ? WHERE dl_id = ?';
		$this->db->Execute($query, array($count, $node));
	}

	/* Increment / Decrement download-count on all parent nodes */
	function UpdateDownloadCount($node, $mode = "+") {
		$info = $this->GetNodeInfo($node);
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET downloads = downloads '.$mode.' 1 WHERE dl_left < ? AND dl_right > ?';

		return $this->db->Execute($query, array($info[0], $info[1]));
	}

	function InsertNode($node, $data, $condition = '') {
		$data = array_merge(array('parent' => $node), $data);

		return $this->Insert($node, $condition, $data);
	}

	function GetDownload($item_id) {
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_downloads WHERE dl_parent_id = ?';
		$result = $this->db->Execute($query, array((int)$item_id));

		if($result->NumRows() > 0)
			return $result->FetchRow();
		else
			return false;
	}

	function DeleteDownload($item_id) {
		$dl = $this->GetDownload($item_id);
		if($dl !== false) {
			if(substr($dl['location'], 0, 2) == '$$') {
				$dldir = cms_join_path(dirname(__FILE__), '..', '..', 'downloads', '');
				@unlink($dldir.substr($dl['location'], 2));
			}

			return true;
		} else return false;
	}

	function DeleteDownloads($node) {
		$condition = '';
		$info = $this->GetNodeInfo($node);

		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_items WHERE (dl_left BETWEEN ? AND ?) OR (dl_id = ?)';
		$result = $this->db->Execute($query, array($info[0], $info[1], $node));

		if($result->NumRows() > 0) {
			while($row = $result->FetchRow()) {
				if($row['type'] == 1) {
					$this->DeleteDownload($row['dl_id']);
					$condition .= ' dl_parent_id = ' . $row['dl_id'] . ' OR ';

					$this->UpdateDownloadCount($row['dl_id'], '-');
				}
			}
			$condition = substr($condition, 0, strlen($condition) - 3);

			$query = 'DELETE FROM '.cms_db_prefix().'module_dlm_downloads WHERE '.$condition;
			$this->db->Execute($query, array());

			$query = 'DELETE FROM '.cms_db_prefix().'module_dlm_mirrors WHERE '.$condition;
			$this->db->Execute($query, array());

			return true;
		} else return false;
	}

	function GetChildren($node) {
		$query = 'SELECT * FROM '.cms_db_prefix().'module_dlm_items WHERE parent = ? ORDER BY dl_left ASC';
		$result = $this->db->Execute($query, array($node));

		while($row = $result->FetchRow()) {
			$rows[] = $row;
		}

		return $rows;
	}

	function GetChildrenCount($node) {
		$info = $this->GetNodeInfo($node);
		return ($info[1] - $info[0] - 1) / 2;
	}

	function GetParentID($node) {
		$info = $this->GetParentInfo((int) $node);
		return $info['dl_id'];
	}

	/*
	function DeleteItem($item_id) {
		$dbitem = $this->tree->GetItem($item_id);

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
}


?>