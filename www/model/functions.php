<?php

// エスケープする関数
function h($s){
  return htmlspecialchars($s ,ENT_QUOTES ,'UTF-8');
}

// 受け取った値を全て表示する
function dd($var){
  var_dump($var);
  exit();
}

// リダイレクトする
function redirect_to($url){
  header('Location: ' . $url);
  exit;
}

// 以下受け取ったパラメータがセットされているか確認
// セットされていなければ空文字
function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

function get_post($name){
  if(isset($_POST[$name]) === true){
    return $_POST[$name];
  };
  return '';
}

function get_file($name){
  if(isset($_FILES[$name]) === true){
    return $_FILES[$name];
  };
  return array();
}

function get_session($name){
  if(isset($_SESSION[$name]) === true){
    return $_SESSION[$name];
  };
  return '';
}
/*
$name セッションのkey
$value 値
 */
function set_session($name, $value){
  $_SESSION[$name] = $value;
}

// errorをセットする
function set_error($error){
  $_SESSION['__errors'][] = $error;
}

// set_error()で何かしらのエラーがセットされてなければarray()を返す
// エラーがあればエラーを返す
function get_errors(){
  $errors = get_session('__errors');
  if($errors === ''){
    return array();
  }
  set_session('__errors',  array());
  return $errors;
}

// エラーを持っていればtrueを返す
function has_error(){
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

// メッセージをセットする
function set_message($message){
  $_SESSION['__messages'][] = $message;
}

// set_message()で何かしらの メッセージがセットされてなければarray()を返す
// メッセージがあればメッセージを返す
function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}
// 何かしらのユーザでログインしてればtrueを返す
function is_logined(){
  return get_session('user_id') !== '';
}

// ファイルをアップロードする処理
// ファイル形式を確認
// exif_imagetype()関数で画像か確認
// 定数に入れている該当する拡張子であれば、ランダムな値.拡張子として返す
function get_upload_filename($file){
  if(is_valid_upload_image($file) === false){
    return '';
  }
  $mimetype = exif_imagetype($file['tmp_name']);
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
  return get_random_string() . '.' . $ext;
}

// ランダムな文字を取得する
function get_random_string($length = 20){
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

// パラメータimageを保存する
function save_image($image, $filename){
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

// $filenameと同じファイル名のものを削除する
// file_existsでファイルが存在するか確認
// unlinkで削除する
function delete_image($filename){
  if(file_exists(IMAGE_DIR . $filename) === true){
    unlink(IMAGE_DIR . $filename);
    return true;
  }
  return false;
  
}
// 文字数の確認
// デフォルトの最大値はintegerの値（定義済の定数）
// param1がparam2[min]以上、param3[max]以下であればtrueを返す
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  $length = mb_strlen($string);
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

// 半角英数字1文字以上であればtrue
function is_alphanumeric($string){
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

// 半角数字０文字以上もしくは０であればtrue
function is_positive_integer($string){
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

// $fomatで$strngが正規表現ないであればtrueを返す
function is_valid_format($string, $format){
  return preg_match($format, $string) === 1;
}

// ファイルの形式をチェック
function is_valid_upload_image($image){
  if(is_uploaded_file($image['tmp_name']) === false){
    set_error('ファイル形式が不正です。');
    return false;
  }
  $mimetype = exif_imagetype($image['tmp_name']);
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  return true;
}


// トークンの生成
// １６進数+ランダムバイトを生成
function get_csrf_token(){
  $token = bin2hex(random_bytes(30));
  set_session('csrf_token',$token);
  return $token;
}

// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  return $token === get_session('csrf_token');
}

// 不正なアクセスがあった時にメッセージを登録するのと、セッションを空にする
function not_receive_token(){
  set_message('不正な処理が行われました。');
  set_session('csrf_token',''); 
}