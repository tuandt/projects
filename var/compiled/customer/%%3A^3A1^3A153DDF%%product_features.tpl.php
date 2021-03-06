<?php /* Smarty version 2.6.18, created on 2015-04-30 22:17:20
         compiled from views/products/components/product_features.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'trim', 'views/products/components/product_features.tpl', 6, false),array('modifier', 'unescape', 'views/products/components/product_features.tpl', 6, false),array('modifier', 'date_format', 'views/products/components/product_features.tpl', 20, false),array('modifier', 'default', 'views/products/components/product_features.tpl', 32, false),array('function', 'set_id', 'views/products/components/product_features.tpl', 48, false),)), $this); ?>
<?php ob_start(); ?>
<?php $_from = $this->_tpl_vars['product_features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feature']):
?>
	<?php if ($this->_tpl_vars['feature']['feature_type'] != 'G'): ?>
		<div class="form-field">
		<?php if (trim($this->_tpl_vars['feature']['full_description'])): ?><?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/help.tpl", 'smarty_include_vars' => array('text' => $this->_tpl_vars['feature']['description'],'content' => smarty_modifier_unescape($this->_tpl_vars['feature']['full_description']),'id' => $this->_tpl_vars['feature']['feature_id'],'show_brackets' => true)));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?><?php endif; ?>
		<label><?php echo smarty_modifier_unescape($this->_tpl_vars['feature']['description']); ?>
:</label>

		<?php if ($this->_tpl_vars['feature']['feature_type'] == 'M'): ?>
			<?php $this->assign('hide_prefix', true, false); ?>
		<?php else: ?>
			<?php $this->assign('hide_prefix', false, false); ?>
		<?php endif; ?>

		<?php echo ''; ?><?php if ($this->_tpl_vars['feature']['prefix'] && ! $this->_tpl_vars['hide_prefix']): ?><?php echo ''; ?><?php echo $this->_tpl_vars['feature']['prefix']; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['feature']['feature_type'] == 'C'): ?><?php echo '<img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/icons/checkbox_'; ?><?php if ($this->_tpl_vars['feature']['value'] != 'Y'): ?><?php echo 'un'; ?><?php endif; ?><?php echo 'ticked.gif" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['feature']['value']; ?><?php echo '" align="top" />'; ?><?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'D'): ?><?php echo ''; ?><?php echo smarty_modifier_date_format($this->_tpl_vars['feature']['value_int'], ($this->_tpl_vars['settings']['Appearance']['date_format'])); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'M' && $this->_tpl_vars['feature']['variants']): ?><?php echo '<ul class="no-markers no-margin">'; ?><?php $_from = $this->_tpl_vars['feature']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['var']):
?><?php echo ''; ?><?php if ($this->_tpl_vars['var']['selected']): ?><?php echo '<li><img src="'; ?><?php echo $this->_tpl_vars['images_dir']; ?><?php echo '/icons/checkbox_ticked.gif" width="13" height="13" alt="'; ?><?php echo $this->_tpl_vars['var']['variant']; ?><?php echo '" />&nbsp;'; ?><?php if ($this->_tpl_vars['hide_prefix']): ?><?php echo ''; ?><?php echo $this->_tpl_vars['feature']['prefix']; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php echo $this->_tpl_vars['var']['variant']; ?><?php echo ''; ?><?php if ($this->_tpl_vars['hide_prefix']): ?><?php echo ''; ?><?php echo $this->_tpl_vars['feature']['suffix']; ?><?php echo ''; ?><?php endif; ?><?php echo '</li>'; ?><?php endif; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo '</ul>'; ?><?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'S' || $this->_tpl_vars['feature']['feature_type'] == 'E'): ?><?php echo ''; ?><?php $_from = $this->_tpl_vars['feature']['variants']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['var']):
?><?php echo ''; ?><?php if ($this->_tpl_vars['var']['selected']): ?><?php echo ''; ?><?php echo $this->_tpl_vars['var']['variant']; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php endforeach; endif; unset($_from); ?><?php echo ''; ?><?php elseif ($this->_tpl_vars['feature']['feature_type'] == 'N' || $this->_tpl_vars['feature']['feature_type'] == 'O'): ?><?php echo ''; ?><?php echo smarty_modifier_default(@$this->_tpl_vars['feature']['value_int'], "-"); ?><?php echo ''; ?><?php else: ?><?php echo ''; ?><?php echo smarty_modifier_default(@$this->_tpl_vars['feature']['value'], "-"); ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?><?php if ($this->_tpl_vars['feature']['suffix'] && ! $this->_tpl_vars['hide_prefix']): ?><?php echo ''; ?><?php echo $this->_tpl_vars['feature']['suffix']; ?><?php echo ''; ?><?php endif; ?><?php echo ''; ?>

		</div>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>

<?php $_from = $this->_tpl_vars['product_features']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['feature']):
?>
	<?php if ($this->_tpl_vars['feature']['feature_type'] == 'G' && $this->_tpl_vars['feature']['subfeatures']): ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "common_templates/subheader.tpl", 'smarty_include_vars' => array('title' => $this->_tpl_vars['feature']['description'],'tooltip' => $this->_tpl_vars['feature']['full_description'],'text' => $this->_tpl_vars['feature']['description'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
		<?php $_smarty_tpl_vars = $this->_tpl_vars;$this->_smarty_include(array('smarty_include_tpl_file' => "views/products/components/product_features.tpl", 'smarty_include_vars' => array('product_features' => $this->_tpl_vars['feature']['subfeatures'])));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
	<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php $this->_smarty_vars['capture']['template_content'] = ob_get_contents(); ob_end_clean(); ?><?php if (trim($this->_smarty_vars['capture']['template_content'])): ?><?php if ($this->_tpl_vars['auth']['area'] == 'A'): ?><span class="cm-template-box" template="views/products/components/product_features.tpl" id="<?php echo smarty_function_set_id(array('name' => "views/products/components/product_features.tpl"), $this);?>
"><img class="cm-template-icon hidden" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/layout_edit.gif" width="16" height="16" alt="" /><?php echo $this->_smarty_vars['capture']['template_content']; ?>
<!--[/tpl_id]--></span><?php else: ?><?php echo $this->_smarty_vars['capture']['template_content']; ?>
<?php endif; ?><?php endif; ?>