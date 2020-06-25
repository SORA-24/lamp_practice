<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$order = 'created DESC'; 

if(get_get('order_num') !== ''){
  $order_num = get_get('order_num');
  $order = get_order_option($order_num);
}

$db = get_db_connect();
$user = get_login_user($db);
$items = get_open_items($db,$order);
$token = get_csrf_token();

include_once VIEW_PATH . 'index_view.php';