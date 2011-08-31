<?php
	if (!isset($gCms)) exit;
	
	$action = &$_GET['maction'];
	
	switch($action) {
		case 'download_details':
			$item_id = (int)$_GET['item_id'];
			$item = $this->tree->GetItem($item_id);
			
			$item = array_merge($item, $this->GetDownload($item_id));
			
			unset($item['dl_left'], $item['dl_right'], $item['dl_level'], $item['expand'], $item['active'], $item['parent'], $item['type'], $item['nflag'], $item['dl_parent_id'], $item['modified_date']);
			
			$item['size'] = FormatFilesize($item['size']);
			$item['created_date'] = strftime('%d.%m.%Y', strtotime($item['created_date']));
			
			//$item['dl_link'] = $this->CreateLink($id, 'download', $returnid, $item['name'], array('item'=>$item_id, 'dlmode' => 'd'), '', true, true, '', false, MakePretty($item_id.'d', false, $item['name']));

			echo json_encode($item);
			
		break;
		
		default:
			//echo 'eek';
	}
?>
