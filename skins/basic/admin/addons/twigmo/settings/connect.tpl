{* $Id: connect.tpl 1269 2011-11-15 09:02:58Z aelita $ *}

<div id="connect_settings">

{if $addons.twigmo.access_id}
{include file="common_templates/subheader.tpl" title=$lang.tw_manage_settings}
{else}
{include file="common_templates/subheader.tpl" title=$lang.tw_connect_your_store}
{/if}

<fieldset>
	
	{if $addons.twigmo.access_id}
	<div class="form-field">
		<label>{$lang.access_id}:</label>
		{$addons.twigmo.access_id}
	</div>
	{/if}

	<div class="form-field">
		<label class="cm-required cm-email" for="elm_tw_email">{$lang.email}:</label>
		<input type="text" id="elm_tw_email" name="tw_register[email]"  value="{$tw_register.email|default:$user_info.email}" class="input-text-large" size="60" />
	</div>

	<div class="form-field">
		<label class="cm-required" for="elm_tw_store_name">{$lang.store_name}:</label>
		<input type="text" id="elm_tw_store_name" name="tw_register[store_name]"  value="{if $tw_register.store_name}{$tw_register.store_name}{else}{$config.http_host|fn_tw_get_domain_name}{/if}" class="input-text-large" size="60" />
	</div>

	<div class="form-field">
		<label for="elm_tw_disable_https">{$lang.disable_https}:</label>
		<input type="checkbox" class="checkbox" id="elm_tw_disable_https" name="tw_register[disable_https]" value="Y" {if $tw_register.disable_https == "Y"}checked="checked"{/if} />
	</div>

	<div class="form-field">
		<label for="elm_tw_use_password">{$lang.use_my_password}:</label>
		<input type="checkbox" class="checkbox" id="elm_tw_use_password" name="tw_register[use_password]" value="Y" onclick="$('#twg_passwords').switchAvailability();" {if $tw_register.use_password == "Y"}checked="checked"{/if} />
	</div>
	
	<div id='twg_passwords' {if $tw_register.use_password == "Y"} class="hidden"{/if}>
		<div class="form-field">
			<label for="elm_tw_password1" {if !$addons.twigmo.access_id}class="cm-required"{/if}>{$lang.password}:</label>
			<input type="password" id="elm_tw_password1" name="tw_register[password1]" class="input-text" size="32" maxlength="32" value="" autocomplete="off" {if $tw_register.use_password == "Y"}disabled="disabled"{/if} />
		</div>

		<div class="form-field">
			<label for="elm_tw_password2" {if !$addons.twigmo.access_id}class="cm-required"{/if}>{$lang.confirm_password}:</label>
			<input type="password" id="elm_tw_password2" name="tw_register[password2]" class="input-text" size="32" maxlength="32" value="" autocomplete="off" {if $tw_register.use_password == "Y"}disabled="disabled"{/if}/>
		</div>
	</div>

	<div class="form-field">
		<label>{$lang.version}:</label>
		{$tw_register.version}
	</div>

{if !$addons.twigmo.access_id}
	<input type="hidden" name="result_ids" value="connect_settings"/> 
	<input type="hidden" name="tw_register[checked_email]" value="{$tw_register.checked_email}"/>
	
	{if $stores}
	<div>{$lang.tw_select_connect_description}</div>
	
	<div class="form-field">
		<div class="select-field">
		{foreach from=$stores item=v key=k}
		<input type="radio" name="tw_register[store_id]" value="{$v.store_id}" {if $v.selected}checked="checked"{/if} class="radio" id="variant_tw_store_id_{$v.store_id}" /><label for="variant_tw_store_id_{$v.store_id}">{$v.title}</label><br />
		{/foreach}
		</div>
	</div>

	{/if}

	<script type="text/javascript">
	//<![CDATA[
	lang.checkout_terms_n_conditions_alert = '{$lang.checkout_terms_n_conditions_alert|escape:javascript}';
	{literal}
	function fn_tw_check_agreement() {
		if (!$('#id_accept_terms').attr('checked')) {
			return lang.checkout_terms_n_conditions_alert;
		}

		return true;
	}
	{/literal}
	//]]>
	</script>

	<div class="form-field">
		<textarea id="twigmo_license" name="tw_register[twigmo_license]" cols="83" rows="24" readonly="readonly">{$tw_register.twigmo_license}</textarea>
		<label for="id_accept_terms" style="margin-left: 0px; width: auto;" class="cm-custom (tw_check_agreement)"><input type="checkbox" id="id_accept_terms" name="accept_terms" value="Y" class="checkbox" />{$lang.checkout_terms_n_conditions}</label>
	</div>

	<script type="text/javascript">
        //<![CDATA[
	if (twigmo_license_text) $("#twigmo_license").text(twigmo_license_text);
	//]]>
        </script>

	<div class="form-field">
		{include file="buttons/button.tpl" but_role="button" but_meta="cm-ajax cm-skip-avail-switch" but_name="dispatch[addons.tw_connect]" but_text=$lang.connect}
	</div>
{/if}

</fieldset>
<!--connect_settings--></div>
