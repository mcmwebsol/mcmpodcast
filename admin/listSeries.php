<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {
  
  
  if ( !count($errors) )
    header('Location: index.php?r=saved');
}

$series = getAllSeries();

include 'header.php';

include 'menu.php';
?>
<h1>Series</h1>
<ul>
<?php
foreach ($series as $sID => $sName) {
?>
  <li>
      <?=$sName?>
      &nbsp;
      <a href="addSeries.php?id=<?=$sID?>">Edit</a> &nbsp; | &nbsp; 
      <a href="deleteSeries.php?id=<?=$sID?>">Delete</a>
  </li>
<?php
}
?> 
</ul>
<?php
include 'footer.php';
?>