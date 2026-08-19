<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'idal_web' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'EK]M42B9+z:5|7VDvB|||<mdcma<a0HNF)N9,z/0J7rkLp9ACsOJwQd+qbn%J*4q' );
define( 'SECURE_AUTH_KEY',  ' <A{v&Uhk+o0CDqi2Z4xS7NfwW+cYJz,F8{64U9!Nmp?)RA2M[0X2}@~Jd-cA=Cd' );
define( 'LOGGED_IN_KEY',    'Tj-~>lenXu8[DiFBg,]HuVXtm$<(ws0DBEqJk9ag)Q6im4/ET&vh f~-0+y56c=}' );
define( 'NONCE_KEY',        'SV6!kR9>Rrq$#XT*lvxOOPEVXK>Zzm]={-)$y8u?/ez(n_b9Hx#q,v@_0W@#y%/0' );
define( 'AUTH_SALT',        '*8MWz(CIZ9 :9U`Uo7u[$*_}6CZ=HnIwG!l|Y+Yq*;X9u`l#Jq{xM0>Gm`W^J$%h' );
define( 'SECURE_AUTH_SALT', '[z}58]!f#i-3%qE7^vv;X:CbNhg7iJ>j7:78jGw_=08[L1Ar`&SP0&Wq$kt7`*Xp' );
define( 'LOGGED_IN_SALT',   'XMkuYPN1B{%i!3F12%!8KDf.K{=jeJS)C$_iUR8`6gwOS1}GQo[)|-Y!f<a.;<=k' );
define( 'NONCE_SALT',       'E:I&|4<7%cK=r`L6/k1zzaH^J1*E80a^Pf]0_nwO:,8Y;ufz2{e4nf1:lNZA;GiV' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
