<?php
//temp
update_option( 'siteurl', 'https://v22.productionsrhizome.org/' );
update_option( 'home', 'https://v22.productionsrhizome.org/' );

// style and scripts
add_action('wp_enqueue_scripts', 'bootscore_child_enqueue_styles');
function bootscore_child_enqueue_styles() {

  // style.css
  wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
  wp_enqueue_style('slick-style', get_stylesheet_directory_uri()  . '/js/slick/slick.css');
  wp_enqueue_style('slick-theme', get_stylesheet_directory_uri()  . '/js/slick/slick-theme.css');
	
  // Compiled Bootstrap
  $modified_bootscoreChildCss = date('YmdHi', filemtime(get_stylesheet_directory() . '/css/lib/bootstrap.min.css'));
  wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/css/lib/bootstrap.min.css', array('parent-style'), $modified_bootscoreChildCss);

  // custom.js
  wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', false, '', true);
	  	
	//jquery ui
	wp_enqueue_script( 'jquery-ui-accordion' );
	
		// slick carousel
  wp_enqueue_script('slick-js', get_stylesheet_directory_uri() . '/js/slick/slick.min.js', false, '', true);
	
	 // MARCHE PAS
//wp_enqueue_script('custom-js', get_stylesheet_directory_uri() . '/js/theme.js', array( 'jquery', 'jquery-ui-accordion' ) );
	
}


//https://stackoverflow.com/questions/62555505/wordpress-custom-template-page-for-a-parent-category-and-all-child-categories
function get_template_for_category( $template ) {

    if ( basename( $template ) === 'category.php' ) { // No custom template for this specific term, let's find it's parent
        // get the current term, e.g. red

        $term = get_queried_object();
// nothing : echo "2".$term->id;
        // check for template file for the page category
        $slug_template = locate_template( "category-{$term->slug}.php" );
        if ( $slug_template ) return $slug_template;
// nothing : echo "3".$slug_template;
        // if the page category doesn't have a template, then start checking back through the parent levels to find a template for a parent slug
        $term_to_check = $term;
        // return child slug echo "4".$term_to_check->slug;
        while ( $term_to_check ->parent ) {
            // get the parent of the this level's parent
            $term_to_check = get_category( $term_to_check->parent );
     //   echo "5_ischildof_".$term_to_check->slug;

if ($term_to_check->slug == "productions") {
	        //$query->set( 'order', 'ASC' );
 //   echo "5a_isproduction";
    // this category has production as parent, will need simpler category template
     $slug_template = locate_template( "category-childproductions.php" );
     if ( $slug_template ) return $slug_template;
}



            if ( ! $term_to_check || is_wp_error( $term_to_check ) )

                echo "break";
                break; // No valid parent found

            // Use locate_template to check if a template exists for this categories slug
            $slug_template = locate_template( "category-{$term_to_check->slug}.php" );
        // nothing    echo "6".$slug_template;
            // if we find a template then return it. 
            //   // Found ya! Let's override $template and get outta here
            // Otherwise the loop will check for this level's parent
            if ( $slug_template ) return $slug_template;
        }
    }

    return $template;
}
add_filter( 'category_template', 'get_template_for_category' );

