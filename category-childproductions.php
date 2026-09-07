<?php
/*
* Productions are categories.
* Extract all sub-posts targeted with a Productions category (child of Productions Category), 
* and show within a page, inside an accordion.
* 
* Separate public and private sub-posts with an extra loop.
*/
/**
 * The template for displaying category pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */
/*
* This template requires function get_template_for_category in functions.php, to get the parent category
*/
get_header();
?>
<!-- Title & Description -->
<div class="breadcrumb-area header-area ">
	<header class="page-header mb-4 category-productions-header entry-header container category-childproductionsdotphp">
     	<h1><?php single_cat_title(); ?></h1>
        <?php the_archive_description('<div class="archive-description productionslist-title2">', '</div>'); ?>
     </header>
</div>
<div id="content" class="site-content container py-5 mt-5 category-productions child">
  <div id="primary" class="content-area">

    <!-- Hook to add something nice -->
    <?php bs_after_primary(); ?>

    <div class="row">
      <div class="col">

        <main id="main" class="site-main">  
	
	
          <!-- Grid Layout -->
  			<div id="accordeon">
			<?php
			// Extract all sub-posts targeted with a Productions category
			// default loop of a category.php template
			// 
			if (have_posts()) : 
		    // Bonus: renverser ordre affichage par defaut de DESC a ASC
			$wp_query->posts = array_reverse( $wp_query->posts );
            while (have_posts()) : the_post(); 
	        // Montrer seulement posts publics. Il faut donc ne pas imprimer les posts privés.
	  		if ( get_post_status ( $ID ) == 'private' ) {  // void
            } else { ?>
		            <!-- accordeon Title -->
		            <h3 class="publicposts"><?php the_title(); ?></h3>
                   <!-- Sub Content, linked by category -->
                   <div>            
                     <?php the_content(); ?>
                   </div>
				<?php	   } // end public post template
	          endwhile;
	  	   wp_reset_query(); ?>
	  		</div><!-- /accordeon -->
	  
	  			<?php 
			// Only render and show  extra content if user is logged in
			 if ( is_user_logged_in() ) {
			
			// Separate public and private sub-posts with an extra loop.
	  			// This will pick the same posts, but only show the private ones.
	  			// Un peu plus complexe car il faut faire un loop custom
	      $category = get_category( get_query_var( 'cat' ) );
          $cat_id = $category->cat_ID;  
	  
     	  $args = array('cat' => $cat_id,
					   'post_status' => 'private');

	      $private_posts = new WP_Query( $args ); 
	      if ( $private_posts->have_posts() ) : 
	  
	  // Entête pour les posts privés, avant leur loop
	  echo "<h2 class='private-title'>Espace Diffuseur</h2>
	    <div id='accordeon-private'>";
	  
	        while ( $private_posts->have_posts() ) : $private_posts->the_post() ?>
	       <?php  //Montrer seulement les posts de statut private. On n'imprime donc pas les posts publics.
		 //  if ( get_post_status ( $ID ) == 'private' ) { 
	       ?>
	  		            <!-- accordeon Title -->
		            <h3 class="privateposts"><?php the_title(); ?></h3>
                   <!-- Sub Content, linked by category -->
                   <div>            
                     <?php the_content(); ?>
                   </div>
	  <?php 
	  ?>
	  	<?php endwhile;
			  wp_reset_query(); 
			?>
      <?php endif; 
			 } //endif userloggedin
			?>
		</div><!-- /accordeon-private -->
          <?php endif; ?>
          <!-- Edit: No Pagination, all subpages in a single category page -->
			<?php //bootscore_pagination(); ?>

        </main><!-- #main -->

      </div><!-- col -->

      <?php //get_sidebar(); ?>
    </div><!-- row -->
		
  </div><!-- #primary -->
</div><!-- #content -->
<?php
get_footer();
