<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link:       http://www.konora.com
 * @since      0.1
 *
 * @package    Konoramembership
 * @subpackage Konoramembership/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Konoramembership
 * @subpackage Konoramembership/admin
 * @author:       Konora <info@konora.com>
 */
class Konoramembership_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    0.1
     * @access   private
     * @var      string    $name    The ID of this plugin.
     */
    private $name;

    /**
     * The version of this plugin.
     *
     * @since    0.1
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.1
     * @var      string    $name       The name of the plugin.
     * @var      string    $version    The version of this plugin.
     */
    public function __construct($name, $version)
    {

        $this->name    = $name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Konoramembership_Public_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Konoramembership_Public_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style($this->name, plugin_dir_url(__FILE__) . 'css/konora-membership-public.css', array(), $this->version, 'all');
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.1
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Konoramembership_Public_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Konoramembership_Public_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script($this->name, plugin_dir_url(__FILE__) . 'js/konora-membership-public.js', array(
            'jquery'
        ), $this->version, FALSE);
    }

    public function goto_login_page() {
        // Todo: Sostituire la pagina login fissa con una variabile nelle opzioni
        $login_page = 'login';

        $page = basename($_SERVER['REQUEST_URI']);

        if( (strpos($page, "wp-login.php") !== false) && $_SERVER['REQUEST_METHOD'] == 'GET') {
            wp_redirect($login_page);
            exit;
        }
    }

    /**
     * Questa funzione verifica se il plugin è attivo. Se non è attivo non fa niente.
     * Se è attivo verifica che non ci troviamo già nella pagina di login e reindirizza
     * alla pagina di login. Se Invece il plugin è attivo e ci troviamo già nella pagina
     * di login, non succede niente.
     */
    public function template_redirect()
    {
        // Todo: Sostituire la pagina login fissa con una variabile nelle opzioni
        $login_page = 'login';
        $pay_page   = 'pay';

        wp_reset_query();
        if (!is_user_logged_in() and !is_page($login_page) and !is_page($pay_page) and wpsf_get_setting('konora_membership_option', 'general', 'enable') == '1') {
            wp_redirect($login_page);
        }
    }

    /**
     * Il login viene diviso in 2 fasi:
     *   1) viene verificata la presenza della email nel circolo
     *   2) viene eseguito (in caso passa il primo punto) il login classico
     *
     * Nella prima fase la password non viene presa in considerazione.
     *
     * Quindi le verifiche da fare sono 2. L'utente le deve passare enrtrambe per
     * poter fare il login.
     */

    public function wp()
    {
        // Todo: Sostituire la pagina login fissa con una variabile nelle opzioni
        $login_page = 'login';
        $pay_page   = 'pay';

        wp_reset_query();
        // Verifica che ci troviamo nella pagina di login e che i campi login
        // e password siano stati inseriti. In caso contrario significa che
        // l'utente non ha ancora compilato il form quindi provvede a mostrargli il
        // form.

        // Todo: Gestire i messaggi di errore come password errata.
        if (is_page('login') and (array_key_exists('login', $_REQUEST)) and (array_key_exists('password', $_REQUEST)) and ($_REQUEST['login'] != '') and ($_REQUEST['password'] != '')) {
            $konora_user     = wpsf_get_setting('konora_membership_option', 'general', 'user');
            $konora_password = wpsf_get_setting('konora_membership_option', 'general', 'password');
            $konora_circle   = wpsf_get_setting('konora_membership_option', 'general', 'circle');

            $user = get_user_by('email', $_REQUEST['login']);
            if (!empty($user->user_login)) {
                $username = $user->user_login;
            }

            $creds                  = array();
            $creds['user_login']    = $username;
            $creds['user_password'] = $_REQUEST['password'];
            $creds['remember']      = true;
            $user                   = wp_signon($creds, false);

            if (is_wp_error($user)) { // Credenziali errato
                echo $user->get_error_message();
            } else { // Credenziali corrette
                // Todo: Mettere tra le opzioni la pagine di reindirizzamento in caso di successo
                /************ API KONORA ******************/
                $url = "http://panel.konora.com/api/circle/" . $konora_circle . "/inside?";

                $fields = array(
                    'user' => trim($konora_user),
                    'password' => trim($konora_password),
                    'email' => trim($_REQUEST['login']),
                    'format' => 'json'
                );

                $fields_string = '';
                foreach ($fields as $key => $value) {
                    $fields_string .= $key . '=' . $value . '&';
                }

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url . $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_HEADER, true);

                $output = curl_exec($ch);

                curl_close($ch);

                list($header, $body) = explode("\r\n\r\n", $output, 2);
                /************ FINE API KONORA ******************/

                if ($body == '"true"') { // Sei nel circolo
                    // Se è presente nel circolo effettua il login a wordpress
                    wp_redirect( home_url() );
                } else { // Non sei nel circolo
                    // Todo: Mettere tra le opzioni la pagine di reindirizzamento in caso di insuccesso
                    wp_logout();
                    wp_redirect( $pay_page . '?email=' . $_REQUEST['login'] );
                }
            }
        }
    }
}
