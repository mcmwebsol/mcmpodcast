<?php

CHANGE ALL TO USE PDO INSTEAD OF mysql FUNCTIONS

function mySqlToUSDate($dateStr) {

  $ret = '';
  
  $dateArr = explode('-', $dateStr);
  $ret = $dateArr[1].'/'.$dateArr[2].'/'.$dateArr[0];
  
  return $ret;

}

function clearPodcastCache() {

  // set modDateTime to 2 days ago, this will force the cache to be regenerated the next time genPodcast.php is called as the cache exp is 1 day
  $u_qs = "UPDATE Podcast_Cache
           SET modDateTime='".mysql_real_escape_string( date('Y-m-d H:i:s', strtotime("-2 day") ) )."'";
  mysql_query($u_qs); 

}

function genChVerseString($sermon) {

  $ret = '';

  if ( ($sermon['chapterStart'] > 0) && ($sermon['verseStart'] > 0) ) {
    $ret .= $sermon['chapterStart'].':'.$sermon['verseStart'];
  
    if ($sermon['chapterEnd'] > 0) {
      $ret .= '-'.$sermon['chapterEnd'];
      if ($sermon['verseEnd'] > 0) {
        $ret .= ':'.$sermon['verseEnd'];
      }
    }
    else if ($sermon['verseEnd'] > 0) {
      $ret .= '-'.$sermon['verseEnd'];
    }
  
  }
  
  return $ret;

}

function getAllSeries() {

  $qs = "SELECT id, name
         FROM Series
         ORDER BY name";
  $rs = mysql_query($qs);
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) {
    $ret[$row['id']] = $row['name'];
  }       

  return $ret;

}

function createSeries() {

  $qs = "INSERT INTO Series (name)
         VALUES ('".mysql_real_escape_string($_POST['name'])."')";
  mysql_query($qs);
  
  return array();

}

function deleteSeries($id) {

  $qs = "DELETE FROM Series
         WHERE id=".intval($id);
  mysql_query($qs);

}

function getSeries($id) {

  $qs = "SELECT name
         FROM Series
         WHERE id=".intval($id);
  $rs = mysql_query($qs);
  $row = mysql_fetch_assoc($rs);

  return $row;

}

function saveSeries($data) {

  $qs = "UPDATE Series
         SET name='".mysql_real_escape_string($data['name'])."'        
         WHERE id=".intval($data['id']);
  mysql_query($qs);

}


function getAuthors() {

  $qs = "SELECT id, firstName, lastName
         FROM Author
         ORDER BY lastName, firstName";
  $rs = mysql_query($qs);
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) {
    $ret[$row['id']] = $row['lastName'].', '.$row['firstName'];
  }       

  return $ret;

}

function createAuthor() {

  $qs = "INSERT INTO Author (firstName, 
                             lastName)
         VALUES ('".mysql_real_escape_string($_POST['firstName'])."', 	
                 '".mysql_real_escape_string($_POST['lastName'])."')";
  mysql_query($qs);
  
  return array();

}

function deleteAuthor($id) {

  $qs = "DELETE FROM Author
         WHERE id=".intval($id);
  mysql_query($qs);

}

function getAuthor($id) {

  $qs = "SELECT firstName, lastName
         FROM Author
         WHERE id=".intval($id);
  $rs = mysql_query($qs);
  $row = mysql_fetch_assoc($rs);

  return $row;

}

function saveAuthor($data) {

  $qs = "UPDATE Author
         SET firstName='".mysql_real_escape_string($data['firstName'])."', 
             lastName='".mysql_real_escape_string($data['lastName'])."'         
         WHERE id=".intval($data['id']);
  mysql_query($qs);

}


function getBooksOfBible() {

  $qs = "SELECT id, name
         FROM Book_Of_Bible
         ORDER BY name";
  $rs = mysql_query($qs);
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) {
    $ret[$row['id']] = $row['name'];
  }       

  return $ret;
  
}

function getSermonsFrontSide($year='',$filters=array(),$sort='',$date='',$title='') {

  $qs = "SELECT title, 
                myDate, 
                chapterStart, 
                verseStart, 
                chapterEnd, 
                verseEnd, 
                description, 
                audioFile, 
                manuscriptFile,
                a.firstName,
                a.lastName,
                b.name as bookOfBible,
                se.name as series 
         FROM Sermon s,
              Book_Of_Bible b,
              Author a,
              Series se
         WHERE ";  
  
  $usedFilters = array();
  $ct = 0;
  
  if ($year != '') {      
    $qs .= " myDate LIKE '".intval($year)."-%'\n"; 
    $ct++;
  }
  else if ( count($filters) ) {
              
    foreach ($filters as $k=>$v) {
      if ($ct > 0)  
        $qs .= " AND ";
        
      $qs .= $k."=".intval($v)."\n";  
      $usedFilters[] = $k;
      $ct++;
    }
  
  }
  
  // JOINs for author, book of Bible, and series
  $joins = array('authorID'=>'a.id', 'bookOfBibleID'=>'b.id', 'seriesID'=>'se.id');
  foreach ($joins as $joinFK=>$joinKey) {
    //if ( !in_array($joinFK, $usedFilters) ) { 
      if ($ct > 0)  
        $qs .= " AND ";
      $qs .= "$joinFK=$joinKey";
      $ct++;
    //}
  }
  
  if ($sort == '')
    $qs .= "\nORDER BY myDate DESC"; 
  else {
    $qs .= "\nORDER BY "; // FILL IN!!!
  }  
  
  //print " qs=$qs ";
    
  $rs = mysql_query($qs);
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) {
    $ret[] = $row;
  }       

  return $ret;  

}

