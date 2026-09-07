<?php

/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Bootscore
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/rhizome_favicon_180x180.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/rhizome_favicon_32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/rhizome_favicon_16x16.png">
    <link rel="manifest" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon/rhizome_favicon.svg" color="#0d6efd">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <?php wp_head(); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js" integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew==" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/masonry/4.2.2/masonry.pkgd.min.js" integrity="sha512-JRlcvSZAXT8+5SQQAvklXGJuxXTouyq8oIMaYERZQasB8SBDHZaUbeASsJWpk0UUrf89DP3/aefPPrlMR1h1yQ==" crossorigin="anonymous"></script>
    <!--link rel="stylesheet" href="http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" /-->
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div id="to-top"></div>

<div id="page" class="site">

    <?php
    //$img = get_field("background_categorie", $background->ID);
    //https://stackoverflow.com/questions/34088234/how-to-display-acf-image-from-image-array
    //source: https://www.advancedcustomfields.com/resources/adding-fields-taxonomy-term/
    // get the current taxonomy term
    $term = get_queried_object();

    // vars
    $image = get_field('background_categorie', $term);
    ?>
    <div class="backgroundimage"
         style="background-image: url(<?php echo $image['url']; ?>);"></div>
    <!-- #background -->
    <?php bootscore_ie_alert(); ?>
    <!-- Theme Navigation -->
    <!--<a id="skip-to-content-link" class="skip-to-content-link" href="#main" tabindex="0">
        Aller au contenu
    </a>-->
    <header class="header-area entete" tabindex="-1">
        <div class="header-top-action">
            <div><!-- container -->
                <div>
                    <div>
                        <div class="top-action-content info-action-content">
                            <div class="info-box info-box-2 d-flex align-items-center space-between">
                                <div class="barreRecherche-table"><?php get_search_form(); ?></div>
                                <div class="d-flex align-items-center">
                                    <ul class="top-action-list d-flex align-items-center nav">
                                        <!-- DESKTOP MENU SEC START -->

                                        <!-- Bootstrap 5 Nav Walker Main Menu -->
                                        <?php
                                        wp_nav_menu(array(
                                            'theme_location' => 'footer-menu',
                                            'container' => false,
                                            'menu_class' => '',
                                            'fallback_cb' => '__return_false',
                                            'items_wrap' => '%3$s',
                                            'depth' => 2,
                                            'walker' => new bootstrap_5_wp_nav_menu_walker()
                                        ));
                                        ?>
                                        <!-- Bootstrap 5 Nav Walker Main Menu End -->
                                        <!-- DESKTOP MENU SEC END-->
                                        <!-- top social links added-->
                                        <li><a href="https://twitter.com/p_rhizome" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="https://www.facebook.com/productionsrhizome" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://www.instagram.com/productions_rhizome/" target="_blank"><i class="fa fa-instagram"></i></a></li>
                                        <li><a href="https://www.youtube.com/channel/UCwgD6IjLpfTg0QEoOB-4AZg" target="_blank"><i class="fa fa-youtube"></i></a></li>
                                        <!--<li><a href="/recherche" target="_blank"><i class="fa fa-search"></i></a></li>-->
                                        <!-- language added-->
                                        <!-- outputs a flags list (without languages names) -->
                                        <?php //pll_the_languages( array( 'display_names_as' => 'slug','show_names' => 1 ) ); ?>
                                        <script>
                                            let i = 0;
                                            function message(){
                                                if(i===0) {
                                                    i = i + 1;
                                                    alert("Please note that the english version of this website has been translated by Google Translate. For more info, please contact us at info@productionsrhizome.org.");
                                                }
                                            }
                                        </script>
                                    </ul>
                                    <div class="d-flex" onclick="message()"><?php echo do_shortcode('[gtranslate]'); ?></div>
                                </div>
                                <!-- CLOSING THE GLOBAL UL top-action-list d-flex align-items-center nav-->
                            </div><!-- end info-box box-2 -->
                        </div><!-- top-action-content -->
                    </div><!-- end col-lg-6 -->
                </div><!-- end row -->
            </div><!-- end container -->
        </div><!-- end header-top-action -->
        <!-- inverted header-action, rem class fixed-top  -->
        <div class="header-top header-menu-action " style="background-color: white; opacity: 1.00;">
            <div class=""><!-- container -->
                <div class="row ostion-top-wrap">
                    <div class="col-lg-5 col-sm-5 site-branding">
                        <div class="logo-action d-flex align-items-center">
                            <div class="ostion-logo">
                                <a href="https://productionsrhizome.org">
                                    <!--img src="/wp-content/themes/bootscore-child-main/img/logo/logo.svg" alt="Rhizome" title="Rhizome" style="height: 60px;"-->
                                    <!--<img src="/wp-content/uploads/Logo-Rhizome-NOIR-grand.png" alt="Rhizome" title="Rhizome">-->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         width="auto" height="100px" viewBox="0 0 1794.000000 1128.000000"
                                         preserveAspectRatio="xMidYMid meet">

                                        <g transform="translate(0.000000,1128.000000) scale(0.100000,-0.100000)"
                                           fill="#000000" stroke="none">
                                            <path d="M3522 11261 c-109 -40 -200 -130 -244 -241 l-22 -55 -7 -1505 c-4
