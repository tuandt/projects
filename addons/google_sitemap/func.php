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

function fn_google_sitemap_generate_link($object, $value, $languages)
{
	$index_script = Registry::get('config.customer_index');
	$http_location = Registry::get('config.http_location');

	switch ($object) {
		case 'product':
			$link = htmlentities('products.view?product_id=' . $value);
			
			break;
		case 'category':
			$link = htmlentities('categories.view?category_id=' . $value);
			
			break;
		case 'page':
			$link = htmlentities('pages.view?page_id=' . $value);
			
			break;
		case 'extended':
			$link = htmlentities('product_features.view?variant_id=' . $value);
			
			break;
		case 'companies':
			$link = htmlentities('companies.view?company_id=' . $value);

			break;
		default:
			fn_set_hook('sitemap_link_object', $link, $object, $value);
	}
	
	$links = array();
	if (count($languages) == 1) {
		$links[] = fn_url($link, 'C', 'http', '&amp;', CART_LANGUAGE);
	} else {
		foreach ($languages as $lang_code => $lang) {
			$links[] = fn_url($link . '&amp;sl=' . $lang_code, 'C', 'http', '&amp;', $lang_code);
		}
	}
	
	fn_set_hook('sitemap_link', $link);
	
	return $links;
}

function fn_google_sitemap_print_item_info($links, $lmod, $frequency, $priority)
{
	$item = '';
	foreach ($links as $link) {
$item .= <<<ITEM
	<url>
		<loc>$link</loc>
		<lastmod>$lmod</lastmod>
		<changefreq>$frequency</changefreq>
		<priority>$priority</priority>
	</url>\n
ITEM;
	}

	return $item;
}

function fn_google_sitemap_get_frequency()
{
	$frequency = array(
		'always' => fn_get_lang_var('always'),
		'hourly' => fn_get_lang_var('hourly'),
		'daily' => fn_get_lang_var('daily'),
		'weekly' => fn_get_lang_var('weekly'),
		'monthly' => fn_get_lang_var('monthly'),
		'yearly' => fn_get_lang_var('yearly'),
		'never' => fn_get_lang_var('never'),
	);
	
	return $frequency;
}

function fn_google_sitemap_get_priority()
{
	$priority = array();
	
	for ($i = 0.1; $i <= 1; $i += 0.1) {
		$priority[(string)$i] = (string)$i;
	}
	
	return $priority;
}

function fn_google_sitemap_clear_url_info()
{
	$search = array('[http_location]', '[admin_index]', '[customer_index]');
	$replace = array(Registry::get('config.http_location'), fn_get_index_script(), Registry::get('config.customer_index'));
	
	return str_replace($search, $replace, fn_get_lang_var('sitemap_clear_cache_info'));
}

