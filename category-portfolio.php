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
    <section id="main" class="breadcrumb-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb-content">
                        <!-- Title & Description -->
                        <h1 class="breadcrumb__title"><?php single_cat_title(); ?></h1>
                        <p><?php the_archive_description('<div class="archive-description">', '</div>'); ?></p>
                        <!-- Filtres -->
                        <?php get_template_part( 'template-parts/filtres'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Grid Layout -->
    <div id="content" class="site-content py-5 mt-5 category-portfoliosdotphp portfolios">
        <div id="primary" class="content-area">
            <main class="site-main grid gallery">
                <?php
                //https://stackoverflow.com/questions/3875895/wordpress-loop-show-limit-posts
                global $wp_query;
                $args = array_merge( $wp_query->query_vars, ['posts_per_page' => 999 ] );
                query_posts( $args );


                if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post();
//isotope classes
                        $isotopeclasses = '';
                        $categories = get_the_category();
                        foreach ( $categories as $cat ) {
                            $isotopeclasses .= $cat->slug . ' ';
                        }
                        ?>
                        <div class="card horizontal col-sm-6 col-md-4 col-lg-3 grid-item full-width <?php echo $isotopeclasses; ?>">
                            <!-- Featured Image
          rewrite url, wrap around image
          wrap around title also
          -->
                            <a class="card-img card-img-left-md" href="<?php
                            //1. verifier si le post est taggué avec productions
                            if ( in_category( 'productions' )) {
                                ///2. trouver la categorie qui a production comme parent, puis l'imprimer en lien
                                // REECRIRE LIEN AVEC X_ID?
                                $categories = get_the_category();
                                foreach ( $categories as $cat ) {
                                    if (cat_is_ancestor_of(167, $cat)) {
                                        $cls = $cat->slug;
                                    }
                                }
                                $rewrite = "/productions/".$cls;
                                echo $rewrite;
                            }	else {
                                the_permalink();
                            } ?>" tabindex="-1">
                                <?php if (has_post_thumbnail()) :
                                    echo '<div class="card-img-wrapper">' . get_the_post_thumbnail(null, 'medium') . '</div>';
                                endif;         ?>
                            </a>
                            <div class="card-body ">
                                <!-- Title -->
                                <h2 class="blog-post-title blog__title">
                                    <a href="<?php the_permalink();?>"><?php the_title(); ?></a>
                                </h2>
                                <div class="productionslist-title2 b3">
                                    <?php the_field('soustitre'); ?>
                                </div>
                            </div><!-- card-body -->
                        </div><!-- card.horizontal -->
                    <?php endwhile; ?>
                <?php endif; ?>
                <!-- Pagination -->
            </main><!-- #main -->
            <?php get_sidebar(); ?>
        </div><!-- #primary -->
    </div><!-- #content -->
<?php
get_footer();