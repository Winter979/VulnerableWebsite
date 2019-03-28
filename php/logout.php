<?php

/*  
 * @Author: zazu
 * @Date:   2018-08-30 02:09:59
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-09-05 09:56:35
*/
   session_start();

   /*Destroy the current session*/
   session_destroy();
   /*Unset the username in case destroy doesnt*/
   unset($_SESSION['userID']);
   header("location: /login.php");
   die();
?>
