<?php

// BEGIN DATABASE CONFIG
$dbHost = 'localhost';
$dbName = ''; // name of database
$dbUser = ''; // database user
$dbPass = ''; // database password
// END DATABASE CONFIG

CHANGE TO PDO
$dbh = mysql_connect($dbHost, $dbUser, $dbPass) or die ('Cannot connect to the database');
mysql_select_db($dbName, $dbh);

?>