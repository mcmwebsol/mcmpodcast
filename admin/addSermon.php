<?php
include '../db.php';
include 'functions.php';

if ( isset($_POST['submit']) ) {
  
  $error = array();
  if ( !isset($_POST['id']) )
    $errors = createSermon($_POST);
  else
    $errors = saveSermon($_POST);
  
  if ( !count($errors) )
    header('Location: index.php?r=saved');
}

$data = array('title' => '', 
             'authorID' => 0, 
             'bookOfBibleID' => 0, 
             'myDate' => date('Y-m-d'), 
             'chapterStart' => '',
             'verseStart' => '',
             'chapterEnd' => '',
             'verseEnd' => '',
             'description' => '',
             'audioFile' => '', 	
             'manuscriptFile' => '',
             'seriesID'=> '');
$id = 0;             
if ( isset($_GET['id']) ) {
  $id = intval($_GET['id']);
  $data = getSermon($id);
}

include 'header.php';

include 'menu.php';
?>
<h1>Add Sermon</h1>
<form action="" method="post">
  <p><strong>Title: <input type="text" name="title" value="<?=htmlentities($data['title'])?>" />
     </strong>
  </p>
  <p><strong>
  Speaker: <select name="authorID">
             <option value="">Select..</option>
           <?php
           $speakers = getAuthors();
           foreach ($speakers as $k=>$v) {
           ?>
             <option value="<?=$k?>"<?=isSelected($k, $data['authorID'])?>><?=$v?></option>
           <?php
           }
           ?>
           </select> 
  </strong>
  </p>
  
  <p><strong>
  Series: <select name="seriesID">
             <option value="">Select..</option>
           <?php
           $seriesArr = getAllSeries();
           foreach ($seriesArr as $k=>$v) {
           ?>
             <option value="<?=$k?>"<?=isSelected($k, $data['seriesID'])?>><?=$v?></option>
           <?php
           }
           ?>
           </select> 
  </strong>
  </p>
  
  <fieldset>
    <legend>Scripture</legend>
    <p><strong>
    Book of Bible: <select name="bookOfBibleID">
                     <option value="">Select..</option>
                   <?php
                   $booksOfBible = getBooksOfBible();
                   foreach ($booksOfBible as $k=>$v) {
                   ?>
                     <option value="<?=$k?>"<?=isSelected($k, $data['bookOfBibleID'])?>><?=$v?></option>
                   <?php
                   }
                   ?>  
                   </select>  
    </strong>
  </p>
  <p><strong>                      
    Start Chapter: <input type="text" name="chapterStart" size="2" value="<?=htmlentities($data['chapterStart'])?>" />
    </strong>
  </p>
  <p><strong>
    Start Verse: <input type="text" name="verseStart" size="2" value="<?=htmlentities($data['verseStart'])?>" />
    </strong>
  </p>
  <p><strong>
    End Chapter: <input type="text" name="chapterEnd" size="2" 
                        value="<?=( ($data['chapterEnd'] != 0) ? htmlentities($data['chapterEnd']) : '' )?>" />
    </strong>
  </p>
  <p><strong>
    End Verse: <input type="text" name="verseEnd" size="2"
                      value="<?=( ($data['verseEnd'] != 0) ? htmlentities($data['verseEnd']) : '' )?>" />
    </strong>
  </p>
  
  </fieldset>
  <p><strong>  
  Date: <input type="text" name="myDate" class="calendarSelectDate" value="<?=htmlentities($data['myDate'])?>" /> (YYYY-MM-DD)
  </strong>
  </p>
  <p><strong>
  Description: <textarea name="description"><?=htmlentities($data['description'])?></textarea>
  </strong>
  </p>
  <p><strong>
  Audio File: <a href="showFilesPopup.php?dir=MP3" target="_blank">Select..</a> 
              <span id="audioFileSpan"><?=htmlentities($data['audioFile'])?></span>
              <input type="hidden" name="audioFile" id="audioFile" value="<?=htmlentities($data['audioFile'])?>" />
  </strong>
  </p>
  <p><strong>
  Manuscript File: <a href="showFilesPopup.php?dir=Word" target="_blank">Select..</a> 
                   <span id="manuscriptFileSpan"><?=htmlentities($data['manuscriptFile'])?></span>
                   <input type="hidden" name="manuscriptFile" id="manuscriptFile" value="<?=htmlentities($data['manuscriptFile'])?>" />
  </strong>
  </p>
  <p><strong>           

  <input type="submit" name="submit" value="Save" />
  <?php
  if ($id > 0) {
  ?>
    <input type="hidden" name="id" value="<?=$id?>" />
  <?php
  }
  ?>
</form>

<div id="calendarDiv"></div>
<?php
include 'footer.php';
?>