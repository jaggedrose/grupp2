<?php
//get theme dir and make it a constant
$themeDir = explode("/", __FILE__);
if (count($themeDir) > 1) {
  array_pop($themeDir);
  $themeDir = implode("/", $themeDir);
} else {
  $themeDir = explode("\\", __FILE__);
  array_pop($themeDir);
  $themeDir = implode("\\", $themeDir);
}
define(THEME_FILE_ROOT, trailingslashit($themeDir));
define(THEME_HTTP_ROOT, trailingslashit(get_template_directory_uri()));



/**
 * Register the required stylesheets for this theme.
 *
 */

function ngThemes_stylesheets()
{
  // Register the style like this for a theme:
  wp_register_style( 'base-style', THEME_HTTP_ROOT . 'css/style.css', array(), '20150225', 'all' );

  // For either a plugin or a theme, you can then enqueue the style:
  wp_enqueue_style( 'base-style' );
}

add_action( 'wp_enqueue_scripts', 'ngThemes_stylesheets' );



/**
 * Register the required scripts for this theme.
 *
 */

function ngTheme_scripts() {
  wp_enqueue_script(
    'angularjs',
    THEME_HTTP_ROOT . 'js/libs/angular.js'
  );
  wp_enqueue_script(
    'angularjs-route',
    THEME_HTTP_ROOT . 'js/libs/angular-route.js'
  );
  wp_enqueue_script(
    'angularjs-resource',
    THEME_HTTP_ROOT . 'js/libs/angular-resource.js'
  );
  wp_enqueue_script(
    'angularjs-ui-bootstrap',
    THEME_HTTP_ROOT . 'js/libs/ui-bootstrap-tpls-0.12.1.js'
  );
  wp_enqueue_script(
    'appjs',
    THEME_HTTP_ROOT . 'js/app.js'
  );

  //autoload all controllers
  $allControllers = scandir(THEME_FILE_ROOT."js/controllers");
  foreach ($allControllers as $controller) {
    if (stripos($controller, ".js") !== false) {
      $scriptName = explode(".js", $controller);
      wp_enqueue_script(
        $scriptName[0],
        THEME_HTTP_ROOT . 'js/controllers/'.$controller
      );
    }
  }

  //autoload all services
  $allServices = scandir(THEME_FILE_ROOT."js/services");
  foreach ($allServices as $service) {
    if (stripos($service, ".js") !== false) {
      $scriptName = explode(".js", $service);
      wp_enqueue_script(
        $scriptName[0],
        THEME_HTTP_ROOT . 'js/services/'.$service
      );
    }
  }

  //autoload all directives
  $allDirectives = scandir(THEME_FILE_ROOT."js/directives");
  foreach ($allDirectives as $directive) {
    if (stripos($directive, ".js") !== false) {
      $scriptName = explode(".js", $directive);
      wp_enqueue_script(
        $scriptName[0],
        THEME_HTTP_ROOT . 'js/directives/'.$directive
      );
    }
  }

  //autoload all custom scripts
  $allCustomScripts = scandir(THEME_FILE_ROOT."js/custom");
  foreach ($allCustomScripts as $script) {
    if (stripos($script, ".js") !== false) {
      $scriptName = explode(".js", $script);
      wp_enqueue_script(
        $scriptName[0],
        THEME_HTTP_ROOT . 'js/custom/'.$script
      );
    }
  }

  wp_localize_script(
    'appjs',
    'myLocalized',
    array(
      'partials' => THEME_HTTP_ROOT . 'partials/'
      ,
      'http_root' => trailingslashit( site_url() ),
      )
  );
}

add_action( 'wp_enqueue_scripts', 'ngTheme_scripts' );



/**
 * Register the required plugins for this theme.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */

include_once(THEME_FILE_ROOT . "php/class-tgm-plugin-activation.class.php");

