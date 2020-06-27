<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

// 商品表示デフォルトは新着順
$order = 'created DESC'; 
if(get_get('order_num') !== ''){
  $order_num = get_get('order_num');
  $order = get_order_option($order_num);
}

$db = get_db_connect();

// 総商品数を取得
$number_items = get_count_all_items($db);
// $pagenationとしてpage=>現在のページ、maxpage=>最大のページ数、start=>商品の表示開始を取得
$pagenation = set_pagenation($db , $number_items);

$user = get_login_user($db);

$items = get_open_items($db,$order,$pagenation['start']);

$ranking_items = get_ranking($db);

$token = get_csrf_token();

include_once VIEW_PATH . 'index_view.php';