function fn_google_sitemap_get_content($map_page = 0)
{
	define('ITEMS_PER_PAGE', 500);
	define('MAX_URLS_IN_MAP', 50000); // 50 000 is the maximum for one sitemap file
	define('MAX_SIZE_IN_KBYTES', 10000); // 10240 KB || 10 Mb is the maximum for one sitemap file
	
	$sitemap_settings = Registry::get('addons.google_sitemap');
	$location = Registry::get('config.http_location');
	$lmod = date("Y-m-d", TIME);
	
	header("Content-Type: text/xml;charset=utf-8");
	
	// HEAD SECTION
	
	$simple_head = <<<HEAD
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">


HEAD;

	$simple_foot = <<<FOOT

</urlset>
FOOT;

	$index_map_url = <<<HEAD
	<url>
		<loc>$location/</loc>
		<lastmod>$lmod</lastmod>
		<changefreq>$sitemap_settings[site_change]</changefreq>
		<priority>$sitemap_settings[site_priority]</priority>
	</url>\n
HEAD;
	
	// END HEAD SECTION
	
	// SITEMAP CONTENT
	$link_counter = 1;
	$file_counter = 1;
	
	$file = fopen(DIR_CACHE . 'sitemap' . $file_counter . '.xml', "wb");
	
	fwrite($file, $simple_head . $index_map_url);
	
	$languages = db_get_hash_single_array("SELECT lang_code, name FROM ?:languages WHERE status = 'A'", array('lang_code', 'name'));
	
	if ($sitemap_settings['include_categories'] == "Y") {
		$categories = db_get_fields("SELECT category_id FROM ?:categories WHERE FIND_IN_SET(?i, usergroup_ids) AND status = 'A'", USERGROUP_ALL);
		
		//Add the all active categories
		foreach ($categories as $category) {
			$links = fn_google_sitemap_generate_link('category', $category, $languages);
			$item = fn_google_sitemap_print_item_info($links, $lmod, $sitemap_settings['categories_change'], $sitemap_settings['categories_priority']);
			
			fn_google_sitemap_check_counter($file, $link_counter, $file_counter, $links, $simple_head, $simple_foot);
			
			fwrite($file, $item);
		}
	}

	if ($sitemap_settings['include_products'] == "Y") {
		$page = 1;
		$total = ITEMS_PER_PAGE;
		
		$params = $_REQUEST;
		$params['page'] = $page;
		$params['custom_extend'] = array('categories');
		$params['sort_by'] = 'null';
		$params['only_short_fields'] = true;
		while (ITEMS_PER_PAGE * ($params['page'] - 1) <= $total) {
			list($products, $search, $total) = fn_get_products($params, ITEMS_PER_PAGE);
			$params['page']++;
			
			foreach ($products as $product) {
				$links = fn_google_sitemap_generate_link('product', $product['product_id'], $languages);
				$item = fn_google_sitemap_print_item_info($links, $lmod, $sitemap_settings['products_change'], $sitemap_settings['products_priority']);

				fn_google_sitemap_check_counter($file, $link_counter, $file_counter, $links, $simple_head, $simple_foot);
				
				fwrite($file, $item);
			}
		}
	unset($products);
	}

	if ($sitemap_settings['include_pages'] == "Y") {
		$pages = db_get_fields("SELECT page_id FROM ?:pages WHERE status = 'A' AND page_type != 'L'");
		
		//Add the all active pages
		foreach ($pages as $page) {
			$links = fn_google_sitemap_generate_link('page', $page, $languages);
			$item = fn_google_sitemap_print_item_info($links, $lmod, $sitemap_settings['pages_change'], $sitemap_settings['pages_priority']);
			
			fn_google_sitemap_check_counter($file, $link_counter, $file_counter, $links, $simple_head, $simple_foot);
			
			fwrite($file, $item);
		}
	}

	if ($sitemap_settings['include_extended'] == "Y") {
		$vars = db_get_fields("SELECT ?:product_feature_variants.variant_id FROM ?:product_feature_variants LEFT JOIN ?:product_features ON (?:product_feature_variants.feature_id = ?:product_features.feature_id) WHERE ?:product_features.feature_type = 'E' AND ?:product_features.status = 'A'");
	
		//Add the all active extended features
		foreach ($vars as $var) {
			$links = fn_google_sitemap_generate_link('extended', $var, $languages);
			$item = fn_google_sitemap_print_item_info($links, $lmod, $sitemap_settings['extended_change'], $sitemap_settings['extended_priority']);
			
			fn_google_sitemap_check_counter($file, $link_counter, $file_counter, $links, $simple_head, $simple_foot);
			
			fwrite($file, $item);
		}
	}
	
	if ($sitemap_settings['include_news'] == 'Y') {
		$news = db_get_fields("SELECT news_id FROM ?:news WHERE status = 'A'");
		
		if (!empty($news)) {
			foreach ($news as $news_id) {
				$links = fn_google_sitemap_generate_link('news', $news_id, $languages);
				$item = fn_google_sitemap_print_item_info($links, $lmod, $sitemap_settings['news_change'], $sitemap_settings['news_priority']);
				
				fn_google_sitemap_check_counter($file, $link_counter, $file_counter, $links, $simple_head, $simple_foot);
			
				fwrite($file, $item);
			}
		}
	}

	if (PRODUCT_TYPE == 'MULTIVENDOR' && $sitemap_settings['include_companies'] == 'Y') {
		$companies = db_get_fields("SELECT company_id FROM ?:companies WHERE status = 'A'");

		if (!empty($companies)) {
			foreach ($companies as $company_id) {
				$links = fn_google_sitemap_generate_link('companies', $company_id, $languages);
				$item = fn_google_sitemap_print_item_info($links, $lmod, $sitemap_settings['companies_change'], $sitemap_settings['companies_priority']);
				
				fn_google_sitemap_check_counter($file, $link_counter, $file_counter, $links, $simple_head, $simple_foot);
			
				fwrite($file, $item);
			}
		}
	}
	
	fn_set_hook('sitemap_item', $sitemap_settings, $file, $lmod, $link_counter, $file_counter);

	fwrite($file, $simple_foot);
	fclose($file);

	if ($file_counter == 1) {
		fn_rename(DIR_CACHE . 'sitemap' . $file_counter . '.xml', DIR_CACHE . 'sitemap.xml');
	} else {
		// Make a map index file
		
		$maps = '';
		$seo_enabled = Registry::get('addons.seo.status') == 'A' ? true : false;
		for ($i = 1; $i <= $file_counter; $i++) {
			if ($seo_enabled) {
				$name = $location . '/sitemap' . $i . '.xml';
			} else {
				$name = fn_url('xmlsitemap.view?page=' . $i, 'C', 'http', '&amp;');
			}
			$maps .= <<<MAP
	<sitemap>
		<loc>$name</loc>
		<lastmod>$lmod</lastmod>
	</sitemap>\n
MAP;
		}
		$index_map = <<<HEAD
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

$maps
</sitemapindex>
HEAD;
		$file = fopen(DIR_CACHE . 'sitemap.xml', "wb");
		fwrite($file, $index_map);
		fclose($file);
	}

	if (!empty($map_page)) {
		$filename = DIR_CACHE . 'sitemap' . $map_page . '.xml';
	} else {
		$filename = DIR_CACHE . 'sitemap.xml';
	}
	
	readfile($filename);
	
	exit();
}

function fn_google_sitemap_check_counter(&$file, &$link_counter, &$file_counter, $links, $header, $footer)
{
	$stat = fstat($file);
	if ((count($links) + $link_counter) > MAX_URLS_IN_MAP || $stat['size'] >= MAX_SIZE_IN_KBYTES * 1024) {
		fwrite($file, $footer);
		fclose($file);
		$file_counter++;
		$filename = DIR_CACHE . 'sitemap' . $file_counter . '.xml';
		$file = fopen($filename, "wb");
		$link_counter = count($links);
		fwrite($file, $header);
	} else {
		$link_counter += count($links);
	}
}

function fn_google_sitemap_get_rewrite_rules($rewrite_rules,  $prefix, $extension, $current_path)
{
	$rewrite_rules['!^(' . $current_path . ')?()()\/sitemap([0-9]*)\.xml$!'] = '$customer_index?dispatch=xmlsitemap.view&page=$matches[4]';
	$rewrite_rules['!^(' . $current_path . ')?' . $prefix . '\/sitemap([0-9]*)\.xml$!'] = '$customer_index?dispatch=xmlsitemap.view&page=$matches[4]';
}

?>