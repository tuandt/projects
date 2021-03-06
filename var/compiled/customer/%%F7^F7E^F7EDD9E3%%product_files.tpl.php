<?php /* Smarty version 2.6.18, created on 2015-04-30 22:17:20
         compiled from views/products/components/product_files.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'fn_url', 'views/products/components/product_files.tpl', 12, false),array('modifier', 'unescape', 'views/products/components/product_files.tpl', 17, false),array('modifier', 'formatfilesize', 'views/products/components/product_files.tpl', 27, false),array('modifier', 'trim', 'views/products/components/product_files.tpl', 31, false),array('function', 'set_id', 'views/products/components/product_files.tpl', 31, false),)), $this); ?>
<?php
fn_preload_lang_vars(array('filename','filesize','licence_agreement','readme'));
?>
<?php  ob_start();  ?><?php ob_start(); ?>
<?php if ($this->_tpl_vars['files']): ?>
<table cellspacing="1" cellpadding="5" class="table" width="30%">
<tr>
	<th><?php echo fn_get_lang_var('filename', $this->getLanguage()); ?>
</th>
	<th><?php echo fn_get_lang_var('filesize', $this->getLanguage()); ?>
</th>
</tr>
<?php $_from = $this->_tpl_vars['files']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['file']):
?>
<tr>
	<td width="80">
		<a href="<?php echo fn_url("orders.get_file?file_id=".($this->_tpl_vars['file']['file_id'])."&preview=Y"); ?>
"><strong><?php echo $this->_tpl_vars['file']['file_name']; ?>
</strong></a>
		<?php if ($this->_tpl_vars['file']['readme'] || $this->_tpl_vars['file']['license']): ?>
		<ul class="bullets-list">
		<?php if ($this->_tpl_vars['file']['license']): ?>
			<li><a onclick="$('#license_<?php echo $this->_tpl_vars['file']['file_id']; ?>
').toggle(); return false;"><?php echo fn_get_lang_var('licence_agreement', $this->getLanguage()); ?>
</a></li>
			<div class="hidden" id="license_<?php echo $this->_tpl_vars['file']['file_id']; ?>
"><?php echo smarty_modifier_unescape($this->_tpl_vars['file']['license']); ?>
</div>
		<?php endif; ?>
		<?php if ($this->_tpl_vars['file']['readme']): ?>
			<li><a onclick="$('#readme_<?php echo $this->_tpl_vars['file']['file_id']; ?>
').toggle(); return false;"><?php echo fn_get_lang_var('readme', $this->getLanguage()); ?>
</a></li>
			<div class="hidden" id="readme_<?php echo $this->_tpl_vars['file']['file_id']; ?>
"><?php echo smarty_modifier_unescape($this->_tpl_vars['file']['readme']); ?>
</div>
		<?php endif; ?>
		</ul>
		<?php endif; ?>
	</td>
	<td width="20%" valign="top">
		 <strong><?php echo smarty_modifier_formatfilesize($this->_tpl_vars['file']['file_size']); ?>
</strong>
	</td>
<?php endforeach; endif; unset($_from); ?>
</table>
<?php endif; ?><?php $this->_smarty_vars['capture']['template_content'] = ob_get_contents(); ob_end_clean(); ?><?php if (trim($this->_smarty_vars['capture']['template_content'])): ?><?php if ($this->_tpl_vars['auth']['area'] == 'A'): ?><span class="cm-template-box" template="views/products/components/product_files.tpl" id="<?php echo smarty_function_set_id(array('name' => "views/products/components/product_files.tpl"), $this);?>
"><img class="cm-template-icon hidden" src="<?php echo $this->_tpl_vars['images_dir']; ?>
/icons/layout_edit.gif" width="16" height="16" alt="" /><?php echo $this->_smarty_vars['capture']['template_content']; ?>
<!--[/tpl_id]--></span><?php else: ?><?php echo $this->_smarty_vars['capture']['template_content']; ?>
<?php endif; ?><?php endif; ?><?php  ob_end_flush();  ?>