<?php
	if (!isset($gCms)) exit;
	if (!$this->CheckPermission('Manage Downloads')) exit;

	$this->smarty->assign('startform', $this->CreateFormStart($id, 'listactions', $returnid));
	$this->smarty->assign('endform', $this->CreateFormEnd());

	$this->smarty->assign('no_children', $this->Lang('no_children'));
	$this->smarty->assign('js_effects', $this->GetPreference('js_effects', 0));

	$items = $this->GetTreeAdmin(0, $id);
	$this->smarty->assign('rootlevel', 0);

	$this->smarty->assign_by_ref('items', $items);
	$this->smarty->assign('itemcount', count($items));

	$this->smarty->assign('th_name', $this->Lang('th_name'));
	$this->smarty->assign('th_type', $this->Lang('th_type'));
	$this->smarty->assign('th_id', $this->Lang('th_id'));
	$this->smarty->assign('th_active', $this->Lang('th_active'));
	$this->smarty->assign('th_reorder', $this->Lang('th_reorder'));
	$this->smarty->assign('th_actions', $this->Lang('th_actions'));

	$this->smarty->assign('areyousure_item', $this->Lang('areyousure_item'));
	$this->smarty->assign('areyousure_items', $this->Lang('areyousure_items'));

	$this->smarty->assign('unselect_children', $this->Lang('unselect_children'));
	$this->smarty->assign('all', $this->Lang('all'));
	$this->smarty->assign('selected', $this->Lang('selected'));
	$this->smarty->assign('reverse_selection', $this->Lang('reverse_selection'));

	$this->smarty->assign('delete', $this->Lang('delete'));
	$this->smarty->assign('move', $this->Lang('move'));
	$this->smarty->assign('activate', $this->Lang('activate'));
	$this->smarty->assign('deactivate', $this->Lang('deactivate'));
	$this->smarty->assign('suborder', $this->Lang('suborder'));

	$this->smarty->assign('expandall', $this->CreateHandlerLink($id, 'expandall', $returnid, $this->theme->DisplayImage('icons/system/expandall.gif', $this->Lang('expandall'),'','','systemicon').$this->Lang('expandall'), array(), '', false, false, ''));
	$this->smarty->assign('contractall', $this->CreateHandlerLink($id, 'contractall', $returnid, $this->theme->DisplayImage('icons/system/contractall.gif', $this->Lang('contractall'),'','','systemicon').$this->Lang('contractall'), array(), '', false, false, ''));

	$this->smarty->assign('add_category', $this->CreateLink($id, 'add_category', $returnid, $this->theme->DisplayImage('icons/system/newobject.gif', $this->Lang('add_category'),'','','systemicon').$this->Lang('add_category'), array(), '', false, false, ''));
	$this->smarty->assign('add_download', $this->CreateLink($id, 'add_download', $returnid, $this->theme->DisplayImage('icons/system/newobject.gif', $this->Lang('add_download'),'','','systemicon').$this->Lang('add_download'), array(), '', false, false, ''));

	echo $this->ProcessTemplate('admin/common.js.tpl');
	echo $this->ProcessTemplate('admin/rows.tpl');
	echo $this->ProcessTemplate('admin/itemlist.tpl');
	echo $this->ProcessTemplate('admin/general.tpl');
?>