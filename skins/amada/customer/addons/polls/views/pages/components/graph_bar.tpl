{* $Id$ *}

{strip}
<div class="graph-bar-{$color|default:2}" align="left">
	<div {if $value_width > 0}class="graph-bar" style="width: {$value_width|default:"0"}%;"{/if}>&nbsp;</div>
		<span>{$ratio|default:"0.00"}%&nbsp;<em>({$count|default:"0"})</em></span>
</div>
{/strip}