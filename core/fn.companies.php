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


/**
 * Function returns array:
 *	companies => hash array of companies (company_id => company)
 *  count => count of returned companies
 * 
 * @param mixed $params Array with search params. Usually REQUEST.
 * @return mixed 
 */
function fn_get_short_companies($params = array())
{
	$condition = $limit = $join = $companies = '';

	if (!empty($params['status'])) {
		$condition .= db_quote(" AND ?:companies.status = ?s ", $params['status']);
	}

	if (!empty($params['item_ids'])) {
		$params['item_ids'] = fn_explode(",", $params['item_ids']);
		$condition .= db_quote(" AND ?:companies.company_id IN (?n) ", $params['item_ids']);
	}

	if (!empty($params['displayed_vendors'])) {
		$limit = 'LIMIT ' . $params['displayed_vendors'];
	}

	$condition .= defined('COMPANY_ID') ? fn_get_company_condition('company_id', true, COMPANY_ID) : '';

	fn_set_hook('get_short_companies', $params, $condition, $join, $limit);

	$count = db_get_field("SELECT COUNT(*) FROM ?:companies $join WHERE 1 $condition");

	$_companies = db_get_hash_single_array("SELECT ?:companies.company_id, ?:companies.company FROM ?:companies $join WHERE 1 $condition ORDER BY ?:companies.company $limit", array('company_id', 'company'));

	$companies[0] = Registry::get('settings.Company.company_name');
	$companies = $companies + $_companies;

	$return = array(
		'companies' => $companies,
		'count' => $count,
	);

	if (!empty($params)) {
		unset($return['companies'][0]);
		return array($return);
	}
	return $companies;
}

function fn_get_company_name($company_id, $default_company_id = 'all')
{
	static $cache_names = array();
	
	if ($company_id == '') {
		$company_id = $default_company_id;
	}
	
	if (isset($cache_names[$company_id])) {
		return $cache_names[$company_id];
	}
	
	$_company_name = Registry::get("s_companies.$company_id.company");
	$name = $_company_name ? $_company_name : db_get_field("SELECT company FROM ?:companies WHERE company_id = ?i", $company_id);
	$cache_names[$company_id] = $name;
	
	return $name;
}

function fn_get_companies($params, &$auth, $items_per_page = 0, $lang_code = CART_LANGUAGE)
{
	// Init filter
	$_view = 'companies';
	$params = fn_init_view($_view, $params);

	// Set default values to input params
	$params['page'] = empty($params['page']) ? 1 : $params['page'];

	// Define fields that should be retrieved
	$fields = array (
		"?:companies.company_id",
		"?:companies.lang_code",
		"?:companies.email",
		"?:companies.company",
		"?:companies.timestamp",
		"?:companies.status",
		"?:companies.logos",
	);

	// Define sort fields
	$sortings = array (
		'id' => "?:companies.company_id",
		'company' => "?:companies.company",
		'email' => "?:companies.email",
		'date' => "?:companies.timestamp",
		'status' => "?:companies.status",
	);

	$directions = array (
		'asc' => 'asc',
		'desc' => 'desc'
	);

	$condition = $join = $group = '';

	$condition .= fn_get_company_condition('?:companies.company_id');

	$group .= " GROUP BY ?:companies.company_id";

	if (isset($params['company']) && fn_string_no_empty($params['company'])) {
		$condition .= db_quote(" AND ?:companies.company LIKE ?l", "%".trim($params['company'])."%");
	}

	if (!empty($params['status'])) {
		if (is_array($params['status'])) {
			$condition .= db_quote(" AND ?:companies.status IN (?a)", $params['status']);
		} else {
			$condition .= db_quote(" AND ?:companies.status = ?s", $params['status']);
		}
	}

	if (isset($params['email']) && fn_string_no_empty($params['email'])) {
		$condition .= db_quote(" AND ?:companies.email LIKE ?l", "%".trim($params['email'])."%");
	}

	if (isset($params['address']) && fn_string_no_empty($params['address'])) {
		$condition .= db_quote(" AND ?:companies.address LIKE ?l", "%".trim($params['address'])."%");
	}

	if (isset($params['zipcode']) && fn_string_no_empty($params['zipcode'])) {
		$condition .= db_quote(" AND ?:companies.zipcode LIKE ?l", "%".trim($params['zipcode'])."%");
	}

	if (!empty($params['country'])) {
		$condition .= db_quote(" AND ?:companies.country = ?s", $params['country']);
	}

	if (isset($params['state']) && fn_string_no_empty($params['state'])) {
		$condition .= db_quote(" AND ?:companies.state LIKE ?l", "%".trim($params['state'])."%");
	}

	if (isset($params['city']) && fn_string_no_empty($params['city'])) {
		$condition .= db_quote(" AND ?:companies.city LIKE ?l", "%".trim($params['city'])."%");
	}

	if (isset($params['phone']) && fn_string_no_empty($params['phone'])) {
		$condition .= db_quote(" AND ?:companies.phone LIKE ?l", "%".trim($params['phone'])."%");
	}

	if (isset($params['url']) && fn_string_no_empty($params['url'])) {
		$condition .= db_quote(" AND ?:companies.url LIKE ?l", "%".trim($params['url'])."%");
	}

	if (isset($params['fax']) && fn_string_no_empty($params['fax'])) {
		$condition .= db_quote(" AND ?:companies.fax LIKE ?l", "%".trim($params['fax'])."%");
	}

	if (!empty($params['company_id'])) {
		$condition .= db_quote(' AND ?:companies.company_id IN (?n)', $params['company_id']);
	}

	if (!empty($params['exclude_company_id'])) {
		$condition .= db_quote(' AND ?:companies.company_id != ?n', $params['exclude_company_id']);
	}

	fn_set_hook('get_companies', $params, $fields, $sortings, $condition, $join, $auth, $lang_code, $group);

	if (empty($params['sort_order']) || empty($directions[$params['sort_order']])) {
		$params['sort_order'] = 'asc';
	}

	if (empty($params['sort_by']) || empty($sortings[$params['sort_by']])) {
		$params['sort_by'] = 'company';
	}

	$sorting = (is_array($sortings[$params['sort_by']]) ? implode(' ' . $directions[$params['sort_order']]. ', ', $sortings[$params['sort_by']]) : $sortings[$params['sort_by']]). " " .$directions[$params['sort_order']];

	// Reverse sorting (for usage in view)
	$params['sort_order'] = $params['sort_order'] == 'asc' ? 'desc' : 'asc';

	// Paginate search results
	$limit = '';
	if (!empty($items_per_page)) {
		$total = db_get_field("SELECT COUNT(DISTINCT(?:companies.company_id)) FROM ?:companies $join WHERE 1 $condition");
		$limit = fn_paginate($params['page'], $total, $items_per_page);
	}

	$companies = db_get_array("SELECT " . implode(', ', $fields) . " FROM ?:companies $join WHERE 1 $condition $group ORDER BY $sorting $limit");

	return array($companies, $params);
}

