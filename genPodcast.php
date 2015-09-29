<?php

// change all mysql functions to PDO!!!
// use left joins where appropriate!!
// need to fix magic quotes issue!!!
// password-protect the admin directory!!!!!!!!!!
// sql file for installing DB

include 'db.php';

// config - FILL THESE VALUES IN - IF IN DOUBT READ THE ITUNES PODCAST DOCUMENTATION
$title = encodeForPodcast('');
$link = encodeForPodcast(''); // absolute URL
$copyright = encodeForPodcast('');
$subtitle = encodeForPodcast('');
$author = encodeForPodcast('');
$authorEmail = encodeForPodcast('');
$summary = encodeForPodcast(''); // also description
$image = encodeForPodcast('http://www.clarksvillepca.org/Images/logo_color_1400x1400.jpg'); // absolute URL - e.g. a 1400x1400 image for iTunes
$category = encodeForPodcast('');
$subCategory = encodeForPodcast('');

$baseURL = ''; // absolute URL to directory where audio files are
// end config


function encodeForPodcast($text) {

  return stripslashes( str_replace( array('&', '<', '>', "’", '"' ), array('&amp;', '&lt;', '&gt;', '&apos;', '&quot;'), $text ) );
 
}


function getSermonsFromDB() {

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
  $rs = mysql_query($qs); CHANGE
  $ret = array();
  while ( $row = mysql_fetch_assoc($rs) ) { CHANGE
    $ret[] = $row;
  }       
  
  return $ret;

}





// cache, updates only daily
$output = '';
$oneDay = 24*60*60;
$now = date('Y-m-d H:i:s', time() - $oneDay );
$qs = "SELECT value
       FROM Podcast_Cache
       WHERE modDateTime > '".mysql_real_escape_string($now)."'";   CHANGE
