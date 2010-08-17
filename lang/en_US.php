<?php
	// -- Un- Installation / Upgrade -- //
	$lang['postinstall']		= 'DlM installed. Make sure /downloads/ and /tmp/downloads/ are writeable and the permissions "Use DlM" and "Set DlM Prefs" are set correctly.';
	$lang['installerror']		= 'An error occurred during the installation of DlM. Possibly the folders /downloads/ and /tmp/downloads/ could not be created. Make sure they exist and are writable.';
	$lang['postuninstall']		= 'DlM - Download Manager uninstalled.';
	$lang['really_uninstall']	= 'Do you really want to remove DlM?';
	
	$lang['uninstalled']	= 'DlM uninstalled.';
	$lang['installed']		= 'DlM version %s installed.';
	$lang['upgraded'] 		= 'DlM upgraded to version %s.';
	
	// -- misc -- //
	$lang['friendlyname']	= 'Download Manager';
	$lang['moddescription']	= 'Comfortable und 	comprehensive Download-Manager';
	
	$lang['overview']	= 'Overview';
	
	$lang['accessdenied'] = 'Access denied, please check your Permissions.';
	$lang['error']		= 'Error.';
	$lang['misc']		= 'Miscellaneous';
	$lang['submit']		= 'Submit'; 
	$lang['cancel']		= 'Cancel'; 
	$lang['savetemp']	= 'Apply';
	
	$lang['no_default'] = 'None / No Default';
	
	// errors
	$lang['error']					= 'An error occurred during the last operation. (Errorcode 00)';
	$lang['error_nocategoryname']	= 'You have to specify a name for the category. (Errorcode 01)';
	$lang['error_nodownloadname']	= 'You have to specify a name for the download. (Errorcode 02)';
	$lang['error_nocategory'] 		= 'That\'s no valid category. (Errorcode 03)';
	$lang['error_nodownload']		= 'That\'s no valid download. (Errorcode 04)';
	$lang['error_noitem']			= 'That\'s no valid item. (Errorcode 05)';
	$lang['error_item_delete']		= 'Could not delete Item. (Errorcode 06)';
	$lang['error_upload']			= 'File could not be uploaded. (Errorcode 07)';
	$lang['error_downloadsdir']		= 'The folder /downloads/ doesn\'t exist or is not writable. (Errorcode 08)';
	$lang['error_fileexists']		= 'This file already exists. (Errorcode 09)';
	$lang['error_fileext']			= 'file extension is not permitted. (Errorcode 10)';
	$lang['error_fileextformat']	= ' has no valid formar. (Errorcode 10.1)';
	$lang['error_malformedurl']		= 'The submitted URL is invalid. (Errorcode 11)';
	$lang['error_nofile']			= 'The specified file isn\'t existant or invalid. (Errorcode 12)';
	$lang['error_filedelete']		= 'An error occured while writing the file. The /downloads/-folder must be writable. (Errorcode 13)';
	$lang['error_delete']			= 'An error occured while deleting the file. (Errorcode 14)';
	$lang['error_noname']			= 'You didn\'t specify a name. (Errorcode 15)';
	$lang['error_dbinsert']			= 'An error occured while writing to the database. (Errorcode 16)';
	
	$lang['error_notfound']			= 'The file you specified yould not be found. (Errorcode 404)';
	$lang['error_forbidden']		= 'Your Browser submitted and illegal referer.<br />Hotlinking is not allowed. (Errorcode 403)';
	
	// misc
	$lang['node_children'] = 'Children';
	
	$lang['download']	= 'Download';
	$lang['downloads']	= 'Downloads';
	$lang['category']	= 'Category';
	$lang['name']		= 'Name';
	$lang['desc'] 		= 'Description';
	$lang['url'] 		= 'Adress (URL)';
	$lang['toggle'] 	= 'Toggle visibility';
	
	$lang['and']	= 'and';
	$lang['or']		= 'or'; 
	
	$lang['up']		= 'Up'; 
	$lang['down']	= 'Down'; 
	
	$lang['unselect_children'] = 'Unselect children';
	$lang['all'] = 'all';
	$lang['selected'] = 'selected'; 
	$lang['reverse_selection'] = 'reverse selection'; 
	
	$lang['delete']		= 'delete';
	$lang['activate']	= 'activate';
	$lang['deactivate']	= 'deactivate';
	$lang['move']		= 'move'; 
	$lang['suborder']	= 'suborder'; 
	
	$lang['areyousure']	= 'Are you sure?';
	$lang['areyousure_item'] = 'Are you sure to delete this node? All children will be deleted, too!';
	$lang['areyousure_items'] = 'Are you sure to delete this %num nodes? All children will be deleted, too!';
	$lang['areyousure_mirror']	= 'Are you sure to delete this mirror?';
	
	$lang['template']	= 'Template';
	
	
	// -- Adminpanel -- //
	
	// - Tabs - //
	$lang['title_prefs'] = 'Preferences';
	$lang['title_categories'] = 'Categories';
	$lang['title_general'] = 'Overview';
	
	// - General tab - //
	$lang['no_children'] = 'No children.';
	
	$lang['th_name']	= 'Name';
	$lang['th_type']	= 'Type';
	$lang['th_id'] 		= 'ID';
	$lang['th_active']	= 'Active';
	$lang['th_reorder']	= 'Order';
	$lang['th_actions']	= 'Actions';
	
	// - Preferences tab - //
	$lang['blacklist_desc'] = '<strong>Blacklist</strong> (forbidden file extensions)';
	$lang['whitelist_desc'] = '<strong>Whitelist</strong> (allowed file extensions. If specified, <em><b>only</b></em> the listed file extensions are allowed!)';
	
	$lang['extensions']	= 'File extensions';
	$lang['extensions_desc'] = 'File extensions - separated whith semicolon <b>;</b> and <b>without</b> leading dot (e.g. <tt>jpg;png;gif;zip</tt>)';
	
	$lang['returnid_desc']	= '<strong>returnid</strong> (ID of the page, DlM should be displayed in (only for use with Pretty-URLs - overwrites all other content on the page))';
	$lang['obfuscate_desc']	= '<strong>Obfuscation</strong> (how should the source file in /downloads/ be obfuscated to prevent hotlinking?)';
	$lang['obfuscate_list']	= 'No obfuscation;temporary copy;Output via PHP';
	$lang['referer_desc']	= '<strong>Referer-Filtering</strong> (Which referers should be allowed?)';
	$lang['referer_list']	= 'no filtering;allow only this domain;userdefined';
	$lang['allowed_referer'] = '<strong>Allowed Referer</strong> (separated by semicolon <b>;</b> e.g. <span style="color: #666">example.com;somedomain.com</span> - this only takes effect if "userdefined" ist chosen as referer-filtering)';
	$lang['js_effects_text'] = '<strong>JavaScript Effects</strong> (<b style="color: #ff0000">Attention:</b> When <em>All</em> is selected there could be performance issues on high item-count.)';
	$lang['js_effetcs_list'] = 'None;Simple;All';
	
	// - Templates tab - //
	$lang['edit_template']	= 'Edit template';
	$lang['delete_template']= 'Delete template';
	
	//$lang['welcome_text'] = '<p>Übersicht über ihre Kategorien:</p>';
	
	// add / edit / delete items //
	$lang['name'] 		= 'Name';
	$lang['filesize'] 	= 'Filesize (in Bytes)';
	$lang['desc'] 		= 'Description';
	$lang['location']	= 'Adress';
	$lang['upload'] 	= 'Upload file';
	
	$lang['allowed_extensions'] = 'Allowed '.$lang['extensions'];
	$lang['forbidden_extensions'] = 'Forbidden '.$lang['extensions'];
	
	$lang['mirror'] 		= 'Mirror';
	$lang['mirror_desc']	= 'You can add additional download-sources here.'; 
	
	$lang['add_mirror']		= 'Add mirror';	
	
	$lang['parent_category'] = 'Parent Category';
	$lang['path_text'] = 'Path';
	
	$lang['edit_location'] = 'Delete and replace this file';
	
	$lang['expandall'] = 'Expand all categories';
	$lang['contractall'] = 'Contract all categories';
	
	$lang['add_category']	= 'Add category';
	$lang['edit_category']	= 'Edit category';
	
	$lang['category_moved'] 	= 'Category successfully moved.';
	$lang['category_deleted']	= 'Category successfully deleted.';
	$lang['category_updated']	= 'Changes on category saved successfully.';
	$lang['category_added'] 	= 'Category successfully added.';
	
	$lang['add_download']	= 'Add Download';
	$lang['edit_download']	= 'Edit Download';
	
	$lang['download_moved']		= 'Download successfully moved.';
	$lang['download_deleted']	= 'Download successfully deleted.';
	$lang['download_updated']	= 'Changes on download saved successfully.';
	$lang['download_added']		= 'Download successfully added.';
	
	$lang['edit_mirror']	= 'Edit Mirror';	
	$lang['mirror_updated']	= 'Mirror edited.';
	
	$lang['delete_item']	= 'Delete  item'; 
	$lang['item_moved']		= 'Item successfully moved.';
	$lang['items_activated']= 'Item successfully (de)activated.';
	$lang['item_deleted']	= 'Item successfully deleted.';
	$lang['items_deleted']	= 'Items successfully deleted.';
	$lang['item_updated']	= 'Changes on item saved successfully.';

	
	// -- Events - evd: event description -- //
	$lang['evd-DownloadAdded']  = 'Send after a Download was added.';
	$lang['evd-DownloadEdited']	= 'Send after a Download was edited.';
	#$lang['evd-DownloadMoved']	= 'Ausführen, nachdem ein Download verschoben wurde.';
	#$lang['evd-DownloadDeleted']= 'Ausführen, nachdem ein Download gelöscht wurde.';
	
	$lang['evd-CategoryAdded']	= 'Send after a Category was added.';
	$lang['evd-CategoryEdited']	= 'Send after a Category was edited.';
	#$lang['evd-CategoryMoved']	= 'Ausführen, nachdem eine Kategorie verschoben wurde.';
	#$lang['evd-CategoryDeleted']= 'Ausführen, nachdem eine Kategorie gelöscht wurde.';
	
	$lang['evd-ItemMoved']		= 'Send, after a node was moved.';
	$lang['evd-ItemDeleted']	= 'Send, after a node was deleted.';
	
	
	// -- Help -- //
	$lang['help-item']	= 'ID of the node that should be shown. If it\'s a category an overview will be displayed. Otherwise there will be a detailpage for the download.';
	$lang['help-root']	= 'ID of the root-node. If this parameter is specified, the path will be starting from <em>root</em>. (Nodes, that are not children of root can be display, but there will be no correct path).';
	$lang['help-returnid']	= 'ID of the page DlM should be embedded in - standard can be set in the Preferences-Tab - only necessary for use with Pretty-URLs.';
	$lang['help-dlmode']	= 'Internal Parameter. (controls if a download is downloaded from a mirror or the primary source).';
	$lang['help-showpath']	= 'Should the path to the current node be shown?';
	$lang['help-showdesc']	= 'Should the description be displayed?';
	$lang['help-showmirror']= 'Should a mirror-overview be displayed?';	
	
	$lang['changelog'] = '
	<dl>
		<dt>0.6</dt>
		<dd>DlM can now be indexed and searched by search-module</dd>
		<dd>reworked mirror-management</dd>
		<dd>some minor bugfixes</dd>
		
		<dt>0.5.2b</dt>
		<dd>Added better support for Pretty-URLs</dd>
		
		<dt>0.5.1b</dt>
		<dd>Added English translation</dd>
		
		<dt>0.5b</dt>
		<dd>First Release</dd>
	</dl>';
	
	$lang['help'] = '
	
	<h3 style="color: #ff0000">What is this Module for?</h3><p>This module provides a fully functional and easy to use Download-Manager with much possibilities.</p>
	<h3>How to use it?</h3>DlM is embedded using <tt>{cms_module module="DlM" <em>parameter</em>}</tt>. <strong>Attention:</strong> If you use Pretty-URLs all other content on the page (specified by <em>returnid</em>) will be overwritten.<br />
	<h3>Important</h3>
	<p><tt>/downloads/</tt> and <tt>/tmp/downloads/</tt> have to exist and be writable (best chmod 0777) - otherwise DlM will refuse to work.</p>
	';
?>
