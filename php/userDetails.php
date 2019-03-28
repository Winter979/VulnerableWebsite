
<?php
/*
 * @Author: zazu
 * @Date:   2018-08-30 18:51:11
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-14 22:16:51
*/

   include_once('config.php');

   $userID = $_SESSION['userID'];

   $defaultSQL = "SELECT username, balance, access, name FROM Users WHERE userID = '$userID' LIMIT 1";

   $defaultRESULTS = mysqli_query($db, $defaultSQL);

   $rowUser = mysqli_fetch_assoc($defaultRESULTS);

   /*These values may have changed, so re-set them*/

   $_SESSION['username'] = $rowUser['username'];
   $_SESSION['balance'] = $rowUser['balance'];
   $_SESSION['access'] = $rowUser['access'];
   $_SESSION['name'] = $rowUser['name'];
?>