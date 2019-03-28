<?php

/*
 * @Author: Zazu
 * @Date:   2018-09-08 23:33:53
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:26:19
*/

if (session_status() == PHP_SESSION_NONE) 
{
   header("location: /account.php?view=reviews");
   die();
}

/*If there is a new review then submit it before loading up all the reviews*/
if(isset($_POST['submitReview']))
{

   $movieID = $_POST['movieID'];

   /*Need to create a list of allowed tags, ATM just allow them all*/
   $review = $_POST['review'];

   /*The Score is just a simple integer.*/
   $score = ($_POST['score'])[0];

   /*Cant have an empty review*/
   if(!empty($review))
   {
      if($_POST['beenReviewed'])
      {  /*Update Current review from user*/
         $sql = "UPDATE Reviews SET review = '$review', score = '$score' WHERE movieID = '$movieID' AND userID = '$userID'";
      } 
      else
      {  /*Insert new review from user*/
         $sql = "INSERT INTO Reviews (movieID, userID, review, score) VALUES ('$movieID','$userID','$review', '$score')";
      }
      
      if(!mysqli_query($db, $sql))
      {
         $error = true;
      }
   }     
}
?>

<style type="text/css">
   
   img{
      border-bottom-left-radius: 0.25rem;
      border-top-left-radius: 0.25rem;
   }

   /*Remove the curved left border on the card and put it on the image instead */
   .card{
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
   }

   .card-header{
      overflow: hidden;
   }

</style>

<script type="text/javascript">
   $(document).ready(function()
   {

      var movies;
      var movieNames = [];

      movies = $(".movieRow");
      $(movies).each(function(ii)
      {
         movieNames[ii] = $(this).attr('data-name')
      })

      /*Hide and show all movies that match or dont match the search string*/
      $('#movieSearch').keyup(function()
      {
         var text = $(this).val();
         $(movieNames).each(function(ii)
         {
            var match = this.toLowerCase().match(text);
            if(match){
               $(movies[ii]).show();
            }
            else{
               $(movies[ii]).hide();
            }
         })
      })
   })
</script>

<div class="row">
   <div class="col float-left">
      <h2>Your Reviews</h2>
   </div>
   <div class="col">
      
   </div>
   <div class="col-md-auto align-middle" style="margin: auto">
      <input type="text" class="form-control form-control-sm" style="width: 14rem;" id="movieSearch" placeholder="Filter Movies">
   </div>
</div>

<br>

<?php 
$sql = "
   SELECT 
      *
   FROM
      Movies
   RIGHT JOIN(
      SELECT 
         *
      FROM
         Purchases
      NATURAL LEFT JOIN Reviews
      WHERE
         userID = '$userID') p 
      ON p.movieID = Movies.movieID
   ORDER BY name";


$results = mysqli_query($db, $sql);

while($row = mysqli_fetch_assoc($results)): ?>

   <div class="row mb-3 movieRow" data-name="<?php echo $row['name']; ?>">
      <div class="col p-0" style="flex-grow: 0;">
         <img src="img/movies/<?php echo $row['image']?>" alt="">
      </div>
      <div class="col p-0 card bg-dark">
         <div class="card-header">
            <h3><?php echo $row['name'] ?></h3>
         </div>
         <div class="card-body">
            <form method="POST"> 
               <div class="row">
                  <div class="col form-group">
                     <label class="col-form-label">Enter Your Review: </label>
                     <span class="text-secondary" >Feel free to use html tags such as <?php echo htmlspecialchars("<b>"); ?> <b>(Bold)</b> <?php echo htmlspecialchars("</b>"); ?>  and <?php echo htmlspecialchars("<i>"); ?> <i>(Italic)</i> <?php echo htmlspecialchars("</i>"); ?> </span>
                     <textarea type="text" class="form-control" name="review" rows="3" placeholder="You have not submitted a review yet." data-toggle="tooltip"><?php if($row['review'] != null) echo $row['review']; ?></textarea>
                  </div>
               </div>
               <div class="row justify-content-end">
                  <div class="col-md-auto">
                     <div class="form-group row">
                        <label class="col col-form-label">Score:</label>
                        <div class="col">
                           <select class="form-control" name="score[]">
                              <option selected hidden><?php echo ($row['score'] != NULL ? $row['score'] : '1'); ?></option>
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
                     <input type="hidden" name="beenReviewed" value=<?php echo ($row['review'] != null ? true : false) ?>>
                     <input type="hidden" name="movieID" value="<?php echo $row['movieID'] ?>">
                     <button type="submit" class="btn btn-success" name="submitReview">Submit</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>

<?php endwhile; ?>




