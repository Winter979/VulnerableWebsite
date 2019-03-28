<!--
 * @Author: zazu
 * @Date:   2018-08-30 02:08:49
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:31:00
-->

<?php   
   require("../php/verifyLogin.php"); 

   if (isset($_POST['movieModded'])) 
   {

      /*Get all required values from the POST and clean them*/
      $modMovieID = mysqli_real_escape_string($db, $_POST["movieID"]);
      $modName = mysqli_real_escape_string($db, $_POST["name"]);
      $modScore = mysqli_real_escape_string($db, $_POST["score"]);
      $modRating = mysqli_real_escape_string($db, $_POST["rating"]);
      $modCost = mysqli_real_escape_string($db, $_POST["cost"]);
      $modDurationH = mysqli_real_escape_string($db, $_POST["durationH"]);
      $modDurationM = mysqli_real_escape_string($db, $_POST["durationM"]);
      $modSynopsis = mysqli_real_escape_string($db, $_POST["synopsis"]);
      $modImage = mysqli_real_escape_string($db, $_POST["image"]);

      /*Only upload the file if there is infact a new image*/
      if($_POST['imageChanged'] == '1')
      {
         if(isset($_FILES['newImage']))
         {
            if(file_exists($_FILES['newImage']['tmp_name'][0]))
            {
               $file_tmp =$_FILES['newImage']['tmp_name'];
               $file_type=$_FILES['newImage']['type'];
               $file_ext_tmp=explode('.',$_FILES['newImage']['name']);
               $file_ext=strtolower(end($file_ext_tmp));

               /*Set the name to be movie ID + the uploaded file extension*/
               $modImage = $modMovieID.".".$file_ext;

               /*Delete the current movie image if it exists*/
               shell_exec("rm ../img/movies/$modMovieID.* 2>/dev/null");

               /*Finally upload the image*/
               move_uploaded_file($file_tmp,"../img/movies/".$modImage);
            }
         }
      }

      $sql = "UPDATE Movies SET name='$modName', score='$modScore', rating='$modRating', cost='$modCost', image='$modImage', 
               durationH='$modDurationH', durationM='$modDurationM', synopsis='$modSynopsis' WHERE movieID = '$modMovieID'";

      /*If any errors occur print them out to the administrator (can go to page since only admin can access it anyway)*/
      if(!mysqli_query($db, $sql))
      {
         $error = true;
      }

   }
   elseif(isset($_POST['movieDeleted']))
   {
      $delMovieID = mysqli_real_escape_string($db, $_POST["movieID"]);

      $sql = "DELETE FROM Movies WHERE movieID = $delMovieID";
      mysqli_query($db, $sql);
   }
   elseif(isset($_POST['movieCreated']))
   {

      /*Get the next movieID that is to be used*/
      $result = mysqli_query($db, "SHOW TABLE STATUS LIKE 'Movies'");
      $data = mysqli_fetch_assoc($result);
      $nextMovieID = $data['Auto_increment'];

      /*Get all POST values and allow the use of quotes without breaking it*/
      $newName = mysqli_real_escape_string($db, $_POST["name"]);
      $newScore = mysqli_real_escape_string($db, $_POST["score"]);
      $newRating = mysqli_real_escape_string($db, $_POST["rating"]);
      $newCost = mysqli_real_escape_string($db, $_POST["cost"]);
      $newDurationH = mysqli_real_escape_string($db, $_POST["durationH"]);
      $newDurationM = mysqli_real_escape_string($db, $_POST["durationM"]);
      $newSynopsis = mysqli_real_escape_string($db, $_POST["synopsis"]);

      /*Set movie image to be default one if none is selected*/
      $newImage = "default.jpg";

      /*If there was an image uploaded then get it*/
      if(isset($_FILES['image']))
      {
         if(file_exists($_FILES['image']['tmp_name'][0]))
         {
            $file_tmp =$_FILES['image']['tmp_name'];
            $file_type=$_FILES['image']['type'];
            $file_ext_tmp=explode('.',$_FILES['image']['name']);
            $file_ext=strtolower(end($file_ext_tmp));

            $newImage = $nextMovieID.".".$file_ext;

            shell_exec("rm ../img/movies/$nextMovieID.* 2>/dev/null");

            move_uploaded_file($file_tmp,"../img/movies/".$newImage);
         }
      }

      $sql = "INSERT INTO Movies (movieID, name, score, rating, cost, image, durationH, durationM, synopsis) VALUES 
               ('$nextMovieID', '$newName', '$newScore', '$newRating', '$newCost', '$newImage', '$newDurationH', '$newDurationM', '$newSynopsis')";
      
      /*If any errors occur print them out to the administrator (can go to page since only admin can access it anyway)*/
      if(!mysqli_query($db, $sql))
      {
         echo mysqli_error($db);
         $error = true;
      }
   }
