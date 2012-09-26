<?
date_default_timezone_set("America/Toronto");
try
{
    $db = new PDO("mysql:host=localhost;dbname=scheduling","root","p");
    //$db = new PDO("mysql:host=sql102.0fees.net;dbname=fees0_10270600_scheduling","fees0_10270600","officehours");
}
catch (PDOException $err)
{
    die("<b>Sorry! Conection Problem: " . $err->getMessage()."</b>");
}
?>