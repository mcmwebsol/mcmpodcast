<?php
// use left joins where appropriate!!

function mcmRawSelectQuery($qs) {

  global $dbh;

  $rows = false;

  try {

    $stmt = $dbh->prepare($qs);

    $stmt->execute();

    $rows = $stmt->fetchAll();

  }
  catch (PDOException $e) {
    print " Error! in query ";
    /*
    print " qs=$qs "; // debug - remove!!!!!!!!!!

    print $e->getMessage();
    print $e->getTraceAsString();
     */
  }

  return $rows;

}


function mcmParamSelectQuery($qs, $data, $fields) {

  global $dbh;

  $rows = false;

  try {

    $stmt = $dbh->prepare($qs);

    foreach ($fields as $f) {
       $val = '';
       if ( isset($data[$f]) )
         $val = $data[$f];
       $stmt->bindValue(':'.$f, $val);
     }
     reset($fields);

    $stmt->execute();

    $rows = $stmt->fetchAll();

  }
  catch (PDOException $e) {
    print " Error! in query ";
  }

  return $rows;

}

function runUpdate($data, $fields, $tableName, $id) {

   global $dbh;

   $fieldsClause = array();
   foreach ($fields as $f) {
     $fieldsClause[] = '`'.$f.'`=:'.$f;
   }
   reset($fields);
   $fieldsClause = implode(',', $fieldsClause);

   try {
     $stmt = $dbh->prepare("UPDATE ".$tableName."
                            SET $fieldsClause
                            WHERE id=:id");

     foreach ($fields as $f) {
       $stmt->bindValue(':'.$f, $data[$f]);
     }
     reset($fields);

     $stmt->bindValue(':id', $id);

     $stmt->execute();
   }
   catch (PDOException $e) {
    print " Error! in run update ";
   }
   return;

}

function runDelete($tableName, $id) {

   global $dbh;


   try {
     $stmt = $dbh->prepare("DELETE FROM ".$tableName."
                            WHERE id=:id");
     $stmt->bindValue(':id', $id);
     $stmt->execute();
   }
   catch (PDOException $e) {
    print " Error! in run delete ";
   }
   return;

}


/**
* returns insert id if succesful
*/
function runInsert($data, $fields, $tableName) {

 global $dbh;

 $fieldsClause = array();
 $fieldNames = array();
 foreach ($fields as $f) {
   $fieldsClause[] = ':'.$f;
   $fieldNames[] = '`'.$f.'`';
 }
 reset($fields);
 $fieldsClause = implode(',', $fieldsClause);

 try {
   $qs = "INSERT INTO ".$tableName." (".implode(', ', $fieldNames).")
          VALUES ($fieldsClause)";
   $stmt = $dbh->prepare($qs);

   foreach ($fields as $f) {
     $val = '';
     if ( isset($data[$f]) )
       $val = $data[$f];
     $stmt->bindValue(':'.$f, $val);
   }
   reset($fields);

   $stmt->execute();

   $id = $dbh->lastInsertId();

   return $id;

 }
 catch (PDOException $e) {
  print " Error! in run insert ";
  /*
  print " qs=$qs "; // debug - remove!!!!!!!!!!

  print $e->getMessage();
  print $e->getTraceAsString();
   */
 }

 return false;


}

function getSermonsFromDB() {

  /* OLD QUERY - REQUIRED BIBLE VERSE AND AUTHOR
  $qs = "SELECT s.id,
                s.title,
                s.myDate,
                s.chapterStart,
                s.verseStart,
                s.chapterEnd,
                s.verseEnd,
                s.description,
                s.audioFile,
                a.firstName,
                a.lastName,
                b.name as bookOfBibleName
         FROM Sermon s,
              Author a,
              Book_Of_Bible b
         WHERE s.authorID=a.id AND
               s.bookOfBibleID=b.id
         ORDER BY myDate DESC";
  */
  // NEW QUERY - USE LEFT JOINS TO MAKE BIBLE VERSE AND AUTHOR OPTIONAL
  $qs = "SELECT s.id,
                s.title,
                s.myDate,
                s.chapterStart,
                s.verseStart,
                s.chapterEnd,
                s.verseEnd,
                s.description,
                s.audioFile,
                a.firstName,
                a.lastName,
                b.name as bookOfBibleName
         FROM Sermon s
         LEFT JOIN
              Author a
              ON
              s.authorID=a.id
         LEFT JOIN
              Book_Of_Bible b
              ON
              s.bookOfBibleID=b.id
         ORDER BY myDate DESC";
  $ret = mcmRawSelectQuery($qs);

  return $ret;

}

function getCache() {
  $oneDay = 24*60*60;
  $oneDayAgo = date('Y-m-d H:i:s', time() - $oneDay );
  $qs = "SELECT value
         FROM Podcast_Cache
         WHERE modDateTime > :modDateTime";
  $data = array('modDateTime'=>$oneDayAgo);
  $rows = mcmParamSelectQuery($qs, $data, array('modDateTime') );

  $output = '';

  if ( count($rows) ) {
    $output = $rows[0]['value'];
  }

  return $output;
}

function setCache($ouput) {
  $fields = array('value',
                  'modDateTime');
  $data = array('value'=>$output,
                'modDateTime'=>date('Y-m-d H:i:s'));
  runUpdate($data, $fields, 'Podcast_Cache', 1);
}

function mySqlToUSDate($dateStr) {

  $ret = '';

  $dateArr = explode('-', $dateStr);
  $ret = $dateArr[1].'/'.$dateArr[2].'/'.$dateArr[0];

  return $ret;

}

function clearPodcastCache() {

  // set modDateTime to 2 days ago, this will force the cache to be regenerated the next time genPodcast.php is called as the cache exp is 1 day
  $fields = array('modDateTime');
  $data = array('modDateTime'=>date('Y-m-d H:i:s', strtotime("-2 day") ));
  runUpdate($data, $fields, 'Podcast_Cache', 1);

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
  $rows = mcmRawSelectQuery($qs);
  $ret = array();
  foreach ($rows as $row) {
    $ret[$row['id']] = $row['name'];
  }

  return $ret;

}

function createSeries() {

  $fields = array('name');
  $data = array('name'=>$_POST['name']);
  runInsert($data, $fields, 'Series');

  return array();

}

function deleteSeries($id) {

  runDelete('Series', $id);

}

function getSeries($id) {

  $qs = "SELECT name
         FROM Series
         WHERE id=:id";
  $data = array('id'=>$id);
  $row = array();
  $rows = mcmParamSelectQuery($qs, $data, array('id') );
  if ( count($rows) )
    $row = $rows[0];

  return $row;

}

function saveSeries($dataIn) {

  $fields = array('name');
  $data = array('name'=>$dataIn['name']);
  runUpdate($data, $fields, 'Series', $dataIn['id']);

}


function getAuthors() {

  $qs = "SELECT id, firstName, lastName
         FROM Author
         ORDER BY lastName, firstName";

  $rows = mcmRawSelectQuery($qs);

  $ret = array();
  foreach ($rows as $row) {
    $ret[$row['id']] = $row['lastName'].', '.$row['firstName'];
  }

  return $ret;

}

function createAuthor() {

  $qs = "INSERT INTO Author (firstName,
                             lastName)
         VALUES (:firstName,
                 :lastName)";
  $data = array('firstName'=>$_POST['firstName'],
                'lastName'=>$_POST['lastName']);
  $fields = array('firstName',
                  'lastName');
  runInsert($data, $fields, 'Author');

  return array();

}

function deleteAuthor($id) {

  runDelete('Author', $id);

}

function getAuthor($id) {

  $qs = "SELECT firstName, lastName
         FROM Author
         WHERE id=:id";

  $data = array('id'=>$id);
  $row = array();
  $rows = mcmParamSelectQuery($qs, $data, array('id') );
  if ( count($rows) )
    $row = $rows[0];

  return $row;

}

function saveAuthor($dataIn) {

  $fields = array('firstName',
                  'lastName');
  $data = array('firstName'=>$dataIn['firstName'],
                'lastName'=>$dataIn['lastName']);
  runUpdate($data, $fields, 'Author', $dataIn['id']);

}


function getBooksOfBible() {

  $qs = "SELECT id, name
         FROM Book_Of_Bible
         ORDER BY name";
  $rows = mcmRawSelectQuery($qs);
  $ret = array();
  foreach ($rows as $row) {
    $ret[$row['id']] = $row['name'];
  }

  return $ret;

}


function getSermons() {

  $qs = "SELECT id, title, myDate
         FROM Sermon
         ORDER BY myDate DESC";
  $rows = mcmRawSelectQuery($qs);
  $ret = array();
  foreach ($rows as $row) {
    $ret[$row['id']] = $row['myDate'].' - '.$row['title'];
  }

  return $ret;

}

function deleteSermon($id) {

  runDelete('Sermon', $id);

}

function saveSermon($dataIn) {

  $fields = array( 'title',
                   'authorID',
                   'bookOfBibleID',
                   'myDate',
                   'chapterStart',
                   'verseStart',
                   'chapterEnd',
                   'verseEnd',
                   'description',
                   'audioFile',
                   'manuscriptFile',
                   'seriesID'
                  );
  $data = array(   'title'=> $dataIn['title'],
                   'authorID'=> $dataIn['authorID'],
                   'bookOfBibleID'=> $dataIn['bookOfBibleID'],
                   'myDate'=> $dataIn['myDate'],
                   'chapterStart'=> $dataIn['chapterStart'],
                   'verseStart'=> $dataIn['verseStart'],
                   'chapterEnd'=> $dataIn['chapterEnd'],
                   'verseEnd'=> $dataIn['verseEnd'],
                   'description'=> $dataIn['description'],
                   'audioFile'=> $dataIn['audioFile'],
                   'manuscriptFile'=> $dataIn['manuscriptFile'],
                   'seriesID'=> $dataIn['seriesID']);
  runUpdate($data, $fields, 'Sermon', $dataIn['id']);

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
         WHERE id=:id";
  $data = array('id'=>$id);
  $row = array();
  $rows = mcmParamSelectQuery($qs, $data, array('id') );
  if ( count($rows) )
    $row = $rows[0];

  return $row;

}


function createSermon() {
  $dataIn = $_POST ;

  $data = array('title' => $dataIn['title'],
               'authorID' => $dataIn['authorID'],
               'bookOfBibleID' => $dataIn['bookOfBibleID'],
               'myDate' => $dataIn['myDate'],
               'chapterStart' => $dataIn['chapterStart'],
               'verseStart' => $dataIn['verseStart'],
               'chapterEnd' => $dataIn['chapterStart'],
               'verseEnd' => $dataIn['verseEnd'],
               'description' => $dataIn['description'],
               'audioFile' => $dataIn['audioFile'],
               'manuscriptFile' => $dataIn['manuscriptFile'],
               'seriesID' => $dataIn['seriesID']);
  $fields = array('title',
               'authorID',
               'bookOfBibleID',
               'myDate',
               'chapterStart',
               'verseStart',
               'chapterEnd',
               'verseEnd',
               'description',
               'audioFile',
               'manuscriptFile',
               'seriesID');
  runInsert($data, $fields, 'Sermon');

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
?>