function getSermons() {

  $qs = "SELECT id, title, myDate
         FROM Sermon
         ORDER BY myDate DESC";
  $rs = mysql_query($qs);
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) {
    $ret[$row['id']] = $row['myDate'].' - '.$row['title'];
  }       

  return $ret;

}

function deleteSermon($id) {

  $qs = "DELETE FROM Sermon
         WHERE id=".intval($id);
  mysql_query($qs);

}

function saveSermon($data) {

  $qs = "UPDATE Sermon
         SET title='".mysql_real_escape_string(stripslashes($data['title']))."',
             authorID=".intval(stripslashes($data['authorID'])).", 
             bookOfBibleID=".intval(stripslashes($data['bookOfBibleID'])).", 
             myDate='".mysql_real_escape_string(stripslashes($data['myDate']))."', 
             chapterStart=".intval(stripslashes($data['chapterStart'])).",
             verseStart=".intval(stripslashes($data['verseStart'])).",
             chapterEnd=".intval(stripslashes($data['chapterEnd'])).",
             verseEnd=".intval(stripslashes($data['verseEnd'])).",
             description='".mysql_real_escape_string(stripslashes($data['description']))."',
             audioFile='".mysql_real_escape_string(stripslashes($data['audioFile']))."', 	
             manuscriptFile='".mysql_real_escape_string(stripslashes($data['manuscriptFile']))."',
             seriesID=".intval(stripslashes($data['seriesID']))."       
         WHERE id=".intval(stripslashes($data['id']));
  mysql_query($qs);
  
  clearPodcastCache();

}

function getSermon($id) {

  $qs = "SELECT title, 
               authorID, 
               bookOfBibleID, 
               myDate, 
               chapterStart,
               verseStart,
               chapterEnd,
               verseEnd,
               description,
               audioFile, 	
               manuscriptFile,
               seriesID
         FROM Sermon
         WHERE id=".intval($id);
  $rs = mysql_query($qs);
  $row = mysql_fetch_assoc($rs);

  return $row;

}


function createSermon($arr) {

  $qs = "INSERT INTO Sermon (title, 
                             authorID, 
                             bookOfBibleID, 
                             myDate, 
                             chapterStart,
                             verseStart,
                             chapterEnd,
                             verseEnd,
                             description,
                             audioFile, 	
                             manuscriptFile,
                             seriesID)
         VALUES ('".mysql_real_escape_string($_POST['title'])."', 
                 ".intval($_POST['authorID']).", 
                 ".intval($_POST['bookOfBibleID']).", 
                 '".mysql_real_escape_string($_POST['myDate'])."', 
                 '".mysql_real_escape_string($_POST['chapterStart'])."',
                 '".mysql_real_escape_string($_POST['verseStart'])."',
                 '".mysql_real_escape_string($_POST['chapterEnd'])."',
                 '".mysql_real_escape_string($_POST['verseEnd'])."',
                 '".mysql_real_escape_string($_POST['description'])."',
                 '".mysql_real_escape_string($_POST['audioFile'])."', 	
                 '".mysql_real_escape_string($_POST['manuscriptFile'])."',
                 ".intval($_POST['seriesID']).")";
  mysql_query($qs);
  
  clearPodcastCache();
  
  return array();
                 
}

function ls($__dir="./",$__pattern="*.*") {
 settype($__dir,"string");
 settype($__pattern,"string");

 $__ls=array();
 $__regexp=preg_quote($__pattern,"/");
 $__regexp=preg_replace("/[\\x5C][\x2A]/",".*",$__regexp);
 $__regexp=preg_replace("/[\\x5C][\x3F]/",".", $__regexp);

 if(is_dir($__dir))
  if(($__dir_h=@opendir($__dir))!==FALSE)
  {
   while(($__file=readdir($__dir_h))!==FALSE)
   if(preg_match("/^".$__regexp."$/",$__file))
     if ( ($__file != '.') && ($__file != '..') ) 
       array_push($__ls,$__file);

   closedir($__dir_h);
   sort($__ls,SORT_STRING);
  }

 return $__ls;
}

function isSelected($a, $b) {
  if ($a == $b)
    return ' selected="selected"';
    
  return '';  
}

function searchSermons($keywords) {

  $qs = "SELECT title, 
                myDate, 
                chapterStart, 
                verseStart, 
                chapterEnd, 
                verseEnd, 
                description, 
                audioFile, 
                manuscriptFile,
                a.firstName,
                a.lastName,
                b.name as bookOfBible,
                se.name as series 
         FROM Sermon s,
              Book_Of_Bible b,
              Author a,
              Series se
         WHERE (s.title LIKE '%".mysql_real_escape_string($keywords)."%' OR
                b.name LIKE '%".mysql_real_escape_string($keywords)."%' OR
                se.name LIKE '%".mysql_real_escape_string($keywords)."%'
               ) AND 
               s.bookOfBibleID=b.id AND
               s.seriesID=se.id AND
               s.authorID=a.id 
         ORDER BY myDate DESC";
         //print " qs=$qs ";
         
  $rs = mysql_query($qs);
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) {
    $ret[] = $row;
  }       

  return $ret;           

}
?>