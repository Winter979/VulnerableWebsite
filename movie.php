<!--
 * @Author: zazu
 * @Date:   2018-08-30 02:08:49
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-18 10:43:43
-->

<?php 
   require_once("php/verifyLogin.php"); 

   /*Must have a movie selected*/
   if(!(isset($_GET['movie']))){ 
      header('location: movies.php');
      die();
   }

   $movieID = $_GET['movie'];
 

   /*If there is a new review then submit it before loading up all the reviews*/
   if(isset($_POST['submitReview']))
   {

      /*Need to create a list of allowed tags, ATM just allow them all*/
      $review = $_POST['review'];

      /*The Score is just a simple integer.*/
      $score = ($_POST['score'])[0];

      /*Cant have an empty review*/
      if(!empty($review))
      {
         if($_POST['beenReviewed']){ /*Update Current review from user*/
            $sql = "UPDATE Reviews SET review = '$review', score = '$score' WHERE movieID = '$movieID' AND userID = '$userID'";
         }else{ /*Insert new review from user*/
            $sql = "INSERT INTO Reviews (movieID, userID, review, score) VALUES ('$movieID','$userID','$review', '$score')";
         }
         if(!mysqli_query($db, $sql))
         {
            $error = true;
         }
      }     
   }
   elseif(isset($_POST['deleteReview']))
   {
      $delMovieID = $_POST['movieID'];
      $delUserID = $_POST['userID'];

      $sql = "DELETE FROM Reviews WHERE userID='$delUserID' AND movieID='$delMovieID'";

      if(!mysqli_query($db, $sql))
         $error = true;
   }

   /*Query used to get the movie and the values of if the current user has purchaes (And|Or) reviews it*/
   $sql = " SELECT 
                *
            FROM
                Movies
                    NATURAL LEFT JOIN
                (SELECT 
                    movieID, userID AS purchased
                FROM
                    Purchases
                WHERE
                    userID = '$userID' AND movieID = '$movieID') a
                    NATURAL LEFT JOIN
                (SELECT 
                    review, score AS yourScore
                FROM
                    Reviews
                WHERE
                    userID = '$userID' AND movieID = '$movieID') b
            WHERE
                movieID = '$movieID'";


   $results = mysqli_query($db, $sql);

   $movie = mysqli_fetch_assoc($results);

   $purchased = $movie['purchased'] != null;

   $newPurchase = false;   

   /*If a purchase request was made and it hasnt already been purchased the process*/
   if(!$purchased && isset($_POST['purchase']))
   {

      $newBalance = $_SESSION['balance']-$movie['cost'];

      $sql = "UPDATE Users SET balance = '".$newBalance."' WHERE userID = '$userID'";
      if(mysqli_query($db, $sql))
      {

         $sql = "INSERT INTO Purchases (userID, movieID) VALUES ($userID, $movieID)";
         if(mysqli_query($db, $sql))
         {
            $newPurchase = true;
            $purchased = true;
         }else
         {
            $error = true;
         }
      }else
      {
         $error = true;
      }
   }


?>

<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs - Movie</title>
   <meta charset="UTF-8"> 

   <?php include_once('php/imports.php') ?>

   <style type="text/css">
      .reviewLabel
      {
         width: 4.5rem;
         text-align: right;
         font-weight: bold;
      }
   </style>

   <script type="text/javascript">
      function confirmDelete(form)
      {
         if(confirm("Are you sure you want to delete this review"))
         {
            form.submit();
         }
      }
   </script>

