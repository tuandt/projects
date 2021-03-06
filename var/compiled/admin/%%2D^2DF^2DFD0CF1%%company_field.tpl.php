<?php /* Smarty version 2.6.18, created on 2015-04-30 22:04:43
         compiled from views/companies/components/company_field.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_get_company_name', 'views/companies/components/company_field.tpl', 1, false),array('modifier', 'default', 'views/companies/components/company_field.tpl', 1, false),array('modifier', 'defined', 'views/companies/components/company_field.tpl', 13, false),array('modifier', 'fn_substr', 'views/companies/components/company_field.tpl', 41, false),array('modifier', 'fn_strlen', 'views/companies/components/company_field.tpl', 42, false),array('modifier', 'fn_url', 'views/companies/components/company_field.tpl', 52, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('vendor','supplier','search','loading'));
?>
<?php  ob_start();  ?>
<?php if (@PRODUCT_TYPE == 'MULTIVENDOR' || @PRODUCT_TYPE == 'MULTISHOP' || ( $this->_tpl_vars['settings']['Suppliers']['enable_suppliers'] == 'Y' && ( @CONTROLLER == 'products' || @CONTROLLER == 'shippings' ) )): ?>

<?php if (@PRODUCT_TYPE == 'MULTIVENDOR' || @PRODUCT_TYPE == 'MULTISHOP'): ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('vendor', $this->getLanguage()), false); ?>
<?php else: ?>
<?php $this->assign('lang_vendor_supplier', fn_get_lang_var('supplier', $this->getLanguage()), false); ?>
<?php endif; ?>

<div class="form-field">
	<label for="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'); ?>
"><?php echo $this->_tpl_vars['lang_vendor_supplier']; ?>
:</label>
	<?php if (defined('COMPANY_ID')): ?>
		<?php echo $this->_tpl_vars['companies'][@COMPANY_ID]; ?>

		<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
" id="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'); ?>
" value="<?php echo @COMPANY_ID; ?>
">
	<?php else: ?>
		<input type="hidden" name="<?php echo $this->_tpl_vars['name']; ?>
" id="<?php echo smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'); ?>
" value="<?php echo smarty_modifier_default(@$this->_tpl_vars['selected'], 0); ?>
" />
		<?php $__parent_tpl_vars = $this->_tpl_vars;$this->_tpl_vars = array_merge($this->_tpl_vars, array('data_url' => "companies.get_companies_list", 'text' => fn_get_company_name($this->_tpl_vars['selected'], 0), 'result_elm' => smarty_modifier_default(@$this->_tpl_vars['id'], 'company_id'), 'id' => ($this->_tpl_vars['id'])."_selector", )); ?><div class="tools-container inline" <?php if ($this->_tpl_vars['elements_switcher_id']): ?> id="<?php echo $this->_tpl_vars['elements_switcher_id']; ?>
ajax_select_object"<?php endif; ?>>
	<?php if ($this->_tpl_vars['label']): ?><label><?php echo $this->_tpl_vars['label']; ?>
:</label><?php endif; ?>
	
	<?php if ($this->_tpl_vars['js_action']): ?>
	<script type="text/javascript">
	//<![CDATA[
		function fn_picker_js_action_<?php echo $this->_tpl_vars['id']; ?>
() {
			<?php echo $this->_tpl_vars['js_action']; ?>

		}
	//]]>
	</script>
	<?php endif; ?>

	<a id="sw_<?php echo $this->_tpl_vars['id']; ?>
_wrap_" class="select-link <?php if (! $this->_tpl_vars['elements_switcher_id']): ?> cm-combo-on cm-combination<?php endif; ?>"><?php echo $this->_tpl_vars['text']; ?>
</a>

	<div id="<?php echo $this->_tpl_vars['id']; ?>
_wrap_" class="popup-tools cm-popup-box cm-smart-position hidden">	
		<div class="select-object-search"><input type="text" value="<?php echo fn_get_lang_var('search', $this->getLanguage()); ?>
..." class="input-text cm-hint cm-ajax-content-input" rev="content_loader_<?php echo $this->_tpl_vars['id']; ?>
" size="16" /></div>
		<div class="ajax-popup-tools" id="scroller_<?php echo $this->_tpl_vars['id']; ?>
">
			<ul class="cm-select-list" id="<?php echo $this->_tpl_vars['id']; ?>
">
				<?php $_from = $this->_tpl_vars['objects']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['object_id'] => $this->_tpl_vars['item']):
?>
					<?php if (defined('TRANSLATION_MODE')): ?>
						<?php $this->assign('name', $this->_tpl_vars['item']['name'], false); ?>
					<?php else: ?>
						<?php $this->assign('name', fn_substr($this->_tpl_vars['item']['name'], 0, 40), false); ?>
						<?php if (fn_strlen($this->_tpl_vars['item']['name']) > 40): ?>
							<?php $this->assign('name', ($this->_tpl_vars['name'])."...", false); ?>
						<?php endif; ?>
					<?php endif; ?>
					
					<li class="<?php echo $this->_tpl_vars['item']['extra_class']; ?>
"><a action="<?php echo $this->_tpl_vars['item']['value']; ?>
" title="<?php echo $this->_tpl_vars['item']['name']; ?>
"><?php echo $this->_tpl_vars['name']; ?>
</a></li>
				<?php endforeach; endif; unset($_from); ?>
			<!--<?php echo $this->_tpl_vars['id']; ?>
--></ul>

			<ul>
				<li id="content_loader_<?php echo $this->_tpl_vars['id']; ?>
" class="cm-ajax-content-more small-description" rel="<?php echo fn_url($this->_tpl_vars['data_url']); ?>
" rev="<?php echo $this->_tpl_vars['id']; ?>
" result_elm="<?php echo $this->_tpl_vars['result_elm']; ?>
"><?php echo fn_get_lang_var('loading', $this->getLanguage()); ?>
</li>				
			</ul>
		</div>
	</div>
</div><?php if (isset($__parent_tpl_vars)) { $this->_tpl_vars = $__parent_tpl_vars; unset($__parent_tpl_vars);} ?>
	<?php endif; ?>
</div>

<?php endif; ?><?php  ob_end_flush();  ?>