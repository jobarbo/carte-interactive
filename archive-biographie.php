<?php

/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */

get_header();
?>

<div id="content" class="site-content py-5 archivephp">
  <div id="primary" class="content-area">

    <div class="row">
      <div class="col">

        <main id="main" class="site-main">

          <!-- Title & Description -->
          <header class="entry-header page-header">
            <h1>Artistes</h1>
          </header>
            <div id="conteneurLettres" class="artistes-lettres">
                <?php
                $alphabet = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
                for($i=0;$i<count($alphabet);$i++){
                    if($_GET["filtre"]==$alphabet[$i]){
                        echo "<h2><a class='artistes-lettres-lettre filtreChoisi' href='https://productionsrhizome.org/biographie?filtre=$alphabet[$i]'>$alphabet[$i]</a></h2>";
                    }else{
                        echo "<h2><a class='artistes-lettres-lettre' href='https://productionsrhizome.org/biographie?filtre=$alphabet[$i]'>$alphabet[$i]</a></h2>";
                    }
                }
                ?>
            </div>
            <div class="mt-5 artistes-liste">
                <?php
                global $wpdb;

                $first_char = esc_attr($_GET["filtre"]);

                $get_title = get_the_title();
                $noms = explode(" ",$get_title);
                $nomDeFamille = $noms[1];
                $initialeNomDeFamille = substr($nomDeFamille, 0, 1);

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
                        'posts_per_page' => -1,
                        'caller_get_posts'=> 1
                    );

                    $my_query = null;
                    $my_query = new WP_Query($args);
                    if( $my_query->have_posts() ) {
                        while ($my_query->have_posts()) : $my_query->the_post(); ?>
                            <p class="blog-post-title">
                                <a href="<?php the_permalink(); ?>" class="nom-artiste" id="<?php the_title();?>">
                                    <?php the_title(); ?>
                                </a>
                            </p>
                        <?php endwhile;
                    }

                    wp_reset_query();
                }
                ?>
            </div>

        </main><!-- #main -->

      </div><!-- col -->

        <?php get_sidebar(); ?>
    </div><!-- row -->

  </div><!-- #primary -->
</div><!-- #content -->
            </div>


<?php
get_footer();
