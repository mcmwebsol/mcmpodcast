<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {

  $error = array();
  if ( !isset($_POST['id']) )
    $errors = createSeries($_POST);
  else
    $errors = saveSeries($_POST);
  
  if ( !count($errors) )
    header('Location: index.php?r=saved');
}

$data = array('name'=>'');
$id = 0;
if ( isset($_GET['id']) ) {
  $id = intval($_GET['id']);
  $data = getSeries($id);
}

include 'header.php';

include 'menu.php';
?>
<h1>Add Series</h1>
<form action="" method="post">
  <p><strong>Name: <input type="text" name="name" value="<?=htmlentities($data['name'])?>" />
     </strong>
  </p>

  <input type="submit" name="submit" value="Save" />
  <?php
  if ($id > 0) {
  ?>
    <input type="hidden" name="id" value="<?=$id?>" />
  <?php
  }
  ?>
</form>

<?php
include 'footer.php';
?>