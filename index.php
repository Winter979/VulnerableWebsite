<!--
 * @Author: zazu
 * @Date:   2018-08-30 02:08:49
 * @Last Modified by:   zazu
 * @Last Modified time: 2018-10-14 01:07:35
-->

<?php require_once("php/verifyLogin.php");?>

<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs: Home</title>
   <meta charset="UTF-8"> 

   <?php include_once('php/imports.php'); ?>

</head>
<body>
   <?php  
      $currentPage = "index";  
      include_once('php/navbar.php'); 
   ?>

   <div class="container">
      <div class="row">
         <div class="col">
            <img src="img/yugi.png" width="100%">
         </div>
         <div class="col" style="margin: auto">
            <h1 class="display-1"><b>Welcome</b></h1>
            <h1 class="display-1"><b><?php echo $_SESSION['name']?></b></h1>
         </div>
      </div>
   </div>
   <?php include('php/errorModal.php') ?>
</body>
</html>

   