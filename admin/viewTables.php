<!--
 * @Author: Zazu
 * @Date:   2018-10-11 08:47:47
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:57:31
-->
<?php
   require_once("../php/verifyLogin.php");
   $sql = "SHOW TABLES";
   $tables = mysqli_query($db, $sql);


   $table = "Users";

   if(isset($_GET['table']))
      $table = $_GET['table'];

   $sql = "SELECT * FROM $table";

   $columns;

   if(!$results = mysqli_query($db, $sql))  
      $error = true;
   else
      $columns = mysqli_fetch_fields($results);
   

?>

<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs: View Tables</title>
   <meta charset="UTF-8">
   <?php include("../php/imports.php") ?>
</head>
<body>
   <?php 
      $currentPage = 'admin';
      include("../php/navbar.php");
    ?>

   <div class="container">
      <div class="row justify-content-between">
         <div class="col-md-auto">
            <h2>View Table: <?php echo $table; ?></h2> 
         </div>
         <div class="col-md-auto">
            <form method="GET" accept-charset="utf-8">
               <select class="custom-select" name="table" onchange="this.form.submit()">
                  <option selected hidden><?php echo $table; ?></option> 
               <?php while($table = mysqli_fetch_array($tables)):?>
                  <option value="<?php echo $table[0] ?>"><?php echo $table[0] ?></option>
               <?php endwhile; ?>
               </select>
            </form>
         </div>
      </div>
      <br>
      <div class="row">
         <div class="col text-center text-warning">
            <h3>Only for Admin to see. Contains sensitive information</h3>
         </div>
      </div>
      <br>
      <?php if(!$error): ?>
      <div class="table-responsive">
         <table class="table table-striped table-dark">
            <thead>
               <tr>
               <?php 
                  foreach ($columns as $key)
                     echo "<th>".$key->name."</th>";
                ?>
               </tr>
            </thead>
            <tbody class="bg-dark">
            <?php while($row = mysqli_fetch_array($results)):?>
               <tr>
                  <?php 
                  for ($ii=0; $ii < count($row)/2; $ii++)
                     echo "<td>".$row[$ii]."</td>";
                   ?>
               </tr>
            <?php endwhile; ?>
            </tbody>
         </table>
      </div>
      <?php endif; ?>
      <div class="row">
         <div class="col text-center text-warning">
            <h3>Only for Admin to see. Contains sensitive information</h3>
         </div>
      </div>
   </div>
   <?php include('../php/errorModal.php') ?>
</body>
</html>