</head>
<body>
   <?php  
      $currentPage = "movies";  
      include_once('php/navbar.php'); 
   ?>

   <div class="container">
      <div class="card bg-dark">
         <div class="card-header container">
            <div class="row">
               <h3 class="col"> <?php echo $movie['name']?></h3> 
               <form action="/movies.php?query=<?php if($_GET['search'] != "All"){echo $_GET['search'];}?>" method="POST">
                  <button type="submit" class="btn btn-link">Back to Search</button>
               </form>
            </div>
         </div>
         <div class="card-body">
            <div class="row">
               <div class="col" style="flex-grow: 0; ">
                  <img src="img/movies/<?php echo $movie['image'] ?>" alt="">
               </div>
               <div class="col">
                  <div class="row">
                     <div class="col">
                        <label class="col-form-label text-right"><strong>Score: </strong></label>
                        <label class="col-form-label"><?php echo $movie['score'] ?></label>                        
                     </div>
                  
                     <div class="col">
                        <label class="col-form-label text-right"><strong>Rating: </strong></label>
                        <label class="col-form-label"><?php echo $movie['rating'] ?></label>                        
                     </div>
                  
                     <div class="col">
                        <label class="col-form-label text-right"><strong>Duration: </strong></label>
                        <label class="col-form-label"><?php echo $movie['durationH']."hr ".$movie['durationM']."min" ?></label>                        
                     </div>
                  </div>
                  <div class="row">
                     <label class="col-12 col-form-label"><b>Synopsis: </b></label>
                     <div class="col-12">
                        <p><?php echo addParagraphs($movie['synopsis']); ?>      
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>      
      <br>
      <div class="card bg-dark">
         <div class="card-header">
            <h3>Purchase</h3> 
         </div>
         <div class="card-body">
            <div class="row justify-content-md-center">
               <?php if($newPurchase){ ?>
                  <label class="col-md-auto col-form-labal"> <i>Movie has been purchased Successfully</i></label>
               <?php }elseif(!$purchased) { ?>
                  <label class="col-md-auto col-form-label">Balance: $<?php echo $_SESSION['balance'] ?></label>
                  <label class="col-md-auto col-form-label">Cost: $<?php echo $movie['cost'] ?></label>
                  <div class="col-md-auto">
                     <label class="col-form-label">New Balance: $</label>
                     <label class="col-form-label">
                        <?php 
                           $newBalance = $_SESSION['balance']-$movie['cost'];
                           echo $newBalance;
                        ?> 
                     </label>
                  </div>
                  <?php if ($newBalance >= 0) { ?>
                     <form method="POST">
                        <button type="submit" class="col-md-auto btn btn-success" name="purchase">Purchase</button>
                     </form>
                  <?php }else{ ?>
                     <form action="/account.php?view=details" method="POST">
                        <button type="button" class="col-md-auto btn btn-success" name="purchase" disabled>Insuffient Funds</button>
                        <button type="submit" class="col-mg-auto btn btn-link" name="needFunds" value="<?php echo "/movie.php?".$_SERVER['QUERY_STRING'] ?>">Add Funds?</button>
                     </form>
                  <?php } ?>
               <?php }else{?>
                  <label class="col-md-auto col-form-labal"> <i>Already Purchased. Why not make a review?</i></label>
               <?php }?>
            </div>
         </div>
      </div>      
      <br>
      <div class="card bg-dark">
         <div class="card-header">
            <h3>Reviews</h3> 
         </div>
         <div class="card-body">
            <?php if($purchased) : ?>
               <div class="row">
                  <div class="col">
                     <form method="POST">
                        <div class="row">
                           <div class="col form-group">
                              <label class="col-form-label">Enter Your Review: </label>
                              <input type="text" hidden name="beenReviewed" value="<?php echo ($movie['review'] != null); ?>"> 
                              <span class="text-secondary" >Feel free to use html tags such as <?php echo htmlspecialchars("<b>"); ?> <b>(Bold)</b> <?php echo htmlspecialchars("</b>"); ?>  and <?php echo htmlspecialchars("<i>"); ?> <i>(Italic)</i> <?php echo htmlspecialchars("</i>"); ?> </span>
                              <textarea type="text" class="form-control" name="review" rows="3" placeholder="You have not submitted a review yet." data-toggle="tooltip"><?php if($movie['review'] != null){echo $movie['review'];} ?></textarea>
                           </div>
                        </div>
                        <div class="row justify-content-end">
                           <div class="col-md-auto">
                              <div class="form-group row">
                                 <label class="col col-form-label">Score:</label>
                                 <div class="col-md-auto">
                                    <select class="form-control" name="score[]">
                                       <option selected hidden><?php echo (isset($movie['yourScore'])) ? $movie['yourScore'] : '1'; ?></option>
                                       <option value="1">1</option>
                                       <option value="2">2</option>
                                       <option value="3">3</option>
                                       <option value="4">4</option>
                                       <option value="5">5</option>
                                       <option value="6">6</option>
                                       <option value="7">7</option>
                                       <option value="8">8</option>
                                       <option value="9">9</option>
                                       <option value="10">10</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-auto">
                              <button type="submit" class="btn btn-success" name="submitReview">Submit</button>
                           </div>
                        </div>
                     </form>
                  </div>
               </div> 
            <?php endif; ?>

            <?php 
               $sql = "SELECT r.review, r.score, u.username, u.userID, u.image, m.movieID From Reviews AS r, Movies as m, Users as u WHERE r.movieID = m.movieID AND r.userID = u.userID AND u.userID != '$userID' AND r.movieID = '$movieID'";

               if(!$results = mysqli_query($db, $sql))
                  $error = true;

               $count = mysqli_num_rows($results);

               if ($count > 0) 
               {
                  for ($ii=0; $ii < $count; $ii++) 
                  {
                     echo "<hr>";
                     $row = mysqli_fetch_assoc($results); 
                     if($row['userID'] != $userID):?>
                        <div class="row">
                           <div class="col-2">
                              <div class="imgContainer w-100">
                                 <img src="img/profile/<?php echo $row['image'] ?>" alt="" class="w-100">
                              </div>
                           </div>
                           <div class="col-10">
                              <div class="row">
                                 <div class="reviewLabel">
                                    <label class="col-form-label">User: </label>
                                 </div>
                                 <div class="col reviewValue">
                                    <label class="col-form-label"><?php echo $row['username'] ?></label>
                                 </div>
                                 <?php if(in_array($_SESSION['access'], array('admin','moderator'))) : ?>
                                 <div class="col-md-auto float-right">
                                    <form method="POST">
                                       <input type="hidden" name="userID" value="<?php echo $row['userID']; ?>">
                                       <input type="hidden" name="movieID" value="<?php echo $row['movieID']; ?>">
                                       <input type="hidden" name="deleteReview">
                                       <button type="button" class="btn btn-danger" onclick="confirmDelete(this.form)">Delete</button>
                                    </form>
                                 </div>
                                 <?php endif; ?>
                              </div>
                              <div class="row">
                                 <div class="reviewLabel">
                                    <label class="col-form-label">Score: </label>
                                 </div>
                                 <div class="col reviewValue">
                                    <label class="col-form-label"><?php echo $row['score'] ?>/10</label>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="reviewLabel">
                                    <label class="col-form-label">Review: </label>
                                 </div>
                                 <div class="col reviewValue">
                                    <label class="col-form-label"><?php echo $row['review'] ?></label>
                                 </div>
                              </div>
                           </div>
                        </div> 
               <?php endif;
                  } 
               }else
                  echo "No reviews have been made yet";
             ?>
         </div>
      </div> 
      <br>     
   </div>
   <?php include('php/errorModal.php') ?>
</body>
</html>