$rs = mysql_query($qs);
if ( mysql_num_rows($rs) ) {
   list($output) = mysql_fetch_row($rs);
}
else { 
   
  $filenames = getSermonsFromDB();
  
  $items = array();
  
  // begin loop
  foreach ($filenames as $filename) {
                                         
    $itemAuthor = $filename['firstName'].' '.$filename['lastName'];
    $itemTitle = $filename['title'];
    $itemDate = $filename['myDate'];
    $bookOfBible = $filename['bookOfBibleName'];
    $chapterOfBible = $filename['chapterStart'];
    $verseStartOfBible = $filename['verseStart'];
    $chapterOfBibleEnd = $filename['chapterEnd'];
    $verseEndOfBible = $filename['verseEnd'];      
    $itemDescription = $filename['description'];
    $file = $filename['audioFile'];
    
    $fileLength = @filesize($file);
    
    if ( trim($bookOfBible) != '' )
      $itemTitle .= " - $bookOfBible $chapterOfBible:$verseStartOfBible";
      
    if ( ($verseEndOfBible != 0) && ($chapterOfBibleEnd == 0) ) // verse start and end in same chapter
      $itemTitle .= '-'.$verseEndOfBible;
    else if ($chapterOfBibleEnd > 0) // // verse start and end NOT in same chapter
      $itemTitle .= '-'.$chapterOfBibleEnd.':'.$verseEndOfBible;
    
    // convert $itemDate to RFC 2822 date format
    list($year, $month, $day) = explode('-', $itemDate);
    $pubDate = '';
    if ( trim($file) == '' )
      continue; // no audio file, skip this one
    else if ( ($year > 0) && ($month > 0) && ($day > 0) ) {
      $time = mktime(11, 0, 0, $month, $day, $year); // sets to 11am on date sermon given (ignoring daylight savings time)
      $pubDate = date('r', $time);
    } 
    else
      continue; // skip this one, info isn't available
    
    //convert $fileExt to fileType
    $fileExt = 'mp3';
    $fileType = '';
    switch ($fileExt) {
                
      case 'mp3':
      default: 
        $fileType = 'audio/mpeg';
      break;
    
    }
    
    @$fileLength = filesize($filename);
  
    $item = array('title'=>encodeForPodcast($itemTitle),
                  'author'=>encodeForPodcast($itemAuthor),
                  'subtitle'=>encodeForPodcast(''),
                  'summary'=>encodeForPodcast($itemDescription),
                  'image'=>encodeForPodcast(''),
                  'url'=>encodeForPodcast($baseURL.$file),
                  'fileLength'=>encodeForPodcast($fileLength),
                  'fileType'=>encodeForPodcast($fileType),
                  'pubDate'=>encodeForPodcast($pubDate),
                  'duration'=>encodeForPodcast(''),
                  'keywords'=>encodeForPodcast('') );
  
    $items[] = $item;
  } // end loop
  
  
  
  
  $output .= '<?xml version="1.0" encoding="UTF-8"?>
  <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" version="2.0"> 
  <docs>http://blogs.law.harvard.edu/tech/rss</docs>    
  <channel>                                                                         
  <title>'.$title.'</title>
  <link>'.$link.'</link>   
  <language>en-us</language>'; 
  if ( trim($copyright) != '' ) { $output .= '<copyright>&#x2117; &amp; &#xA9; '.$copyright.'</copyright>'; } 
  if ( trim($subtitle) != '' ) { $output .= '<itunes:subtitle>'.$subtitle.'</itunes:subtitle>'; } 
  if ( trim($author) != '' ) { $output .= '<itunes:author>'.$author.'</itunes:author>'; } 
  if ( trim($summary) != '' ) { 
    $output .= '<itunes:summary>'.$summary.'</itunes:summary>
    <description>'.$summary.'</description>'; 
  }
  $output .= ' 
  <itunes:owner>';  
  if ( trim($author) != '' ) { $output .= '<itunes:name>'.$author.'</itunes:name>'; } 
  if ( trim($authorEmail) != '' ) { $output .= '<itunes:email>'.$authorEmail.'</itunes:email>'; } 
  $output .= ' 
  </itunes:owner>';
  if ( trim($authorEmail) != '' ) { $output .= '<webMaster>'.$authorEmail.'</webMaster>'; } 
  if ( trim($image) != '' ) { $output .= '<itunes:image href="'.$image.'" />'; } 
  if ( trim($category) != '' ) { $output .= '<itunes:category text="'.$category.'">';
    if ( trim($subCategory) != '' ) { $output .= '<itunes:category text="'.$subCategory.'"/>'; } 
    $output .= '</itunes:category>'; 
  }
  
  foreach ($items as $item) {
    $item['url'] = str_replace(' ', '%20', $item['url']);
    $output .= '
  <item>    
    <title>'.$item['title'].'</title>
    <itunes:author>'.$item['author'].'</itunes:author>';                
    if ( trim($item['subtitle']) != '' ) { $output .= '<itunes:subtitle>'.$item['subtitle'].'</itunes:subtitle>'; } 
    if ( trim($item['summary']) != '' ) { $output .= '<itunes:summary>'.$item['summary'].'</itunes:summary>'; } 
    if ( trim($item['image']) != '' ) { $output .= '<itunes:image href="'.$item['image'].'" />'; }                                     
    $output .= '
    <enclosure url="'.$item['url'].'" ';
    if ( trim($item['fileLength']) != '' ) { $output .= 'length="'.$item['fileLength'].'"'; }
    $output .= ' type="'.$item['fileType'].'" />     
    <guid>'.$item['url'].'</guid>   
    <description></description>';    
    if ( trim($item['pubDate']) != '' ) { $output .= '<pubDate>'.$item['pubDate'].'</pubDate>'; }                      
    if ( trim($item['duration']) != '' ) { $output .= '<itunes:duration>'.$item['duration'].'</itunes:duration>'; }                               
    if ( trim($item['keywords']) != '' ) { $output .= '<itunes:keywords>'.$item['keywords'].'</itunes:keywords>'; }   
  $output .= '
  </item>';
  } // end foreach
  $output .= '
  </channel>
  </rss>';

  $u_qs = "UPDATE Podcast_Cache
           SET value='".mysql_real_escape_string($output)."',
               modDateTime='".date('Y-m-d H:i:s')."'";
  mysql_query($u_qs); CHANGE
 
}

echo $output;
?>  