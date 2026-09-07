<?php

/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Bootscore
 */

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="card horizontal mb-4">
    <div class="row">
      <!-- Featured Image-->
          <?php if (has_post_thumbnail()) : ?>
              <div class='card-img-left-md recherche-image'><a href="<?php the_permalink() ?>"><?php the_post_thumbnail(); ?></a></div>
          <?php else : ?>
              <div class='card-img-left-md recherche-image'><a href="<?php the_permalink() ?>"><img src='/wp-content/uploads/logo_recherche.png' class='rounded wp-post-image' alt='Logo Rhizome'></a></div>
          <?php endif; ?>
      <div class="col recherche-carte">
        <div class="card-body recherche-carte-contenu">

          <?php //bootscore_category_badge(); ?>

          <!-- Title -->
          <h2 class="blog-post-title blog__title">
            <a href="<?php the_permalink(); ?>">
              <?php the_title(); ?>
            </a>
          </h2>
          <!-- Meta -->
          <?php if ('post' === get_post_type()) : ?>
            <small class="text-muted mb-2">
              <?php
              //bootscore_date();
              //bootscore_author();
              //bootscore_comments();
              //bootscore_edit();
              ?>
            </small>
          <?php endif; ?>
          <!-- Excerpt & Read more -->
          <div class="card-text mt-auto">
              <?php the_field('soustitre'); ?>
          </div>
          <!-- Tags -->
          <?php //bootscore_tags(); ?>
        </div>
      </div>
    </div>
  </div>
</article>
<!-- #post-<?php the_ID(); ?> -->