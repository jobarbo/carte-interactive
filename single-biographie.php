<?php
/*
	 * Template Post Type: post
	 */

get_header();
$bio = (new WikiBiographie())->get_biographie(get_the_ID());
?>

    <script>
        //	console.log("single");
    </script>

    <!--<div class="breadcrumb-area header-area ">
        <header class="entry-header container">
            <?php /*the_title('<h1 class="">', '</h1>'); */?>
            <div class='productionslist-title2'>
                <?php /*if(!empty($bio['occupation'])): */?>
                    <div>
                        <div>
                            <?php /*echo esc_attr($bio['occupation']); */?>
                        </div>
                    </div>
                <?php /*endif; */?>
            </div>
        </header>
    </div>-->
    <div id="content" class="site-content is_single <?php print $is_book; ?>">
        <div>

            <!-- Hook to add something nice -->
            <?php //bs_after_primary(); ?>
            <!-- Option: Breadcrumb -->
            <?php //the_breadcrumb(); ?>

            <div class="row blog-content-wrap">
                <?php
                // Exception Livre:
                // Format 8 colonnes pour le contenu, 4 colonnes pour le bouton Achat Shopify
                if ( in_category( 'livres' )) : 		?>

                    <div class="col-sm-8">
                        <div class="prjmain">
                            <?php bootscore_post_thumbnail(); ?>
                            <?php the_content(); ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <?php
                        if( get_field('code_shopify') ): ?><div class="code_shopify"><?php the_field('code_shopify'); ?></div><?php endif; ?>
                    </div>
                <?php  else: ?>
                    <div class="col-sm-1"></div>
                    <div style="padding: 0">
                        <main id="main" class="site-main">

                            <?php if(!empty($bio['image'])): ?>
                            <div class="ficheArtiste">
                                <div class="ficheArtiste-image">
                                    <img src="<?php echo esc_url($bio['image']); ?>" alt="<?php _e('Photo of', 'wikibiographie'); ?> <?php the_title(); ?>" style="width: 100%;">
                                </div>
                                <?php else: ?>
                                <div class="ficheArtisteSansImage">
                                    <?php endif; ?>
                                <div>
                                    <?php the_title('<h1 class="ficheArtiste-titre">', '</h1>'); ?>
                                    <?php if(!empty($bio['occupation'])): ?>
                                        <div class='ficheArtiste-occupation'>
                                            <?php echo esc_attr($bio['occupation']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($bio['pseudo'])): ?>
                                        <div>
                                            <div><?php _e('Nickname', 'wikibiographie'); ?></div>
                                            <div>
                                                <?php echo esc_attr($bio['pseudo']); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($bio['introduction']) || !empty($bio['introduction_complement'])): ?>
                                        <div class="ficheArtiste-description">
                                            <div>
                                                <?php echo html_entity_decode($bio['introduction']); ?>
                                                <?php if(!empty($bio['introduction_complement'])): ?>
                                                    <?php echo html_entity_decode($bio['introduction_complement']); ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if(!empty($bio['website'])): ?>
                                        <div class="ficheArtiste-site">
                                            <div><?php _e('Official website', 'wikibiographie'); ?> : <a href="<?php echo esc_url($bio['website']); ?>" target="_blank"><?php echo esc_url($bio['website']); ?></a></div>
                                        </div>
                                    <?php endif; ?>
                                    <?php $relations = get_field('relations_artistes_realisations'); ?>
                                    <?php if($relations): ?>
                                        <br>
                                        <p style="font-size: 18px"><strong>Collaborations avec Rhizome :</strong></p>
                                        <ul>
                                            <?php foreach ($relations as $relation):?>
                                                <li><a href="<?php echo $relation->guid ?>"><?php echo $relation->post_title ?></a></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <footer class="entry-footer clear-both">
                                <div class="mb-4">
                                    <?php bootscore_tags(); ?>
                                </div>
                                <nav aria-label="Page navigation example">
                                    <ul class="pagination justify-content-center">
                                        <li class="page-item">
                                            <?php previous_post_link('%link'); ?>
                                        </li>
                                        <li class="page-item">
                                            <?php next_post_link('%link'); ?>
                                        </li>
                                    </ul>
                                </nav>
                            </footer>

                            <?php comments_template(); ?>

                        </main> <!-- #main -->

                    </div><!-- col -->
                <?php  // end nouvelles format (1-10-1 cols)
                endif; ?>
                <?php get_sidebar(); ?>
            </div><!-- row -->

        </div><!-- #primary -->
    </div><!-- #content -->
<?php get_footer(); ?>