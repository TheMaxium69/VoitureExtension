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

        new ExVoiture_Admin();
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
        wp_register_style('ExtVoiture', plugins_url('extend2.css', __FILE__));
        wp_enqueue_style('ExtVoiture');

        wp_register_script('ExtVoiture', plugins_url('extend2.js', __FILE__));
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

    public function handleDeleteEmail()
    {
        if (array_key_exists("id", $_POST) && is_numeric($_POST["id"])) {
            $result = $this->deleteEmail($_POST["id"]);
            if ($result) {
                echo json_encode([
                    "result" => true,
                    "message" => "Email bien supprimé"
                ]);
            } else {
                echo json_encode([
                    "result" => false,
                    "message" => "Une erreur est survenue lors de la suppression"
                ]);
            }
        } else {
            echo json_encode([
                "result" => false,
                "message" => "L'ID du contact à supprimer n'est pas indiqué"
            ]);
        }
        exit();
    }

    public function deleteEmail($id)
    {
        global $wpdb;

        $result = $wpdb->delete("{$wpdb->prefix}voiture", array("id" => $id));
        return $result;
    }



}
$extVoiture = new ExtVoiture();

add_action('wp_ajax_nopriv_exvoiture_delete', array($extVoiture, 'handleDeleteEmail'));
add_action('wp_ajax_exvoiture_delete', array($extVoiture, 'handleDeleteEmail'));