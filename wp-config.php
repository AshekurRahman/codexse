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
define( 'DB_NAME', 'codexse' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'mysql' );

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
define( 'AUTH_KEY',         ':n-T4&_GBYAnu6v.,]iV#<8{k}lKba&,Ultk~ZFZDwZ6R+jI3k*}<*L>5siGu/F ' );
define( 'SECURE_AUTH_KEY',  'S.F4[U=kJ6A+#M M$} a8.=H&0KAdy4{kU]j`d3aaDo^`$_5wqPN![4/qVg[LkBx' );
define( 'LOGGED_IN_KEY',    'gXfv>jOr*dkI[uI8FmVQA3r]8&vdh]L/gZPntW i~Qi:}Nqh+HJY&$eL0@Pi?eI`' );
define( 'NONCE_KEY',        'n7*vv3fFVV|4^J)%avc (JVO@xjou`m6/zgQ}@P&fx_U!k/.HUCF#hp#md^w4O$p' );
define( 'AUTH_SALT',        '+q AIiG{7/uG% Q$hfNAm16+*<~^G?j&8=`iG7,p:EWr1Lt4T1(i,%I<}GRo0NXC' );
define( 'SECURE_AUTH_SALT', 'bj{-@7c%b+}/jdFi(;KGt)Gy[wuZ%s+j|xJxhf7x4Otzxx>V^B`T >W}4IobnlD|' );
define( 'LOGGED_IN_SALT',   'v`S4lZY(R/llh/atbNx0pvCI1WirWqyDNZ=yO8pOM4ObxIIK%7tJ<r(Eykv/:mO&' );
define( 'NONCE_SALT',       '^TA^D}a@lZ,x$i2!SV_uhZM3q)KBG-KU8GJR;yA3Y:`1{1wu[d`4aS!J 63SDB@U' );

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
