<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

// ユーザIDに該当するカート情報を取得
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
  ";
  return fetch_all_query($db, $sql);
}

// カートにある商品の$item_idに該当する商品情報を取得
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
    AND
      items.item_id = {$item_id}
  ";

  return fetch_query($db, $sql);

}

// カートに入れるを押した時
// カートになければ、インサートする
// 既にカートにあれば、一個追加する
function add_cart($db, $user_id, $item_id ) {   
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// カートに新たに追加する
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES({$item_id}, {$user_id}, {$amount})
  ";

  return execute_query($db, $sql);
}

// カートの個数をアップデートする
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = {$amount}
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
  return execute_query($db, $sql);
}

// カートにある商品を削除する
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = {$cart_id}
    LIMIT 1
  ";
  return execute_query($db, $sql);
}

// カートにある商品を購入する
// カートに商品がなればfalseを返す
// 各商品の在庫を減らす
// 完了すればカートにある商品を削除する
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  try{
  $db -> beginTransaction();
  // 注文履歴にデータを登録し、注文番号を受け取る
  $order_id = insert_history($db , $carts[0]['user_id']);
  // この部分で在庫の変更と、購入履歴に登録する。
    foreach($carts as $cart){
      if(update_item_stock($db,$cart['item_id'],$cart['stock'] - $cart['amount']) === false ||
        insert_details($db , $cart , $order_id) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
    }
    delete_user_carts($db, $carts[0]['user_id']);
    if(!has_error()){
      $db -> commit();
    }else{
      set_error('購入処理に失敗しました。再度お試しください');
      $db -> rollBack();
      redirect_to(CART_URL);
    }
  }catch(PDOExeption $e){
    set_error('購入処理に失敗しました');
    $db -> rollBack();
  }
}

// カートにある商品を削除する
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = {$user_id}
  ";
  execute_query($db, $sql);
}

// カートの中にある商品の合計金額を取得
function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

// 商品購入ページに移行した際にエラーをチェック
// カートに商品がなければカートに商品が入っていません
// カートにある商品で非公開のものは現在購入できません
// カートの注文数以下の在庫であれば、在庫がたりませんと表示
// エラーが一個でもセットされていればfalse
function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

// 注文履歴にデータを登録
function insert_history($db, $user_id){
  $sql = "
    INSERT INTO history (
      user_id
    ) VALUES ( ? )";
  execute_query($db, $sql ,array($user_id));
  return $db -> lastInsertId();
}

// 注文詳細にデータを登録
function insert_details($db , $cart , $order_id){
  $sql = "
  INSERT INTO `details`(
    `order_id`, 
    `item_id`, 
    `price`, 
    `amount`, 
    `sum_price`) 
    VALUES (
      ? , ? , ? , ? , ? 
    )";
    $sum_price = ($cart['price'] * $cart['amount']);
    execute_query($db, $sql,array($order_id, $cart['item_id'], $cart['price'] , $cart['amount'],$sum_price));
}


// ユーザー新規登録するSQL文
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (?, ?);
  ";
  return execute_query($db, $sql,array($name, $password));
  
}

/*
明細を取得
*/
function get_details($db, $order_id,$user){
  $sql = "
    SELECT  
    a.`item_id`, 
    a.`price`, 
    a.`amount`, 
    a.`sum_price`, 
    b.created,
    b.order_id,
    c.name   
  FROM 
    `details`AS a
  LEFT JOIN 
    history AS b
  ON 
    a.order_id = b.order_id
  LEFT JOIN 
    items AS c
  ON 
    a.item_id = c.item_id
  WHERE 
    a.order_id = ?
  ";
  if(is_admin($user) === true){ 
    $sql .= '
      ORDER BY created DESC 
    ';
    return fetch_all_query($db, $sql,array($order_id));
  }else{
    $sql .= '
      AND b.user_id = ? 
      ORDER BY created DESC 
    ';
    $user_id = $user['user_id'];
    return fetch_all_query($db, $sql,array($order_id,$user['user_id']));
  }
    
}

// ユーザの購入履歴を取得
// オーダーIDでグループ化
function get_history($db, $user){
  $sql="
    SELECT 
      a.`order_id`, 
      SUM(a.sum_price) AS `total_price` , 
      b.created,
      b.user_id
    FROM `details`AS a 
    LEFT JOIN `history` AS b 
    ON a.order_id = b.order_id 
  ";
  if(is_admin($user) === true){ 
    $sql .= '
      GROUP BY a.order_id 
      ORDER BY created DESC 
    ';
    return fetch_all_query($db, $sql);
  }else{
    $sql .= '
      WHERE b.user_id = ?
      GROUP BY a.order_id 
      ORDER BY created DESC 
    ';
    $user_id = $user['user_id'];
    return fetch_all_query($db, $sql,array($user_id));
  }
  
}
