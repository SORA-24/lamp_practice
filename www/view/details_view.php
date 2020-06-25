<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>注文明細</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>注文明細</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>
    <?php if(count($details) > 0){
        $totalprice = 0;
        foreach($details as $detail){
            $totalprice += $detail['sum_price'] ;
            } ?>
        <table class="table table-bordered">
            <thead class="thead-light">
              <tr>
                <th>注文番号</th>
                <th>購入日時</th>
                <th>合計金額</th>
              </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php print number_format($details[0]['order_id']) ; ?></td>
                    <td><?php print h($details[0]['created']) ; ?></td>
                    <td><?php print number_format($totalprice) ; ?> 円 </td>
                </tr>
            </tbody>
        </table>    
    <?php } ?>
    <?php if(count($details) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品名</th>
            <th>料金</th>
            <th>注文数</th>
            <th>小計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($details as $detail){ ?>
          <tr>
            <td><?php print h($detail['name']) ; ?></td>
            <td><?php print number_format($detail['price']) ; ?>円</td>
            <td><?php print number_format($detail['amount']) ; ?>個</td>
            <td><?php print number_format($detail['sum_price']) ; ?> 円 </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>注文履歴はありません。</p>
    <?php } ?> 
  </div>
</body>
</html>