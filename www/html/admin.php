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

// 商品表示デフォルトは新着順
$order = 'created DESC'; 
if(get_get('order_num') !== ''){
  $order_num = get_get('order_num');
  $order = get_order_option($order_num);
}
// データベースに接続
$db = get_db_connect();

// 総商品数を取得
$number_items = get_count_all_items($db);
// $pagenationとしてpage=>現在のページ、maxpage=>最大のページ数、start=>商品の表示開始を取得
$pagenation = set_pagenation($db , $number_items);

// user.phpでユーザー情報を取得
$user = get_login_user($db);

// user.php ユーザータイプが管理者でなけれあbログインぺ時にリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// アイテム情報を取得
$items = get_all_items($db,$order,$pagenation['start']);

// 商品管理ページを表示
include_once VIEW_PATH . '/admin_view.php';