/**
* Filter the excerpt length to 10 words.
*
* @param int $length Excerpt length.
* @return int (Maybe) modified excerpt length.
*/
function wpdocs_custom_excerpt_length( $length ) {
return 10;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

/* Override private post prefix

Reference: https://www.wpbeginner.com/wp-tutorials/how-to-change-private-and-protected-posts-prefix-in-wordpress/
*/
function change_private_title_prefix() {
    return '%s';
}
add_filter('private_title_format', 'change_private_title_prefix');

/* Redirect login. Via https://firstsiteguide.com/how-to-redirect-users-in-wordpress/*/
/*Login*/
function my_login_redirect( $url, $request, $user ){ 
	if( $user && is_object( $user ) && is_a( $user, 'WP_User' ) ) {
		if( $user->has_cap( 'administrator') or $user->has_cap( 'author')) {
			$url = admin_url();
		} else {
			$url = home_url('/');

		}
	}
	return $url;
}
add_filter('login_redirect', 'my_login_redirect', 10, 3 );

/*Logout*/
add_action('wp_logout','auto_redirect_after_logout');
function auto_redirect_after_logout(){
	wp_redirect( home_url() );
	exit();
}
/* non visible 
function wpc_dashboard_widgets() {
	global $wp_meta_boxes;
	// Widget Aujourd'hui
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	// Derniers commentaires
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	// Liens entrants
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
	// Extensions
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
}
add_action('wp_dashboard_setup', 'wpc_dashboard_widgets');
 */

/*via https://supermarketeur.com/limiter-lacces-tableau-bord-wordpress-abonnes/ 
empeche la connexion
add_action( 'init', 'limit_dashboard_access' );
function limit_dashboard_access() {
	if ( is_admin() && ! current_user_can( 'administrator' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
	wp_redirect( home_url() );
	exit;
	}
}
*/

/*https://digwp.com/2014/02/disable-default-dashboard-widgets/*/
function remove_default_dashboard_widgets() {
	
	if (!current_user_can('manage_options')) {
		
		remove_action('welcome_panel', 'wp_welcome_panel');
		
		remove_meta_box('dashboard_primary',       'dashboard', 'side');
		remove_meta_box('dashboard_secondary',     'dashboard', 'side');
		remove_meta_box('dashboard_quick_press',   'dashboard', 'side');
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');
		
		remove_meta_box('dashboard_php_nag',           'dashboard', 'normal');
		remove_meta_box('dashboard_browser_nag',       'dashboard', 'normal');
		remove_meta_box('health_check_status',         'dashboard', 'normal');
		remove_meta_box('dashboard_activity',          'dashboard', 'normal');
		remove_meta_box('dashboard_right_now',         'dashboard', 'normal');
		remove_meta_box('network_dashboard_right_now', 'dashboard', 'normal');
		remove_meta_box('dashboard_recent_comments',   'dashboard', 'normal');
		remove_meta_box('dashboard_incoming_links',    'dashboard', 'normal');
		remove_meta_box('dashboard_plugins',           'dashboard', 'normal');
		
	}
	
}
add_action('wp_dashboard_setup', 'remove_default_dashboard_widgets');

/*Option: remove boxes from article form
 * 
 * if (is_admin()) :
function remove_post_meta_boxes() {
 if(!current_user_can('administrator')) {
  remove_meta_box('tagsdiv-post_tag', 'post', 'normal');
  remove_meta_box('categorydiv', 'post', 'normal');
  remove_meta_box('postimagediv', 'post', 'normal');
  remove_meta_box('authordiv', 'post', 'normal');
  remove_meta_box('postexcerpt', 'post', 'normal');
  remove_meta_box('trackbacksdiv', 'post', 'normal');
  remove_meta_box('commentstatusdiv', 'post', 'normal');
  remove_meta_box('postcustom', 'post', 'normal');
  remove_meta_box('commentstatusdiv', 'post', 'normal');
  remove_meta_box('commentsdiv', 'post', 'normal');
  remove_meta_box('revisionsdiv', 'post', 'normal');
  remove_meta_box('authordiv', 'post', 'normal');
  remove_meta_box('slugdiv', 'post', 'normal');
 }
}
add_action( 'admin_menu', 'remove_post_meta_boxes' );
endif;
 * */


/*Exclude Pages from Search Results
 * TODO: exclude category logos
 * */
function search_filter($query) {
    if ( ! is_admin() && $query->is_main_query() ) {
        if ( $query->is_search ) {
            $query->set( 'post_type', 'post' );
        }
    }
}
add_action( 'pre_get_posts', 'search_filter' );


/*
 *  * */
function custom_rewrite_rule() {
    add_rewrite_rule('^productions/([^/]*)/([^/]*)/?','index.php?&category=$matches[2]','top');
}
add_action('init', 'custom_rewrite_rule', 10, 0);

function bidirectional_acf_update_value( $value, $post_id, $field  ) {
    
    // vars
    $field_name = $field['name'];
    $field_key = $field['key'];
    $global_name = 'is_updating_' . $field_name;
    
    
    // bail early if this filter was triggered from the update_field() function called within the loop below
    // - this prevents an inifinte loop
    if( !empty($GLOBALS[ $global_name ]) ) return $value;
    
    
    // set global variable to avoid inifite loop
    // - could also remove_filter() then add_filter() again, but this is simpler
    $GLOBALS[ $global_name ] = 1;
    
    
    // loop over selected posts and add this $post_id
    if( is_array($value) ) {
    
        foreach( $value as $post_id2 ) {
            
            // load existing related posts
            $value2 = get_field($field_name, $post_id2, false);
            
            
            // allow for selected posts to not contain a value
            if( empty($value2) ) {
                
                $value2 = array();
                
            }
            
            
            // bail early if the current $post_id is already found in selected post's $value2
            if( in_array($post_id, $value2) ) continue;
            
            
            // append the current $post_id to the selected post's 'related_posts' value
            $value2[] = $post_id;
            
            
            // update the selected post's value (use field's key for performance)
            update_field($field_key, $value2, $post_id2);
            
        }
    
    }
    
    
    // find posts which have been removed
    $old_value = get_field($field_name, $post_id, false);
    
    if( is_array($old_value) ) {
        
        foreach( $old_value as $post_id2 ) {
            
            // bail early if this value has not been removed
            if( is_array($value) && in_array($post_id2, $value) ) continue;
            
            
            // load existing related posts
            $value2 = get_field($field_name, $post_id2, false);
            
            
            // bail early if no value
            if( empty($value2) ) continue;
            
            
            // find the position of $post_id within $value2 so we can remove it
            $pos = array_search($post_id, $value2);
            
            
            // remove
            unset( $value2[ $pos] );
            
            
            // update the un-selected post's value (use field's key for performance)
            update_field($field_key, $value2, $post_id2);
            
        }
        
    }
    
    
    // reset global variable to allow this filter to function as per normal
    $GLOBALS[ $global_name ] = 0;
    
    
    // return
    return $value;
    
}

add_filter('acf/update_value/name=relations_artistes_realisations', 'bidirectional_acf_update_value', 10, 3);

/* Carte interactive Avez-vous lu (ACF + Elementor) */
require_once get_stylesheet_directory() . '/inc/carte-avl.php';