function fn_get_company_condition($db_field = 'company_id', $and = true, $company = '', $show_admin = false, $area_c = false)
{
	if (PRODUCT_TYPE == 'MULTISHOP' && defined('COMPANY_ID') && (empty($company) || $company == 0)) {
		$company = COMPANY_ID;
	}
	
	$company = ($company === '') ? (defined('COMPANY_ID') ? COMPANY_ID : '') : $company;

	return ($company === '' || $company === 'all' || (AREA == 'C' && !$area_c && PRODUCT_TYPE != 'MULTISHOP')) ? '' : ((($and == true) ? ' AND' : '') . (($show_admin && $company) ? " $db_field IN (0, $company)" : " $db_field = $company"));
}

function fn_get_company_data($company_id, $lang_code = DESCR_SL, $get_description = true)
{
	if (!empty($company_id)) {

		if ($get_description && (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'MULTISHOP')) {
			$descriptions_list = "?:company_descriptions.*";
			$field_list = "$descriptions_list, ?:companies.*";
		} else {
			$field_list = "?:companies.*";
		}
		
		$join = '';

		$condition = fn_get_company_condition('?:companies.company_id');
		
		fn_set_hook('get_company_data', $company_id, $field_list, $join, $condition, $lang_code);
		
		if ($get_description && (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'MULTISHOP')) {
			$company_data = db_get_row("SELECT $field_list FROM ?:companies LEFT JOIN ?:company_descriptions ON ?:company_descriptions.company_id = ?:companies.company_id AND ?:company_descriptions.lang_code = ?s ?p WHERE ?:companies.company_id = ?i $condition", $lang_code, $join, $company_id);
		} else {
			$company_data = db_get_row("SELECT $field_list FROM ?:companies ?p WHERE ?:companies.company_id = ?i $condition", $join, $company_id);
		}

		if (empty($company_data)) {
			return false;
		}

		$company_data['category_ids'] = explode(',', $company_data['categories']);
		$company_data['shippings_ids'] = explode(',', $company_data['shippings']);
		
		$company_data['logos_data'] = unserialize($company_data['logos']);
		
		$company_data['company_id'] = $company_id;
		
		fn_set_hook('get_company_data_post', $company_data);
	}

	return (!empty($company_data) ? $company_data : false);
}

