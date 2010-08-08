<?php
	if (!isset($gCms)) exit;
	
	$root = isset($params['root']) ? (int) $params['root'] : 0;
	$root_info = ($root !== false) ? array_merge($this->tree->GetNodeInfo($root), array($root)) : false;

	$item_id = isset($params['item']) ? (int) $params['item'] : $root;
	$dbitem = ($item_id !== false) ? $this->GetItem($item_id) : false;
	
	if(!isset($params['showpath']) || $params['showpath'] === true) {
		$this->smarty->assign('path_text', $this->Lang('path_text'));
		$this->smarty->assign('path', $this->GetPath($item_id, $id, $returnid, $root_info));
	}	
	
	if(($dbitem === false || $dbitem['type'] == 0) && $dbitem['active'] == 1) {
		$items = $this->GetTree($item_id , $id, $returnid);
		
		if($items !== false) {
			$this->smarty->assign('headline', (($dbitem != false) && ($dbitem['name'] != 'root')) ? $dbitem['name'] : 'Download-Übersicht');
			
			$this->smarty->assign_by_ref('items', $items);
			$this->smarty->assign('itemcount', count($items));
			
			$this->smarty->assign('th_name', $this->Lang('th_name'));
			$this->smarty->assign('th_downloads', $this->Lang('downloads'));
		} else {
			$this->smarty->assign('itemcount', 0);
			$this->smarty->assign('no_children', $this->Lang('no_children'));
		}
		
		echo $this->ProcessTemplate('overview.tpl');
	} elseif($dbitem['active'] == 1){
		$download = $this->GetDownload($item_id);
		$location	= $this->CreateLink($id, 'download', $returnid, $dbitem['name'], array('item'=>$item_id, 'dlmode' => 'd'), '', true, true, '', false, MakePretty($item_id.'d', false, $dbitem['name']));
		
		if(!isset($params['showmirror']) || $params['showmirror'] === true) {
			$mirrors = $this->GetMirrors($item_id, false, false, false, $download['size']);
			$mirrorurl	= $this->CreateLink($id, 'download', $returnid, $dbitem['name'], array('item'=>$item_id, 'dlmode' => 'm[%mirrorid%]'), '', true, true, '', false, MakePretty($item_id.'m[%mirrorid%]', false, $dbitem['name']));
		} else $mirrors = false;
		
		$info = FileInfo($download['location']);
		$filename = $info['filename'];
		$fileext = $info['fileext'];
		
		$this->smarty->assign('dl_id', $item_id);
		$this->smarty->assign('dl_name', $dbitem['name']);
		$this->smarty->assign('dl_date', strftime('%d.%m.%Y', strtotime($download['created_date'])));
		$this->smarty->assign('dl_link', $location);
		
		$this->smarty->assign('dl_filename', $filename/*.'ABCDEFGHIJKLMNOPQRSTUVWXYZ'*/);
		$this->smarty->assign('dl_fileext', $fileext);
		
		if(!isset($params['showdesc']) || $params['showdesc'] === true) {
			$this->smarty->assign('dl_description', $dbitem['description']);
			$this->smarty->assign('dl_description_text',  $this->Lang('desc'));
		}	
		
		$this->smarty->assign('dl_size', FormatFilesize($download['size']));
		$this->smarty->assign('dl_downloads', $download['downloads']);
		$this->smarty->assign('dl_traffic', FormatFilesize((int)$download['downloads'] * (int)$download['size']));
		
		$this->smarty->assign('dl_mirrors', $mirrors);
		$this->smarty->assign('dl_mirrorurl', $mirrorurl);
		
		echo $this->ProcessTemplate('detail.tpl');
	} else {
		$this->smarty->assign('itemcount', 0);
		$this->smarty->assign('error_none', $this->Lang('error_noitem'));
		echo $this->ProcessTemplate('overview.tpl');	
	}
?>