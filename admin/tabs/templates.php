<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Modify Templates')) exit;

	$this->smarty->assign('th_actions', $this->Lang('th_actions'));
	$this->smarty->assign('th_templates', lang('templates'));

	#$this->SetTemplate('test2', 'blubb');
	$this->smarty->assign('templates', $this->ListTemplates($id, $returnid));

	echo $this->DisplayErrors();
	echo $this->ProcessTemplate('admin/templates.tpl');
?>