function fn_companies_apply_cart_shipping_rates(&$cart, $cart_products, $auth, &$shipping_rates, $calculate = true)
{
	$cart['use_suppliers'] = false;
	$cart['shipping_failed'] = $cart['company_shipping_failed'] = false;

	// Get suppliers products
	$supplier_products = array();
	$total_freight = 0;
	foreach ($cart_products as $k => $v) {
		$s_id = !empty($v['company_id']) ? $v['company_id'] : 0;
		$supplier_products[$s_id][] = $k;
	}

	if (!empty($supplier_products) && !defined('CACHED_SHIPPING_RATES') && $calculate) {
		$supplier_rates = array();
		foreach ($supplier_products as $rate_id => $products) {
			foreach ($products as $cart_id) {
				if ($cart_products[$cart_id]['free_shipping'] == 'Y' || ($cart_products[$cart_id]['is_edp'] == 'Y' && $cart_products[$cart_id]['edp_shipping'] != 'Y')) { 
					$rate = 0;
				} else {
					$rate = $cart_products[$cart_id]['shipping_freight'] * $cart_products[$cart_id]['amount'];
				}
				
				empty($supplier_rates[$rate_id]) ? $supplier_rates[$rate_id] = $rate : $supplier_rates[$rate_id] += $rate;
				$total_freight += $rate;
			}
		}
		
		if (!empty($supplier_rates)) {
			foreach ($shipping_rates as $shipping_id => $shipping) {
				if (!empty($shipping['rates'])) {
					foreach ($shipping['rates'] as $rate_id => $rate) {
						if (isset($supplier_rates[$rate_id])) {
							$shipping_rates[$shipping_id]['rates'][$rate_id] = $rate - $total_freight + $supplier_rates[$rate_id];
						} else {
							unset($shipping_rates[$shipping_id]['rates'][$rate_id]);
						}
					}
				}
			}
		}
	}

	// Add zero rates to free shipping
	foreach ($shipping_rates as $sh_id => $v) {
		if (!empty($v['added_manually'])) {
			$shipping_rates[$sh_id]['rates'] = fn_array_combine(array_keys($supplier_products), 0);
		}
	}

	// If all suppliers should be displayed in one box, filter them

	if (PRODUCT_TYPE != 'MULTIVENDOR' && Registry::get('settings.Suppliers.display_shipping_methods_separately') !== 'Y') {
		$s_ids = array_keys($supplier_products);

		foreach ($shipping_rates as $sh_id => $v) {
			if (sizeof(array_intersect($s_ids, array_keys($v['rates']))) != sizeof($s_ids)) {
				unset($shipping_rates[$sh_id]);
			}
		}
	}
	
	// Get suppliers and determine what shipping methods applicable to them
	$suppliers = array();
	foreach ($supplier_products as $s_id => $p_ids) {
		if (!empty($s_id)) {
			$s_data = fn_get_company_data($s_id);
			$cart['use_suppliers'] = true;
		} else {
			$s_data = array(
				'company' => Registry::get('settings.Company.company_name')
			);
		}

		$suppliers[$s_id] = array (
			'company' => $s_data['company'],
			'products' => $p_ids,
			'rates' => array(),
			'packages_info' => array(),
		);

		// Get shipping methods
		foreach ($shipping_rates as $sh_id => $shipping) {
			if (isset($shipping['rates'][$s_id])) {
				$shipping['rate'] = $shipping['rates'][$s_id];
				unset($shipping['rates']);
				$suppliers[$s_id]['rates'][$sh_id] = $shipping;
			}
		}
	}

	// Select shipping for each supplier
	$cart_shipping = !empty($cart['shipping']) ? $cart['shipping'] : (!empty($cart['chosen_shipping']) ? $cart['chosen_shipping'] : array());
	$cart['shipping'] = array();
	foreach ($suppliers as $s_id => $supplier) {
		
		if (!empty($supplier['products']) && is_array($supplier['products'])) {
			$all_edp_no_shipping = true;
			$all_edp_free_shipping = true;
			$all_free_shipping = true;
			foreach ($supplier['products'] as $pcart_id) {
				$all_edp_no_shipping = $all_edp_no_shipping && ($cart_products[$pcart_id]['is_edp'] == "Y" && $cart_products[$pcart_id]['edp_shipping'] == "N");
				$all_edp_free_shipping = $all_edp_free_shipping && ($cart_products[$pcart_id]['is_edp'] == "Y" && $cart_products[$pcart_id]['edp_shipping'] == "Y" && $cart_products[$pcart_id]['free_shipping'] == "Y");
				$all_free_shipping = $all_free_shipping && ($cart_products[$pcart_id]['is_edp'] == "N" && $cart_products[$pcart_id]['free_shipping'] == "Y");
			}
			$suppliers[$s_id]['all_edp_free_shipping'] = $all_edp_free_shipping;
			$suppliers[$s_id]['all_edp_no_shipping'] = $all_edp_no_shipping;
			$suppliers[$s_id]['all_free_shipping'] = $all_free_shipping;
		}

		if (empty($supplier['rates'])) {
			if (!empty($supplier['products']) && is_array($supplier['products'])) {
				foreach ((array)$supplier['products'] as $pcart_id) {
					if ($cart_products[$pcart_id]['free_shipping'] != "Y" && ($cart_products[$pcart_id]['is_edp'] != "Y" || ($cart_products[$pcart_id]['is_edp'] == "Y" && $cart_products[$pcart_id]['edp_shipping'] == "Y" ))) {
						$cart['shipping_failed'] = $cart['company_shipping_failed'] = true;
						$cart['products'][$pcart_id]['shipping_failed'] = true;
						$suppliers[$s_id]['shipping_failed'] = true;
					} elseif (isset($cart['products'][$pcart_id]['shipping_failed'])) {
						unset($cart['products'][$pcart_id]['shipping_failed']);
					}
				}
			} else {
				$cart['shipping_failed'] = $cart['company_shipping_failed'] = true;
				$suppliers[$s_id]['shipping_failed'] = true;
			}
			continue;
		}

		$sh_ids = array_keys($supplier['rates']);
		$shipping_selected = false;

		// Check if shipping method from this supplier is selected
		foreach ($sh_ids as $sh_id) {
			if (isset($cart_shipping[$sh_id]) && isset($cart_shipping[$sh_id]['rates'][$s_id])) {
				if ($shipping_selected == false) {
					if (!isset($cart['shipping'][$sh_id])) {
						$cart['shipping'][$sh_id] = $cart_shipping[$sh_id];
						$cart['shipping'][$sh_id]['rates'] = array();
					}
					$cart['shipping'][$sh_id]['rates'][$s_id] = $supplier['rates'][$sh_id]['rate']; // set new rate
					$cart['shipping'][$sh_id]['packages_info'] = $shipping_rates[$sh_id]['packages_info'];
					$shipping_selected = true;
				} else {
					//unset($cart['shipping'][$sh_id]['rates'][$s_id]);
				}
			}
		}

		if ($shipping_selected == false) {
			$sh_id = reset($sh_ids);
			if (empty($cart['shipping'][$sh_id])) {
				if (empty($cart_shipping[$sh_id])) {
					$cart['shipping'][$sh_id] = array(
						'shipping' => $supplier['rates'][$sh_id]['name'],
					);
				} else {
					$cart['shipping'][$sh_id] = $cart_shipping[$sh_id];
				}
			}

			$cart['shipping'][$sh_id]['rates'][$s_id] = $supplier['rates'][$sh_id]['rate'];
			$cart['shipping'][$sh_id]['packages_info'] = $shipping_rates[$sh_id]['packages_info'];
		}
	}

	// Calculate total shipping cost
	$cart['shipping_cost'] = 0;
	foreach ($cart['shipping'] as $sh_id => $shipping) {
		$cart['shipping_cost'] += array_sum($shipping['rates']);
	}

	ksort($suppliers);
	Registry::get('view')->assign('suppliers', $suppliers); // FIXME: That's bad...
	Registry::get('view')->assign('supplier_ids', array_keys($suppliers)); // FIXME: That's bad...

	return true;
}


