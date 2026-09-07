<?php
/*1. Choisir une citation au hasard*/
$args = array(
    'post_type'   => 'citations',
    'posts_per_page' => -1,
);
$citations = get_posts( $args );
$nombreAleatoire = rand(0,count($citations)-1);

/*2. Mettre la citation et l'auteur dans des variables*/
$citationTexte = $citations[$nombreAleatoire]->post_title;
$citationAuteur = get_field('auteur_citation',$citations[$nombreAleatoire]->ID);
?>

<div class="banniere-wrapper">
    <div id="banniere" class="banniereclass">
        <div class="banniere-blocCitation">
            <p class="banniere-blocCitation-citation">
                <span class="notranslate">«</span>&#8239;<?php echo $citationTexte?>&#8239;<span class="notranslate">»</span>
            </p>
            <p class="banniere-blocCitation-auteur notranslate">
                - <?php echo $citationAuteur;?>
            </p>
        </div>
    </div>
</div>
