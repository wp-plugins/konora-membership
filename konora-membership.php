<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * this starts the plugin.
 *
 * @link:       http://www.konora.com
 * @since             0.1
 * @package           Konoramembership
 *
 * @wordpress-plugin
 * Plugin Name:       konora-membership
 * Plugin URI:        http://www.konora.com
 * Description:        Gestione della membership tramite circoli di Konora
 * Version:           0.1
 * Author:            Konora
 * Author URI:        http://www.konora.com
 * License:            GPL-2.0+
 * License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:            konoramembership
 * Domain Path:            /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-konora-membership-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-konora-membership-deactivator.php';

/** This action is documented in includes/class-konora-membership-activator.php */
register_activation_hook( __FILE__, array( 'Konoramembership_Activator', 'activate' ) );

/** This action is documented in includes/class-konora-membership-deactivator.php */
register_activation_hook( __FILE__, array( 'Konoramembership_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-konora-membership.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1
 */
function run_Konoramembership() {

	$plugin = new Konoramembership();
	$plugin->run();

}
run_Konoramembership();
