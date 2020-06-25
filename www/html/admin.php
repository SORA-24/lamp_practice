<?php
// 定数を読み込み
require_once '../conf/const.php';
// 関数を読み込み
require_once MODEL_PATH . 'functions.php';
// ユーザー情報を読み込み
require_once MODEL_PATH . 'user.php';
// 商品を読み込み
require_once MODEL_PATH . 'item.php';

session_start();

// ログインしていなかったらログインページにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$order = 'created DESC'; 
if(get_get('order_num') !== ''){
  $order_num = get_get('order_num');
  $order = get_order_option($order_num);
}


// データベースに接続
$db = get_db_connect();

// user.phpでユーザー情報を取得
$user = get_login_user($db);

// user.php ユーザータイプが管理者でなけれあbログインぺ時にリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// アイテム情報を取得
$items = get_all_items($db,$order);

// 商品管理ページを表示
include_once VIEW_PATH . '/admin_view.php';
