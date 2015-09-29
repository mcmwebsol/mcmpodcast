<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {

  $error = array();
  if ( !isset($_POST['id']) )
    $errors = createAuthor($_POST);
  else
    $errors = saveAuthor($_POST);
  
  if ( !count($errors) )
    header('Location: index.php?r=saved');
}

$data = array('firstName'=>'', 
              'lastName'=>'');
$id = 0;
if ( isset($_GET['id']) ) {
  $id = intval($_GET['id']);
  $data = getAuthor($id);
}

include 'header.php';

include 'menu.php';
?>
<h1>Add Speaker</h1>
<form action="" method="post">
  <p><strong>First Name: <input type="text" name="firstName" value="<?=htmlentities($data['firstName'])?>" />
     </strong>
  </p>
  <p><strong>Last Name: <input type="text" name="lastName" value="<?=htmlentities($data['lastName'])?>" />
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