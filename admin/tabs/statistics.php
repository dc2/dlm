<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$query = 'SELECT SUM(downloads) AS dlcnt FROM '.cms_db_prefix().'module_dlm_downloads';
	$result = $this->db->execute($query);
	$dlcnt = $result->FetchRow();
	$dlcnt = $dlcnt['dlcnt'];

	$query = 'SELECT i.dl_id, i.name, d.downloads FROM '.cms_db_prefix().'module_dlm_downloads d, '.cms_db_prefix().'module_dlm_items i WHERE i.type = 1 AND i.dl_id = d.dl_parent_id ORDER BY d.downloads DESC LIMIT 15';
	$result = $this->db->execute($query);
	$popular_downloads = $result->GetArray();

	$query = 'SELECT i.dl_id, i.name, (d.downloads * d.size) AS traffic FROM '.cms_db_prefix().'module_dlm_downloads d, '.cms_db_prefix().'module_dlm_items i WHERE i.type = 1 AND i.dl_id = d.dl_parent_id ORDER BY traffic DESC LIMIT 15';
	$result = $this->db->execute($query);
	$traffic_downloads = $result->GetArray();

	$query = 'SELECT i.dl_id, i.name, d.created_date AS date FROM '.cms_db_prefix().'module_dlm_downloads d, '.cms_db_prefix().'module_dlm_items i WHERE i.type = 1 AND i.dl_id = d.dl_parent_id ORDER BY d.created_date DESC LIMIT 15';
	$result = $this->db->execute($query);
	$new_downloads = $result->GetArray();

	for($i = 0; $i < count($popular_downloads); $i++) {
		$popular_downloads[$i]['link'] = $this->CreateLink($id, 'edit_download', $returnid, $popular_downloads[$i]['name'], array('item_id'=>$popular_downloads[$i]['dl_id']));
		$new_downloads[$i]['link'] = $this->CreateLink($id, 'edit_download', $returnid, $new_downloads[$i]['name'], array('item_id'=>$new_downloads[$i]['dl_id']));

		$traffic_downloads[$i]['link'] = $this->CreateLink($id, 'edit_download', $returnid, $traffic_downloads[$i]['name'], array('item_id'=>$traffic_downloads[$i]['dl_id']));
		$traffic_downloads[$i]['traffic'] = FormatFilesize($traffic_downloads[$i]['traffic']);
	}

	$this->smarty->assign('dlcnt', $dlcnt);
	$this->smarty->assign_by_ref('popular_downloads', $popular_downloads);
	$this->smarty->assign_by_ref('traffic_downloads', $traffic_downloads);
	$this->smarty->assign_by_ref('new_downloads', $new_downloads);

	$this->smarty->assign('th_name', $this->Lang('th_name'));
	$this->smarty->assign('th_downloads', 'DL');
	$this->smarty->assign('th_date', $this->Lang('th_date'));

	echo $this->ProcessTemplate('admin/statistics.tpl');
?>