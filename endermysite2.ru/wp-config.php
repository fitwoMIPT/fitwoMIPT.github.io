<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'endermysite' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'root' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', 'root' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'NO]nDhQJi7ThZ7qqo%KcI:q@Nj(=tSFHzAdz1p%_8b3}OCKL=ISxzTd#PTU<! |-' );
define( 'SECURE_AUTH_KEY',  't&~2S{z NBsN;WkY3`B!<g~zF_9Umfp#|X0x*fixlZ+$%YH9I7sq7]lq?>pRd3VU' );
define( 'LOGGED_IN_KEY',    'j?^WH,v4b}:1F.s FIkBXa^0ryN.%<|Em>*k=hE(g0Wq~:6Lf@L_;]H@eMYFTq@s' );
define( 'NONCE_KEY',        'uN.5^w[&t/Cj7`fjBdx{Hs]$ac/cw(j/]ML7/%? -t}1=!)t>{xnNf6 rv/&4b{;' );
define( 'AUTH_SALT',        'x?+oSl]sgNtxvV3pr2-y18;kHz,L~]3>AXbb`<:5N}uakJE#oHr;(AxK&XKaU|Ey' );
define( 'SECURE_AUTH_SALT', ']S(u>?u8.f!PYf};t,8zZWI8aK{%>+K>OJ0F&Bcj/W???&CT,~ (8YGS~CI?))[1' );
define( 'LOGGED_IN_SALT',   'eN-rW]&kA93w~&d/gegmk[}i+iKFY07}G+^/@/]v_R_T&G7H?{h31*[8z}j:T&a,' );
define( 'NONCE_SALT',       '-CcLJ3o4K!31?9Ys$fHXCvuZy:EJL$Bb@AY=Q4c5?|ox/%>hQL^}|cJCeX]QjN(W' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
