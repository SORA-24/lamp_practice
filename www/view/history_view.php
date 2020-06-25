<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>注文履歴</title>
  <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>">
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <h1>注文履歴</h1>
  <div class="container">

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if(count($historys) > 0){ ?>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>注文時間</th>
            <th>合計金額</th>
            <th>リンク</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($historys as $history){ ?>
          <tr>
            <td><?php print number_format($history['order_id']) ; ?></td>
            <td><?php print h($history['created']) ; ?></td>
            <td><?php print number_format($history['total_price']) ; ?> 円 </td>
            <td>
              <form action="details.php" method="get">
                <input type="hidden" name="order_id" value="<?php print $history['order_id']?>">
                <input type="submit" value="注文履歴詳細を見る">
              </form>
            </td>
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