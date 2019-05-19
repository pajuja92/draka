<?php
function cptui_register_my_cpts() {

	// /**
	//  * Post Type: Instruktorzy.
	//  */
	//
	// $labels = array(
	// 	"name" => __( "Instruktorzy", "draka_plugin" ),
	// 	"singular_name" => __( "Instruktor", "draka_plugin" ),
	// 	"menu_name" => __( "Instruktor", "draka_plugin" ),
	// 	"all_items" => __( "Wszyscy instruktorzy", "draka_plugin" ),
	// 	"add_new" => __( "Dodaj instruktora", "draka_plugin" ),
	// 	"edit_item" => __( "Edytuj instruktora", "draka_plugin" ),
	// 	"archives" => __( "Więcej członków", "draka_plugin" ),
	// );
	//
	// $args = array(
	// 	"label" => __( "Instruktorzy", "draka_plugin" ),
	// 	"labels" => $labels,
	// 	"description" => "Członkowie ZHP",
	// 	"public" => true,
	// 	"publicly_queryable" => true,
	// 	"show_ui" => true,
	// 	"delete_with_user" => false,
	// 	"show_in_rest" => true,
	// 	"rest_base" => "",
	// 	"rest_controller_class" => "WP_REST_Posts_Controller",
	// 	"has_archive" => true,
	// 	"show_in_menu" => true,
	// 	"show_in_nav_menus" => true,
	// 	"exclude_from_search" => false,
	// 	"capability_type" => "post",
	// 	"map_meta_cap" => true,
	// 	"hierarchical" => true,
	// 	"rewrite" => false,
	// 	"query_var" => true,
	// 	"supports" => array( "title", "editor", "thumbnail", "custom-fields" ),
	// );
	//
	// register_post_type( "instruktorzy", $args );

	/**
	 * Post Type: draka.
	 */

	$labels = array(
		"name" => __( "Draka", "draka_plugin" ),
		"singular_name" => __( "draka", "draka_plugin" ),
		"menu_name" => __( "Draka", "draka_plugin" ),
		"all_items" => __( "Pytania", "draka_plugin" ),
		"add_new" => __( "Dodaj nowe", "draka_plugin" ),
		"add_new_item" => __( "Dodaj nowe pytanie", "draka_plugin" ),
		"edit_item" => __( "Edytuj pytanie", "draka_plugin" ),
		"new_item" => __( "Nowe pytanie", "draka_plugin" ),
		"view_item" => __( "Zobacz pytanie", "draka_plugin" ),
		"search_items" => __( "Wyszukaj pytania", "draka_plugin" ),
	);

	$args = array(
		"label" => __( "draka", "draka_plugin" ),
		"labels" => $labels,
		"description" => "Draka",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"delete_with_user" => false,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"exclude_from_search" => false,
		"capability_type" => "page",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"rewrite" => array( "slug" => "draka", "with_front" => true ),
		"query_var" => true,
		"supports" => array( "title", "editor", "thumbnail" ),
	);

	register_post_type( "draka", $args );
}
add_action( 'init', 'cptui_register_my_cpts' );


