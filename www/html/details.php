<?php
// 定数を読み込み
require_once '../conf/const.php';
// 関数を読み込み
require_once MODEL_PATH . 'functions.php';
// ユーザー情報を読み込み
require_once MODEL_PATH . 'user.php';
// 商品を読み込み
require_once MODEL_PATH . 'cart.php';

session_start();

// ログインしていなかったらログインページにリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}elseif(get_get('order_id') === ''){
  redirect_to(HISTORY_URL);
}
$order_id = get_get('order_id');
// データベースに接続
$db = get_db_connect();

// user.phpでユーザー情報を取得
$user = get_login_user($db);

// アイテム情報を取得
$details = get_details($db, $order_id ,$user);


// 商品管理ページを表示
include_once VIEW_PATH . '/details_view.php';
