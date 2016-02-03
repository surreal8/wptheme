<?php
/**
 * Plugin Name: Stieglitz Custom Post Types and Taxonomies
 * Plugin URI: http://yoursite.com
 * Description: A simple plugin that adds custom post types and taxonomies
 * Version: 0.1
 * Author: Tina Shah
 * Author URI: http://yoursite.com
 * License: GPL2
 */

/*  Copyright YEAR  Tina Shah  (email : tshah2@artic.edu)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

function my_custom_posttypes() {
	
    //Detail Page post type
    $labels = array(
        'name'               => 'Detail Pages',
        'singular_name'      => 'Detail Page',
        'menu_name'          => 'Detail Pages',
        'name_admin_bar'     => 'Detail Page',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Detail Page',
        'new_item'           => 'New Detail Page',
        'edit_item'          => 'Edit Detail Page',
        'view_item'          => 'View Detail Page',
        'all_items'          => 'All Detail Pages',
        'search_items'       => 'Search Detail Pages',
        'parent_item_colon'  => 'Parent Detail Pages:',
        'not_found'          => 'No detail pages found.',
        'not_found_in_trash' => 'No detail pages found in Trash.',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-id-alt',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'detailpage' ),
        'capability_type'    => 'page',
        'has_archive'        => false,
        'hierarchical'       => true,
        'menu_position'      => 4,
        'supports'           => array( 'title', 'editor', 'author', 'comments', 'thumbnail' )
    );
    register_post_type( 'detailpage', $args );
	
	//Test page post type
	$labels = array(
        'name'               => 'Test Pages',
        'singular_name'      => 'Test Page',
        'menu_name'          => 'Test Pages',
        'name_admin_bar'     => 'Test Page',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Test Page',
        'new_item'           => 'New Test Page',
        'edit_item'          => 'Edit Test Page',
        'view_item'          => 'View Test Page',
        'all_items'          => 'All Test Pages',
        'search_items'       => 'Search Test Pages',
        'parent_item_colon'  => 'Parent Test Pages:',
        'not_found'          => 'No Test pages found.',
        'not_found_in_trash' => 'No Test pages found in Trash.',
    );
    
    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-star-half',
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'testpages' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 4,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'author', 'excerpt', 'comments' ),
		'taxonomies'         => array( 'category', 'post_tag' )
    );
    register_post_type( 'testpages', $args );
}
add_action( 'init', 'my_custom_posttypes' );


// Flush rewrite rules to add "review" as a permalink slug
function my_rewrite_flush() {
    my_custom_posttypes();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'my_rewrite_flush' );


/* Custom Taxonomies */

function my_custom_taxonomies() {
	// Type of Detail Page taxonomy
    $labels = array(
        'name'              => 'Type of Detail Pages',
        'singular_name'     => 'Type of Detail Page',
        'search_items'      => 'Search Types of Detail Pages',
        'all_items'         => 'All Types of Detail Pages',
        'parent_item'       => 'Parent Type of Detail Page',
        'parent_item_colon' => 'Parent Type of Detail Page:',
        'edit_item'         => 'Edit Type of Detail Page',
        'update_item'       => 'Update Type of Detail Page',
        'add_new_item'      => 'Add New Type of Detail Page',
        'new_item_name'     => 'New Type of Detail Page Name',
        'menu_name'         => 'Type of Detail Page',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'detail-types' ),
    );

    register_taxonomy( 'detail-type', array( 'detailpage' ), $args );
	
	// Mood taxonomy (non-hierarchical)
    $labels = array(
        'name'                       => 'Moods',
        'singular_name'              => 'Mood',
        'search_items'               => 'Search Moods',
        'popular_items'              => 'Popular Moods',
        'all_items'                  => 'All Moods',
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => 'Edit Mood',
        'update_item'                => 'Update Mood',
        'add_new_item'               => 'Add New Mood',
        'new_item_name'              => 'New Mood Name',
        'separate_items_with_commas' => 'Separate moods with commas',
        'add_or_remove_items'        => 'Add or remove moods',
        'choose_from_most_used'      => 'Choose from the most used moods',
        'not_found'                  => 'No moods found.',
        'menu_name'                  => 'Moods',
    );

    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'moods' ),
    );

    register_taxonomy( 'mood', array( 'testpages', 'detailpage' ), $args );
	
	// Price Range taxonomy
    $labels = array(
        'name'              => 'Price Ranges',
        'singular_name'     => 'Price Range',
        'search_items'      => 'Search Price Ranges',
        'all_items'         => 'All Price Ranges',
        'parent_item'       => 'Parent Price Range',
        'parent_item_colon' => 'Parent Price Range:',
        'edit_item'         => 'Edit Price Range',
        'update_item'       => 'Update Price Range',
        'add_new_item'      => 'Add New Price Range',
        'new_item_name'     => 'New Price Range Name',
        'menu_name'         => 'Price Range',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'prices' ),
    );

    register_taxonomy( 'price', array('testpages', 'detailpage' ), $args );
}
add_action( 'init', 'my_custom_taxonomies' );