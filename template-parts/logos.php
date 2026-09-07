<div class="logos-wrapper">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
	<div id="logos" class="logosclass">
<?php
$args  = array(
    'posts_per_page'  => 100,
 'category__in' => array( 312 ),
    'orderby'         => 'post_date',
    'order'           => 'DESC',
    'post_type'       => 'post',
    'post_status'     => 'publish' ); 
$posts = get_posts($args);
    foreach ($posts as $post) :
    ?>
			<div class="logo-item">
          <?php 
//LINK FROM ACF

$link = get_field('lien_externe');
if( $link ): ?>
    <a class="logo-lien" target="_blank" href="<?php echo esc_url( $link ); ?>">
<?php endif; 
		
				//IMAGE DE THUMBNAIL (GUTENBERG)
			$title_attribute = get_the_title();
			echo the_post_thumbnail('thumb', 
									   array(
        'alt'   => $title_attribute, 
        'title' => $title_attribute 
    ));
			   //echo the_title("<div class='logos-title'>","</div>");
          ?>
		<?php // fermer la balise du lien ACF qui est appliqué à l'image thumbnail
		if( $link ): ?></a>
				<?php endif; ?>		
</div>
    <?php endforeach; ?>	
</div>
</div>
</div>
</div>
</div>
<script>
	//console.log('script');
	//OPTIONS SLICK POUR CAROUSEL LOGO ACCUEIL 
jQuery(document).ready(function(){
jQuery('#logos').slick({
	//		console.log('slick');
	//		speed inactive
	  autoplay: true,
	  arrows: true,
	  dots: false,
 	 infinite: true,
 	 autoplaySpeed: 3000,
 	 cssEase: 'linear',
	  initialSlide: 0,
	  draggable: true,
  speed: 300,
  slidesToShow: 3,
  slidesToScroll: 1,
  responsive: [
    {
      breakpoint: 1024,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,
        dots: true
      }
    },
    {
      breakpoint: 600,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
});
});	
</script>
