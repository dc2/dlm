<?php
	if (!isset($gCms)) exit;

	$template = $this->LoadTemplate(trim($params['template']));

	$root = isset($params['root']) ? (int) $params['root'] : 0;
	$root_info = ($root !== false) ? array_merge($this->tree->GetNodeInfo($root), array($root)) : false;

	$item_id = isset($params['item']) ? (int) $params['item'] : $root;
	$item = ($item_id !== false) ? $this->tree->GetItem($item_id) : false;

	if(!isset($params['showpath']) || $params['showpath'] === true) {
		$this->smarty->assign('th_path', $this->Lang('th_path'));
		$this->smarty->assign('path', $this->GetPath($item_id, $id, $returnid, $root_info));
	}

	if(($item === false || $item['type'] == 0) && $item['active'] == 1) {
		$items = $this->GetTree($item_id , $id, $returnid);

		if($items !== false) {
			$this->smarty->assign('headline', (($item != false) && ($item['name'] != 'root')) ? $item['name'] : '');

			$this->smarty->assign_by_ref('items', $items);
			$this->smarty->assign('itemcount', count($items));

			$this->smarty->assign('th_name', $this->Lang('th_name'));
			$this->smarty->assign('th_download', $this->Lang('download'));
			$this->smarty->assign('th_downloads', $this->Lang('downloads'));
			$this->smarty->assign('th_traffic', $this->Lang('th_traffic'));


			if($item_id != 0 && (!isset($params['showdesc']) || $params['showdesc'] === true)) {
				$this->smarty->assign('description', $item['description']);
				$this->smarty->assign('th_description',  $this->Lang('desc'));
			}
		} else {
			$this->smarty->assign('itemcount', 0);
		}

		$this->smarty->assign('no_children', $this->Lang('no_children'));
		echo $this->ProcessTemplateFromData($template[0]);
	} elseif($item['active'] == 1){
		$download = $this->GetDownload($item_id);
		$location = $this->CreateLink($id, 'download', $returnid, $item['name'], array('item'=>$item_id, 'dlmode' => 'd'), '', true, true, '', false, MakePretty($item_id.'d', false, $item['name']));

		if(!isset($params['showmirror']) || $params['showmirror'] === true) {
			$mirrors = $this->GetMirrors($item_id, false, $download['size']);
			$mirrorurl	= $this->CreateLink($id, 'download', $returnid, $item['name'], array('item'=>$item_id, 'dlmode' => 'm[%mirrorid%]'), '', true, true, '', false, MakePretty($item_id.'m[%mirrorid%]', false, $item['name']));
		} else $mirrors = false;

		$info = FileInfo($download['location']);
		$filename = $info['filename'];
		$fileext = $info['fileext'];

		$this->smarty->assign('th_date', $this->Lang('th_date'));
		$this->smarty->assign('th_traffic', $this->Lang('th_traffic'));
		$this->smarty->assign('th_filename', $this->Lang('filename'));
		$this->smarty->assign('th_filesize', $this->Lang('filesize'));
		$this->smarty->assign('th_downloads', $this->Lang('downloads'));
		$this->smarty->assign('th_download', $this->Lang('download'));
		$this->smarty->assign('th_available_sources', $this->Lang('available_sources'));

		$this->smarty->assign('dl_id', $item_id);
		$this->smarty->assign('dl_name', $item['name']);
		$this->smarty->assign('dl_date', strftime('%d.%m.%Y', strtotime($download['created_date'])));
		$this->smarty->assign('dl_link', $location);

		$this->smarty->assign('dl_filename', $filename);
		$this->smarty->assign('dl_fileext', $fileext);

		if(!isset($params['showdesc']) || $params['showdesc'] === true) {
			$this->smarty->assign('dl_description', $item['description']);
			$this->smarty->assign('th_dl_description',  $this->Lang('desc'));
		}

		$this->smarty->assign('dl_size', FormatFilesize($download['size']));
		$this->smarty->assign('dl_downloads', $download['downloads']);
		$this->smarty->assign('dl_traffic', FormatFilesize((int)$download['downloads'] * (int)$download['size']));

		$this->smarty->assign('dl_mirrors', $mirrors);
		$this->smarty->assign('dl_mirrorurl', $mirrorurl);

		echo $this->ProcessTemplateFromData($template[1]);
	} else {
		$this->smarty->assign('itemcount', 0);
		$this->smarty->assign('error_none', $this->Lang('error_noitem'));
		echo $this->ProcessTemplateFromData($template[0]);
	}
?>