-828 -10 -2141 -13 -2917 l-7 -1413 415 0 415 0 4 1293 c3 1171 5 1301 20
1387 89 486 352 743 787 767 427 23 728 -165 848 -530 72 -218 79 -301 92
-1087 6 -360 15 -709 20 -774 30 -351 152 -624 372 -832 177 -167 391 -268
663 -311 124 -19 431 -13 540 12 249 56 455 166 613 327 165 168 267 368 324
635 l23 108 3 1558 3 1557 -395 0 -395 0 -3 -1502 c-4 -1669 1 -1546 -70
-1679 -169 -319 -675 -310 -832 14 -56 115 -56 118 -56 802 0 952 -19 1168
-131 1508 -169 516 -519 826 -1044 925 -133 25 -507 24 -625 -2 -210 -45 -416
-133 -552 -236 -36 -28 -98 -84 -137 -126 l-71 -76 -2 1059 -3 1058 -32 67
c-40 85 -106 151 -191 191 -58 28 -79 32 -162 35 -74 2 -107 -2 -148 -17z"/>
                                            <path d="M7849 10671 c-90 -29 -156 -71 -222 -141 -242 -257 -170 -655 148
-811 282 -138 622 8 715 309 22 71 26 197 9 272 -38 166 -181 319 -347 370
-83 26 -224 26 -303 1z"/>
                                            <path d="M1840 9324 c-14 -2 -59 -9 -100 -15 -263 -38 -532 -189 -702 -392
l-48 -58 0 190 0 191 -415 0 -415 0 0 -2055 0 -2055 415 0 415 0 0 1158 c1
1164 6 1392 35 1538 48 239 141 415 283 537 70 60 148 100 257 133 85 26 101
28 295 28 223 1 309 -12 419 -61 52 -23 56 -23 72 -6 9 10 105 128 214 263
109 135 216 266 237 292 l38 46 -82 50 c-183 110 -329 168 -492 197 -87 15
-376 28 -426 19z"/>
                                            <path d="M15930 4320 c-218 -27 -445 -92 -616 -178 -318 -160 -571 -417 -738
