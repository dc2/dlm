{cms_jquery exclude='jquery-ui-1.8.14.min.js,jquery.ui.nestedSortable-1.3.4.js,jquery.json-2.2.js' append='modules/DLM/libs/js/jquery.tools.min.js'}

<script type="text/javascript">
<!--
{literal}
$(function() {
	$('.downloadDetails').click(getDetailsAjax);
});

function getDetailsAjax($link) {
	var $link = $($link.currentTarget);
	
	if(!$link.next().hasClass('tooltip')) {
		$.ajax({
			url: 'index.php?mact=DLM,cntnt01,frontend_ajax,1&showtemplate=false&maction=download_details&item_id='+$link.attr('data-id'),
			
			success: function(data) {
				data = $.parseJSON(data);
				
				$link.after(
					'<div class="tooltip">'+
						'<h4><a href="'+$link.attr('href')+'" title="Download-Details anzeigen">'+data.name+'</a></h4>'+
						data.description+
					'</div>'
				);
				
				var $tooltip = $link.tooltip({
					position: "bottom left",
					offset: [-50, 100],
					opacity: 0.9,
					delay: 200,
					effect: 'fade',
					fadeInSpeed: 300,
					events: {
						def: "click,mouseout"
					}
				});
				
				$link.trigger('click');
			}
		});
	}
	
	return false;
}
{/literal}
-->
</script>

{if !isset($error_none)}
	<div class="dlm">
		{capture assign="pagetitle"}{$pagetitle}{$path[1]}{$headline|default:'Addon-Übersicht'}{/capture}
		{if strlen($path[0]) > 0}
			<div style="margin-bottom: 15px; font-size: 0.75em"><strong>{$th_path}</strong>: {$path[0]}</div>
		{/if}
		<h3 style="margin-bottom: 5px">{$headline|default:'Addon-Übersicht'}</h3>
		{if $itemcount > 0}
			{if trim($description) != ''}<p class="description">{$description}</p>{/if}
			<table cellspacing="1" class="default" style="margin: auto; width: 95%">
				{*<thead>
					<tr>
						<th>{$th_name}</th>
						<th style="width:20px"><a title="{$th_downloads}">Einträge</a></th>
					</tr>
				</thead>*}
				<tbody>
				{foreach from=$items item='item'}
				{cycle values="row1,row2" assign=rowclass}
					<tr class="{$rowclass}">
						<td>
							{if $item.type == 1}
								<a href="{$item.href}" class="downloadDetails" data-id="{$item.id}">{$item.name}</a>
							{else}
								<a href="{$item.href}">{$item.name}</a>
							{/if}
							{if $item.downloadurl !== false}
								<a href="{$item.downloadurl}" class="dlbutton_small" style="float:right" rel="nofollow">
									<span>{$th_download}</span>
								</a>
							{/if}
						</td>
						<td style="text-align:center">{$item.files}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		{else}
			<strong>{$no_children}</strong><br />
		{/if}
	</div>
{else}
	<strong>{$error_none}</strong>
{/if}

<!-- // :::TPL-SEPARATOR::: // -->

{if !isset($error_none)}
	<div class="dlm detail">{capture assign="pagetitle"}{$pagetitle}{$path[1]}{$dl_name}{/capture}
		{if strlen($path[0]) > 0}
			<div style="margin-bottom: 15px; font-size: 0.75em">
				<strong>{$th_path}</strong>: {$path[0]}
			</div>
		{/if}

		<h3>{$dl_name}</h3>
		<a href="{$dl_link}" class="dlbutton" rel="nofollow"><span>{$th_download}</span> </a>

		{if strlen($dl_description) > 0}
			<div style="margin-left: 10px; overflow: hidden" id="dldesc_{$dl_id}">
				{eval var=$dl_description}
			</div><br />
		{/if}


		<div style="margin-left: 10px; max-width: 400px">
			<span style="float: right">
				<strong>{$th_date}</strong>: {$dl_date}<br />
				<strong>{$th_traffic}</strong>: {$dl_traffic}
			</span>
			<span style="float: left">
				<strong>{$th_filename}</strong>: {$dl_filename|truncate:25}{$dl_fileext}<br />
				<strong>{$th_filesize}</strong>: {$dl_size}<br />
				<strong>{$th_downloads}</strong>: {$dl_downloads}<br />
			</span>
			<br style="clear: both" />
		</div>

		{if $dl_mirrors !== false}
			<div style="margin-top: 15px">
				<h4>{$th_available_sources}</h4>
				<table cellspacing="1" class="default" style="margin: auto; width: 95%">
					<thead>
						<tr>
							<th>{$th_mirror}</th>
							<th style="width: 1px"><a title="Downloads">DL</a></th>
							<th style="width: 1px">{$th_traffic}</th>
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
	</div>
{else}
	<strong>{$error_none}</strong>
{/if}