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
// $Id: order_sections.php $
//

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
	'object_name' => 'order_section',
	'key' => array('section_id'),
	'fields' => array (
		'section_id' => array (
			'db_field' => 'section_id'
		),
		'name' => array (
			'db_field' => 'name'
		),
		'total' => array (
			'db_field' => 'total'
		),
		'orders' => array (
			'schema' => array (
				'type' => 'orders',
			)
		)
	)
);

?>