<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', '/Users/igorkovalenko/Documents/WebDev/new.ikovalenko/wp-content/plugins/wp-super-cache/' );
define('DB_NAME', 'ikovalenko');

/** MySQL database username */
define('DB_USER', 'ikovalenko');

/** MySQL database password */
define('DB_PASSWORD', 'password');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FS_METHOD', 'direct');

define( 'WP_ALLOW_MULTISITE', true );


/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'qA|F[4{TxgX?m/1;(u >B{6<,oz,z,;oT3s$s?=$#Ojx)ngT&T9RgkUY|>*%]UdL');
define('SECURE_AUTH_KEY',  '41)-44hdDl<xO5$}9H>oXuBMwsqk2%{=#b8}rQ3a~++Iq(&oJrEKU|8tN+^g!jm{');
define('LOGGED_IN_KEY',    'pu$N7fZBu-b<qKfoBtQ_1aZ/X~DeHIW~pqd )(h~Nuq~r![z)O,3TvrKTNbS?kxq');
define('NONCE_KEY',        'ZvZx7wx&I23T4K<(|# &7qb]1p0[,Q 8.Je0L6bv1ZK#IfY(ys47GpP*e37l63-o');
define('AUTH_SALT',        'R=Fx`!uRiBa^La6]0~5sXR]jEbi0^CwRK@me09>2J&s%%wPT4o`vZxa89LKo{=b-');
define('SECURE_AUTH_SALT', 'Orq_Pe(F.)vX3o3AU(iu9@15&IzEM2E1(OaMZVpsGvbEH#wQmxA[>X}m.930z;9+');
define('LOGGED_IN_SALT',   'J~X3S.$%8EFW3GeLF:6RVF@V%I9jmlZ|=,T&vmG<!Td|5kW;i00H1M9p<N~Tv,b?');
define('NONCE_SALT',       'i[gQJJ|w4TIp]I)we|bGT6s:!Me{x?J~!xc628`$@HwZk![q*p8]],&UugVy!9;m');
define('GF_LICENSE_KEY',   '2962b64bb06ef76f4f98adf34ffb3f64');
define("ACF_OPTIONS_KEY", "b3JkZXJfaWQ9MzgzNTR8dHlwZT1kZXZlbG9wZXJ8ZGF0ZT0yMDE0LTA4LTI3IDE3OjQwOjI0");

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', true);
define('DOMAIN_CURRENT_SITE', 'new.ikovalenko.192.168.1.220.xip.io');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
