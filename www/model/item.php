<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// DB利用

/*
商品情報１個を取得
*/
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";

  return fetch_query($db, $sql);
}

/*
　is_open = false 商品全てを取得
　is_open = true 公開されている商品のみ取得
 */
function get_items($db, $order, $start ,$is_open = false){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ";
  if($is_open === true){
    $sql .= "
      WHERE status = 1
      ORDER BY {$order}
      LIMIT ? , 8
    ";
  }else{
    $sql .= "
      ORDER BY {$order}
      LIMIT ? , 8
    ";
  }
  return fetch_all_query($db, $sql, array($start));
}

// 商品全てを取得
function get_all_items($db,$order,$start){
  return get_items($db,$order,$start);
}

// 公開されている商品のみを取得
function get_open_items($db,$order,$start){
  return get_items($db, $order, $start ,true);
}

// 商品登録の際にエラーがないかをチェック
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

/*
エラーがなければ、データベースに商品を登録
画像をイメージディレクトリに登録
*/
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}
// 商品登録のSQl文
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";

  return execute_query($db, $sql);
}
// 商品アップデートのSQL文↓

// ステータス
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}

// 在庫
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql,array($stock,$item_id));
}

// 商品とその写真を削除する
// 商品が存在しなければ実行しない
function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}
// 商品削除のSQL文
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  return execute_query($db, $sql);
}


// 非DB

// $itemで受け取った商品が公開設定になっていればtrueを返す
function is_open($item){
  return $item['status'] === 1;
}

// 受け取ったパラメータを確認し、問題がなければ戻り値として渡す
function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

/* 以下　定数にある範囲外でなければ、エラーを返す
$name　商品ネーム
$price　料金
$stock　在庫
$filename 写真の名前
status　公開非公開設定
*/

function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}

// 並び替えオプション
// 送られてきたオプション数値をチェック
function get_order_option($order_num){
  if(isset(ORDER_ITEMS_OPTION[$order_num]) === false){
    $order = ORDER_ITEMS_OPTION[1];
  }else{
    $order = ORDER_ITEMS_OPTION[$order_num];
  }
  return $order;
}



/*page番号に対する表示
$number_items 商品全体から全ての個数を取得
$max_page 個数から最大のページを計算
$page 現在のページは指定がなければ、１ページ目 最小ページ１以上最大ページ以下以外をMAX,MINで表現
$start 現在のページ数から何番目から表示するかを指定
*/
function set_pagenation($db , $cnt ){
if(get_get('page') !== ""){
  $page = get_get('page');
}else{
  $page = 1;
}
// $page　現在表示しているページ  
$page = max($page , 1);
// $maxpage　最後のページ 
$maxpage = ceil($cnt / 8);
$page = min($page , $maxpage);
$start = ($page -1 ) * 8;

return array("page" => $page , "maxpage" => $maxpage , "start" => $start);

}
/*
　is_open = false 商品全ての個数取得
　is_open = true 公開されている商品の個数を取得
 */
function get_count_items($db, $is_open = false){
  $sql = "
    SELECT
     COUNT(*) AS cnt
    FROM
      items
  ";
  if($is_open === true){
    $sql .= "
      WHERE status = 1
     " ;
  }
  $cnt = fetch_query($db, $sql);
  return $cnt['cnt'];
}

// 商品全ての個数取得
function get_count_all_items($db){
  return get_count_items($db);
}

// 公開されている商品の個数を取得
function get_count_open_items($db){
  return get_count_items($db, true);
}