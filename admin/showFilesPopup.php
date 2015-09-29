<?php

include 'functions.php';


$dir = ( ($_GET['dir'] == 'Word') ? '../Word/' : '../MP3/' );

$files = ls($dir);

include 'header.php';
?>
<script type="text/javascript">
function setInParent(dir, filename) {
  // set the hidden field in the parent window to filename
  if (dir == '../MP3/') {   CHANGE
    window.opener.document.getElementById('audioFile').value = filename;
    window.opener.document.getElementById('audioFileSpan').innerHTML = filename;
    window.close();
  }
  else {
    window.opener.document.getElementById('manuscriptFile').value = filename;
    window.opener.document.getElementById('manuscriptFileSpan').innerHTML = filename;
    //alert("Word="+filename);
    window.close();
  }
}
</script>
Click to Select File
<ul>
<?php
foreach ($files as $f) {
?>
  <li><a href="javascript:setInParent('<?=$dir?>', '<?=str_replace( array('"', "'"), array('\"', "\'"), $f )?>');"><?=$f?></a></li>
<?php
}  
?>
</ul>
<?php
include 'footer.php';
?>