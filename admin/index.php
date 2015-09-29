<?php

include 'header.php';

include 'menu.php';

if ( isset($_GET['r']) ) {
  $msg = '';
  switch ($_GET['r']) {
    case 'speakerDeleted':
      $msg = 'Speaker Deleted';
    break;
    
    case 'sermonDeleted':
      $msg = 'Sermon Deleted';
    break;
    
     case 'seriesDeleted':
      $msg = 'Series Deleted';
    break;
    
    case 'saved':
      $msg = 'Saved';
    break;
  }
?>
  <h3><?=$msg?></h3>
<?php
}

include 'footer.php';
?>