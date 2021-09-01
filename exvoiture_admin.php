<?php

class ExVoiture_Admin
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_shortcode('exvoiture_suscribers_list', array($this, 'shortcodeSuscribersList'));
    }

    public function addAdminMenu()
    {
        add_menu_page(
            'Voiture Extension - Mon plugin',
            'Voitures Extension',
            'manage_options',
            'VoitureExtension',
            array($this, 'generateHtml'),
            plugin_dir_url(__FILE__) . 'extend.png'
        );

        add_submenu_page(
            'VoitureExtension',
            'Home',
            'Home',
            'manage_options',
            'VoitureExtension',
            array($this, 'generateHtml')
        );

        add_submenu_page(
            'VoitureExtension',
            'Les voiture',
            'Voitures',
            'manage_options',
            'VoitureExtension_Liste',
            array($this, 'generateSuscribersHtml')
        );
    }

    public function generateHtml()
    {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        echo '<p>Bienvenue sur l\'accueil de mon extensions de voitures</p>';
        echo '<p>Pour afficher la liste des voitures dans un article, utilisez le shortcode <br><code>[exvoiture_suscribers_list subtitle="sous titre facultatif"][/exvoiture_suscribers_list]</code></p>';
    }

    public function generateSuscribersHtml()
    {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        echo $this->genHtmlList(true);
    }

    private function getAllContacts()
    {
        global $wpdb;
        $suscribers = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}voiture");

        return $suscribers;
    }

    public function shortcodeSuscribersList($attr, $content)
    {
        $html = "<h2>Liste des inscrits</h2>";
        if (isset($attr['subtitle'])) $html .= "<h3>{$attr['subtitle']}</h3>";
        $html .= $this->genHtmlList();
        return $html;
    }

    public function genHtmlList(bool $admin = false)
    {
        $suscribers = $this->getAllContacts();
        $html = "";
        if (count($suscribers) > 0) {
            $html .= '<table class="exvoiture-liste" style="border-collapse:collapse"><tbody>';
            foreach ($suscribers as $suscriber) {
                $html .= "<tr>
                    <td width='150' style='border:1px solid black;'>{$suscriber->name}</td>
                    <td width='300' style='border:1px solid black;'>{$suscriber->brand}</td> 
                    <td width='300' style='border:1px solid black;'>{$suscriber->model}</td>
                    <td width='300' style='border:1px solid black;'>{$suscriber->year}</td>
                    <td width='300' style='border:1px solid black;'>{$suscriber->plaque}</td>
                    ";
                if ($admin){
                    $html .= "<td width='auto' style='border:1px solid black;'><button class='btn-supp2' data-id='{$suscriber->id}'>Delete</button></td>";
                }
                $html .= "</tr>";
            }
            $html .= '<tbody></table>';

        } else {
            $html .= "<p>Not voiture</p>";
        }
        return $html;
    }
}
