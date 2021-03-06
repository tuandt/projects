{* $Id$ *}

{assign var="escaped_current_url" value=$config.current_url|escape:url}
{if !$auth.user_id}
	<a id="sw_login" {if $settings.General.secure_auth == "Y"} rel="nofollow" href="{if $controller == "auth" && $mode == "login_form"}{$config.current_url|fn_url}{else}{"auth.login_form?return_url=`$escaped_current_url`"|fn_url}{/if}"{else}class="cm-combination"{/if}>{$lang.sign_in}</a>
	{$lang.or}
	<a href="{"profiles.add"|fn_url}" rel="nofollow">{$lang.register}</a>
{else}
	<a href="{"profiles.update"|fn_url}" class="strong">{if $user_info.firstname && $user_info.lastname}{$user_info.firstname}&nbsp;{$user_info.lastname}{elseif $user_info.firstname}{$user_info.firstname}{else}{$user_info.email}{/if}</a>
	({include file="buttons/button.tpl" but_role="text" but_href="auth.logout?redirect_url=`$escaped_current_url`" but_text=$lang.sign_out})
{/if}

{if $settings.General.secure_auth != "Y"}
	<div id="login" class="cm-popup-box hidden">
		<div class="login-popup">
			<div class="header">{$lang.sign_in}</div>
			{include file="views/auth/login_form.tpl" style="popup" form_name="login_popup_form" id="popup"}
		</div>
	</div>
{/if}
