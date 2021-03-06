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
// $Id: init.php $
//

if ( !defined('AREA') ) { die('Access denied'); }

require_once(DIR_ADDONS . 'twigmo/core/class.twigmo.php');
require_once(DIR_ADDONS . 'twigmo/core/class.apidata.php');
require_once(DIR_ADDONS . 'twigmo/core/api/class.twgapibase.php');
require_once(DIR_ADDONS . 'twigmo/core/api/class.twgapi.php');
require_once(DIR_ADDONS . 'twigmo/core/api/class.twgapiv2.php');
require_once(DIR_ADDONS . 'twigmo/core/fn.api.php');

fn_register_hooks(
	'additional_fields_in_search',
	'before_dispatch',
	'get_categories',
	'get_products',
	'get_shipments',
	'get_users',
	'place_order',
	'set_admin_notification'
);

?>