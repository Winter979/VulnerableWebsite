<!--
 * @Author: zazu
 * @Date:   2018-08-30 02:08:49
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:33:04
-->

<?php 
   require_once("php/verifyLogin.php"); 
   $query = '';

   /*Only run the query if it exists and isnt empty*/
   if(isset($_GET['query']) && ! empty(trim($_GET["query"])))
   {
      
      /*Clean the input that is doing into the database*/
      $query = cleanInput($_GET['query']);
      /*Get matches thats name contains the search*/
      $sql = "SELECT * FROM Movies NATURAL LEFT JOIN( SELECT * FROM Purchases WHERE userID = '$userID') p WHERE name LIKE '%$query%' ORDER BY name";
      
   }
   else
   {/*Just get all of it*/
      $sql = "SELECT * FROM Movies NATURAL LEFT JOIN( SELECT * FROM Purchases WHERE userID = '$userID') p ORDER BY name";
   }

   if($results = mysqli_query($db, $sql))
      $count = mysqli_num_rows($results);
   else
      $error = true;
?>
<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs - Movies</title>
   <meta charset="UTF-8"> 

   <?php include("php/imports.php") ?>

   <script type="text/javascript">
      function movieSelect(id){
         window.location.href = "movie.php?movie=" + id + "&search=<?php echo $query;?>";
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

</head>
<body>
   <?php  
      $currentPage = "movies";  
      include('php/navbar.php'); 
   ?>

   <div class="container body">
         
      <div class="row">
         <div class="col">
            <h2>Search: <?php echo ($query != "All" && $query != '' ?  "$query" : "All" )?></h2>
         </div>
         <div class="col-lg-4 col-md-5">
            <form class="form" method="GET">
               <div class="input-group">
                  <input type="text" class="form-control" placeholder="Leave empty for all" name="query" value="<?php if($query != "All" && $query != '') echo "$query" ?>" autofocus>
                  <div class="input-group-append">
                     <button class="btn btn-dark" type="submit">Search</button>
                  </div>
               </div>
            </form>
         </div>   
      </div>
         
      <br>

      <?php if(!$error): ?>
      <div class="row justify-content-center">
         <?php while($row = mysqli_fetch_assoc($results)): ?>
               <div class="card bg-dark m-2" style="width: 212px; cursor: pointer; border-width: 0" onclick="movieSelect(<?php echo $row['movieID']; ?>)">
                  <div class="imgContainer">
                     <img class="card-img-top" src="img/movies/<?php echo $row['image']?>" >
                     <?php if ($row['userID'] != null): ?>
                        <div class="imgText">Already Purchased</div>
                     <?php endif ?>
                  </div>
                  <div class="card-body">
                     <h5 class="card-title"><?php echo $row['name'];?></h5>
                  </div>
               </div>
         <?php endwhile; ?>
      </div>
      <?php endif; ?>
   </div>
   <br>
   <?php include('php/errorModal.php') ?>
</body>
</html>