?>

<!DOCTYPE html>
<html>
<head>
   <title>Assignment: Edit Movies</title>
   <meta charset="UTF-8"> 

   <?php include_once('../php/imports.php'); ?> 

   <script type="text/javascript">

      var movies;
      var movieNames = [];

      $(document).ready( function()
      {
         /*Store all movie names in the array*/
         movies = $("tbody tr");
         $(movies).each(function(ii)
         {
            movieNames[ii] = $(this).attr('data-name')
         })

         /*Update the text of the input field on file choice*/
         $('#newMovieImage').on('change', function()
         {
            $('#newMovieImageText').text(this.files[0].name)
         })

         /*Update the text of the input field on file choice*/
         $('#modMovieImage').on('change', function()
         {
            $('#modMovieImageText').text(this.files[0].name)
         })

         /*Tell the form if the image has been changed or not*/
         $('#modalEditMovie').on('hidden.bs.modal', function()
         {
            $('#currentImage').show()
            $('#newImage').hide()
            $('#imageChanged').val('0');
         })

         /*When a key has been pressed change the real time searching*/
         $('#movieSearch').keyup(function()
         {
            var text = $(this).val();
            var background = 0.5
            /*Go thorugh all movies*/
            $(movieNames).each(function(ii)
            {
               /*If movie name contains match then show*/
               var match = this.toLowerCase().match(text);
               if(match)
               {
                  $(movies[ii]).show();

                  /*Alternate between 2 types of background to create stripped effect*/
                  if(background)
                     $(movies[ii]).css("background-color","rgba(255,255,255,0.05)")
                  else
                     $(movies[ii]).css("background-color","transparent")

                  /*Revert iy*/
                  background = !background;
               }
               else /*Hide the movie for the time being*/
                  $(movies[ii]).hide();
            })
         })
      })


      /*Show the modal that is used to edit an existing movie*/
      function openModal(row)
      {

         /*Fill the modal with values that relate to the selected movie*/
         var children = row.children;
         var modal = $('#modalEditMovie');

         modal.find('#movieID').val($(children['movieID']).html());
         modal.find('#name').val($(children['name']).html());
         modal.find('#score').val($(children['score']).html());
         
         var rating = $(children['rating']).html();

         /*Cant have spaces in the ID for unknown reasons. So this fixes it :) */
         switch(rating)
         {
            case "MA 15+":
               rating = "MA";
               break;
            case "R 18+":
               rating = "R";
               break;
         }

         modal.find('#opt'+rating).prop('selected', 'selected');

         modal.find('#cost').val($(children['cost']).html());
         modal.find('#durationH').val($(children['durationH']).html());
         modal.find('#durationM').val($(children['durationM']).html());


         modal.find('#image').val($(children['image']).html());
         modal.find('#synopsis').val($(children['synopsis']).html());

         /*Show the movie*/
         $('#modalEditMovie').modal('show');
      }

      /*Show the modal that is used to create a movie*/
      function createMovie()
      {
         $('#modalCreateMovie').modal('show');  
      }

      /*Confirm that a movie is to be deleted and if so run the form*/
      function verifyDelete(form)
      {
         if (confirm("Are you sure you want to delete this"))
         {

            /*Append an input field that is used to show it is to be deleted*/
            var customVar = document.createElement("input");

            customVar.type = "hidden";
            customVar.name = "movieDeleted";

            form.appendChild(customVar);
            form.submit();
         }
      }

      /*Hide the name of the current image and allow the user to select a new image*/
      function editImage()
      {
         $('#currentImage').hide()
         $('#newImage').show()
         $('#imageChanged').val('1');
      }

   </script>
