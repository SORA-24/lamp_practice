<?php
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

/*
$user_idに該当する情報を取得
@ return　ユーザーID
@ return　ユーザーネーム
@ return　パスワード
@ return　ユーザータイプ
 */
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";

  return fetch_query($db, $sql);
} 

/*
$nameに該当する情報を取得
@ return　ユーザーID
@ return　ユーザーネーム
@ return　パスワード
@ return　ユーザータイプ
 */
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";

  return fetch_query($db, $sql);
}

// ログインした時に正しくログインできれば、セッションにuser_idをセットする
// 　password_verifyでハッシュされた$user['password']と$passwordと照合する
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || password_verify($password , $user['password'] === false)){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}

// セッションIDからログインしたユーザー情報を取得
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

// ユーザー新規登録
// エラーがなければ、$passwordをハッシュ化してデータベースに登録
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  $hash = password_hash($password, PASSWORD_DEFAULT);
  return insert_user($db, $name, $hash );
}

// ユーザータイプが管理者であればtrueを返す
function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

// ユーザ登録時に指定の範囲内であるかチェックする
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

// 以下指定した範囲内でなければエラーをセットする
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

