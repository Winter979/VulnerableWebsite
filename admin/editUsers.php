<!--
 * @Author: zazu
 * @Date:   2018-08-30 02:08:49
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:17:49
-->

<?php   
   require_once("../php/verifyLogin.php"); 

   $keys = array("userID", "username", "name", "email", "access", "balance", "image",);

   if (isset($_POST['userModded']))
   {
      $modUserID = $_POST["userID"];
      $modUsername = $_POST["username"];
      $modName = $_POST["name"];
      $modEmail = $_POST["email"];
      $modAccess = $_POST["access"];
      $modBalance = $_POST["balance"];
   
      $sql = "UPDATE Users SET username='$modUsername', email='$modEmail', access='$modAccess', balance='$modBalance', name='$modName' WHERE userID = '$modUserID'";

      if(!mysqli_query($db, $sql))
      {
         $error = true;
      }

      if(isset($_FILES['newPhoto']) && file_exists($_FILES['newPhoto']['tmp_name'][0]))
      {
         $fileTmp =$_FILES['newPhoto']['tmp_name'];
         $fileType=$_FILES['newPhoto']['type'];
         $fileExtTmp=explode('.',$_FILES['newPhoto']['name']);
         $fileExt=strtolower(end($fileExtTmp));

         /*Change the filename to the UserID but maintain the extension*/
         /*A list of allowed extensions need to be created. ATM just allow them all*/
         $fileName = $modUserID.".".$fileExt;         

         /*Delete the current profile pic*/
         shell_exec("rm ../img/profile/$modUserID.* 2>/dev/null");

         /*Upload the file*/
         move_uploaded_file($fileTmp,"../img/profile/".$fileName);

         $sql = "UPDATE Users SET image = '$fileName' WHERE userID = '$modUserID'";

         mysqli_query($db, $sql);
         /*Set the new image for the image locally for the time being*/
         $user['image']=$fileName;
      }

   }
   elseif(isset($_POST['userDeleted']))
   {
      $delUserID = $_POST["userID"];

      if($delUserID != $_SESSION['userID'])
      {
         $sql = "DELETE FROM Users WHERE userID ='$delUserID'";
         if(!mysqli_query($db, $sql)) 
            $error = true;
      }
      else
         $error = true;
   }
   elseif(isset($_POST['resetPassword']))
   {
      $sql = "UPDATE Users SET password = DEFAULT WHERE userID = ".$_POST['userID'];

      if(!mysqli_query($db, $sql)) 
         $error = true;

   }
   elseif(isset($_POST['userCreated']))
   {
      $modUsername = $_POST["username"];
      $modName = $_POST["name"];
      $modEmail = $_POST["email"];
      $modAccess = $_POST["access"];
      $modBalance = $_POST["balance"];
      $modImage = $_POST["image"];

      $sql = "INSERT INTO Users (username, name, email, access, balance, image) VALUES ('$modUsername','$modName', '$modEmail', '$modAccess', '$modBalance', '$modImage')";

      if(!mysqli_query($db, $sql))
         $error = true;
   }
?>

