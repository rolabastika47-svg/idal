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
define( 'DB_NAME', 'idal' );

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
define( 'AUTH_KEY',         '/IG=Bs9T&4`k[9e4xr hi3s2&p+QC7b,]Gxg5+N*Tj~exA ^GQ`+9XT[ cj`[IWP' );
define( 'SECURE_AUTH_KEY',  'Dbxj7&)hjLj~DuafM~yahGkdl$c@Kfc7||Z8i6=V[i/%,kyLNDbJfR1Y1Bo.(HYH' );
define( 'LOGGED_IN_KEY',    'g?t=y#nI1MFc_4tw/Dd|kFE,U%n:]S)|HPVs[M_hwq}BZ%Co#/m?ffu9-UYQ3mQ=' );
define( 'NONCE_KEY',        'yj:!J*g0C5E()F Cvp?OZCc|m(}On_3?[@4ZF%s{D<!:i|$D0C+AJMZJ,B}`+nU#' );
define( 'AUTH_SALT',        '>~*dwpM*.-6Vdjm2hRw>/g`+*69<W*!qYN00PR%ua#:lo,?vQ+zR+{BI;u1#Y#]_' );
define( 'SECURE_AUTH_SALT', 'w~WF[:;uqyz#iGlX;Mw9lz =bj]{)aO >%Q0C|q<+DYNJvnGn4(tjg^H{`j9{,*f' );
define( 'LOGGED_IN_SALT',   '=WZ9U{ N hB?#2D)<F&/lg_u+4w#1wZ% _m3y?dDh?z`~b>t]XO|h^H>m)=  !Y`' );
define( 'NONCE_SALT',       'g7yK`v$_u4i,&n/#MgAU5+yy4hT&HA6gRdXB9LkW I2_OXwa95#_-&I?tG:I/eMp' );

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
$table_prefix = 'om_';

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

@ini_set( 'upload_max_filesize', '51200M' );
@ini_set( 'post_max_size', '55000M' );
@ini_set( 'memory_limit', '1024M' );
@ini_set( 'max_execution_time', '1200' );
@ini_set( 'max_input_time', '1200' );


/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
