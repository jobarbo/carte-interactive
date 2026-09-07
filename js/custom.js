jQuery(function ($) {
	// Test JQuery
    //console.log("jquery ok");
	
	// 1- Portfolios - Production avec sous-pages: Accordéon
	// Utilise la fonction accordion par defaut de JQuery
	jQuery( "#accordeon" ).accordion({
		collapsible: true,
		heightStyle: "content",
		icons: { "header": "ui-icon-plus", "activeHeader": "ui-icon-minus" }
    });
	
	jQuery( "#accordeon-private" ).accordion({
		collapsible: true,
		heightStyle: "content",
		active: false,
		icons: { "header": "ui-icon-plus", "activeHeader": "ui-icon-minus" }
    });
	
	// Getter
var icons = $( ".selector" ).accordion( "option", "icons" );
 
// Setter
$( ".selector" ).accordion( "option", "icons", { "header": "ui-icon-plus", "activeHeader": "ui-icon-minus" } );
	
	// 2- Menus : 
    //sous 1200px, le bouton Hambourger ouvre et ferme un menu tiroir de droite
    jQuery(".mobile-menu-toggle").click(function(){
      jQuery(".side-nav-container").toggleClass("active");
    });
    jQuery(".side-menu-close").click(function(){
      jQuery(".side-nav-container").toggleClass("active");
    });
	/* FERMÉ - sous toutes tailles, le lien A propos ouvre un 2e menu tiroir de droite.
    jQuery(".about-menu-toggle").click(function(){
      jQuery(".side-nav-container2").toggleClass("active");
    });*/

	// 3- Articles: RANDOM COLOR
	// Sur single post seulement (Exclus les categories), mais inclus les pages
	// chaque couleur est une classe, qui réfère à un code Hexa dans le CSS.
	// Si pas un livre, utiliser une couleur random
    var colors = ['jaune', 'bleu', 'rose'];
	var random_color = colors[Math.floor(Math.random() * colors.length)];
	//console.log(random_color);
	jQuery('.single-post #content.not_book').addClass(random_color);
	
	
    // 4- MASONRY FILTRE PAR CATEGORIE	
    //source: https://codepen.io/alexpetergill/pen/EWWojp
	
	const $menu = $('.filtering')

	const onMouseUp = e => {
	 if (!$menu.is(e.target)
	   || $menu.has(e.target).length === 0)
	   {
		 $('.dropdown-menu').removeClass('show');
		 $('.dropdown-toggle').removeClass('active');
	  }
	}

	$('.nav-item').on('click', () => {
	  $('.dropdown-menu','.series').toggleClass('show').promise().done(() => {
		if ($('.dropdown-menu').hasClass('show')) {
		  $(document).on('mouseup', onMouseUp)
		} else {
		  $(document).off('mouseup', onMouseUp)
		}
	  })
	})
	
	//source: https://www.bootdey.com/snippets/view/portfolio-with-category-filter-and-masonry
    //demo: https://www.bootdey.com/snippets/preview/portfolio-with-category-filter-and-masonry?full-screen=true
	
	$(".grid").masonry({ itemSelector: ".grid-item" });    
	//$(".grid").isotope({ itemSelector: ".grid-item" });
    
    $(".filtering").on("click", "a", function () {
        var a = $(".gallery").isotope({});
        var e = $(this).attr("data-filter");
        a.isotope({ filter: e });
    });
    $(".filtering").on("click", "a", function () {
		$(this).closest('.filtering').find('a').removeClass("active");	
		$(this).addClass("active");
		
    });

	
	// 4B - Actualités, Portfolio Filtres Masonry : sousfiltres
	// le lien dropdown-menu ouvre et ferme le tiroir dessous
	// Articles Communauté
    jQuery(".category-2 .filtering .dropdown-toggle").click(function(){
      jQuery(".category-2 .filtering .dropdown-menu").toggleClass("show");
    });
	// Articles Portfolios
	// Bonification : remplacer par this.()
	// actuellement series, types, annees ont un dropdown
	// forcer la fermeture des autres catégories
	jQuery(".dropdown-toggle.series").click(function(){
      jQuery(".dropdown-menu.series").toggleClass("show");
	  jQuery(".dropdown-menu.types").removeClass("show");
	  jQuery(".dropdown-menu.annees").removeClass("show");
	});

	$('#filtrePortfolioSeries').keypress(function (e) {
		var key = e.which;
		if(key == 13) {
			jQuery(".dropdown-menu.series").toggleClass("show");
			jQuery(".dropdown-menu.types").removeClass("show");
			jQuery(".dropdown-menu.annees").removeClass("show");
		}
	});

	jQuery(".dropdown-toggle.types").click(function(){
      jQuery(".dropdown-menu.types").toggleClass("show");
	  jQuery(".dropdown-menu.series").removeClass("show");
	  jQuery(".dropdown-menu.annees").removeClass("show");
	});

	$('#filtrePortfolioTypes').keypress(function (e) {
		var key = e.which;
		if(key == 13) {
			jQuery(".dropdown-menu.types").toggleClass("show");
			jQuery(".dropdown-menu.series").removeClass("show");
			jQuery(".dropdown-menu.annees").removeClass("show");
		}
	});

	jQuery(".dropdown-toggle.annees").click(function(){
      jQuery(".dropdown-menu.annees").toggleClass("show");
	  jQuery(".dropdown-menu.series").removeClass("show");
	  jQuery(".dropdown-menu.types").removeClass("show");
	});

	$('#filtrePortfolioAnnees').keypress(function (e) {
		var key = e.which;
		if(key == 13) {
			jQuery(".dropdown-menu.annees").toggleClass("show");
			jQuery(".dropdown-menu.series").removeClass("show");
			jQuery(".dropdown-menu.types").removeClass("show");
		}
	});

	jQuery(".dropdown-toggle.series-numerique").click(function(){
		jQuery(".dropdown-menu.series-numerique").toggleClass("show");
	});

	$('#filtreNumeriqueSeries').keypress(function (e) {
		var key = e.which;
		if(key == 13) {
			jQuery(".dropdown-menu.series").toggleClass("show");
		}
	});
	
}); // jQuery End

/*let lettres = document.getElementsByClassName("artistes-lettres-lettre");
let artistes = document.getElementsByClassName("blog-post-title");
let nomArtistes = document.getElementsByClassName("nom-artiste");

for(let i=0; i<lettres.length; i++){
	//jQuery(lettres).click(filtrer);
	lettres[i].addEventListener("click",filtrer);
}*/

/*function filtrer(e) {
	//console.log(e.target.id);
	for(let i=0; i<lettres.length; i++){
		lettres[i].classList.remove("artisteChoisi");
	}

	if(e.target.classList.contains("artisteChoisi")){
		e.target.classList.remove("artisteChoisi");
	} else {
		e.target.classList.add("artisteChoisi");
	}

	for(let i=0; i<nomArtistes.length; i++){
		if((e.target.id === "E" && nomArtistes[i].id.indexOf("É")!=-1) || (e.target.id === "E" && nomArtistes[i].id.indexOf("E")!=-1)) {
			artistes[i].classList.remove("cacherArtiste");
		} else if(nomArtistes[i].id.indexOf(e.target.id)===-1){
			artistes[i].classList.add("cacherArtiste");
		} else {
			artistes[i].classList.remove("cacherArtiste");
		}
	}
}*/