function fn_get_company_id($table, $key, $key_id, $company_id = '')
{
	$condition = ($company_id !== '') ? db_quote(' AND company_id = ?i ', $company_id) : '';
	
	$id = db_get_field("SELECT company_id FROM ?:$table WHERE $key = ?i $condition", $key_id);
	
	return ($id !== NULL) ? $id : false;
}

function fn_check_company_id($table, $key, $key_id, $company_id = '')
{
if (!defined('COMPANY_ID')) {
		return true;
	}

	if ($company_id === '') {
		$company_id = COMPANY_ID;
	}

	$id = db_get_field("SELECT $key FROM ?:$table WHERE $key = ?i AND company_id = ?i", $key_id, $company_id);

	return (!empty($id)) ? true : false;
}

/**
 * Set company_id to actual company_id
 *
 * @param mixed $data Array with data
 */
function fn_set_company_id(&$data, $key_name = 'company_id', $only_defined = false)
{
	if (defined('COMPANY_ID')) {
		$data[$key_name] = COMPANY_ID;
	} elseif (!$only_defined) {
		if (!isset($data[$key_name])) {
			$data[$key_name] = 0;
		}
	}
}

function fn_get_products_companies($products)
{
	$companies = array();

	foreach ($products as $v) {
		$_company_id = !empty($v['company_id']) ? $v['company_id'] : 0;
		$companies[$_company_id] = $_company_id;
	}

	return $companies;
}

function fn_core_delete_shipping($shipping_id)
{
	db_query("UPDATE ?:companies SET shippings = ?p", fn_remove_from_set('shippings', $shipping_id));
}

function fn_companies_suppliers_order_notification($order_info, $order_statuses, $force_notification)
{

	$suppliers = array();

	foreach ($order_info['items'] as $k => $v) {
		if (isset($v['company_id'])) {
			$suppliers[$v['company_id']] = 0;
		}
	}

	if (!empty($suppliers)) {
		if (!empty($order_info['shipping'])) {
			foreach ($order_info['shipping'] as $shipping_id => $shipping) {
				foreach ((array)$shipping['rates'] as $supplier_id => $rate) {
					if (isset($suppliers[$supplier_id])) {
						$suppliers[$supplier_id] += $rate;
					}
				}
			}
		}

		Registry::get('view_mail')->assign('order_info', $order_info);
		Registry::get('view_mail')->assign('status_inventory', $order_statuses[$order_info['status']]['inventory']);
		foreach ($suppliers as $supplier_id => $shipping_cost) {
			if ($supplier_id != 0) {
				$supplier = fn_get_company_data($supplier_id);
				
				Registry::get('view_mail')->assign('shipping_cost', $shipping_cost);
				Registry::get('view_mail')->assign('supplier_id', $supplier_id);
				Registry::get('view_mail')->assign('order_status', fn_get_status_data($order_info['status'], STATUSES_ORDER, $order_info['order_id'], $supplier['lang_code']));
				Registry::get('view_mail')->assign('profile_fields', fn_get_profile_fields('I', '', $supplier['lang_code']));

				fn_send_mail($supplier['email'], Registry::get('settings.Company.company_orders_department'), 'orders/supplier_notification_subj.tpl', 'orders/supplier_notification.tpl', '', $supplier['lang_code'], Registry::get('settings.Company.company_orders_department'));
			}
		}

		return true;
	}

	return false;
}

