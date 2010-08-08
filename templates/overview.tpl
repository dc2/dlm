{if !isset($error_none)}
<div class="dlm">
{capture assign="pagetitle"}{$pagetitle}{$path[1]}{$headline}{/capture}
{if strlen($path[0]) > 0}
	<div style="margin-bottom: 15px; font-size: 0.75em"><strong>{$path_text}</strong>: {$path[0]}</div>
{/if}	
<h3 style="margin-bottom: 5px">{$headline}</h3>
{if $itemcount > 0}
	<table cellspacing="1" class="default" style="margin: auto; width: 95%">
		<thead>
			<tr style="text-align:center">
				<th>{$th_name}</th>
				{*<th>{$th_type}</th>*}
				{*<th>{$th_id}</th>*}
				<th style="width:20px"><a title="{$th_downloads}">DL</a></th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$items item=entry}
		{cycle values="row1,row2" assign=rowclass}
			<tr class="{$rowclass}">
				<td>{$entry->name}{if $entry->downloadurl !== false}<a href="{$entry->downloadurl}" class="dlbutton_small" style="float:right" rel="nofollow"><span>Download</span></a>{/if}</td>
				{*<td>{$entry->type}</td>*}
				{*<td>{$entry->id}</td>*}
				<td style="text-align:center">{$entry->downloads}</td>
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