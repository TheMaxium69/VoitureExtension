<?php

/*
Plugin Name: VoitureExtension
Plugin URI : https://github.com/TheMaxium69/VoitureExtension
Author: Maxime Tournier
Author URI: https://tyrolium.fr
Description: Extension WordPress, Extension de Voiture
Version: 1.0-BETA
*/

require_once "exvoiture.php";

class ExtVoiture {
    public function __construct()
    {
        add_action( 'widgets_init', function () {
            register_widget('ExVoiture_Widget');
        });

        register_activation_hook(__FILE__, array('MyFormulaire', 'install'));
        register_uninstall_hook(__FILE__, array('MyFormulaire', 'uninstall'));
    }

    public static function install() {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}voiture (id INT(11) NOT NULL AUTO_INCREMENT , name VARCHAR(255) NOT NULL , brand VARCHAR(255) NOT NULL ,  model VARCHAR(255) NOT NULL ,  year INT(11) NOT NULL ,  plaque VARCHAR(255) NOT NULL ,    PRIMARY KEY  (id));");
    }

    public static function uninstall() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}voiture;");
    }

    public function saveVoiture() {

        if (isset($_POST['plaque']) && !empty($_POST['plaque']) &&
            isset($_POST['name']) && !empty($_POST['name']) &&
            isset($_POST['brand']) && !empty($_POST['brand']) &&
            isset($_POST['model']) && !empty($_POST['model']) &&
            isset($_POST['year']) && !empty($_POST['year'])
        ) {

                $plaque = $_POST['plaque'];
                $name = $_POST['name'];
                $brand = $_POST['brand'];
                $year = $_POST['year'];
                $model = $_POST['model'];

                global $wpdb;

                $user = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}voiture WHERE plaque = '$plaque'");

                if (is_null($user)) {

                    $datas = ['plaque' => $plaque,
                              'name' => $name,
                              'brand' => $brand,
                              'year' => $year,
                              'model' => $model,
                             ];

                    $result = $wpdb->insert("{$wpdb->prefix}voiture", $datas);
                    if ($result === false) {
                       // $myFormulaire_Session->createMessage("error", "Il y a une erreur, réseillez plus tarrd");
                    } else {
                      //  $myFormulaire_Session->createMessage("success", "Ajout dans le newsletter effectuer.");
                    }
                } else {
                   // $myFormulaire_Session->createMessage("error", "Vous etes déjà inscrit a notre newsletter.");
                }

        }
    }
}

new ExtVoiture();