function fn_companies_suppliers_rma_notification($order_info, $return_info)
{

	$suppliers = array();

	foreach ($order_info['items'] as $k => $v) {
		if (isset($v['company_id'])) {
			$suppliers[$v['company_id']] = 0;
		}
	}

	if (!empty($suppliers)) {
		foreach ($suppliers as $supplier_id => $shipping_cost) {
			if ($supplier_id != 0) {
				$supplier = fn_get_company_data($supplier_id);
				// Translate descriptions to admin language
				Registry::get('view_mail')->assign('return_status', fn_get_status_data($return_info['status'], STATUSES_RETURN, $return_info['return_id'], $supplier['lang_code']));
				fn_send_mail($supplier['email'], Registry::get('settings.Company.company_orders_department'), 'addons/rma/slip_notification_subj.tpl', 'addons/rma/slip_notification.tpl', '', $supplier['lang_code'], array($order_info['email'], Registry::get('settings.Company.company_orders_department')));
			}
		}
		return true;
	}

	return false;
}

function fn_check_suppliers_functionality()
{
	if (PRODUCT_TYPE == 'MULTIVENDOR' || Registry::get('settings.Suppliers.enable_suppliers') == 'Y') {
		return true;
	} else {
		return false;
	}
}

function fn_get_companies_shipping_ids($company_id)
{
	$shippings = array();

	$companies_shippings = explode(',', db_get_field("SELECT shippings FROM ?:companies WHERE company_id = ?i", $company_id));
	$default_shippings = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE company_id = ?i", $company_id);
	$shippings = array_merge($companies_shippings, $default_shippings);

	return $shippings;
}

function fn_check_companies_have_suppliers($companies)
{
	unset($companies[0]);
	return !empty($companies) ? 'Y' : 'N';
}

function fn_update_company($company_data, $company_id = 0, $lang_code = CART_LANGUAGE)
{
	fn_set_hook('update_company_pre', $company_data, $company_id, $lang_code);
	
	if (PRODUCT_TYPE == 'MULTIVENDOR' && defined('COMPANY_ID')) {
		unset($company_data['comission'], $company_data['comission_type'], $company_data['categories'], $company_data['shippings']);
	}
	
	unset($company_data['company_id']);
	$_data = $company_data;

	// Check if company with same email already exists
	$is_exist = db_get_field("SELECT email FROM ?:companies WHERE company_id != ?i AND email = ?s", $company_id, $_data['email']);
	if (!empty($is_exist)) {
		fn_save_post_data();
		$_text = (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'MULTISHOP') ? 'error_vendor_exists' : 'error_supplier_exists';
		fn_set_notification('E', fn_get_lang_var('error'), fn_get_lang_var($_text));
		return false;
	}

	$_data['shippings'] = empty($company_data['shippings']) ? '' : fn_create_set($company_data['shippings']);

	// add new company
	if (empty($company_id)) {
		// company title can't be empty
		if(empty($company_data['company'])) {
			return false;
		}

		$_data['timestamp'] = TIME;

		$company_id = db_query("INSERT INTO ?:companies ?e", $_data);

		if (empty($company_id)) {
			return false;
		}

		$old_logos = array();
		
		// Adding same company descriptions for all cart languages
		$_data = array(
			'company_id' => $company_id,
			'company_description' => !empty($company_data['company_description']) ? $company_data['company_description'] : '',
		);

		if (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'MULTISHOP') {
			foreach ((array)Registry::get('languages') as $_data['lang_code'] => $_v) {
				db_query("INSERT INTO ?:company_descriptions ?e", $_data);
			}
		}

	// update company information
	} else {
		if (isset($company_data['company']) && empty($company_data['company'])) {
			unset($company_data['company']);
		}

		if (!empty($_data['status'])) {
			$status_from = db_get_field("SELECT status FROM ?:companies WHERE company_id = ?i", $company_id);
		}
		db_query("UPDATE ?:companies SET ?u WHERE company_id = ?i", $_data, $company_id);

		if (isset($status_from) && $status_from != $_data['status']) {
			fn_companies_change_status($company_id, $_data['status'], '', $status_from, true);
		}

		$old_logos = db_get_field("SELECT logos FROM ?:companies WHERE company_id = ?i", $company_id);
		$old_logos = !empty($old_logos) ? unserialize($old_logos) : array();

		if (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'MULTISHOP') {
			// Updating company description
			$descr = !empty($company_data['company_description']) ? $company_data['company_description'] : '';
			db_query("UPDATE ?:company_descriptions SET company_description = ?s WHERE company_id = ?i AND lang_code = ?s", $descr, $company_id, DESCR_SL);
		}
	}
	// Do not upload logo if a dummy company is being added.
	if (!empty($_data['email'])) {
		fn_companies_update_logos($company_id, $old_logos);
	}
	
	fn_set_hook('update_company', $company_data, $company_id, $lang_code);
	
	return $company_id;
}

