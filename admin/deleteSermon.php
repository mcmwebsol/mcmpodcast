<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {

  $error = array();
  if ( isset($_POST['id']) )
    $errors = deleteSermon($_POST['id']);
  
  if ( !count($errors) )
    header('Location: index.php?r=sermonDeleted');
}

$data = array();    
$id = 0;
if ( isset($_GET['id']) ) {
  $id = intval($_GET['id']);
  $data = getSermon($id);
}

include 'header.php';

include 'menu.php';
?>
<h1>Delete Sermon</h1>
<form action="" method="post">
  <p><strong>Title: <?=htmlentities($data['title'])?>
     </strong>
  </p>
  <p><strong>  
  Date: <?=htmlentities($data['myDate'])?> (YYYY-MM-DD)
  </strong>
  </p>
  <p><strong>
  Description: <?=htmlentities($data['description'])?>
  </strong>
  </p>           

  <input type="submit" name="submit" value="Delete" />
  <input type="hidden" name="id" value="<?=$id?>" />
  <input type="hidden" name="confirmed" value="1" />
</form>

<?php
include 'footer.php';
?>