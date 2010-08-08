{capture assign=dlm_rows}
{foreach from=$items item=entry}
{cycle values="row1,row2" assign=rowclass}
	<tr class="{$rowclass} level{$entry->level}" id="row{$entry->id}">
		<td>{$entry->expandlink}</td>
		<td>{$entry->name}</td>
		{* <td>{$entry->type}</td> *}
		<td>{$entry->id}</td>
		<td>{$entry->activatelink}</td>
		<td class="movelinks move">{$entry->downlink}{$entry->uplink}</td>
		<td>{$entry->editlink}&nbsp;&nbsp;{$entry->deletelink}</td>
		<td><input type="checkbox" name="m1_listitems[{$entry->id}]" id="m1_listitem{$entry->id}" onclick="checkRow(jQuery(this))" /></td>
	</tr>
{/foreach}
{/capture}

{if $showrows == true}
	{$dlm_rows}
{/if}