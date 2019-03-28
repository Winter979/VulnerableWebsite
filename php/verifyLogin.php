<?php

/*
 * @Author: zazu
 * @Date:   2018-08-30 02:09:59
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:20:43
*/
   session_start();

   if (!isset($_SESSION['userID'])) 
   {
      $encode = urlencode($_SERVER['REQUEST_URI']);
      header("location: /login.php?redirect=$encode");
      die();
   }


   include_once('userDetails.php');

   /*Gets a string that has newlines and convertes it to add a <p> tag for printing in format*/
   function addParagraphs($input)
   {
      return preg_replace("/\r\n|\r|\n/",'<p>',$input);
   }
?>

