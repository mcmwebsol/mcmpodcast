<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {

  $error = array();
  if ( isset($_POST['id']) )
    $errors = deleteSeries($_POST['id']);
  
  if ( !count($errors) )
    header('Location: index.php?r=seriesDeleted');
}

$data = array();
$id = 0;
if ( isset($_GET['id']) ) {
  $id = intval($_GET['id']);
  $data = getSeries($id);
}

include 'header.php';

include 'menu.php';
?>
<h1>Delete Series</h1>
<form action="" method="post">
  <p><strong>Name: <?=htmlentities($data['name'])?>
     </strong>
  </p>

  <input type="submit" name="submit" value="Delete" />
  <input type="hidden" name="id" value="<?=$id?>" />
  <input type="hidden" name="confirmed" value="1" />
</form>

<?php
include 'footer.php';
?>