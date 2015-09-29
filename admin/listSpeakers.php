<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {
  
  
  if ( !count($errors) )
    header('Location: index.php?r=saved');
}

$speakers = getAuthors();

include 'header.php';

include 'menu.php';
?>
<h1>Speakers</h1>
<ul>
<?php
foreach ($speakers as $sID => $sName) {
?>
  <li>
      <?=$sName?>
      &nbsp;
      <a href="addSpeaker.php?id=<?=$sID?>">Edit</a> &nbsp; | &nbsp; 
      <a href="deleteSpeaker.php?id=<?=$sID?>">Delete</a>
  </li>
<?php
}
?> 
</ul>
<?php
include 'footer.php';
?>