<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs: Edit Users</title>
   <meta charset="UTF-8"> 

   <?php include("../php/imports.php") ?>

   <script type="text/javascript">
      $(document).ready(function()
      {
         $('#newPhoto').on('change', function()
         {
            console.log(this.files[0].name)
            $('#imgText').val(this.files[0].name)
         })
      });

      /*Open up the modal to edit users and populate it with the right information*/
      function openModal(row)
      {

         var children = row.children;
         var modal = $('#modalEditUser');

         modal.find('#userID').val($(children['userID']).html());
 
 
         modal.find('#username').val($(children['username']).html())
         modal.find('#email').val($(children['email']).html())

         var access = $(children['access']).html()
         if(access == "admin")
            modal.find('#optAdmin').prop('selected', 'selected');
         else if(access == "moderator")
            modal.find('#optModerator').prop('selected', 'selected');
         else /*Access must be user but to be safe select user for any unknown */
            modal.find('#optUser').prop('selected', 'selected');
         
         modal.find("img").attr("src", "/img/profile/"+$(children['image']).html())

         var name = $(children['name']).html()

         modal.find("#name").val(name)

         modal.find('#balance').val($(children['balance']).html())
 
         $('#modalEditUser').modal('show')
      }

      function resetPassword(form)
      {
         if(confirm("Are you sure you want to reset their password?"))
         {

            var newInput = document.createElement('input');

            newInput.type = 'hidden';
            newInput.name = "resetPassword";

            form.appendChild(newInput);
            form.submit();
         }
      }

      /*Show the create user modal*/
      function createUser()
      {
         $('#modalCreateUser').modal('show')  
      }

      /*Confirm to delete selected user*/
      function verifyDelete(form)
      {
         if (confirm("Are you sure you want to delete this user?"))
         {

            /*Add input field with name userDeteled. Means can use current form*/
            var customVar = document.createElement("input");

            customVar.type = "hidden";
            customVar.name = "userDeleted";

            form.appendChild(customVar)
            form.submit()
         }
      }

   </script>

