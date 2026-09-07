<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */

get_header();
?>
<div class=" header-area ">
    <?php if(is_page('Artistes')): ?>
        <header class="entry-header container">
            <!-- Title -->
            <?php the_title('<h1>', '</h1>'); ?>
            <!--<span class="titre-page-artistes">--><?php //the_title('<h1>', '</h1>'); ?><!--<a href="https://fr-ca.wordpress.org/plugins/wikibiographie/"><img class="logo-wikibiographie" alt="Alimenté par WikiBiographie" src="https://productionsrhizome.org/wp-content/uploads/Wikibiographie-visuel.png"></a></span>-->
            <?php if (the_field('soustitrepage')!=""){
                echo "<p class='productionslist-title2'>".the_field('soustitrepage')."</p>";
            }
            ?>
        </header>
    <?php else: ?>
        <header class="entry-header container">
                        <!-- Title -->
        <?php the_title('<h1>', '</h1>'); ?>
          <?php if (the_field('soustitrepage')!=""){
              echo "<p class='productionslist-title2'>".the_field('soustitrepage')."</p>";
          }
          ?>
      </header>
    <?php endif; ?>
</div>
<?php if(is_page('Artistes')): ?>
    <div id="content" class="site-content py-5 archivephp">
        <div id="primary" class="content-area">
            <div class="row">
                <div class="col">
                    <main id="main" class="site-main">
                        <div id="conteneurLettres" class="artistes-lettres">
                            <?php
                            global $wpdb;

                            $names = $wpdb->get_col($wpdb->prepare("
                            SELECT post_title
                            FROM $wpdb->posts
                            WHERE $wpdb->posts.post_type = 'biographie'"));

                            $letters = array();

                            foreach ($names as $name) {
                                $surname = explode(" ", $name)[1]; // get the surname
                                $first_letter = strtoupper(substr($surname, 0, 1)); // get the first letter of the surname
                                if (!in_array($first_letter, $letters)&$first_letter!=null) {
                                    $letters[] = $first_letter; // add the first letter to the array if it's not already there
                                }
                            }

                            sort($letters); // sort the array alphabetically

                            foreach ($letters as $letter) {
                                if($_GET["filtre"]==$letter){
                                    echo "<h2><a class='notranslate artistes-lettres-lettre filtreChoisi' href='https://productionsrhizome.org/artistes?filtre=$letter'>$letter</a></h2>";
                                }else{
                                    echo "<h2><a class='notranslate artistes-lettres-lettre' href='https://productionsrhizome.org/artistes?filtre=$letter'>$letter</a></h2>";
                                }
                            }

                            /*$alphabet = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
                            for($i=0;$i<count($alphabet);$i++){
                                if($_GET["filtre"]==$alphabet[$i]){
                                    echo "<h2><a class='notranslate artistes-lettres-lettre filtreChoisi' href='https://v23.productionsrhizome.org/artistes?filtre=$alphabet[$i]'>$alphabet[$i]</a></h2>";
                                }else{
                                    echo "<h2><a class='notranslate artistes-lettres-lettre' href='https://v23.productionsrhizome.org/artistes?filtre=$alphabet[$i]'>$alphabet[$i]</a></h2>";
                                }
                            }*/
                            ?>
                        </div>
                        <div class="mt-5 artistes-liste">
                            <?php
                            global $wpdb;

                            $first_char = esc_attr($_GET["filtre"]);

                            $postids = $wpdb->get_col($wpdb->prepare("
                    SELECT      ID
                    FROM        $wpdb->posts
                    WHERE       SUBSTR($wpdb->posts.post_title,(LOCATE(' ',$wpdb->posts.post_title)+1),1) = %s
                    AND 		$wpdb->posts.post_type = 'biographie'
                    ORDER BY    $wpdb->posts.post_title",$first_char));

                            if ( $postids ) {
                                $args = array(
                                    'post__in' => $postids,
                                    'post_type' => 'biographie',
                                    'post_status' => 'publish',
                                    'orderby' => 'title',
                                    'order' => 'ASC',
                                    'posts_per_page' => -1,
                                    'caller_get_posts'=> 1
                                );

                                $my_query = null;
                                $my_query = new WP_Query($args);
                                if( $my_query->have_posts() ) {
                                    while ($my_query->have_posts()) : $my_query->the_post(); ?>
                                        <p class="blog-post-title notranslate">
                                            <a href="<?php the_permalink(); ?>" class="nom-artiste">
                                                <?php the_title(); ?>
                                            </a>
                                        </p>
                                    <?php endwhile;
                                }

                                wp_reset_query();
                            }
                            ?>
                        </div>
                        <a href="https://fr-ca.wordpress.org/plugins/wikibiographie/" target="_blank"><img class="logo-wikibiographie" alt="Alimenté par WikiBiographie" src="https://productionsrhizome.org/wp-content/uploads/Wikibiographie-visuel.png"></a>
                    </main>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div id="content" class="site-content container pagephp">
        <div id="primary" class="causes-detail-area content-area container">
            <div class="row blog-content-wrap">
                <!-- Hook to add something nice -->
                <?php bs_after_primary(); ?>

                <div class="col-sm-12">

                    <main id="main" class="site-main">
                        <!-- header removed -->

                        <div class="entry-content">
                            <?php bootscore_post_thumbnail(); ?>
                            <?php //bootscore_category_badge(); ?>
                            <?php the_content(); ?>
                        </div>

                        <footer class="entry-footer clear-both">
                        </footer>
                        <!-- Comments -->
                        <?php comments_template(); ?>

                    </main><!-- #main -->

                </div><!-- col -->
                <?php get_sidebar(); ?>
            </div><!-- row -->

        </div><!-- #primary -->
    </div><!-- #content -->
<?php endif; ?>
<?php get_footer();