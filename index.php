<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */

get_header();

get_template_part( 'template-parts/banniere');
?>
<!-- pas de soustitre -->
<!-- Grid Layout -->
<div id="content" class="site-content  py-5 mt-5 indexphp copied-from-category-portfoliosdotphp portfolios">
  <div id="primary" class="content-area">


         <main id="main" class="site-main grid gallery">
          <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post();
//isotope classes
		  	$isotopeclasses = '';
		    $categories = get_the_category();
		    foreach ( $categories as $cat ) {
		         $isotopeclasses .= $cat->slug . ' ';
	        }
			 //mb-4
		  ?>
 <div class="card horizontal col-sm-6 col-md-4 col-lg-3 grid-item full-width <?php echo $isotopeclasses; ?>">
                <div class="row full-width">
                  <!-- Featured Image-->
				  <a href="<?php the_permalink();?>" tabindex="-1">
                  <?php if (has_post_thumbnail())
                    echo '<div class="card-img card-img-left-md">' . get_the_post_thumbnail(null, 'medium') . '</div>';
                  ?>
				  </a>
                  <div class="col">
                    <div class="card-body">
                      <!-- Title -->
                      <h2 class="blog-post-title blog__title">
						<a href="<?php the_permalink();?>">
<?php the_title(); ?>
</a>
</h2>
						
                      <div class="productionslist-title2 b3">
                        <?php the_field('soustitre'); ?>
                      </div>
                        <?php 
						foreach ( $categories as $cat ) {
							if($cat->slug == "portfolio" || $cat->slug == "actualites" || $cat->slug == "livres" || $cat->slug == "numerique") {
								echo "<span class='accueil-tag'>".$cat->name."</span><br><br>";
							}
  						} 
						?>
                  </div><!-- card-body -->
				</div><!-- card.row -->
              </div><!-- card.col -->
              </div><!-- card.horizontal -->
            <?php endwhile; ?>
          <?php endif; ?>
          <!-- Pagination -->
        </main><!-- #main -->
      <!--/div> col -->
      <?php get_sidebar(); ?>
    <!--/div> row -->
  </div><!-- #primary -->
</div><!-- #content -->
<!-- More lists custom container 
<div class="container plusdeliens">
<div class="row">
<div class="col-4"><a href="/fr/articles/" class="btn btn-light btn-block btn-home" role="button">Plus d'articles</a></div>
<div class="col-4"><a href="/fr/productions/" class="btn btn-light btn-block btn-home" role="button">Plus de productions</a></div>
<div class="col-4"><a href="/fr/livres/" class="btn btn-light btn-block btn-home" role="button">Plus de livres</a></div>
</div>
</div>-->
<?php 
get_template_part( 'template-parts/logos');
get_footer();
?>