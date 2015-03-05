<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'grupp2');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mysql');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'RiAb-YRZ}[wU6d10ljhj9hYMr* *7;g^&p|0TeT-$Dt#g=|Pro-P85F&@Hu6:yg;');
define('SECURE_AUTH_KEY',  'Us&I-l$,S0t-U[^*$17XsG|t-kP+!wTmot|p1uO$b`1/%f-|L5T9_tZa8X!Y(e+$');
define('LOGGED_IN_KEY',    'a,%dPa;{u.+{4}cc+[1.m,n+u3b:TI_ChmSE[7Ug5-Jg%W6!$U%SF<%-T`=MX:J*');
define('NONCE_KEY',        'uB}VkV|EcNscE?OrJ1+)(cy-iy%rQMI9<ne]A;IlyvEeIaz*{[DGr?J`|m[rUPFC');
define('AUTH_SALT',        '_W*Cm+@@UEO`Wxjg6`6wMK}{|k+q.L>sCp=.(7H*0z?zf3#WU(mx8K:l/F12.#1W');
define('SECURE_AUTH_SALT', 'k5g;(z|QlO~3fKGwq4W0#xR,APFl+if>J2ES|pGFhk5f,KS4sHb~|jscjCm}rg6L');
define('LOGGED_IN_SALT',   'EsX`kB`J[omry0eKAceLO]/)v3V~O[l%Y:~=!Ds*OPb|D3H]df-Z#V|S}YnEsz/J');
define('NONCE_SALT',       'vd@#S~<UGU#^V]LIz]%rm-l.W$kB.L#y[8bh(K!<II1niFtz-FR/}j2XrP44XE?w');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