-751 -292 -583 -338 -1448 -111 -2112 49 -143 160 -364 243 -484 342 -492 847
-755 1452 -755 441 0 844 111 1155 318 122 81 277 211 273 228 -2 8 -101 135
-221 284 l-218 269 -77 -53 c-332 -230 -590 -315 -919 -304 -279 10 -486 100
-669 289 -176 183 -281 425 -339 781 l-6 38 1388 2 1388 3 9 65 c16 122 12
553 -7 690 -96 691 -398 1193 -882 1469 -154 88 -327 148 -540 186 -106 20
-451 28 -565 15z m391 -746 c350 -60 587 -270 703 -624 30 -93 53 -203 71
-337 l5 -43 -970 0 c-917 0 -970 1 -970 18 0 9 7 56 15 105 87 494 353 811
742 881 107 19 290 19 404 0z"/>
                                            <path d="M5288 4279 c-349 -31 -681 -159 -943 -365 -288 -225 -497 -533 -624
-920 -93 -281 -126 -514 -125 -869 1 -271 18 -430 70 -644 189 -772 715 -1309
1420 -1452 136 -27 454 -38 602 -19 389 48 733 202 1006 452 331 302 541 734
624 1281 22 145 24 655 4 792 -82 542 -274 943 -607 1263 -373 359 -881 530
-1427 481z m402 -799 c161 -40 347 -145 452 -258 236 -252 358 -623 358 -1084
-1 -302 -57 -555 -178 -791 -208 -411 -559 -616 -967 -567 -456 55 -768 382
-879 922 -71 344 -53 770 45 1071 135 417 400 666 774 728 84 13 300 2 395
-21z"/>
                                            <path d="M9647 4279 c-307 -34 -596 -178 -770 -383 l-57 -67 0 185 0 186 -420
0 -420 0 0 -2060 0 -2060 420 0 419 0 4 1343 3 1342 22 97 c47 207 112 345
213 453 142 151 309 215 563 215 459 0 685 -211 773 -722 16 -94 17 -217 20
-1415 l4 -1313 419 0 420 0 0 1298 c0 1385 0 1386 50 1577 12 44 44 127 72
185 44 91 62 116 137 191 93 92 177 141 301 176 93 26 396 26 490 0 254 -69
401 -224 485 -511 60 -208 59 -180 62 -1608 l4 -1308 415 0 415 0 -4 1372 c-3
1501 -1 1439 -63 1718 -69 305 -191 557 -360 739 -164 177 -408 301 -694 353
-152 27 -481 30 -643 4 -373 -59 -653 -218 -914 -519 l-50 -57 -36 57 c-169
274 -464 460 -821 518 -117 19 -349 26 -459 14z"/>
                                            <path d="M160 3845 l0 -355 813 -2 c446 -2 925 -3 1064 -3 l251 0 -16 -21
