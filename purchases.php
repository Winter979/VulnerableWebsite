<?php

/*
 * @Author: zazu
 * @Date:   2018-09-02 15:44:52
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:32:47
*/

if (session_status() == PHP_SESSION_NONE) 
{
   header("location: /account.php?view=movies");
   die();
}

?>

<script type="text/javascript">
   function movieSelect(movie, id){
      window.location.href = "movie.php?movie=" + id + "&search=All";
   }
</script>

<style type="text/css">
   .imgContainer{
      height: 300px; 
      overflow: hidden;
      border-radius: .25rem .25rem 0 0;
   }

   .card-title{
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
   }

   .imgText{
      position: absolute;
      top: 0px;
      width: 100%;
      background: green;
      border-radius: .25rem .25rem 0 0;
      padding: .4rem;
      text-align: center;
      border-bottom: solid #343a40 1px;
   }

   .card-img-top{
      height: 100%;
      transition: transform 0.2s;
   }

   .card-img-top:hover{
      transform: scale(1.1);
   }

</style>
<h2>Your Purchases</h2>
<br>

<div class="row justify-content-center">
   <?php
      /*Get all movies*/
      $sql = "SELECT m.movieID, m.name, m.image FROM Purchases AS p NATURAL JOIN Users AS u LEFT JOIN Movies AS m ON p.movieID = m.movieID WHERE u.userID = '$userID' ORDER BY m.name"; 
      
      if(!$results = mysqli_query($db, $sql))
      {
         $error = true;
      }
      else
      {
         /*Create a card for each movie*/
         $count = mysqli_num_rows($results);
         for ($ii=0; $ii < $count; $ii++) {
            $row = mysqli_fetch_assoc($results); ?>

            <div class="card bg-dark m-2" style="width: 212px; cursor: pointer; border-width: 0" onclick="movieSelect(this, <?php echo $row['movieID']; ?>)">
               <div class="imgContainer">
                  <img class="card-img-top" src="img/movies/<?php echo $row['image']?>" >
                  <div class="imgText">Already Purchased</div>
               </div>
               <div class="card-body">
                  <h5 class="card-title"><?php echo $row['name'];?></h5>
               </div>
            </div>
         <?php         
         }
      }
    ?>
</div>
