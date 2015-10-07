<?php

$message = $argv[1];

//$message = 'M114';



// where is the socket server?
$host="192.168.1.3";
$port = 8888;
 
// open a client connection
$fp = fsockopen ($host, $port, $errno, $errstr);
 
if (!$fp)
{
$result = "Error: could not open socket connection";
}

else
{
fputs ($fp, $message);
$result = fgets ($fp, 1024);



fclose ($fp);
 
}
?>