</head>
<body>
   <?php  
      $currentPage = "admin";  
      include('../php/navbar.php'); 
   ?>

   <div class="container">
      <div class="row">
         <div class="col float-left">
            <h2 class="">Edit Movies</h2>
         </div>
         <div class="col">
            
         </div>
         <div class="col-md-auto align-middle" style="margin: auto">
            <?php if($_SESSION['access'] == 'admin'){ ?>
               <button type="button" class="btn btn-success btn-sm" onclick="createMovie()">Add Movie</button>
            <?php }else{?>
               <button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" data-placement="left" title="Only admin can create movies" disabled>Add Movie</button>
            <?php } ?>
         </div>
         <div class="col-md-auto align-middle" style="margin: auto">
            <input type="text" class="form-control form-control-sm" style="width: 14rem;" id="movieSearch" placeholder="Search Movies" autofocus>
         </div>
      </div>
      <br>
      <div class="table-responsive" style="border-radius: .3rem">
         <table class="table table-striped table-dark">
            <thead>
               <tr>
                  <th>Movie ID</th>
                  <th>Name</th>
                  <th>Score</th>
                  <th>Rating</th>
                  <th>Cost</th>
                  <th>DurationH</th>
                  <th>DurationM</th>
                  <th>Image</th>
                  <th class="d-none">Synopsis</th>
               </tr>
            </thead>   
            <tbody class="bg-dark">
               <?php 
                  $sql = "SELECT * FROM Movies";

                  $results = mysqli_query($db, $sql);

                  while($row = mysqli_fetch_assoc($results)) : ?>
                     <tr onclick="openModal(this)" data-name="<?php echo $row['name']; ?>"> 
                        <td id="movieID"><?php echo $row["movieID"]; ?></td>
                        <td id="name"><?php echo $row["name"]; ?></td>
                        <td id="score"><?php echo $row["score"]; ?></td>
                        <td id="rating"><?php echo $row["rating"]; ?></td>
                        <td id="cost"><?php echo $row["cost"]; ?></td>
                        <td id="durationH"><?php echo $row["durationH"]; ?></td>
                        <td id="durationM"><?php echo $row["durationM"]; ?></td>
                        <td id="image"><?php echo $row["image"]; ?></td>
                        <td id="synopsis" class="d-none"><?php echo $row["synopsis"]; ?></td>
                     </tr>
                  <?php endwhile ;?>
            </tbody>
         </table>
      </div>
   </div>

   <div class="modal fade centerModal" id="modalEditMovie" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <form class="form w-100" method="POST" enctype="multipart/form-data">
            <div class="modal-content bg-dark w-100">
               <div class="modal-header" style="border-color: rgba(0,0,0,.125)">
                  <h5 class="modal-title" id="exampleModalLabel">Modify Movie</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div>
                     <div class="form-group row">
                        <label class="col-2 col-form-label" style="margin-right: 3px">Movie ID: </label>
                        <div class="col-3">
                           <input type="text" readonly name="movieID" class="form-control" id="movieID">
                        </div>
                        <div class="col">
                           <label>The ID is automatically generated and cannot be changed</label>
                        </div>
                     </div>
                     <div class="form-group row">
                        <label class="col-2 col-form-label" style="margin-right: 3px">Name: </label>
                        <div class="col">
                           <input type="text" name="name" class="form-control"  id="name">
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Score: </label>
                           <div class="col">
                              <input type="number" name="score" class="form-control"  id="score" step="0.1" min="1" max="10">
                           </div>
                        </div>
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Rating: </label>
                           <div class="col">
                              <select name="rating" class="form-control">
                                 <option value="G" id="optG">G</option>
                                 <option value="PG" id="optPG">PG</option>
                                 <option value="M" id="optM">M</option>
                                 <option value="MA 15+" id="optMA">MA 15+</option>
                                 <option value="R 18+" id="optR">R 18+</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">DurationH: </label>
                           <div class="col">
                              <input type="number" name="durationH" class="form-control"  id="durationH" min="0">
                           </div>
                        </div>
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">DurationM: </label>
                           <div class="col">
                              <input type="number" name="durationM" class="form-control"  id="durationM" min="0">
                           </div>
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Cost: </label>
                           <div class="col input-group">
                              <div class="input-group-prepend">
                                 <span class="input-group-text">$</span>
                              </div>
                              <input type="number" name="cost" class="form-control"  id="cost" min="0">
                           </div>
                        </div>
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Image: </label>
                           <input type="hidden" name="imageChanged" value="0" id="imageChanged">
                           <div class="col input-group" id="currentImage">
                              <input type="text" class="form-control" name="image" id="image" readonly>
                              <div class="input-group-append">
                                 <button class="btn btn-light" type="button" onclick="editImage()">Edit</button>
                              </div>
                           </div>
                           <div class="col custom-file" id="newImage" style="display: none"> 
                             <input type="file" class="custom-file-input" name="newImage" id="modMovieImage">
                             <label class="custom-file-label" for="movieImage" id="modMovieImageText" style="margin: 0 .8rem">Choose file</label>
                           </div>
                        </div>
                     </div>
                     <div class="form-group row">
                        <label class="col-4 col-form-label">Synopsis: </label>
                        <div class="col-12">
                           <textarea type="text" class="form-control" name="synopsis" id="synopsis" placeholder="No synopsis has been submitted as of yet." rows="5"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer" style="border-color: rgba(0,0,0,.125)">
                  <?php if($_SESSION['access'] == 'admin'){ ?>
                     <button type="button" class="btn btn-danger" onclick="verifyDelete(this.form)">Delete</button>
                  <?php }else{?>
                     <button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="left" title="Only admin can delete" disabled>Delete</button>
                  <?php } ?>
                  <button type="submit" class="btn btn-success" name="movieModded">Save changes</button>
               </div>
            </div>
         </form>
      </div>
   </div>

   <div class="modal fade centerModal" id="modalCreateMovie" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <form class="form w-100" method="POST" enctype="multipart/form-data"> 
            <div class="modal-content bg-dark w-100">
               <div class="modal-header" style="border-color: rgba(0,0,0,.125)">
                  <h5 class="modal-title" id="exampleModalLabel">Add New Movie</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div>
                     <div class="form-group row">
                        <label class="col-2 col-form-label" style="margin-right: 3px">Movie ID: </label>
                        <div class="col-3">
                           <input required type="text" readonly name="movieID" class="form-control" id="movieID">
                        </div>
                        <div class="col">
                           <label>The ID is automatically generated and cannot be changed</label>
                        </div>
                     </div>
                     <div class="form-group row">
                        <label class="col-2 col-form-label" style="margin-right: 3px">Name: </label>
                        <div class="col">
                           <input required type="text" name="name" class="form-control"  id="name">
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Score: </label>
                           <div class="col">
                              <input required type="number" name="score" class="form-control"  id="score" step="0.01" min="1" max="10">
                           </div>
                        </div>
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Rating: </label>
                           <div class="col">
                              <select name="rating" class="form-control">
                                 <option value="G" id="optG">G</option>
                                 <option value="PG" id="optPG">PG</option>
                                 <option value="M" id="optM">M</option>
                                 <option value="MA 15+" id="optMA">MA 15+</option>
                                 <option value="R 18+" id="optR">R 18+</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">DurationH: </label>
                           <div class="col">
                              <input required type="number" name="durationH" class="form-control"  id="durationH" min="0">
                           </div>
                        </div>
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">DurationM: </label>
                           <div class="col">
                              <input required type="number" name="durationM" class="form-control"  id="durationM" min="0">
                           </div>
                        </div>
                     </div>
                     <div class="form-row">
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Cost: </label>
                           <div class="col input-group">
                              <div class="input-group-prepend">
                                 <span class="input-group-text">$</span>
                              </div>
                              <input required type="number" name="cost" class="form-control"  id="cost" min="0">
                           </div>
                        </div>
                        <div class="form-group row col">
                           <label class="col-4 col-form-label">Image: </label>
                           <div class="col custom-file">
                             <input required type="file" class="custom-file-input" name="image" id="newMovieImage">
                             <label class="custom-file-label" for="movieImage" id="newMovieImageText" style="margin: 0 .8rem">Choose file</label>
                           </div>
                        </div>

                     </div>
                     <div class="form-group row">
                        <label class="col-4 col-form-label">Synopsis: </label>
                        <div class="col-12">
                           <textarea type="text" class="form-control" name="synopsis" id="synopsis" placeholder="Enter synopsis here" rows="5"></textarea>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer" style="border-color: rgba(0,0,0,.125)">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-success" name="movieCreated">Save changes</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <br>
   <?php include('../php/errorModal.php') ?>
</body>
</html>