function ngTheme_register_required_plugins() {
 
    /**
     * Array of plugin arrays. Required keys are name and slug.
     * If the source is NOT from the .org repo, then source is also required.
     */
    $plugins = array(
      // This is an example of how to include a plugin from the WordPress Plugin Repository.
      array(
          'name'      => 'JSON REST API',
          'slug'      => 'json-rest-api',
          'required'  => true,
      ),
      array(
          'name'      => 'JSON REST API Menu routes',
          'slug'      => 'wp-api-menus',
          'required'  => true,
      ),
 
    );
 
    /**
     * Array of configuration settings. Amend each line as needed.
     * If you want the default strings to be available under your own theme domain,
     * leave the strings uncommented.
     * Some of the strings are added into a sprintf, so see the comments at the
     * end of each line for what each argument will be.
     */
    $config = array(
        'default_path' => '',                      // Default absolute path to pre-packaged plugins.
        'menu'         => 'tgmpa-install-plugins', // Menu slug.
        'has_notices'  => true,                    // Show admin notices or not.
        'dismissable'  => false,                    // If false, a user cannot dismiss the nag message.
        'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
        'is_automatic' => true,                   // Automatically activate plugins after installation or not.
        'message'      => '',                      // Message to output right before the plugins table.
        'strings'      => array(
            'page_title'                      => __( 'Install Required Plugins', 'tgmpa' ),
            'menu_title'                      => __( 'Install Plugins', 'tgmpa' ),
            'installing'                      => __( 'Installing Plugin: %s', 'tgmpa' ), // %s = plugin name.
            'oops'                            => __( 'Something went wrong with the plugin API.', 'tgmpa' ),
            'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s).
            'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s).
            'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s).
            'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s).
            'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
            'activate_link'                   => _n_noop( 'Begin activating plugin', 'Begin activating plugins' ),
            'return'                          => __( 'Return to Required Plugins Installer', 'tgmpa' ),
            'plugin_activated'                => __( 'Plugin activated successfully.', 'tgmpa' ),
            'complete'                        => __( 'All plugins installed and activated successfully. %s', 'tgmpa' ), // %s = dashboard link.
            'nag_type'                        => 'updated' // Determines admin notice type - can only be 'updated', 'update-nag' or 'error'.
        )
    );
 
    tgmpa( $plugins, $config );
 
}

add_theme_support( 'menus' );
if ( function_exists( 'register_nav_menus' ) ) {
    register_nav_menus(
      array(
        'menu_slug' => 'Menu Name',
      )
    );
}


add_action( 'tgmpa_register', 'ngTheme_register_required_plugins' );



function property_init() {
  register_taxonomy(
    'property',
    'attachment',
    array(
      'label' => __('property'),
      'rewrite' => array( 'slug' => 'property' ),

      )
    );
}

add_action('init', 'property_init');

function ngwp_add_property_tax_to_posts() {
  register_taxonomy_for_object_type( 'property', 'post');
}

add_action('init' , 'ngwp_add_property_tax_to_posts');

//Wordpress metadata



function addMetaSearch() {
  global $wp;


  array_push($wp->public_query_vars, 'meta_key');
  array_push($wp->public_query_vars, 'meta_value');
}
add_action("init", "addMetaSearch");


add_filter( 'json_prepare_post', function ($data, $post, $context) {
  /*
    stad
    bostadstyp
    pris
    hyra
    rum
    yta_m2
    badkar
  */
 
  $data['banan_data'] = array(
    'stad' => get_post_meta( $post['ID'], 'stad', true ),
    'bostadstyp' => get_post_meta( $post['ID'], 'bostadstyp', true ),
    'pris' => get_post_meta( $post['ID'], 'pris', true ),
    'hyra' => get_post_meta( $post['ID'], 'hyra', true ),
    'rum' => get_post_meta( $post['ID'], 'rum', true ),
    'yta_m2' => get_post_meta( $post['ID'], 'yta_m2', true ),
    'badkar' => get_post_meta( $post['ID'], 'badkar', true ),
  );
  return $data;
}, 7, 3 );
