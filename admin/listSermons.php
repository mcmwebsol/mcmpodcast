<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {
  
  
  if ( !count($errors) )
    header('Location: index.php?r=saved');
}

$sermons = getSermons();

include 'header.php';

include 'menu.php';
?>
<h1>Sermons</h1>
<ul>
<?php
foreach ($sermons as $sID => $sName) {
?>
  <li>
      <?=$sName?>
      &nbsp;
      <a href="addSermon.php?id=<?=$sID?>">Edit</a> &nbsp; | &nbsp; 
      <a href="deleteSermon.php?id=<?=$sID?>">Delete</a>
  </li>
<?php
}
?> 
</ul>
<?php
include 'footer.php';
?>