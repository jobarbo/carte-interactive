<?php

/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bootscore
 */

?>
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init();
</script>
<footer>
	<div class="footer-couleurs">
		<div class="footer-couleurs-couleur footer-couleurs-1"></div>
		<div class="footer-couleurs-couleur footer-couleurs-2"></div>
		<div class="footer-couleurs-couleur footer-couleurs-3"></div>
		<div class="footer-couleurs-couleur footer-couleurs-4"></div>
		<div class="footer-couleurs-couleur footer-couleurs-5"></div>
		<div class="footer-couleurs-couleur footer-couleurs-6"></div>
		<div class="footer-couleurs-couleur footer-couleurs-7"></div>
		<div class="footer-couleurs-couleur footer-couleurs-8"></div>
		<div class="footer-couleurs-couleur footer-couleurs-9"></div>
		<div class="footer-couleurs-couleur footer-couleurs-10"></div>
		<div class="footer-couleurs-couleur footer-couleurs-11"></div>
	</div>
<section class="footer-area">
    <div class="newsletter-area">
        <div class="container">

        </div><!-- end container -->
    </div><!-- end newsletter-area -->
    <div class="footer-top">
        <div class="container">
            <div class="row footer-widget-wrap">

                <div class="col footer-item">
                    <h3 class="widget__title">Productions Rhizome</h3>
                    <div class="contact__info">
                        <p>870, avenue de Salaberry, bureau 104<br>Québec QC G1R 2T9 Canada<br><a href="mailto:info@productionsrhizome.org" style="text-transform: none;">info@productionsrhizome.org</a><br><a href="tel:+1-418-525-0305">418-525-0305</a></p>
                    </div>
                </div>
                <div class="col footer-item footer-item4">
                    <div class="footer__social">
                        <ul>
                                <li><a href="https://www.facebook.com/productionsrhizome" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                <li><a href="https://www.instagram.com/productions_rhizome/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                <li><a href="https://www.youtube.com/channel/UCwgD6IjLpfTg0QEoOB-4AZg" target="_blank"><i class="fa fa-youtube"></i></a></li>

                        </ul>
                        <br>
                        <a href="https://infolettre.productionsrhizome.org/infolettre" style="text-transform: none;"><i class="fa fa-newspaper-o	"></i>&nbsp; S'abonner à l'infolettre</a>
                    </div>
                </div><!-- end footer-item -->
            </div><!-- end row -->
        </div><!-- end container -->
    </div><!-- end footer-top -->
    <div class="footer-copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="copyright-desc">
                        <p>© 2013-<?php echo Date('Y'); ?> <a href="/">PRODUCTIONS RHIZOME</a></p>
                    </div>
                </div><!-- end col-lg-12 -->
            </div><!-- end row -->
        </div><!-- end container -->
    </div><!-- end footer-copyright -->
</section>	
	<!-- Default Theme Footer 1 -->
  <div class="bootscore-footer pt-5 pb-3">
    <div class="container">

      <!-- Top Footer Widget -->
      <?php if (is_active_sidebar('top-footer')) : ?>
        <div>
          <?php dynamic_sidebar('top footer'); ?>
        </div>
      <?php endif; ?>

      <div class="row">

        <!-- Footer 1 Widget -->
        <div class="col-md-6 col-lg-3">
          <?php if (is_active_sidebar('footer-1')) : ?>
            <div>
              <?php dynamic_sidebar('footer-1'); ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Footer 2 Widget -->
        <div class="col-md-6 col-lg-3">
          <?php if (is_active_sidebar('footer-2')) : ?>
            <div>
              <?php dynamic_sidebar('footer-2'); ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Footer 3 Widget -->
        <div class="col-md-6 col-lg-3">
          <?php if (is_active_sidebar('footer-3')) : ?>
            <div>
              <?php dynamic_sidebar('footer-3'); ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Footer 4 Widget -->
        <div class="col-md-6 col-lg-3">
          <?php if (is_active_sidebar('footer-4')) : ?>
            <div>
              <?php dynamic_sidebar('footer-4'); ?>
            </div>
          <?php endif; ?>
        </div>
        <!-- Footer Widgets End -->

      </div>

      <!-- Bootstrap 5 Nav Walker Footer Menu -->
      <?php
      wp_nav_menu(array(
        'theme_location' => 'footer-menu',
        'container' => false,
        'menu_class' => '',
        'fallback_cb' => '__return_false',
        'items_wrap' => '<ul id="footer-menu" class="nav %2$s">%3$s</ul>',
        'depth' => 1,
        'walker' => new bootstrap_5_wp_nav_menu_walker()
      ));
      ?>
      <!-- Bootstrap 5 Nav Walker Footer Menu End -->

    </div>
  </div>
	<!-- Rem Default Theme Footer 2 (copyright) -->
</footer>
<!-- Default Theme Back to top -->
<div class="top-button position-fixed zi-1020">
  <a href="#to-top" class="btn btn-primary shadow"><i class="fas fa-chevron-up"></i></a>
</div>
<!-- Original Back to top -->
<div id="back-to-top" class="back-btn-shown">
    <i class="fa fa-angle-up" title="haut"></i>
</div>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>