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
// $Id: cart.php $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'object_name' => 'cart',
	'fields' => array (
		'products' => array (
			'process_get' => array (
				'func' => 'fn_api_get_cart_products',
				'params' => array (
					'products' => array (
						'db_field' => 'products'
					),
					'lang_code' => array (
						'param' => 'lang_code'
					)
				)
			),
		),
		'amount' => array (
			'name' => 'amount'
		),
		'total' => array (
			'name' => 'total'
		),
		'subtotal' => array (
			'name' => 'subtotal'
		),
		'discount' => array (
			'name' => 'discount'
		),
		'shipping_cost' => array (
			'name' => 'shipping_cost'
		),
		'tax_subtotal' => array (
			'name' => 'tax_subtotal'
		),
	)
);

?>