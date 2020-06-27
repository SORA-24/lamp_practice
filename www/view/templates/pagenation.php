<?php if(!empty($items)){ ?>
<!-- 商品数を表示 -->
<div>
  <?php 
     if(($pagenation['start'] + 8) > $number_items){
    $i = $number_items;
     }else{
    $i = ($pagenation['start']+8) ;
   } ?>
  <p><?php print "{$number_items}件中".($pagenation['start'] +1 ). "-" . $i. "件目の商品" ; ?></p>
</div>
<!-- ページネーション -->
     <div class='paging'>
<?php if($pagenation['page'] > 1 ){ ?>
     <a href="index.php?page=<?php print ($pagenation['page'] - 1); ?>">前のページへ</a>
<?php }
for($pagenum = $pagenation['page']- 2 ; $pagenum < $pagenation['page']+ 3 ; $pagenum++){ 
  if($pagenum > 0 && $pagenum <= $pagenation['maxpage']){
    if($pagenum == $pagenation['page']){ ?>
      <a class="page now_page" href="index.php?page=<?php print $pagenum; ?>"><?php print $pagenum ?></a>
      <?php }else{ ?>
      <a class="page" href="index.php?page=<?php print $pagenum ; ?>"><?php print $pagenum ?></a>
<?php }}} ?>
<?php if($pagenation['page']< $pagenation['maxpage'] ){ ?>
     <a href="index.php?page=<?php print ($pagenation['page']+ 1 ); ?>">次のページへ</a>
<?php } ?>
     </div>
<?php  }  ?>