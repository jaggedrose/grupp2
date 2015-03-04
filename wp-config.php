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
define('DB_NAME', 'ria1');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'mysql');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '*7U*O1415^_J^c9?H5?U)h[h)h>:LJbiaL`[yk%_Z&|7IhRr)ILe+&#9wD/0+2UH');
define('SECURE_AUTH_KEY',  '6s]yOZZH<Q)~+!z66<]&dqKmb-NucZ_Lp~3D?`kzzNLJ{qi+3^j!#0h*I%kUu0Hi');
define('LOGGED_IN_KEY',    '`6Yf-4gE[VqnTrOIS) 6c*+lt)i+|e!ipF|Y{6b;umZ7f%zj~eqe#GmQ(=>XUODs');
define('NONCE_KEY',        'mGvI9aI,jOf{O^3@=fP;0`QD`IS+%+-_.>:}HmP.-Mx$91?sI|P=`s{ki^nO<hQ#');
define('AUTH_SALT',        'kq-=t=CCBcHW(1%m:9*H}/QwQg`.&s$T2(=;HE{+Swa>-6|&4Tm+[]$(!=UY`C|v');
define('SECURE_AUTH_SALT', 'omUW8+D>W3@BJ:6n0>|2KsKB:Vro9=U?a14:=z0hP.^l7|-]O4>mv!N%IPKzl9K0');
define('LOGGED_IN_SALT',   'gUpr#Wi^RVP+8IJC{Yl>x-ejm+k?BA0+N 1t? _<0y7H4z/$K:3]&m[vJyS>RS(<');
define('NONCE_SALT',       'Y_f%Pq9G@{Z^lI4Y#C_y>)eaZV^tP6;^1hm|K<jc<-tStJctWK+a}9%-YkO2|MfQ');

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
