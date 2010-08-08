{if !isset($error_none)}
<div class="dlm detail">{capture assign="pagetitle"}{$pagetitle}{$path[1]}{$dl_name}{/capture}

{if strlen($path[0]) > 0}
<div style="margin-bottom: 15px; font-size: 0.75em">
	<strong>{$path_text}</strong>: {$path[0]}
</div>
{/if}

<h3>{$dl_name}</h3>
<a href="{$dl_link}" class="dlbutton" rel="nofollow"> <span>Download</span> </a>
<div style="margin-left: 10px; max-width: 400px">
	<span style="float: right">
		<strong>Datum</strong>: {$dl_date}<br />
		<strong>Traffic</strong>: {$dl_traffic}
	</span>
	<span style="float: left">
		<strong>Dateiname</strong>: {$dl_filename|truncate:25}{$dl_fileext}<br />
		<strong>Dateigröße</strong>: {$dl_size}<br />
		<strong>Downloads</strong>: {$dl_downloads}<br />
	</span>
	<br style="clear: both" />
</div>
{if $dl_mirrors !== false}
<div style="margin-top: 15px">
	<h4>Verfügbare Downloadquellen</h4>
	<table cellspacing="1" class="default" style="margin: auto; width: 95%">
		<thead>
			<tr>
				<th>Mirror</th>
				<th style="width: 1px"><a title="Downloads">DL</a></th>
				<th style="width: 1px">Traffic</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$dl_mirrors item=item key=key}
		{cycle values="row1,row2" assign=rowclass}
			<tr class="{$rowclass}">
				<td><a href="{$dl_mirrorurl|urldecode|replace:'[%mirrorid%]':$key}" rel="nofollow">{$item.name}</a></td>
				<td style="text-align: center">{$item.downloads}</td>
				<td style="text-align: center">{$item.traffic}</td>
			</tr>
		{/foreach}
		</tbody>
		
	</table>
</div>
{/if}


{if strlen($dl_description) > 0} <br />
<h4 id="Beschreibung">{$dl_description_text}</h4>
<div style="margin-left: 10px; overflow: hidden" id="dldesc_{$dl_id}">
	{eval var=$dl_description}
</div>

{*<a href="javascript:toggle('dldesc_{$dl_id}')" style="display: none">Weiterlesen...</a>


{literal}<script type="text/javascript"><!--
	function toggle(id, show) {
		var el = document.getElementById(id);
		
		if(show == true) {
			el.style.maxHeight = 'none';
			el.nextSibling.innerHTML = 'Einklappen...';
		} else {
			if(el.style.maxHeight != 'none') {
				el.style.maxHeight = 'none';
				el.nextSibling.innerHTML = 'Einklappen...';
			} else {
				el.style.maxHeight = '10em';
				el.nextSibling.innerHTML = 'Weiterlesen...';
				window.scrollTo(0, el.offsetTop - 100);	
			}	
		}		
	}
	
	var el = document.getElementById('{/literal}dldesc_{$dl_id}{literal}');
	if(el.clientHeight > 130) {
		el.style.maxHeight = '10em';
		el.style.margin = '0 0 1em 10px';
		
		el = el.nextSibling.style.display = 'inline';
	}
	
	var links = document.getElementById('{/literal}dldesc_{$dl_id}{literal}').getElementsByTagName('a');
	for(i=0;i<links.length;++i) {
		if(links[i].href.indexOf('#') != -1) {
			links[i].setAttribute('onclick', "toggle('{/literal}dldesc_{$dl_id}{literal}', true)");
		}
	}
--></script>{/literal}
*}

{/if}
</div>
{else}
<strong>{$error_none}</strong>
{/if}