</head>
<body>
   <?php  
      $currentPage = "admin";  
      include('../php/navbar.php'); 
   ?>

   <div class="container">
      <div class="row justify-content-between">
         <div class="col-md-auto">
            <h2>Edit Users</h2>
         </div>
         <div class="col-md-auto">
            <?php if($_SESSION['access'] == 'admin'){ ?>
               <button type="button" class="btn btn-success btn-sm" style="margin-top: 0.5rem" onclick="createUser()">Add User</button>
            <?php }
            else{?>
               <button type="button" class="btn btn-success btn-sm" style="margin-top: 0.5rem" data-toggle="tooltip" data-placement="left" title="Only admin can create users" disabled>Add User</button>
            <?php } ?>
         </div>   
      </div>
      <br>
      <div class="table-responsive" style="border-radius: .3rem">
         <table class="table table-striped table-dark">
            <thead>
               <tr>
                  <?php 
                     foreach($keys as $key)
                        echo "<th>$key</th>";
                   ?>
               </tr>
            </thead>   
            <tbody class="bg-dark" style="cursor: pointer">
               <?php 
                  $sql = "SELECT userID, username, name, email, access, balance, image FROM Users";

                  $results = mysqli_query($db, $sql);

                  while($row = mysqli_fetch_assoc($results))
                  { 
                     echo "<tr onclick=\"openModal(this)\">"; 
                        
                     for ($ii=0; $ii < count($keys); $ii++) { ?> 
                        <td id="<?php echo $keys[$ii];?>"><?php echo $row["$keys[$ii]"];?></td>
                     <?php }
                     echo "</tr>";
                  }
                ?>
            </tbody>
         </table>
      </div>
   </div>

   <div class="modal fade centerModal" id="modalEditUser" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
         <form class="form w-100" method="POST" enctype="multipart/form-data">
            <div class="modal-content bg-dark">
               <div class="modal-header" style="border-color: rgba(0,0,0,.125)">
                  <h5 class="modal-title" id="exampleModalLabel">Modify User</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div class="container-fluid">
                     <div class="row">   
                        <div class="col-lg-5 mb-4">
                           <div class="imgContainer w-100">
                              <img src="/img/profile/default.jpg" alt="" class="w-100">
                           </div>
                           <hr>
                           <div class="input-group">
                              <input type="file" name="newPhoto" id="newPhoto" value="default.jpg" style="display: none">
                              <div class="input-group">
                                 <input type="text" id="imgText" class="form-control" placeholder="Click to select new image" readonly style="cursor: pointer;" onclick="$('#newPhoto').click()">
                                 <div class="input-group-append">
                                    <button class="btn btn-light" type="button" for="newPhoto" onclick="$('#newPhoto').click()">Browse</button>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-7">
                           <div class="form-group row">
                              <label class="col-4 col-form-label text-right">User ID: </label>
                              <div class="col">
                                 <input type="text" readonly name="userID" class="form-control"  id="userID">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-4 col-form-label text-right">User Name: </label>
                              <div class="col">
                                 <input type="text" name="username" class="form-control"  id="username">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-4 col-form-label text-right">Name: </label>
                              <div class="col">
                                 <input type="text" name="name" class="form-control"  id="name">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-4 col-form-label text-right">Email: </label>
                              <div class="col">
                                 <input type="email" name="email" class="form-control"  id="email">
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-4 col-form-label text-right">Access: </label>
                              <div class="col">
                                 <select name="access" class="form-control">
                                    <option value="user" id="optUser" selected>User</option>
                                    <option value="moderator" id="optModerator">Moderator</option>
                                    <option value="admin" id="optAdmin">Admin</option>
                                 </select>
                              </div>
                           </div>
                           <div class="form-group row">
                              <label class="col-4 col-form-label text-right">Balance: </label>
                              <div class="col input-group">
                                 <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                 </div>
                                 <input type="number" name="balance" class="form-control"  id="balance" min="0">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="modal-footer" style="border-color: rgba(0,0,0,.125)">
                  <div class="float-left w-100">
                     <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                     <button type="button" class="btn btn-secondary" onclick="resetPassword(this.form)">Reset Password</button>
                  </div>
                  <?php if($_SESSION['access'] == 'admin'){ ?>
                     <button type="button" class="btn btn-danger" onclick="verifyDelete(this.form)">Delete</button>
                  <?php }
                  else{?>
                     <button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="left" title="Only admin can delete" disabled>Delete</button>
                  <?php } ?>
                  <button type="submit" class="btn btn-success" name="userModded">Save changes</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <?php 
   /*Only include create Modal if access is admin*/
   if($_SESSION['access'] == 'admin'): ?>
      <div class="modal fade centerModal" id="modalCreateUser" tabindex="-1">
         <div class="modal-dialog modal-dialog-centered" role="document">
            <form class="form w-100" method="POST">
               <div class="modal-content bg-dark">
                  <div class="modal-header" style="border-color: rgba(0,0,0,.125)">
                     <h5 class="modal-title" id="exampleModalLabel">Create User</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                       <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <div class="container-fluid">
                        <div class="form-group row">
                           <label class="col-3 col-form-label text-right">User ID: </label>
                           <div class="col-9">
                              <input type="text" readonly disabled name="userID" class="form-control" id="userID" placeholder="Auto Assigned">
                           </div>
                        </div>
                        <div class="form-group row">
                           <label class="col-3 col-form-label text-right">User Name: </label>
                           <div class="col-9">
                              <input type="text" name="username" class="form-control" required id="username">
                           </div>
                        </div>
                        <div class="form-group row">
                           <label class="col-3 col-form-label text-right">Name: </label>
                           <div class="col-9">
                              <input type="text" name="name" class="form-control"  id="name">
                           </div>
                        </div>
                        <div class="form-group row">
                           <label class="col-3 col-form-label text-right">Email: </label>
                           <div class="col-9">
                              <input type="email" name="email" class="form-control" required id="email">
                           </div>
                        </div>
                        <div class="form-group row">
                           <label class="col-3 col-form-label text-right">Access: </label>
                           <div class="col-9">
                              <select name="access" class="form-control">
                                 <option value="user" id="optUser" selected>User</option>
                                 <option value="admin" id="optModerator">Moderator</option>
                                 <option value="admin" id="optAdmin">Admin</option>
                              </select>
                           </div>
                        </div>
                        <div class="form-group row">
                           <label class="col-3 col-form-label text-right">Balance: </label>
                           <div class="col input-group">
                              <div class="input-group-prepend">
                                 <span class="input-group-text">$</span>
                              </div>
                              <input type="number" name="balance" class="form-control" required id="balance" value="0" placeholder="DEFAULT: 0">
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer" style="border-color: rgba(0,0,0,.125)">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-success" name="userCreated">Create</button>
                  </div>
               </div>
            </form>
         </div>
      </div>
   <?php endif; ?>
   <?php include('../php/errorModal.php') ?>
</body>
</html>

