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
require_once "exvoiture_sesssion.php";
require_once "exvoiture_admin.php";

class ExtVoiture {
    public function __construct()
    {
        add_action( 'widgets_init', function () {
            register_widget('ExVoiture_Widget');
        });
        add_action('init', array('ExtVoiture', 'loadFiles'));
        register_activation_hook(__FILE__, array('ExtVoiture', 'install'));
        register_uninstall_hook(__FILE__, array('ExtVoiture', 'uninstall'));
        add_action('wp_loaded', array($this, 'saveVoiture'), 1);
        add_action('wp_loaded', array($this, 'checkInfo'), 2);
    }

    public static function install() {
        global $wpdb;
        $wpdb->query("CREATE TABLE IF NOT EXISTS {$wpdb->prefix}voiture (id INT(11) NOT NULL AUTO_INCREMENT , name VARCHAR(255) NOT NULL , brand VARCHAR(255) NOT NULL ,  model VARCHAR(255) NOT NULL ,  year INT(11) NOT NULL ,  plaque VARCHAR(255) NOT NULL ,    PRIMARY KEY  (id));");
    }

    public static function uninstall() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}voiture;");
    }

    public static function loadFiles()
    {
        wp_register_style('ExtVoiture', plugins_url('extend.css', __FILE__));
        wp_enqueue_style('ExtVoiture');

        wp_register_script('ExtVoiture', plugins_url('extend.js', __FILE__));
        wp_enqueue_script('ExtVoiture');

        wp_localize_script('ExtVoiture', 'myFormScript', array(
            'adminUrl' => admin_url('admin-ajax.php')
        ));
    }

    public function saveVoiture() {

        if (isset($_POST['plaque']) && !empty($_POST['plaque']) &&
            isset($_POST['name']) && !empty($_POST['name']) &&
            isset($_POST['brand']) && !empty($_POST['brand']) &&
            isset($_POST['model']) && !empty($_POST['model']) &&
            isset($_POST['year']) && !empty($_POST['year'])
        ) {

            $exVoiture_Session = new ExVoiture_Session();

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
                       $exVoiture_Session->createMessage("error", "Il y a une erreur, réseillez plus tarrd");
                    } else {
                       $exVoiture_Session->createMessage("success", "Ajout de votre voiture effectuez.");
                    }
                } else {
                    $exVoiture_Session->createMessage("error", "Votre plaque est déjà connue de nos service.");
                }

        }
    }

    public function checkInfo()
    {
        $exVoiture_Session = new ExVoiture_Session();

        $message = $exVoiture_Session->getMessage();

        if ($message !== false) {
            echo ("
                <p class='ex-voiture-info " . $message["type"] . "'>
                    " . $message["message"] . "
                </p>
            ");
        }

        $message = $exVoiture_Session->destroy();
    }

}

new ExtVoiture();