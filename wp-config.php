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
define('DB_NAME', 'comcon');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '+i=F{H,NXP!q_IVMN@TBJItReQ)wraHXVl4%g $`Nn5ry7cd?[|C<JL{:f]c?nW^');
define('SECURE_AUTH_KEY',  'Q$O+>K+8j$~/RKJS~#PkRFQ2EkK5J#pGP]Z#}nI9x~v3ZemP^m.h0ZHza]6&.35R');
define('LOGGED_IN_KEY',    '5R$2m8T`uMU3Xjh8{GG;$K8wbj0k~T({Q-/!%W.tZ5-3d89d6j.ZQ9CWrydwiLgL');
define('NONCE_KEY',        'DUj2,)_D1 Q^8[~,f.$cVu>7d`Yu>7@w[7H![*0$kcdwY`xtWpYrE d)YEze=u?r');
define('AUTH_SALT',        '4OeL(Te_TYk}FV-%D%V/:/+D=47f=,|EH:?7nR.,<d!bB({*@_]k0Klw/fQX>J!@');
define('SECURE_AUTH_SALT', '2.YESieDIa#>G[5sj~;s#F{kx$nT5VMV-$j49-*imD[8M!mfT{(~*3dSljOTvttQ');
define('LOGGED_IN_SALT',   'dQD|sYe RMkq}jx^y}faC%YOt]%o>;gZH3W#/BC7H@7e l+y;A8B2Y1mAs>/LZtD');
define('NONCE_SALT',       'e/)W)u6jfj40p^xQdNP`3t@B7,VobjR5^Z{8B9]7h=)ZP{(OvNPnU;N>HLg=Zmuu');

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

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
