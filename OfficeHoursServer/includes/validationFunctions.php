<?
    function isEmpty($var){
        return ($var == '' || $var == null)?true:false;
    }
    
    function mysql_entities_fix_string($string)
    {
        return htmlentities(mysql_fix_string($string));
    }
    
    function mysql_fix_string($string)
    {
        if(get_magic_quotes_gpc()):
            $string = stripslashes($string);
            $string = strip_tags($string);
        endif;
        return mysql_real_escape_string($string);
    }
   
?>
