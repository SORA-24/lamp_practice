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
}

// データベースに接続
$db = get_db_connect();

// user.phpでユーザー情報を取得
$user = get_login_user($db);

// アイテム情報を取得
$historys = get_history($db, $user);


// 商品管理ページを表示
include_once VIEW_PATH . '/history_view.php';
