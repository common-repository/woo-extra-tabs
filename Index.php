<?php
/*
Plugin Name:Woocommerce Extra Tabs
Plugin URI:
Description: Add additional woocommerce product description tabs .
Version: 1.1
Author: ODude

/* Setup the plugin. */
add_action( 'plugins_loaded', 'woo_extra_tab_plugin_setup' );

include(dirname(__FILE__)."/lib.php");

/* Register plugin activation hook. */
register_activation_hook( __FILE__, 'woo_extra_tab_plugin_activation' );

/* Register plugin activation hook. */
register_deactivation_hook( __FILE__, 'woo_extra_tab_plugin_deactivation' );
/**
 * Do things on plugin activation.
 *
 */
function woo_extra_tab_plugin_activation() {
	/* Flush permalinks. */
    flush_rewrite_rules();
	
	/* Register post type. */
	  add_action( 'init','woo_extra_tabs_post_type', 0 );
}
/**
 * Flush permalinks on plugin deactivation.
 */
function woo_extra_tab_plugin_deactivation() {
    flush_rewrite_rules();
}
function woo_extra_tab_plugin_setup() 
{

/* Get the plugin directory URI. */
define( 'WOO_TAB_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

add_action( 'init','woo_extra_tabs_post_type', 0 );

/* Add meta boxes on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'woo_extra_tabs_meta_boxes' );

/* Save post meta on the 'save_post' hook. */
add_action( 'save_post', 'woo_extra_tabs_save_meta', 10, 2 );
  
//add tabs to product page
if (!is_admin()){
	add_filter( 'woocommerce_product_tabs','odude_woo_product_tabs' );
}


}

function woo_extra_tabs_post_type(){
 $labels = array(
            'name'                => _x( 'Woocommerce Extra Tabs', 'Post Type General Name', 'Woo_extra_tab' ),
            'singular_name'       => _x( 'Woocommerce Extra Tab', 'Post Type Singular Name', 'Woo_extra_tab' ),
            'menu_name'           => __( 'Woo Extra Tabs', 'Woo_extra_tab' ),
            'parent_item_colon'   => __( '', 'Woo_extra_tab' ),
            'all_items'           => __( 'Woocommerce Extra Tabs', 'Woo_extra_tab' ),
            'view_item'           => __( '', 'Woo_extra_tab' ),
            'add_new_item'        => __( 'Add Woocommerce Extra Tab', 'Woo_extra_tab' ),
            'add_new'             => __( 'Add New', 'Woo_extra_tab' ),
            'edit_item'           => __( 'Edit Woo Tab', 'Woo_extra_tab' ),
            'update_item'         => __( 'Update Woo Tab', 'Woo_extra_tab' ),
            'search_items'        => __( 'Search Woo Tab', 'Woo_extra_tab' ),
            'not_found'           => __( 'Not found', 'Woo_extra_tab' ),
            'not_found_in_trash'  => __( 'Not found in Trash', 'Woo_extra_tab' ),
        );
        $args = array(
            'label'               => __( 'Woo Extra Tabs', 'Woo_extra_tab' ),
            'description'         => __( 'Custom WooCommerce Tabs', 'Woo_extra_tab' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor', 'custom-fields' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => false,
            'show_in_admin_bar'   => true,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-paperclip',
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => true,
            'publicly_queryable'  => false,
            'capability_type'     => 'post',
			 'taxonomies'          => array( 'product_cat' ),
			
			
        );
        register_post_type( 'woo-extra-tab', $args );	
				
}


function custom_cross_content_type_taxonomies(){
  register_taxonomy_for_object_type( 'product_cat', 'woo-extra-tab' );
}
add_action( 'init', 'custom_cross_content_type_taxonomies', 11);

	
function woo_extra_tabs_meta_boxes() {

  add_meta_box(
    'woo-tabs-meta',      // Unique ID
    esc_html__( 'Select Woocommerce Extra Tabs', 'example' ),    // Title
    'woo_extra_tabs_meta_box',   // Callback function
    'product',         // Admin page (or post type)
    'side',         // Context
    'default'         // Priority
  );
}
function woo_extra_tabs_meta_box( $object, $box ) { ?>
  
  <p>
  Press CTRL and choose to select multiple tabs.
  <br>
  <select  name="woo-custom-extra-tab[]"  multiple="multiple" >
        <?php   
              $tabs_ids = get_post_meta( $object->ID, 'woo_custom_extra_tabs', true );
			  $woo_extra_tab_ids = ! empty( $tabs_ids ) ?  $tabs_ids : array();
              foreach ( woo_extra_tabs_list() as $id => $label ) {
				$selected = in_array($id, $woo_extra_tab_ids)?  'selected="selected"' : '';
                echo '<option value="' . esc_attr( $id ) . '"'.$selected.'>' . esc_html( $label ) . '</option>';
             }
        ?>
  </select>
  </p>
<?php }

/* Save the meta box's post metadata. */
function woo_extra_tabs_save_meta( $post_id, $post ) {


  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_meta_value = ( isset( $_POST['woo-custom-extra-tab'] ) ? sanitize_html_class( $_POST['woo-custom-extra-tab'] ) : '' );

  /* Get the meta key. */
  $meta_key = 'woo_custom_extra_tabs';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}

 /**
 * Woo Extra Tabs_list
 */
    function woo_extra_tabs_list(){
        $args = array(
            'post_type'      => 'Woo-extra-tab',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids'
        );
        $woo_extra_tabs_arr = array();
        $posts = get_posts($args);
        if ( $posts ){
			foreach ( $posts as $post_id ) {
            $woo_extra_tabs_arr[ $post_id ] = get_the_title($post_id);
        }
        return $woo_extra_tabs_arr;
		}
	}
	
/**
* odude_woo_product_tabs
*/
    function odude_woo_product_tabs($tabs)
	{
		
        global $post;        
        $woo_extra_tabs = get_post_meta( $post->ID, 'woo_custom_extra_tabs', true );
        $woo_extra_tab_ids = ! empty($woo_extra_tabs  ) ? $woo_extra_tabs : null;
 
		
	
		$a=array();
        $terms = get_the_terms( $post->ID, 'product_cat' );
		$i=0;
        foreach ((array) $terms as $term) 
		{
            $product_category = $term->name;
			//product associated category is compared with tab category
			
			$i=odude_cross_check_categoris($product_category);
			
			$arrlength = count($i);
			for($x = 0; $x < $arrlength; $x++) 
			{
			array_push($a,$i[$x]);
			}
			
           
			
			
        }
		

		 if ($woo_extra_tab_ids)
		{           
            foreach ($woo_extra_tab_ids as $id) 
			{   
			array_push($a,$id);
			}
		} 
		
		
		//var_dump($a);
		
		
		$b=array_unique($a);
 
		
		
       if (!empty($b))
	   {           
            foreach ($b as $id) {       	
					
					//$id is tab id
					
	                $tabs['woo_extra_tab_'.$id] = array(
	                    'title'    => get_the_title($id),
	                    'priority' =>  50 ,
	                    'callback' => 'odude_add_woo_extra_tab',
	                    'content'  => apply_filters('the_content',get_post_field( 'post_content', $id)) 
	                );          	
            }
       }
	   
	   
        return $tabs;
    } 
/**
 * ADD_tab     
*/
function odude_add_woo_extra_tab($key,$tab){
        global $post;
        echo '<h2>'.apply_filters('woo_extra_tab_title',$tab['title'],$tab,$key).'</h2>';
        echo apply_filters('woo_extra_tab_content',$tab['content'],$tab,$key);
}