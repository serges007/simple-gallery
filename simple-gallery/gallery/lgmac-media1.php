<?php

//=========================================================================
//==============   custom post type lgmac_media 
//=========================================================================

//Initialize custom post type lgmac-media
function lgmac_media_init() {
    $labels = array(
        'name' => 'Gallery',
        'singular_name' => 'Photo',
        'add_new' => 'Ajouter un élément',
        'add_new_item' => 'Ajouter un élément de la Gallery',
        'edit_item' => 'Modifier un élément de Gallery',
        'new_item' => 'Nouveau media',
        'all_items' => 'Voir la liste',
        'view_item' => 'Voir l\'élément',
        'search_item' => 'Chercher un media',
        'not_found' => 'Aucun élément trouvé',
        'not_found_in_trash' => 'Aucun media dans la corbeille',
        'menu_name' => 'Gallery'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'publicly_queryable' => true,
        'query_var' =>true,
        'rewrite' => array('slug' => 'gallery'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' =>  get_stylesheet_directory_uri() . '/assets/disc_16.png',
        'exclude_from_search' => false,
        'supports' => array('title', 'editor', 'thumbnail')
    );    
    register_post_type('lgmac_media', $args);
}//end function lgmac_media_init

add_action('init', 'lgmac_media_init');


//=========================================================================
//==============   meta boxes pour custom post type lgmac_media 
//=========================================================================
function lgmac_media_register_meta_box()  {
    add_meta_box('lgmac_media_meta','Références de la gallery','lgmac_media_meta_building', 'lgmac_media', 'normal', 'high');	
}  // end function  lgmac_media_register_meta_box

function lgmac_media_meta_building($post) {
    $lgmac_meta_an      = get_post_meta($post->ID, '_media_meta_an',      true);
    $lgmac_meta_editeur = get_post_meta($post->ID, '_media_meta_editeur', true);

    wp_nonce_field('lgmac_media_meta_box_saving', 'lgmac_25896');

    $lgmac_years = array();
    $lgmac_years[0] = 'compil';
    for($i=1970; $i<2019; $i++) { $lgmac_years[] = $i; }

    echo '<div>';
    echo '<p><label for="media_detail_an"> Année -&gt;&nbsp;</label>';
    echo '<select id="media_detail_an" name="media_detail_an">';
            foreach($lgmac_years as $lgmac_year):
                    echo '<option value="' . $lgmac_year . '"' . selected($lgmac_meta_an, $lgmac_year, false). '>' . $lgmac_year . '</option>';
            endforeach;
    echo '</select></p>';

    echo '<p><label for="media_detail_editeur">&Eacute;diteur -&gt;&nbsp;</label>';
    echo '<input type="text" size="30" value="'.$lgmac_meta_editeur.'"  id="media_detail_editeur" name="media_detail_editeur"></p>';

    echo '</div>';	
}  // end function  mv_media_meta_building

//---------------Action hook to initialize the meta boxes for lgmac_media custom post type
add_action ('add_meta_boxes', 'lgmac_media_register_meta_box');


//=========================================================================
//==============   sauvegarde meta boxes pour custom post type lgmac_media 
//=========================================================================

function lgmac_media_save_meta_box($post_id) {

	if ( get_post_type( $post_id ) == 'lgmac_media' && isset( $_POST['media_detail_an'] ) ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {  return;  }
		check_admin_referer('lgmac_media_meta_box_saving', 'lgmac_25896');
		update_post_meta($post_id, '_media_meta_an',       sanitize_text_field($_POST['media_detail_an']));
		update_post_meta($post_id, '_media_meta_editeur',  sanitize_text_field($_POST['media_detail_editeur']));

	}

}  // end function lgmac_media_save_meta_box


//======================================================================================
//=====  ajout de l'image et année et éditeur dans la colonne admin pour le lgmac_media
//======================================================================================
add_filter('manage_edit-lgmac_media_columns', 'lgmac_col_change2');  // change nom colonnes

function lgmac_col_change2($columns)  {  
    $columns['lgmac_media_annee']   = "Année"; 
    $columns['lgmac_media_editeur'] = "Éditeur";   
    $columns['lgmac_media_style']   = "Style";   
    $columns['lgmac_media_image']   = "Image affichée";   

    return $columns;	
}

add_action('manage_lgmac_media_posts_custom_column', 'lgmac_content_show2', 10 ,2);  // affiche contenu

function lgmac_content_show2($column, $post_id)  { 
    global $post; 

    if ( $column == 'lgmac_media_image' ) {
        echo the_post_thumbnail(array(100,100));	
    }

    if ( $column == 'lgmac_media_editeur' ) {
        $lgmac_meta_editeur    = get_post_meta($post_id, '_media_meta_editeur',      true);
        echo $lgmac_meta_editeur;	
    }

    if ( $column == 'lgmac_media_style' ) {
        $my_style = wp_get_post_terms( $post_id, 'genre_mus');
        echo $my_style[0]->name;	
    }

    if ( $column == 'lgmac_media_annee' ) {
        $lgmac_meta_annee    = get_post_meta($post_id, '_media_meta_an',      true);
        echo $lgmac_meta_annee;	
    }
}


// ----------Action hook to save meta-box data when the post is saved
add_action('save_post','lgmac_media_save_meta_box');
