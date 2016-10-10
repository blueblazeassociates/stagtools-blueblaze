<?php
/**
 * StagTools Extensions by Blue Blaze
 *
 * @author  Blue Blaze Associates
 * @license GPL-2.0+
 * @link    https://github.com/blueblazeassociates/stagtools-blueblaze
 */

/*
 * Plugin Name:       StagTools Extensions by Blue Blaze
 * Depends:           StagTools
 * Description:       Extends and modifies the StagTools plugin.
 * Version:           1.2.6
 * Author:            Blue Blaze Associates
 * Author URI:        http://www.blueblazeassociates.com
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/blueblazeassociates/stagtools-blueblaze
 */

// TODO: This may not work all the time. May need to generalize this.
define( 'STAGTOOLS_PLUGIN_FILE', 'stagtools/stagtools.php' );

/**
 * Make sure that this plugin runs AFTER StagTools.
 *
 * See http://wordpress.org/support/topic/how-to-change-plugins-load-order
 * See http://stv.whtly.com/2011/09/03/forcing-a-wordpress-plugin-to-be-loaded-before-all-other-plugins/
 */
function stagtools_blueblaze__alter_plugin_execution_order() {

  // Calculate the location of this plugin's plugin file.
  $this_plugin_file = substr( __FILE__, strlen( WP_PLUGIN_DIR . DIRECTORY_SEPARATOR ));
  $this_plugin_file = str_replace( DIRECTORY_SEPARATOR, '/', $this_plugin_file );

  // Grab an array of all the active plugins.
  if ( $plugins = get_option( 'active_plugins' ) ) {

    // Lookup the location of StagTools and this plugin in the active plugins array.
    $this_plugin_key = array_search( $this_plugin_file, $plugins );
    $stagtools_plugin_key = array_search( STAGTOOLS_PLUGIN_FILE, $plugins );

    // If everything lookup OK, rearrange the active plugins array.
    // And save it back into WordPress.
    if ( false !== $this_plugin_key && false !== $stagtools_plugin_key ) {
      // Remove this plugins entry.
      array_splice( $plugins, $this_plugin_key, 1 );
      // Add it back in, AFTER StagTools.
      array_splice( $plugins, $stagtools_plugin_key, 0 , $this_plugin_file );
      // Save the modified active plugins array back into WordPress.
      update_option( 'active_plugins', $plugins );
    }
  }
}
add_action( 'activated_plugin', 'stagtools_blueblaze__alter_plugin_execution_order' );

/**
 * Modify the skills taxonomy provided by StagTools.
 *
 * This function does the following:
 * * alters promotes the taxonomy's rewrite rule to a higher precedence.
 * * Sets with_front to false in the taxonomy's rewrite rule.
 */
function stagtools_blueblaze__alter_taxonomy_skills() {

  if ( taxonomy_exists( 'skill' ) ) {

    // Grab a handle to the skills taxonomy.
    global $wp_taxonomies;
    $taxonomy_skills = & $wp_taxonomies['skill'];

    // Via re-adding, redefine the rewrite rule for the skills taxonomy.
    // And put in on top.
    // This is needed because StagTools defines the skills taxonomy after defining the portfolio custom type.
    // Because of this, in the default setup, the portfolio rewrite rule has precedence.
    // We want the skills taxonomy to have precedence.
    add_rewrite_rule( $taxonomy_skills->rewrite['slug'] . '/(.+?)/?$', 'index.php?skill=$matches[1]', 'top' );

    // For the skills taxonomy, set with_front to false.
    $taxonomy_skills->rewrite['with_front'] = false;
  }
}
add_action( 'init', 'stagtools_blueblaze__alter_taxonomy_skills' );

/**
 * Redefine the stag_two_third_last shortcode from StagTools.
 *
 * This is to fix a bug:
 * Add clear element to [stag_two_third_last].
 *
 * See https://github.com/mauryaratan/stagtools/issues/20
 *
 * @param unknown $atts
 * @param string $content
 * @return string
 */
function stagtools_blueblaze__stag_two_third_last( $atts, $content = null ) {
  return '<div class="stag-column stag-two-third stag-column-last">' . do_shortcode( $content ) . '</div><div class="clear"></div>';
}
add_shortcode( 'stag_two_third_last', 'stagtools_blueblaze__stag_two_third_last' );

/**
 * Widget styles.
 *
 * @return void
 */
function stagtools_blueblaze__widget_styles() {
  global $pagenow;
  if( $pagenow != 'widgets.php' ) return;
?>
<style type="text/css">
div[id*="_cluster_danadobson"] .widget-top {
  background: #853D46 !important;
  border-color: #B4D0DD !important;
  box-shadow: inset 0 1px 0 white !important;
  -webkit-box-shadow: inset 0 1px 0 white !important;
  -moz-box-shadow: inset 0 1px 0 white !important;
  -ms-box-shadow: inset 0 1px 0 white !important;
  -o-box-shadow: inset 0 1px 0 white !important;
  background: -moz-linear-gradient(top,  #FFFFFF 0%, #853D46 100%) !important;
  background: -webkit-linear-gradient(top, #FFFFFF 0%,#853D46 100%) !important;
  background: linear-gradient(to bottom, #FFFFFF 0%, #853D46 100%) !important;
  border-bottom: 1px solid #98B3C0 !important;
  margin-top: 0px;
}
</style>
<?php
}
add_action( 'admin_head', 'stagtools_blueblaze__widget_styles' );