function fn_companies_filter_company_product_categories(&$request, &$product_data)
{
	if (PRODUCT_TYPE == 'MULTIVENDOR' && defined('COMPANY_ID')) {
		$company_data = Registry::get('s_companies.' . COMPANY_ID);
		$company_categories = !empty($company_data['categories']) ? explode(',', $company_data['categories']) : array();
		if (empty($company_categories)) {
			// all categories are allowed
			return true;
		}

		if (!empty($request['category_id']) && !in_array($request['category_id'], $company_categories)) {
			unset($request['category_id']);
			$changed = true;
		}
		if (!empty($product_data['main_category']) && !in_array($product_data['main_category'], $company_categories)) {
			unset($product_data['main_category']);
			$changed = true;
		}
		if (!empty($product_data['add_categories'])) {
			$add_categories = explode(',', $product_data['add_categories']);
			foreach ($add_categories as $k => $v) {
				if (!in_array($v, $company_categories)) {
					unset($add_categories[$k]);
					$changed = true;
				}
			}
			$product_data['add_categories'] = implode(',', $add_categories);
		}
	}
	
	return empty($changed);
}

function fn_companies_get_manifest_definition()
{
	$manifest_definition = fn_get_manifest_definition();

	$available_areas = array('C', 'M', 'A');
	
	foreach ($manifest_definition as $area => $v) {
		if (!in_array($area, $available_areas)) {
			unset($manifest_definition[$area]);
		}
	}

	return $manifest_definition;
}

function fn_companies_update_logos($company_id, $old_logos)
{
	$logotypes = fn_filter_uploaded_data('logotypes');

	$areas = fn_companies_get_manifest_definition();

	// Update company logotypes
	if (!empty($logotypes)) {
		$logos = $old_logos;
		foreach ($logotypes as $type => $logo) {
			$area = $areas[$type];

			$short_name = "company/{$company_id}/{$type}_{$logo['name']}";
			$filename = DIR_IMAGES . $short_name;
			fn_mkdir(dirname($filename));

			if (fn_get_image_size($logo['path'])) {
				if (fn_copy($logo['path'], $filename)) {
					list($w, $h, ) = fn_get_image_size($filename);

					$logos[$area['name']] = array(
						'vendor' => 1,
						'filename' => $short_name,
						'width' => $w,
						'height' => $h,
					);

					//remove old logo
					if (!empty($old_logos[$area['name']]['filename']) && $filename != DIR_IMAGES . $old_logos[$area['name']]['filename']) {
						@unlink(DIR_IMAGES . $old_logos[$area['name']]['filename']);
					}
				} else {
					$text = fn_get_lang_var('text_cannot_create_file');
					$text = str_replace('[file]', $filename, $text);
					fn_set_notification('E', fn_get_lang_var('error'), $text);
				}
			} else {
				$text = fn_get_lang_var('error_file_not_image');
				$text = str_replace('[file]', $filename, $text);
				fn_set_notification('E', fn_get_lang_var('error'), $text);
			}
			@unlink($logo['path']);
		}
		$logos = serialize($logos);
		db_query("UPDATE ?:companies SET logos = ?s WHERE company_id = ?i", $logos, $company_id);
	}

	fn_save_logo_alt($areas, $company_id);
}

function fn_delete_company($company_id)
{
	if (empty($company_id)) {
		return false;
	}

	if (PRODUCT_TYPE == 'MULTIVENDOR') {
		// Do not delete vendor if there're any orders associated with this company
		if (db_get_field("SELECT COUNT(*) FROM ?:orders WHERE company_id = ?i", $company_id)) {
			fn_set_notification('W', fn_get_lang_var('warning'), fn_get_lang_var('unable_delete_vendor_orders_exists'));

			return false;
		}
	}
	
	db_query("DELETE FROM ?:companies WHERE company_id = ?i", $company_id);

	// deleting products
	$product_ids = db_get_fields("SELECT product_id FROM ?:products WHERE company_id = ?i", $company_id);
	foreach ($product_ids as $product_id) {
		fn_delete_product($product_id);
	}

	// deleting shipping
	$shipping_ids = db_get_fields("SELECT shipping_id FROM ?:shippings WHERE company_id = ?i", $company_id);
	foreach ($shipping_ids as $shipping_id) {
		fn_delete_shipping($shipping_id);
	}
	
	if (PRODUCT_TYPE == 'MULTIVENDOR' || PRODUCT_TYPE == 'MULTISHOP') {
		db_query("DELETE FROM ?:company_descriptions WHERE company_id = ?i", $company_id);

		// deleting product_options
		$option_ids = db_get_fields("SELECT option_id FROM ?:product_options WHERE company_id = ?i", $company_id);
		foreach ($option_ids as $option_id) {
			fn_delete_product_option($option_id);
		}

		// deleting company admins
		$user_ids = db_get_fields("SELECT user_id FROM ?:users WHERE company_id = ?i AND user_type = 'A'", $company_id);
		foreach ($user_ids as $user_id) {
			fn_delete_user($user_id);
		}

		// deleting pages
		$page_ids = db_get_fields("SELECT page_id FROM ?:pages WHERE company_id = ?i", $company_id);
		foreach ($page_ids as $page_id) {
			fn_delete_page($page_id);
		}

		// deleting promotions
		$promotion_ids = db_get_fields("SELECT promotion_id FROM ?:promotions WHERE company_id = ?i", $company_id);
		fn_delete_promotions($promotion_ids);
		
		//FIXME: multishop add settings deleting
	}

	fn_set_hook('delete_company', $company_id);

	return true;
}

