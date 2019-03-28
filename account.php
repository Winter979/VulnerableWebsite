<!--
 * @Author: zazu
 * @Date:   2018-08-30 02:08:49
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:20:52
-->

<?php 
   require_once("php/verifyLogin.php"); 

   /*If view page is set then use that one*/
   if(isset($_GET['view']))
   {
      $view = $_GET['view'];
   }
   else
   {/*Else just use the default one*/
      $view = "details";
   }
?>

<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs - My Account</title>
   <meta charset="UTF-8"> 

   <?php include_once('php/imports.php'); ?>

</head>
<body>
   <?php  
      $currentPage = "account";  
      include('php/navbar.php'); 
   ?>

   <div class="container">
      
      <?php include("$view.php"); ?>

   </div>
   <br>
   <?php include('php/errorModal.php') ?>
</body>
</html>

