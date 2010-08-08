<?php
	// -- Un- Installation / Upgrade -- //
	$lang['postinstall']		= 'DlM erfolgreich installiert. Stellen Sie sicher, dass /downloads/ und /tmp/downloads/ beschreibbar sind.';
	$lang['installerror']		= 'Während der Installation von DlM sind Fehler aufgetreten, möglicherweise konnten die Verzeichnisse /downloads/ und /tmp/downloads/ nicht erstellt werden. Stellen Sie sicher, dass sie vorhanden und beschreibbar sind.';
	$lang['postuninstall']		= 'DlM - Download Manager erfolgreich deinstalliert.';
	$lang['really_uninstall']	= 'Wollen Sie das DlM Modul wirklich deinstallieren?';
	
	$lang['uninstalled']	= 'Modul deinstalliert';
	$lang['installed']		= 'DlM Version %s installiert.';
	$lang['upgraded'] 		= 'DlM auf Version %s aktualisiert.';
	
	// -- misc -- //
	$lang['friendlyname']	= 'Download Manager';
	$lang['moddescription']	= 'Komfortabler und umfangreicher Download-Manager';
	
	$lang['overview']	= 'Übersicht';
	
	$lang['accessdenied'] = 'Zugang verwehrt, bitte überprüfen Sie Ihre Berechtigungen.';
	$lang['error']		= 'Fehler!';
	$lang['misc']		= 'Sonstiges';
	$lang['submit']		= 'Absenden'; 
	$lang['cancel']		= 'Abbrechen'; 
	$lang['savetemp']	= 'Zwischenspeichern'; 
	
	$lang['no_default'] = 'Keine / Ohne Vorgabe';
	
	// errors
	$lang['error']					= 'Bei der Operation ist ein Fehler aufgetreten. (Fehlercode 00)';
	$lang['error_nocategoryname']	= 'Es wurde kein Name für die Kategorie angegeben! (Fehlercode 01)';
	$lang['error_nodownloadname']	= 'Es wurde kein Name für den Download angegeben! (Fehlercode 02)';
	$lang['error_nocategory'] 		= 'Das ist keine gültige Kategorie. (Fehlercode 03)';
	$lang['error_nodownload']		= 'Das ist kein gültiger Download. (Fehlercode 04)';
	$lang['error_noitem']			= 'Das ist kein gültiger Eintrag. (Fehlercode 05)';
	$lang['error_item_delete']		= 'Beim Löschen des Eintrags ist ein Fehler aufgetreten. (Fehlercode 06)';
	$lang['error_upload']			= 'Beim Hochladen der Datei ist ein Fehler aufgetreten. (Fehlercode 07)';
	$lang['error_downloadsdir']		= 'Das Verzeichnis /downloads/ existiert nicht oder kann nicht beschrieben werden. (Fehlercode 08)';
	$lang['error_fileexists']		= 'Diese Datei existiert bereits. (Fehlercode 09)';
	$lang['error_fileext']			= 'Unzulässige Dateiendung. (Fehlercode 10)';
	$lang['error_fileextformat']	= ' hat ein ungültiges Format. (Fehlercode 10.1)';
	$lang['error_malformedurl']		= 'Die eingegebene URL ist ungültig. (Fehlercode 11)';
	$lang['error_nofile']			= 'Die angegebene Datei existiert nicht oder ist ungültig. (Fehlercode 12)';
	$lang['error_filedelete']		= 'Beim Anlegen der Datei ist ein Fehler aufgetreten. Der /downloads/-Ordner muss chmod 777 besitzen. (Fehlercode 13)';
	$lang['error_delete']			= 'Beim Löschen der Datei ist ein Fehler aufgetreten. (Fehlercode 14)';
	$lang['error_noname']			= 'Es wurde kein Name angegeben (Fehlercode 15)';
	$lang['error_dbinsert']			= 'Beim Eintragen in die Datenbank ist ein Fehler aufgetreten. (Fehlercode 16)';
	
	$lang['error_notfound']			= 'Die angegebene Datei wurde nicht gefunden. (Fehlercode 404)';
	$lang['error_forbidden']		= 'Ihr Browser hat einen unzulässiger Referer übertragen.<br />Direktlinks sind nicht gestattet. (Fehlercode 403)';
	
	// misc
	$lang['node_children'] = 'Untergeordnete Kategorien und Downloads';
	
	$lang['download']	= 'Download';
	$lang['downloads']	= 'Downloads';
	$lang['category']	= 'Kategorie';
	$lang['name']		= 'Name';
	$lang['desc'] 		= 'Beschreibung';
	$lang['url'] 		= 'Adresse (URL)';
	$lang['toggle'] 	= 'Aus- / Einblenden';
	
	$lang['and']	= 'und';
	$lang['or']		= 'oder'; 
	
	$lang['up']		= 'Hoch'; 
	$lang['down']	= 'Runter'; 
	
	$lang['unselect_children'] = 'Untergeornete Knoten abwählen';
	$lang['all'] = 'Alle';
	$lang['selected'] = 'Ausgewählte'; 
	$lang['reverse_selection'] = 'Auswahl umkehren'; 
	
	$lang['delete']		= 'Löschen';
	$lang['activate']	= 'Aktivieren';
	$lang['deactivate']	= 'Deaktivieren';
	$lang['move']		= 'Verschieben'; 
	$lang['suborder']	= 'Unterordnen'; 
	
	$lang['areyousure']	= 'Sind Sie sich sicher?';
	$lang['areyousure_item'] = "Sind Sie sich sicher, dass Sie diesen Eintrag löschen wollen? Alle untergeordneten Einträge werden ebenfalls gelöscht.";
	$lang['areyousure_items'] = "Sind Sie sich sicher, dass Sie diese %num Einträge löschen wollen? Alle untergeordneten Einträge werden ebenfalls gelöscht.";
	$lang['areyousure_mirror']	= 'Wollen Sie diesen Mirror wirklich löschen?';
	
	$lang['template']	= 'Template';
	
	// -- Adminpanel -- //
	
	// - Tabs - //
	$lang['title_prefs']	= 'Optionen';
	$lang['title_categories']	= 'Kategorien';
	$lang['title_general']		= 'Übersicht';
	
	// - General tab - //
	$lang['no_children'] = 'Es sind keine untergeordneten Einträge vorhanden.';
	
	$lang['th_name'] = 'Name';
	$lang['th_type'] = 'Typ';
	$lang['th_id'] 	= 'ID';
	$lang['th_active'] = 'Aktiv';
	$lang['th_reorder'] = 'Ordnen';
	$lang['th_actions'] = 'Aktionen';
	
	// - Preferences tab - //
	$lang['blacklist_desc'] = '<strong>Blacklist</strong> (verbotene Dateiendungen)';
	$lang['whitelist_desc'] = '<strong>Whitelist</strong> (erlaubte Dateiendungen, falls gesetzt sind <em><b>nur</b></em> die angegebenen Dateitypen erlaubt)';
	
	$lang['extensions']	= 'Dateiendungen';
	$lang['extensions_desc'] = 'Dateiendungen getrennt durch Semikolon <b>;</b> und <b>ohne</b> führenden Punkt (z.B. jpg;png;gif;zip)';
	
	$lang['returnid_desc']	= '<strong>returnid</strong> (ID der Seite, in der DlM eingebunden werden soll (nur für die Nutzung mit Pretty-URLs - jeder andere Content auf der entsprechenden Seite wird überschrieben))';
	$lang['obfuscate_desc']	= '<strong>Verschleierung</strong> (Wie soll der Dateiname im /downloads/-Verzeichnis verschleiert werden, um Hotlinking zu unterbinden?)';
	$lang['obfuscate_list']	= 'Keine Verschleierung;temporäre Kopie;Ausgabe via PHP';
	$lang['referer_desc']	= '<strong>Referer-Filterung</strong> (welche Referer sollen zugelassen werden)';
	$lang['referer_list']	= 'keine Filterung;nur diese Domain erlauben;benutzerdefiniert';
	$lang['allowed_referer'] = '<strong>Erlaubte Referer</strong> (getrennt durch Semikolon <b>;</b> z.B. <span style="color: #666">example.com;somedomain.com</span> - diese Einstellung zeigt nur Wirkung, falls als Referer-Filterung "benutzerdefiniert" gewählt ist)';
	$lang['js_effects_text'] = '<strong>JavaScript Effekte</strong> (<b style="color: #ff0000">Achtung:</b> Bei der Einstellung <em>Alle</em> kann es bei vielen Einträgen zu Performance-Problemen kommen)';
	$lang['js_effetcs_list'] = 'Keine;Einfach;Alle';
	
	// add / edit / delete items //
	$lang['name'] 		= 'Name';
	$lang['filesize'] 	= 'Dateigröße (in Bytes)';
	$lang['desc'] 		= 'Beschreibung';
	$lang['location']	= 'Adresse';
	$lang['upload'] 	= 'Datei hochladen';
	
	$lang['allowed_extensions'] = 'Erlaubte '.$lang['extensions'];
	$lang['forbidden_extensions'] = 'Verbotene '.$lang['extensions'];
	
	$lang['mirror'] 		= 'Mirror';
	$lang['mirror_desc']	= 'Hier können zusätzliche Downloadquellen hinzugefügt werden.'; 
	
	$lang['add_mirror']		= 'Mirror hinzufügen';
	
	$lang['parent_category'] = 'Übergeordnete Kategorie';
	$lang['path_text'] = 'Pfad';
	
	$lang['edit_location'] = 'Diese Datei löschen und ersetzen';
	
	$lang['expandall'] = 'Alle Kategorien ausklappen';
	$lang['contractall'] = 'Alle Kategorien einklappen';
	
	$lang['add_category']	= 'Kategorie hinzufügen';
	$lang['edit_category']	= 'Kategorie bearbeiten';
	
	$lang['category_moved'] 	= 'Kategorie wurde verschoben.';
	$lang['category_deleted']	= 'Kategorie wurde erfolgreich gelöscht.';
	$lang['category_updated']	= 'Änderungen an der Kategorie erfolgreich gespeichert.';
	$lang['category_added'] 	= 'Kategorie erfolgreich hinzugefügt';
	
	
	$lang['add_download']	= 'Download hinzufügen';
	$lang['edit_download']	= 'Download bearbeiten';
	
	$lang['download_moved']		= 'Download wurde verschoben.';
	$lang['download_deleted']	= 'Download wurde erfolgreich gelöscht.';
	$lang['download_updated']	= 'Änderungen an dem Download erfolgreich gespeichert.';
	$lang['download_added']		= 'Download erfolgreich hinzugefügt';
	
	$lang['edit_mirror']	= 'Mirror bearbeiten';	
	$lang['mirror_updated']	= 'Mirror erfolgreich bearbeitet.';
	
	$lang['delete_item']	= 'Eintrag löschen'; 
	$lang['item_moved']		= 'Eintrag wurde verschoben.';
	$lang['items_activated']= 'Einträge wurden (de)aktiviert.';
	$lang['item_deleted']	= 'Eintrag wurde erfolgreich gelöscht.';
	$lang['items_deleted']	= 'Einträge wurden erfolgreich gelöscht.';
	$lang['item_updated']	= 'Eintrag erfolgreich bearbeitet.';
	
	// -- Events - evd: event description -- //
	$lang['evd-DownloadAdded']  = 'Ausführen, nachdem ein Download hinzugefügt wurde.';
	$lang['evd-DownloadEdited']	= 'Ausführen, nachdem ein Download bearbeitet wurde.';
	#$lang['evd-DownloadMoved']	= 'Ausführen, nachdem ein Download verschoben wurde.';
	#$lang['evd-DownloadDeleted']= 'Ausführen, nachdem ein Download gelöscht wurde.';
	
	$lang['evd-CategoryAdded']	= 'Ausführen, nachdem eine Kategorie hinzugefügt wurde.';
	$lang['evd-CategoryEdited']	= 'Ausführen, nachdem eine Kategorie bearbeitet wurde.';
	#$lang['evd-CategoryMoved']	= 'Ausführen, nachdem eine Kategorie verschoben wurde.';
	#$lang['evd-CategoryDeleted']= 'Ausführen, nachdem eine Kategorie gelöscht wurde.';
	
	$lang['evd-ItemMoved']		= 'Ausführen, nachdem ein Knoten verschoben wurde.';
	$lang['evd-ItemDeleted']	= 'Ausführen, nachdem ein Knoten gelöscht wurde.';
	
	// -- Module - Help -- //
	$lang['help-item'] = 'ID des Knotens, der angezeigt werden soll. Ist der Eintrag eine Typ Kategorie, so wird eine Übersicht angezeigt. Handelt es sich um einen Download, so wird die Detail-Seite angezeigt.';
	$lang['help-root'] = 'ID des Ursprungsknotens. Ist dieser Parameter angegeben, wird der Pfad ausgehend von <em>root</em> angezeigt. (Knoten, die nicht unterhalb <em>root</em> liegen, können zwar auch angesehen werden, dort ist die Pfad-Anzeige allerdings fehlerhaft).';
	$lang['help-dlmode'] = 'Interner Parameter. (Steuert, ob ein Download vom Mirror oder von der primären Quelle geladen wird).';
	$lang['help-showpath'] = 'Soll der Pfad zum aktuellen Knoten angezeigt werden?';
	$lang['help-showdesc'] = 'Soll die Beschreibung angezeigt werden?';
	$lang['help-showmirror'] = 'Soll eine Mirror-Übersicht bei Downloads eingeblendet werden?';
	
	$lang['changelog'] = '
	<dl>
		<dt>0.6</dt>
		<dd>DlM kann jetzt vom Suche-Modul indiziert und durchsucht werden</dd>
		<dd>Mirror-Administration überarbeitet</dd>
		<dd>einige kleine Fehler beseitigt</dd>
		
		<dt>0.5.2b</dt>
		<dd>Bessere Unterstützung für Pretty-URLs</dd>
		
		<dt>0.5.1b</dt>
		<dd>englische Übersetzung hinzugefügt</dd>
		
		<dt>0.5b</dt>
		<dd>erste Veröffentlichung von DlM</dd>
	</dl>';
	$lang['help'] = '
	
	<h3>Was macht dieses Modul?</h3><p>Dieses Modul stellt einen umfangreichen und komfortabel zu bedienenden Download-Manager zur Verfügung.</p>
	<h3>Wie wird dieses Modul eingesetzt?</h3>DlM lässt sich per <pre style="display: inline">{cms_module module="DlM" <em>parameter</em>}</pre> einbinden. <strong>Wichtig:</strong> Bei der Nutzung von Pretty-URLs wird der sonstige Content auf der Seite überschrieben.
	<h3 style="color: #ff0000">Wichtig</h3>
	<p><tt>/downloads/</tt> und <tt>/tmp/downloads/</tt> müssen existieren und beschreibbar sein (am besten chmod 0777) - ansonsten wird DlM nicht richtig funktionieren.</p>
	
	';
?>
