<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
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

$schema['products']['appearances']['addons/hot_deals_block/blocks/hot_deals.tpl'] = array (
	'bulk_modifier' => array (
		'fn_gather_additional_products_data' => array (
			'products' => '#this',
			'params' => array (
				'get_icon' => true,
				'get_detailed' => true,
				'get_options' => false,
			),
		),
	),
	'params' => array (
		'hot_deals' => true,
		'extend' => array('categories'),
	),
);

?>