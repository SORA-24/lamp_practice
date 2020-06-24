<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

// ログインされていなければリダイレクト
if(is_logined() === false){
  redirect_to(LOGIN_URL);
}
// データベースに接続
$db = get_db_connect();

// ログインのユーザー情報を取得
$user = get_login_user($db);

// 管理者ユーザーでなければ、ログインページにリダイレクト
if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// サブミットされた商品の値を取得
$item_id = get_post('item_id');
$changes_to = get_post('changes_to');

// 公開に変更するなら１を、非公開に変更するなら０を渡して商品情報をアップデート
if($changes_to === 'open'){
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  set_message('ステータスを変更しました。');
}else if($changes_to === 'close'){
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  set_message('ステータスを変更しました。');
}else {
  set_error('不正なリクエストです。');
}

// 処理終了後に、元のページに戻る
redirect_to(ADMIN_URL);