function fn_chown_company($from, $to)
{
	// Only allow the superadmin to merge vendors

	if (empty($from) || empty($to) || !isset($_SESSION['auth']['is_root']) || $_SESSION['auth']['is_root'] != 'Y' || defined('COMPANY_ID')) {
		return false;
	}

	// Chown & disable vendor's admin accounts
	db_query("UPDATE ?:users SET status = 'D', company_id = ?i WHERE company_id = ?i AND user_type = 'A'", $to, $from);

	$config = Registry::get('config');
	$tables = db_get_fields("SELECT INFORMATION_SCHEMA.COLUMNS.TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE INFORMATION_SCHEMA.COLUMNS.COLUMN_NAME = 'company_id' AND TABLE_SCHEMA = ?s;", $config['db_name']);

	foreach ($tables as $table) {
		$table = str_replace(TABLE_PREFIX, '', $table);
		if ($table != 'companies' && $table != 'company_descriptions') {
			db_query("UPDATE ?:$table SET company_id = ?i WHERE company_id = ?i", $to, $from);
		}
	}

	return true;
}

/**
 * Function returns address of company and emails of company' departments.
 *
 * @param integer $company_id ID of company
 * @param string $lang_code Language of retrieving data. If null, lang_code of company will be used.
 * @return array Company address, emails and lang_code.
 */
function fn_get_company_placement_info($company_id, $lang_code = null)
{
	if (empty($company_id)) {
		return Registry::get('settings.Company');
	} else {

		$company = fn_get_company_data($company_id, !empty($lang_code) ? $lang_code : CART_LANGUAGE, false);

		$company_placement_info = array(
			'company_state' => $company['state'],
			'company_city' => $company['city'],
			'company_address' => $company['address'],
			'company_phone' => $company['phone'],
			'company_fax' => $company['fax'],
			'company_name' => $company['company'],
			'company_website' => $company['url'],
			'company_zipcode' => $company['zipcode'],
			'company_country' => $company['country'],
			'company_users_department' => $company['email'],
			'company_site_administrator' => $company['email'],
			'company_orders_department' => $company['email'],
			'company_support_department' => $company['email'],
			'company_newsletter_email' => $company['email'],
			'lang_code' => $company['lang_code'],
		);
		
		if (empty($lang_code)) {
			$lang_code = $company['lang_code'];
		}
		
		$company_placement_info['company_country_descr'] = fn_get_country_name($company['country'], $lang_code);
		$company_placement_info['company_state_descr'] = fn_get_state_name($company['state'], $company['country'], $lang_code);

		return $company_placement_info;
	}
}

function fn_get_company_language($company_id)
{
	if (empty($company_id)) {
		return Registry::get('settings.Appearance.admin_default_language');
	} else {
		$company = fn_get_company_data($company_id, DESCR_SL, false);
		return $company['lang_code'];
	}
}

/**
 * Fucntion changes company status. Allowed statuses are A(ctive) and D(isabled)
 *
 * @param int $company_id
 * @param string $status_to A or D
 * @param string $reason The reason of the change
 * @param string $status_from Previous status
 * @param boolean $skip_query By default false. Update query might be skipped if status is already changed.
 * @return boolean True on success or false on failure
 */
