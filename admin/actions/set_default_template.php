<?php
	if(!isset($gCms)) exit;
	if(!$this->CheckPermission('Use DLM') || !$this->CheckPermission('Modify Templates')) exit;

	$tpl_name = trim(urldecode($params['tpl_name']));
	$tpl_name = !empty($tpl_name) ? $tpl_name : false;
	if($tpl_name === false) $this->Redirect($id, 'defaultadmin', $returnid);

	$tpl = $this->_GetTemplate($tpl_name);

	if(trim($tpl)!='') {
		$query = 'UPDATE '.cms_db_prefix().'module_dlm_items SET description = ? WHERE dl_id = ?';
		$result = $this->db->Execute($query, array($tpl_name, 0));
	} else
		$this->errors[] = $this->Lang('error_notplcontent');
?>