<?php

class ExVoiture_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct( 'ex_formulaire', 'Formulaire voiture', array( 'description' => "Formulaire d'ajout de voiture'." ) );
    }

    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        echo $args['before_title'];
        echo $instance['title'];
        echo $args['after_title'];
        echo $args['after_widget'];
        echo('<form action="" method="POST"><p>
<label for="my-formulaire-name">Votre Nom :</label><input type="text" name="name" id="my-formulaire-name">
<label for="my-formulaire-brand">La marque de voitre voiture :</label><input type="text" name="brand" id="my-formulaire-brand">
<label for="my-formulaire-model">Votre model :</label><input type="text" name="model" id="my-formulaire-model">
<label for="my-formulaire-year">Votre year :</label><input type="number" name="year" id="my-formulaire-year">
<label for="my-formulaire-plaque">Votre plaque d\'imatriculation :</label><input type="text" name="plaque" id="my-formulaire-plaque">


</p><input type="submit" value="S\'inscrire"></form>');
    }

    public function form( $instance ) {
        $title = isset( $instance['title'] ) ? $instance['title'] : '';
        echo('<p><label for="' . $this->get_field_id( 'title' ) . '">Titre :</label><input class="widefat" id="' . $this->get_field_id( 'title' ) . '" name="' . $this->get_field_name( 'title' ) . '" type="text" value="' . $title . '" /></p>');
    }
}

