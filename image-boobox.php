<?php
/*
Plugin Name: Image boo-box
Plugin URI: http://boo-box.com
Description: Automatically places boo-box links on images. When the links are clicked on virtual shopping windows appear on top of the page showing offers from selected affiliate programs. Offers are placed accordingly to the alt="" or title="" attributes of the picture. Based on <a href="http://pauloduarte.com/projetos/plugin-wordpress-imagem-boo-box.html">Image boo-box</a> by <a href="http://pauloduarte.com/">Paulo Duarte</a>.
Version: 0.3
Author: boo-box team
Author URI: http://boo-box.com
*/

/*
 	Version update from
 	* 
	* Plugin Name: Image Boo-Box
	* Plugin URI: http://pauloduarte.com
	* Description: Adds a image boo-box under images that have their title defined. You MUST add boo-box script on your template
	* Version: 0.1
	* Author: Paulo Duarte
	* Author URI: http://www.pauloduarte.com
	*
	
	*
	Copyright 2008  Paulo Duarte (contato@pauloduarte.info)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
    * 

	*
	contains some code from other plugin,
	http://danielsantos.org/arquivos/2007/02/18/plugin-boo-box-para-wordpress/
	thanks Daniel Santos!
	* 
*/

// config/menus/head-script
require_once('boobox_config.php');

// parse content functions
function boo_images_parse_content($content) {
	if (get_option('boo_shopid') && get_option('boo_affid')) {
		$content = preg_replace_callback('/(<a[^>]*>)?[\s]?(<img[^>]*[\/]*>)[\s]?(<\/a>)?/i', "boo_images_insert_linka", $content);
	}
	return $content;
}

// insert link'á where match!
function boo_images_insert_linka($matches) {
	// matches[0]: <a> + <img> + </a>
	// matches[1]: <a href>
	// matches[2]: <img>
	$code = $matches[0];
	$anchor = $matches[1];
	$img = $matches[2];
	
	// order makes no difference
	$atts = array('src','width','height','alt','title','class');
	
	// search for attributes and values of img tag
	$attr_values = array();
	preg_match_all('/(src|width|height|alt|title|class)="([^"]*)"/i', $img, $attr_values);
	$attributes = $attr_values[1];
	$values = $attr_values[2];
	
	// params is an array used to store attribute and value pairs
	$params = array();
	
	// hash for params
	$i = 0;
	foreach ($attributes as $attribute) {
		foreach ($atts as $att) {
			// test for accepted attributes
			if ( stristr($attribute, $att) )
				$params[$att] = $values[$i];
		}
		$i++;
	}
	
	// if title, use title, if  alt use alt. if both use alt
	if (!empty($params['title']) || !empty($params['alt'])) {
		$tags = $params['title'];
		if (!empty($params['alt'])) {
			$tags = $params['alt'];
		}
	}
	$tags = str_replace(' ','+',$tags);
	
	// return boo-link if are tags
	if (!empty($anchor)) {
		$anchor_close = '</a>';
	} else if (!empty($tags)){
		$anchor = '<a href="http://boo-box.com/link/aff:'.get_option('boo_shopid').'/uid:'.get_option('boo_affid').'/tags:'.$tags.'" class="bbli">';
		$anchor_close = '</a>';
	} else {
		$anchor = '';
		$anchor_close = '';
	}
	
	return $anchor . $img . $anchor_close;
}

// wp-hooks
// wrap the content THE the function
add_filter('the_content', 'boo_images_parse_content');

?>
