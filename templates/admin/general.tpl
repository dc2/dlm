{$startform}
	{*
	<div class="pageoptions" style="margin-bottom: 10px">
		{$add_category}&nbsp;{$add_download}
	</div>
	*}
	<script src="../modules/DLM/libs/js/jquery-1.4.2.min.js" type="text/javascript"></script>
	{$itemlist}
	<div class="pageoptions" style="margin: 15px 0 -10px 0">
		{$add_category}&nbsp;{$add_download}{if $itemcount > 0}&nbsp;{$expandall}&nbsp;{$contractall}{/if}
	</div>
{$endform}