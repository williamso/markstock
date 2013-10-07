<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'password');

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
define('AUTH_KEY',         '#i#Xu!n4|w~sq<{L]Z*wcj$e[hs+s~|1x=DmhY@+Z@~:<.E{}{Ez<~GRS&OY8-bP');
define('SECURE_AUTH_KEY',  '3-qf4^i,K,NamB@1|#p/g>l[sU91[eTzb85skkZ.d<&O;+{[o,K?.L{wrB.Bh>iA');
define('LOGGED_IN_KEY',    '}j)-ZL1]j$.#@7s|c+EF1C{pRn^;v/0SCfqyN5jX%-QtD~fC.]JLJQgbe+f{/Z -');
define('NONCE_KEY',        '+ZOK4[Eb_Y-|Tq*)9A`~tH#5+4n)+J=>;fWZmh_jf+we{K$vBm+hR]zsM=ZWM#{%');
define('AUTH_SALT',        'shptm$]9j++dc8W~S~F)q9L|?<QW+G|rm@=p;FBa]ODQNqPt,no>]-BF45#!hTA]');
define('SECURE_AUTH_SALT', 'C4{?C>gFhQK`7c&!@O}W!d@,G1f.E/#%nphfLE17J#Hvg&|-+fn&],#-ax6nefn}');
define('LOGGED_IN_SALT',   's:CnL^UW:t@iaT+pw-$>G3aNhPP>u>tn-e^D,#:O=eX?|8y`u31NW{nl+($!Z}v@');
define('NONCE_SALT',       'GzOWb[SpL}T!Lvm9w&1hk<`wg:O@JOA/(+@W/g!uq^^ PeS3p8A<s@63c[-KqW7/');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
 define('WPLANG', 'pt_BR');

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

