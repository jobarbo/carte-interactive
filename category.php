<?php

/**
 * The template for displaying category pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */

get_header();
?>
<section id="main" class="breadcrumb-area category_php">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-content">
                 <!-- Title & Description -->
                   <h1 class="breadcrumb__title"><?php single_cat_title(); ?></h1>
                    <p></p>
                    <?php
                    if (the_archive_description()!=""){
                        echo "<div class='productionslist-title2'>".the_archive_description('', '')."</div>";
                    }
                    ?>
                    <!-- Filtres -->
                        <?php $url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if((str_contains($url,'portfolio'))||(str_contains($url,'series'))||(str_contains($url,'productions-series'))||(str_contains($url,'types'))||(str_contains($url,'annees'))): ?>
                            <?php get_template_part( 'template-parts/filtres'); ?>
                        <?php elseif(str_contains($url,'numerique')): ?>
                            <?php get_template_part( 'template-parts/filtres-numerique'); ?>
                        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Grid Layout -->
<div id="content" class="site-content  py-5 mt-5 categoryphp_content">
  <div id="primary" class="content-area">
        <main class="site-main grid gallery">

		          <?php
			 //https://stackoverflow.com/questions/3875895/wordpress-loop-show-limit-posts
 global $wp_query;
 $args = array_merge( $wp_query->query_vars, ['posts_per_page' => 999 ] );
 query_posts( $args );

          //Grid Layout : copied from index.php TODO in template
 if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
              <?php if (is_sticky()) continue; //ignore sticky posts
//isotope classes
			$isotopeclasses = '';
		    $categories = get_the_category();
		    foreach ( $categories as $cat ) {
		         $isotopeclasses .= $cat->slug . ' ';
	        }
              ?>
			 <!-- each grid item -->

              <div class="card horizontal col-sm-6 col-md-4 col-lg-3 grid-item full-width  <?php echo $isotopeclasses; ?>">

                <div class="row full-width">
                  <!-- Featured Image-->
				  <a class="card-img card-img-left-md" href="<?php the_permalink();?>" tabindex="-1">
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
                      <div class="productionslist-title2">
                        <?php the_field('soustitre'); ?>
                      </div>
                      <!-- Meta -->
                      <?php if ('post' === get_post_type()) : ?>
                        <small class="text-secondary mb-2"></small>
                      <?php endif; ?>
                      <!-- Excerpt & Read more -->
                      <div class="card-text mt-auto">
                      </div>
                      <!-- Tags -->
                      <?php bootscore_tags(); ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </main><!-- #main -->
      <?php get_sidebar(); ?>
  </div><!-- #primary -->
</div><!-- #content -->
<?php
get_footer();
