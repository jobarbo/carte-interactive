<?php
/*
	 * Template Post Type: post
	 */

get_header();  ?>
<!-- soustitre -->
<?php 
$img = get_field("background", $background->ID);
//https://stackoverflow.com/questions/34088234/how-to-display-acf-image-from-image-array 
//
//Seulement pour la catégorie Livre ne pas utiliser l'image random
//
	
if(has_category(array('livres', 'books'))) {
	$is_book = "is_book";
} else {
	$is_book = "not_book";	
}
//
?>
<script>
//	console.log("single");
</script>
<div class="backgroundimage" style="background-image: url(<?php print $img["url"] ?>);"></div><!-- #background -->
<div class="breadcrumb-area header-area ">
	<header class="entry-header container">
		<?php the_title('<h1 class="">', '</h1>'); ?>
        <?php if (the_field('soustitre')!=""){
            echo "<div class='productionslist-title2'>".the_field('soustitre')."</div>";
        }
        ?>
	</header>
</div>     
<div id="content" class="site-content is_single <?php print $is_book; ?>">
  <div id="primary" class="causes-detail-area content-area container">

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
		            <?php if( get_field('code_shopify') ): ?>
		  				
		  				<div class="code_shopify">
							<?php 
								$code_shopify = get_field('code_shopify');
								echo $code_shopify;
							?>
		  				</div>
                    <?php endif; ?>
		</div>	
		<?php  else: ?>
	      <div class="col-sm-1"></div>	
      <div class="col-sm-10">

        <main id="main" class="site-main">

<!-- header removed -->

          <div class="entry-content">
            
			<?php //bootscore_category_badge(); ?>
            <?php the_content(); ?>
              <?php $relations = get_field('relations_artistes_realisations'); ?>
              <?php if($relations): ?>
                  <br>
                  <p style="font-size: 18px"><strong>Artistes :</strong></p>
                  <ul>
                      <?php foreach ($relations as $relation):?>
                          <li><a href="<?php echo $relation->guid ?>"><?php echo $relation->post_title ?></a></li>
                      <?php endforeach; ?>
                  </ul>
              <?php endif; ?>
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