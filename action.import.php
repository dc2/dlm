<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Use DLM')) exit;

	$return = !empty($params['return']) ? explode(',', $params['return']) : false;

	$this->smarty->assign('headline', $this->Lang('import_folder'));

	$this->smarty->assign('submit', $this->CreateInputSubmit($id, 'submit', $this->Lang('submit')));
	$this->smarty->assign('cancel', $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel')));

	$this->smarty->assign('startform', $this->CreateFormStart($id, 'edit_category', $returnid));
	$this->smarty->assign('endform', $this->CreateFormEnd());

	$this->smarty->assign('hidden', ($return !== false ? $this->CreateInputHidden($id, 'return', implode(',', $return)) : ''));

	echo $this->ProcessTemplate('admin/import.tpl');
?>