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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'workreap' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '`:/Dw@BLJy`p_]7i;N%C%0aI5_]0.EOQNa)G5&0,YIr[JMb?pSLk`%0wM>,yZ:(9' );
define( 'SECURE_AUTH_KEY',  'PhI/alRT^FD`B/)6jr#p6VO!qkSk,6JWyam(pjeXabR t4z=[f;g56}WlKp#Fs D' );
define( 'LOGGED_IN_KEY',    'qxX2S.Y(j$l6`@GX|s&,G0t|}M[]M;*;DTIo_6o%{2mcqKtqjnwH{YP6+pFb`p!,' );
define( 'NONCE_KEY',        '&[<G9i{AZRU4QDS(7$x+=.%pNhoSB|e6=kjIA0Ku}?;X=Oo?nD+<B{<r~R$b^vBz' );
define( 'AUTH_SALT',        '$rJy(WWi,L8ClEOF1-jNv]DZF}m?# >ohJj[k:$(@IA+J1lI:#2%Wd1A`ewx_Zis' );
define( 'SECURE_AUTH_SALT', 'vU|:#8.|gd|H*Ad=Z!FO[1kE@Y(aJ2$cdw}B;qIriR`m>g&O[suwJ9kyN0XVG0eA' );
define( 'LOGGED_IN_SALT',   '@`<Z`,Me&GUxL]NwS<l5jorv D`5B_^TrkS~b].uZiVs=Q5&0vs~VDJA0|}43^x9' );
define( 'NONCE_SALT',       '/)I;`S2u?]IGa*4l1~Wf{&@egQsj^iFjd)9yykGN0=U(2`*#i#Ew$>l-4U]^%*8P' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wkr_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );

define( 'WP_DEBUG_LOG', true );

define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