c-10 -12 -89 -106 -178 -210 -88 -104 -595 -710 -1127 -1346 l-967 -1157 0
-336 0 -335 1625 0 1625 0 -2 357 -3 358 -1123 5 -1123 5 107 125 c59 69 173
206 253 304 80 99 538 659 1018 1245 l872 1066 1 328 0 327 -1545 0 -1545 0 0
-355z"/>
                                        </g>
                                    </svg>
                                </a>
                            </div><!-- .ostion-logo -->
                        </div><!-- end logo-action -->
                    </div><!-- site-branding -->
                    <div class="col-lg-7 col-sm-7 ostion-menu">
                        <div class="ostion-menu-innner">
                            <div class="ostion-menu-content">
                                <div class="navigation-top">
                                    <nav class="main-navigation">
                                        <!-- DESKTOP MAIN MENU START -->
                                        <div class="walkermenu">
                                            <!-- Bootstrap 5 Nav Walker Main Menu -->
                                            <?php
                                            wp_nav_menu(array(
                                                'theme_location' => 'main-menu',
                                                'container' => false,
                                                'menu_class' => '',
                                                'fallback_cb' => '__return_false',
                                                'items_wrap' => '<ul id="bootscore-navbar" class="navbar-nav ms-auto %2$s">%3$s</ul>',
                                                'depth' => 2,
                                                'walker' => new bootstrap_5_wp_nav_menu_walker()
                                            ));
                                            ?>
                                            <!-- Bootstrap 5 Nav Walker Main Menu End -->
                                        </div>
                                        <!-- DESKTOP MAIN MENU END-->
                                    </nav><!-- end main-navigation -->
                                </div><!-- end navigation-top -->
                            </div><!-- end ostion-menu-content -->
                            <div class="mobile-menu-toggle">
                                <i class="fa fa-bars"></i>
                            </div>
                        </div><!-- end ostion-menu-innner -->
                    </div><!-- ostion-menu -->
                </div><!-- end row -->
            </div><!-- end container -->
        </div><!-- end header-top -->
        <!-- start side-nav-container -->
        <div class="side-nav-container">
            <div class="humburger-menu">
                <div class="humburger-menu-lines side-menu-close">
                </div><!-- end humburger-menu-lines -->
            </div><!-- end humburger-menu -->
            <!-- MOBILE MENU MAIN START -->
            <div class="side-menu-wrap">
                <!-- Bootstrap 5 Nav Walker Main Menu -->
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'main-menu',
                    'container' => false,
                    'menu_class' => '',
                    'fallback_cb' => '__return_false',
                    'items_wrap' => '<ul id="mobile-menu-main" class="side-menu-ul %2$s">%3$s</ul>',
                    'depth' => 2,
                    'walker' => new bootstrap_5_wp_nav_menu_walker()
                ));
                ?>
                <!-- Bootstrap 5 Nav Walker Main Menu End -->
            </div>
            <!-- MOBILE MENU MAIN END-->
            <!-- MOBILE MENU SEC START -->
            <div class="side-menu-wrap">
                <!-- Bootstrap 5 Nav Walker Main Menu -->
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'footer-menu',
                    'container' => false,
                    'menu_class' => '',
                    'fallback_cb' => '__return_false',
                    'items_wrap' => '<ul id="mobile-menu-sec" class="side-menu-ul %2$s">%3$s</ul>',
                    'depth' => 2,
                    'walker' => new bootstrap_5_wp_nav_menu_walker()
                ));
                ?>
                <!-- Bootstrap 5 Nav Walker Main Menu End -->
            </div>
            <!-- MOBILE MENU SEC END-->


            <div class="side-menu-wrap">
                <ul class="side-menu-ul">
                    <!-- loupe -->
                    <!--<li><a href="/recherche"><i class="fa fa-search"></i></a></li>-->
                    <!-- Language added - Outputs a flags list in <li> (without languages names) -->
                    <!--				--><?php //pll_the_languages( array( 'display_names_as' => 'slug','show_names' => 1 ) ); ?>
                </ul>
                <script>
                    let n = 0;
                    function messageMobile(){
                        if(n===0) {
                            n = n + 1;
                            alert("Please note that the english version of this website has been translated by Google Translate. For more info, please contact us at info@productionsrhizome.org.");
                        }
                    }
                </script>
                <div onclick="messageMobile()"><div class="d-flex"><?php echo do_shortcode('[gtranslate]'); ?></div></div>
                <!--ul class="side-menu-ul">

                    <li class="sidenav__item"><a href="/fr/communaute/">Communauté</a></li>
                    <li class="sidenav__item"><a href="/fr/productions/">Productions</a></li>
                    <li class="sidenav__item"><a href="/fr/livres/">Livres</a></li>
                    <li class="sidenav__item"><a href="/fr/contact/">Contact</a></li>

                </ul-->
                <ul class="side-social">
                    <li><a href="https://x24.li/rhtw1"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="https://x24.li/rhfb1"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="https://x24.li/rhin1"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="https://x24.li/rhyt1"><i class="fa fa-youtube"></i></a></li>
                </ul>
            </div><!-- end side-menu-wrap -->
        </div>
        <!--end side-nav-container -->
        <div class="entete-barreRecherche barreRecherche-mobile"><?php get_search_form(); ?></div>
    </header>