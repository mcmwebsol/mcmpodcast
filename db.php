<?php

// BEGIN DATABASE CONFIG - FILL IN
$dbHost = 'localhost';
$dbName = ''; // name of database
$dbUser = ''; // database user
$dbPass = ''; // database password
// END DATABASE CONFIG


try {            
    $dbh = new PDO('mysql:host='.$dhHost.';dbname='.$dbName, $dbUser, $dbPass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // throw exceptions on PDO errors
} 
catch (PDOException $e) {
    print " Error!: Unable to connect to database ";
        
    // DO NOT ENABLE BELOW 2 LINES   
    /* 
    print $e->getMessage();
    print $e->getTraceAsString();    
     */
    die();
} 

?>