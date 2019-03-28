<?php

/*  
 * @Author: zazu
 * @Date:   2018-08-30 02:09:59
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:18:21
*/

   $error = false;

   define('DB_SERVER', 'localhost');
   define('DB_USERNAME', 'ccsep');
   define('DB_PASSWORD', 'ccsep_2018');
   define('DB_DATABASE', 'assignment');
   $db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);

   if (mysqli_connect_errno())
   {
      $error = true;
      exit();
   }


   /*This function needs to be fixed and implememted properly*/
   function cleanInput($input)
   {
      /*global $db;            
      //Remove white space
      $input = trim($input);                  
      //Remove script tags
      $input = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $input);                  
      //escape all escape characters in the string.
      $input = mysqli_real_escape_string($db, $input); */            
      return $input;
   }
?>