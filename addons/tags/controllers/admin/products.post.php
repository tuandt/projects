<?php
/***************************************************************************
*                                                                          *
*    Copyright (c) 2009 Simbirsk Technologies Ltd. All rights reserved.    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


//
// $Id$
//

if ( !defined('AREA') ) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	return;
}

//
// View product details
//
if ($mode == 'add' && Registry::get('addons.tags.tags_for_products') == 'Y') {
	Registry::set('navigation.tabs.tags', array(
		'title' => fn_get_lang_var('tags'),
		'js' => true
	));

} elseif ($mode == 'update' && Registry::get('addons.tags.tags_for_products') == 'Y') {
	Registry::set('navigation.tabs.tags', array(
		'title' => fn_get_lang_var('tags'),
		'js' => true
	));
	
	$product = $view->get_var('product_data');

	$product['tags']['popular'] = $product['tags']['user'] = array();
	list($tags) = fn_get_tags(array('object_type' => 'P', 'object_id' => $product['product_id'], 'user_and_popular' => $auth['user_id'], 'skip_view' => 'Y'));

	foreach ($tags as $k => $v) {
		if (!empty($v['my_tag'])) {
			$product['tags']['user'][$v['tag_id']] = $v;
		}
		if ($v['status'] == 'A') {
			$product['tags']['popular'][$v['tag_id']] = $v;
		}
	}

	$view->assign('product_data', $product);
}

?>