// Register Custom Taxonomy
function reg_draka_category() {

	$labels = array(
		'name'                       => _x( 'Kategorie pytań', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Kategoria pytania', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Kategoria pytania', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'view_item'                  => __( 'View Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
		'no_terms'                   => __( 'No items', 'text_domain' ),
		'items_list'                 => __( 'Items list', 'text_domain' ),
		'items_list_navigation'      => __( 'Items list navigation', 'text_domain' ),
	);
	$rewrite = array(
		'slug'                       => 'draka_cat',
		'with_front'                 => false,
		'hierarchical'               => true,
	);
	$args = array(
		'labels'              => $labels,
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_admin_column'   => true,
		'show_in_nav_menus'   => true,
    'show_in_rest'        => true,
		'show_tagcloud'       => true,
		'rewrite' 						=> $rewrite,
    'capability_type'     => 'page',
		'rest_base' 					=> 'draka_cat',
	);
	register_taxonomy( 'draka_category', array( 'draka' ), $args );

}
add_action( 'init', 'reg_draka_category', 0 );


//
//
// function cptui_register_my_cpts_instruktorzy() {
//
// 	/**
// 	 * Post Type: Instruktorzy.
// 	 */
//
// 	$labels = array(
// 		"name" => __( "Instruktorzy", "draka_plugin" ),
// 		"singular_name" => __( "Instruktor", "draka_plugin" ),
// 		"menu_name" => __( "Instruktor", "draka_plugin" ),
// 		"all_items" => __( "Wszyscy instruktorzy", "draka_plugin" ),
// 		"add_new" => __( "Dodaj instruktora", "draka_plugin" ),
// 		"edit_item" => __( "Edytuj instruktora", "draka_plugin" ),
// 		"archives" => __( "Więcej członków", "draka_plugin" ),
// 	);
//
// 	$args = array(
// 		"label" => __( "Instruktorzy", "draka_plugin" ),
// 		"labels" => $labels,
// 		"description" => "Członkowie ZHP",
// 		"public" => true,
// 		"publicly_queryable" => true,
// 		"show_ui" => true,
// 		"delete_with_user" => false,
// 		"show_in_rest" => true,
// 		"rest_base" => "",
// 		"rest_controller_class" => "WP_REST_Posts_Controller",
// 		"has_archive" => true,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"exclude_from_search" => false,
// 		"capability_type" => "post",
// 		"map_meta_cap" => true,
// 		"hierarchical" => true,
// 		"rewrite" => false,
// 		"query_var" => true,
// 		"supports" => array( "title", "editor", "thumbnail", "custom-fields" ),
// 	);
//
// 	register_post_type( "instruktorzy", $args );
// }
//
// add_action( 'init', 'cptui_register_my_cpts_instruktorzy' );
//
//
// function cptui_register_my_cpts_draka() {
//
// 	/**
// 	 * Post Type: draka.
// 	 */
//
// 	$labels = array(
// 		"name" => __( "draka", "draka_plugin" ),
// 		"singular_name" => __( "draka", "draka_plugin" ),
// 		"menu_name" => __( "Draka", "draka_plugin" ),
// 		"all_items" => __( "Pytania", "draka_plugin" ),
// 		"add_new" => __( "Dodaj nowe", "draka_plugin" ),
// 		"add_new_item" => __( "Dodaj nowe pytanie", "draka_plugin" ),
// 		"edit_item" => __( "Edytuj pytanie", "draka_plugin" ),
// 		"new_item" => __( "Nowe pytanie", "draka_plugin" ),
// 		"view_item" => __( "Zobacz pytanie", "draka_plugin" ),
// 		"search_items" => __( "Wyszukaj pytania", "draka_plugin" ),
// 	);
//
// 	$args = array(
// 		"label" => __( "draka", "draka_plugin" ),
// 		"labels" => $labels,
// 		"description" => "Draka",
// 		"public" => true,
// 		"publicly_queryable" => true,
// 		"show_ui" => true,
// 		"delete_with_user" => false,
// 		"show_in_rest" => true,
// 		"rest_base" => "",
// 		"rest_controller_class" => "WP_REST_Posts_Controller",
// 		"has_archive" => false,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"exclude_from_search" => false,
// 		"capability_type" => "post",
// 		"map_meta_cap" => true,
// 		"hierarchical" => true,
// 		"rewrite" => array( "slug" => "draka", "with_front" => true ),
// 		"query_var" => true,
// 		"supports" => array( "title", "editor", "thumbnail" ),
// 	);
//
// 	register_post_type( "draka", $args );
// }
//
// add_action( 'init', 'cptui_register_my_cpts_draka' );
//
// function cptui_register_my_taxes() {
//
// 	/**
// 	 * Taxonomy: Zespoły.
// 	 */
//
// 	$labels = array(
// 		"name" => __( "Zespoły", "draka_plugin" ),
// 		"singular_name" => __( "Zespół", "draka_plugin" ),
// 		"menu_name" => __( "Zespoły", "draka_plugin" ),
// 		"all_items" => __( "Zespoły", "draka_plugin" ),
// 		"edit_item" => __( "Edytuj zespół", "draka_plugin" ),
// 		"view_item" => __( "Zobacz zespoły", "draka_plugin" ),
// 	);
//
// 	$args = array(
// 		"label" => __( "Zespoły", "draka_plugin" ),
// 		"labels" => $labels,
// 		"public" => true,
// 		"publicly_queryable" => false,
// 		"hierarchical" => true,
// 		"show_ui" => true,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"query_var" => true,
// 		"rewrite" => false,
// 		"show_admin_column" => true,
// 		"show_in_rest" => true,
// 		"rest_base" => "Zespół",
// 		"rest_controller_class" => "WP_REST_Terms_Controller",
// 		"show_in_quick_edit" => true,
// 		"meta_box_cb" => false,
// 		);
// 	register_taxonomy( "Zespół", array( "portfolio" ), $args );
//
// 	/**
// 	 * Taxonomy: Zespoły.
// 	 */
//
// 	$labels = array(
// 		"name" => __( "Zespoły", "draka_plugin" ),
// 		"singular_name" => __( "Zespół", "draka_plugin" ),
// 		"menu_name" => __( "Zespoły", "draka_plugin" ),
// 		"all_items" => __( "Zespoły", "draka_plugin" ),
// 		"edit_item" => __( "Edytuj zespół", "draka_plugin" ),
// 		"view_item" => __( "Zobacz zespoły", "draka_plugin" ),
// 	);
//
// 	$args = array(
// 		"label" => __( "Zespoły", "draka_plugin" ),
// 		"labels" => $labels,
// 		"public" => true,
// 		"publicly_queryable" => false,
// 		"hierarchical" => true,
// 		"show_ui" => true,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"query_var" => true,
// 		"rewrite" => false,
// 		"show_admin_column" => true,
// 		"show_in_rest" => true,
// 		"rest_base" => "ad",
// 		"rest_controller_class" => "WP_REST_Terms_Controller",
// 		"show_in_quick_edit" => true,
// 		"meta_box_cb" => false,
// 		);
// 	register_taxonomy( "zespol", array( "instruktorzy" ), $args );
// }
// add_action( 'init', 'cptui_register_my_taxes' );
//
//
// function cptui_register_my_taxes_Zespół() {
//
// 	/**
// 	 * Taxonomy: Zespoły.
// 	 */
//
// 	$labels = array(
// 		"name" => __( "Zespoły", "draka_plugin" ),
// 		"singular_name" => __( "Zespół", "draka_plugin" ),
// 		"menu_name" => __( "Zespoły", "draka_plugin" ),
// 		"all_items" => __( "Zespoły", "draka_plugin" ),
// 		"edit_item" => __( "Edytuj zespół", "draka_plugin" ),
// 		"view_item" => __( "Zobacz zespoły", "draka_plugin" ),
// 	);
//
// 	$args = array(
// 		"label" => __( "Zespoły", "draka_plugin" ),
// 		"labels" => $labels,
// 		"public" => true,
// 		"publicly_queryable" => false,
// 		"hierarchical" => true,
// 		"show_ui" => true,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"query_var" => true,
// 		"rewrite" => false,
// 		"show_admin_column" => true,
// 		"show_in_rest" => true,
// 		"rest_base" => "Zespół",
// 		"rest_controller_class" => "WP_REST_Terms_Controller",
// 		"show_in_quick_edit" => true,
// 		"meta_box_cb" => false,
// 		);
// 	register_taxonomy( "Zespół", array( "portfolio" ), $args );
// }
// add_action( 'init', 'cptui_register_my_taxes_Zespół' );
//
// function cptui_register_my_taxes_zespol() {
//
// 	/**
// 	 * Taxonomy: Zespoły.
// 	 */
//
// 	$labels = array(
// 		"name" => __( "Zespoły", "draka_plugin" ),
// 		"singular_name" => __( "Zespół", "draka_plugin" ),
// 		"menu_name" => __( "Zespoły", "draka_plugin" ),
// 		"all_items" => __( "Zespoły", "draka_plugin" ),
// 		"edit_item" => __( "Edytuj zespół", "draka_plugin" ),
// 		"view_item" => __( "Zobacz zespoły", "draka_plugin" ),
// 	);
//
// 	$args = array(
// 		"label" => __( "Zespoły", "draka_plugin" ),
// 		"labels" => $labels,
// 		"public" => true,
// 		"publicly_queryable" => false,
// 		"hierarchical" => true,
// 		"show_ui" => true,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"query_var" => true,
// 		"rewrite" => false,
// 		"show_admin_column" => true,
// 		"show_in_rest" => true,
// 		"rest_base" => "ad",
// 		"rest_controller_class" => "WP_REST_Terms_Controller",
// 		"show_in_quick_edit" => true,
// 		"meta_box_cb" => false,
// 		);
// 	register_taxonomy( "zespol", array( "instruktorzy" ), $args );
// }
// add_action( 'init', 'cptui_register_my_taxes_zespol' );
