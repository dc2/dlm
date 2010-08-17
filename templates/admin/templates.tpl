{$startform}
<table class="pagetable" style="max-width: 800px; margin: 10px 20px 10px 15px">
	<thead>
		<tr>
			<th>{$th_templates}</th>
			<th style="width:10px">{$th_actions}</th>
		</tr>	
	</thead>
	<tbody>
	{foreach from=$templates item=tpl}
	{cycle values="row1,row2" assign=rowclass}
		<tr class="{$rowclass}"><td>{$tpl.name}</td><td style="text-align:center">{$tpl.edit}&nbsp;&nbsp;{$tpl.delete}</td></tr>
	{/foreach}
	</tbody>
</table>

{*{$tplsubmit}*}
{$endform}