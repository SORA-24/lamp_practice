<?php
/* フレーム内のページ表示を全ドメインで禁止したい場合 */
header('X-Frame-Options: DENY');
// ディレクトリを定数化
define('MODEL_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../model/');
define('VIEW_PATH', $_SERVER['DOCUMENT_ROOT'] . '/../view/');

define('IMAGE_PATH', '/assets/images/');
define('STYLESHEET_PATH', '/assets/css/');
define('IMAGE_DIR', $_SERVER['DOCUMENT_ROOT'] . '/assets/images/' );

// データベースに入れる定数
define('DB_HOST', 'mysql');
define('DB_NAME', 'sample');
define('DB_USER', 'testuser');
define('DB_PASS', 'password');
define('DB_CHARSET', 'utf8');

// ページディレクトリの定数
define('SIGNUP_URL', '/signup.php');
define('LOGIN_URL', '/login.php');
define('LOGOUT_URL', '/logout.php');
define('HOME_URL', '/index.php');
define('CART_URL', '/cart.php');
define('FINISH_URL', '/finish.php');
define('ADMIN_URL', '/admin.php');
define('HISTORY_URL', '/history.php');
define('DETAILS_URL', '/datails.php');

// 正規表現
// 半角英数字1文字以上
define('REGEXP_ALPHANUMERIC', '/\A[0-9a-zA-Z]+\z/');
// 半角数字０文字以上もしくは０
define('REGEXP_POSITIVE_INTEGER', '/\A([1-9][0-9]*|0)\z/');

// ユーザネームとパスワードは６文字以上１００文字以下
define('USER_NAME_LENGTH_MIN', 6);
define('USER_NAME_LENGTH_MAX', 100);
define('USER_PASSWORD_LENGTH_MIN', 6);
define('USER_PASSWORD_LENGTH_MAX', 100);

// ユーザータイプ
define('USER_TYPE_ADMIN', 1);
define('USER_TYPE_NORMAL', 2);

// 商品名は１文字以上１００文字以下
define('ITEM_NAME_LENGTH_MIN', 1);
define('ITEM_NAME_LENGTH_MAX', 100);

// 商品の公開、非公開定数
define('ITEM_STATUS_OPEN', 1);
define('ITEM_STATUS_CLOSE', 0);

// アイテムのステータスは１が公開、０が非公開
define('PERMITTED_ITEM_STATUSES', array(
  'open' => 1,
  'close' => 0,
));
// 
define('ORDER_ITEMS_OPTION', array(
  1 => 'created DESC',
  2 => 'created ASC',
  3 => 'price ASC',
  4 => 'price DESC'
));

// 商品登録できる写真の拡張子
define('PERMITTED_IMAGE_TYPES', array(
  IMAGETYPE_JPEG => 'jpg',
  IMAGETYPE_PNG => 'png',
));