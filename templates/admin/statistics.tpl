<h3>Downloads insgesamt</h3>&nbsp;&nbsp;&nbsp;{$dlcnt}
<br />
<div style="float:left; margin-right: 30px">
<h3>Beliebteste Downloads</h3>
<table class="pagetable" style="max-width: 550px; margin: 0 20px 0 10px">
	<thead>
		<tr>
			<th>{$th_name}</th>
			<th style="text-align:center">{$th_downloads}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$popular_downloads item=dl}
		{cycle values="row1,row2" assign=rowclass name=dlcnt_row}
		<tr class="{$rowclass}">
			<td>{$dl.link}</td>
			<td style="text-align:center">{$dl.downloads}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
</div>
<div style="float:left">
<h3>Neueste Downloads</h3>
<table class="pagetable" style="max-width: 550px; margin: 0 20px 0 10px">
	<thead>
		<tr>
			<th>{$th_name}</th>
			<th style="text-align:center">{$th_date}</th>
		</tr>
	</thead>
	<tbody>
		{foreach from=$new_downloads item=dl}
		{cycle values="row1,row2" assign=rowclass name=newdl_row}
		<tr class="{$rowclass}">
			<td>{$dl.link}</td>
			<td style="text-align:center">{$dl.date|cms_date_format:"%d.%m.%Y"}</td>
		</tr>
		{/foreach}
	</tbody>
</table>
</div>
<br style="clear: left" />