function fn_companies_change_status($company_id, $status_to, $reason, &$status_from = '', $skip_query = false, $notify = true)
{
	if (empty($status_from)) {
		$status_from = db_get_field("SELECT status FROM ?:companies WHERE company_id = ?i", $company_id);
	}

	if (!in_array($status_to, array('A', 'P', 'D')) || $status_from == $status_to) {
		return false;
	}

	$result = $skip_query ? true : db_query("UPDATE ?:companies SET status = ?s WHERE company_id = ?i", $status_to, $company_id);

	if (!$result) {
		return false;
	}

	$company_data = fn_get_company_data($company_id);

	$account = $username = '';
	if ($status_from == 'N' && ($status_to == 'A' || $status_to == 'P')) {
		if (Registry::get('settings.Suppliers.create_vendor_administrator_account') == 'Y') {
			if (!empty($company_data['request_user_id'])) {
				$password_change_timestamp = db_get_field("SELECT password_change_timestamp FROM ?:users WHERE user_id = ?i", $company_data['request_user_id']);
				$_set = '';
				if (empty($password_change_timestamp)) {
					$_set = ", password_change_timestamp = 1 ";
				}
				db_query("UPDATE ?:users SET company_id = ?i, user_type = 'A'$_set WHERE user_id = ?i", $company_id, $company_data['request_user_id']);

				$username = fn_get_user_name($company_data['request_user_id']);
				$account = 'updated';

				$msg = fn_get_lang_var('new_administrator_account_created') . "<a href=?dispatch=profiles.update&user_id=" . $company_data['request_user_id'] . ">" . fn_get_lang_var('you_can_edit_account_details') . '</a>';
				fn_set_notification('N', fn_get_lang_var('notice'), $msg, 'K');

			} else {
				$user_data = array();

				if (!empty($company_data['request_account_name'])) {
					$user_data['user_login'] = $company_data['request_account_name'];
				} else {
					$user_data['user_login'] = $company_data['email'];
				}

				$request_account_data = unserialize($company_data['request_account_data']);
				$user_data['fields'] = $request_account_data['fields'];
				$user_data['firstname'] = $user_data['b_firstname'] = $user_data['s_firstname'] = $request_account_data['admin_firstname'];
				$user_data['lastname'] = $user_data['b_lastname'] = $user_data['s_lastname'] = $request_account_data['admin_lastname'];

				$user_data['user_type'] = 'A';
				$user_data['password1'] = fn_generate_password();
				$user_data['password2'] = $user_data['password1'];
				$user_data['status'] = 'A';
				$user_data['company_id'] = $company_id;
				$user_data['email'] = $company_data['email'];
				$user_data['company'] = $company_data['company'];
				$user_data['last_login'] = 0;
				$user_data['lang_code'] = $company_data['lang_code'];
				$user_data['password_change_timestamp'] = 0;

				// Copy vendor admin billing and shipping addresses from the company's credentials
				$user_data['b_address'] = $user_data['s_address'] = $company_data['address'];
				$user_data['b_city'] = $user_data['s_city'] = $company_data['city'];
				$user_data['b_country'] = $user_data['s_country'] = $company_data['country'];
				$user_data['b_state'] = $user_data['s_state'] = $company_data['state'];
				$user_data['b_zipcode'] = $user_data['s_zipcode'] = $company_data['zipcode'];

				list($added_user_id, $null) = fn_update_user(0, $user_data, $null, false,  false);

				if ($added_user_id) {
					$msg = fn_get_lang_var('new_administrator_account_created') . "<a href=?dispatch=profiles.update&user_id=$added_user_id>" . fn_get_lang_var('you_can_edit_account_details') . '</a>';
					fn_set_notification('N', fn_get_lang_var('notice'), $msg, 'K');

					$username = $user_data['user_login'];
					$account = 'new';
				}
			}
		}
	}
	
	if (empty($user_data)) {
		$user_id = db_get_field("SELECT user_id FROM ?:users WHERE company_id = ?i AND is_root = 'Y' AND user_type = 'A'", $company_id);
		$user_data = fn_get_user_info($user_id);
	}
	
	if ($notify && !empty($company_data['email'])) {
		$view_mail = & Registry::get('view_mail');
		$view_mail->assign('company_data', $company_data);
		$view_mail->assign('user_data', $user_data);
		$view_mail->assign('reason', $reason);
		$view_mail->assign('status', fn_get_lang_var($status_to == 'A' ? 'active' : 'disabled'));

		if ($status_from == 'N' && ($status_to == 'A' || $status_to == 'P')) {
			$view_mail->assign('username', $username);
			$view_mail->assign('account', $account);
			if ($account == 'new') {
				$view_mail->assign('password', $user_data['password1']);
			}
		}

		$mail_template = strtolower($status_from . '_' . $status_to);

		fn_send_mail($company_data['email'], Registry::get('settings.Company.company_support_department'), 'companies/status_' . $mail_template . '_notification_subj.tpl', 'companies/status_' . $mail_template . '_notification.tpl', '', CART_LANGUAGE);
	}

	return $result;
}

function fn_get_company_by_product_id($product_id)
{
	return db_get_row("SELECT * FROM ?:companies AS com LEFT JOIN ?:products AS prod ON com.company_id = prod.company_id WHERE prod.product_id = ?i", $product_id);
}

function fn_core_get_products(&$params, &$fields, &$sortings, &$condition, &$join, &$sorting, &$group_by, $lang_code)
{
	// code for products filter by company (supplier or vendor)
	if (fn_check_suppliers_functionality()) {
		if (isset($params['company_id']) && $params['company_id'] != '') {
			$params['company_id'] = intval($params['company_id']);
			$condition .= db_quote(' AND products.company_id = ?i ', $params['company_id']);
		}
	}
}

function fn_get_companies_sorting($simple_mode = true)
{
	$sorting = array(
		'company' => array('description' => fn_get_lang_var('name'), 'default_order' => 'asc'),
	);
	
	fn_set_hook('companies_sorting', $sorting);
	if ($simple_mode) {
		foreach ($sorting as &$sort_item) {
			$sort_item = $sort_item['description'];
		}
	}
	
	return $sorting;
}

function fn_helpdesk_process_messages($messages)
{
	if (!empty($messages)) {
		$messages_queue = fn_get_storage_data('hd_messages');
		if (empty($messages_queue)) {
			$messages_queue = array();
		} else {
			$messages_queue = unserialize($messages_queue);
		}
		
		foreach ($messages->Message as $message) {
			$message_id = empty($message->Id) ? intval(fn_crc32(microtime()) / 2) : (string) $message->Id;
			$message = array(
				'type' => empty($message->Type) ? 'W' : (string) $message->Type,
				'title' => (empty($message->Title)) ? fn_get_lang_var('notice') : (string) $message->Title,
				'text' => (string) $message->Text,
			);
			
			$messages_queue[$message_id] = $message;
		}
		
		fn_set_storage_data('hd_messages', serialize($messages_queue));
	}
}

/**
 * 
 * Gets company manifest from ini file by company id
 * @param int $company_id Id of company
 * @return array of manifest data
 */
function fn_get_company_manifest($company_id){
	// If name of settings skin_name_admin	or skin_name_customer will be changed. Please fix code below.
	if (AREA == 'A') {
		$area = 'admin';
	} else {  
		$area = 'customer';
	}
	return fn_get_manifest($area, CART_LANGUAGE, $company_id);
}
?>