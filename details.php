<?php

/*
 * @Author: Zazu
 * @Date:   2018-09-04 22:25:38
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:44:51
*/

if (session_status() == PHP_SESSION_NONE) 

{
   header("location: /account.php?view=details");
   die();
}

/*Array to store the return error of the update if any*/
$errors = array("balance"=>"", "password"=>"");

$balance = $_SESSION['balance'];

if(isset($_POST["changePassword"]))
{

   $sql = "SELECT password FROM Users WHERE userID = '$userID'";

   if($result = mysqli_query($db, $sql))
   {
      $row = mysqli_fetch_assoc($result);

      $passwordCurrent = cleanInput($_POST['currentPassword']);

      $password1 = cleanInput($_POST['password1']);
      $password2 = cleanInput($_POST['password2']);

      if($password1 != $password2){
         $errors['password'] = "The passwords dont match";
      }
      elseif(md5($passwordCurrent) == $row['password']) {
         $hashPassord = md5($password1);

         $sql = "UPDATE Users Set password='$hashPassord' WHERE userID = '$userID'";
         if(!mysqli_query($db, $sql)){
            $errors['password'] = "Unexpected error occured";
            $error = true;
         }
         else{
            $errors['password'] = "Password successfully updated";
         }
      }
      else
      {
         $errors['password'] = "Incorrect password";
      }
   }
   else
   {
      $error = true;
   }

}
elseif(isset($_POST["addBalance"]))
{
   
   $sql = "UPDATE Users SET balance='".$_POST['newBalance']."' WHERE userID = '$userID'";

   if(!mysqli_query($db, $sql))
   {
      $errors["balance"] = "Unexpected error occured";
      $error = true;
   }
   else
   {
      $balance = $_SESSION['balance'] = cleanInput($_POST['newBalance']);

      $errors["balance"] = "Balance successfully updated";

      if(isset($_POST["returnAddress"]))
      {   
         $address = $_POST['returnAddress'];      
         header("location: $address");
         die();
      }
   }
   
}
elseif(isset($_FILES['newPhoto']))
{
   /*Check that the file exists. Refreshing page after upload didnt resend photo*/
   if(file_exists($_FILES['newPhoto']['tmp_name'][0]))
   {
      $file_tmp =$_FILES['newPhoto']['tmp_name'];
      $file_type=$_FILES['newPhoto']['type'];
      $file_ext_tmp=explode('.',$_FILES['newPhoto']['name']);
      $file_ext=strtolower(end($file_ext_tmp));

      /*Change the filename to the UserID but maintain the extension*/
      /*A list of allowed extensions need to be created. ATM just allow them all*/
      $file_name = $userID.".".$file_ext;         

      /*Delete the current profile pic*/
      shell_exec("rm img/profile/$userID.* 2>/dev/null");

      /*Upload the file*/
      move_uploaded_file($file_tmp,"img/profile/".$file_name);

      $sql = "UPDATE Users SET image = '$file_name' WHERE userID = '$userID'";

      mysqli_query($db, $sql);

      /*Set the new image for the image locally for the time being*/
      $user['image']=$file_name;
   }
}

$sql = "SELECT * FROM Users WHERE userID = '$userID'";
if($results = mysqli_query($db, $sql))
   $user = mysqli_fetch_assoc($results);
else
   $error = true;
?>

<script type="text/javascript">
   $(document).ready(function()
   {
      $('#addBalance').change(function()
      {
         var newBalance = +this.value + +<?php echo $balance ?> 
         console.log(newBalance);
         $('#newBalance').val(newBalance);
      });

      $('#newPhoto').on('change', function()
      {
         console.log(this.files[0].name)
         $('#imgText').val(this.files[0].name)
      })
   });

   function selectImage()
   {
      $('#newPhoto').click();
   }

   function submitImage(form)
   {
      if($('#imgText').val() == '')
         selectImage();
      else
         form.submit();
   }

</script>

<style type="text/css">
   .imgContainer{
      position: relative;
      max-height: 350px;
      overflow: hidden;
   }

   .imgContainer img{
      position: relative;
      top:-100%; left:0; right: 0; bottom:-100%;
      margin: auto;
   }
</style>

<h2>Your Details</h2>

<br>

<div class="row">
   <div class="col">
      <div class="card bg-dark">
         <div class="card-header">
            <h3>My Details</h3>
         </div>
         <div class="card-body row">
            <div class="col-lg-4 col-md-5">
               <div class="imgContainer w-100">
                  <img src="img/profile/<?php echo $user['image'] ?>" alt="" class="w-100">
               </div>
               <hr>
               <form method="POST" enctype="multipart/form-data">
                  <input type="file" name="newPhoto" id="newPhoto" value="default.jpg" required style="display: none">
                  <div class="input-group">
                     <input type="text" id="imgText" class="form-control" placeholder="Click to select new image" readonly style="cursor: pointer;" onclick="selectImage()">
                     <div class="input-group-append">
                        <button class="btn btn-success form-control" type="submit">Confirm</button>
                     </div>
                  </div>
               </form>
            </div>
            <div class="col">
               <div class="form-group row">
                  <label class="col-lg-3 col-md-4 col-sm-5 col-form-label text-right" for="username">User ID: </label>
                  <div class="col">
                     <input type="text" class="form-control" name="userID" readonly value="<?php echo $user['userID']?>">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-md-4 col-sm-5 col-form-label text-right" for="username">Username: </label>
                  <div class="col">
                     <input type="text" class="form-control" name="username" readonly value="<?php echo $user['username']?>">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-md-4 col-sm-5 col-form-label text-right" for="firstname">Firstname: </label>
                  <div class="col">
                     <input type="text" class="form-control" name="firstname" readonly value="<?php echo $user['name']?>">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-md-4 col-sm-5 col-form-label text-right" for="email">Email: </label>
                  <div class="col">
                     <input type="text" class="form-control" name="email" readonly value="<?php echo $user['email']?>">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-lg-3 col-md-4 col-sm-5 col-form-label text-right" for="access">Access: </label>
                  <div class="col">
                     <input type="text" class="form-control" name="access" readonly value="<?php echo $user['access']?>">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<br>
<div class="row">
   <div class="col-md-6">
      <div class="card bg-dark">
         <div class="card-header">
            <h3>Change Password</h3>
         </div>
         <div class="card-body">
            <form class="form" method="POST">
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label text-right" for="currentPassword">Current Password: </label>
                  <div class="col-sm-8">
                     <input type="password" class="form-control" name="currentPassword" autocomplete="off">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label text-right" for="password2">New Password: </label>
                  <div class="col-sm-8">
                     <input type="password" class="form-control" name="password1">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label text-right" for="password2">Confirm: </label>
                  <div class="col-sm-8">
                     <input type="password" class="form-control" name="password2">
                  </div>
               </div>
               <hr/>
               <div class="form-group form-inline mb-0">
                  <label class="col col-form-label"><?php echo $errors['password'] ?></label> 
                  <button type="submit" class="btn btn-success btn-lg float-right" name="changePassword">Submit</button>
               </div>
            </form>
         </div>
      </div>
   </div>
   <div class="col-md-6 col-md-offset-2">
      <div class="card bg-dark">
         <div class="card-header">
            <h3>Your Funds</h3>
         </div>
         <div class="card-body">
            <form class="form" method="POST">
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label text-right" for="currBalance">Current Funds: </label>
                  <div class="col-sm-8 input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                     </div>
                     <input type="number" class="form-control" name="currBalance" value="<?php echo $user['balance']; ?>" readonly>
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label text-right" for="addBalance">Add Balance: </label>
                  <div class="col-sm-8 input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                     </div>
                     <input type="number" class="form-control" name="addBalance" id="addBalance" value="0" required min="0">
                  </div>
               </div>
               <div class="form-group row">
                  <label class="col-sm-4 col-form-label text-right" for="newBalance">New Balance: </label>
                  <div class="col-sm-8 input-group">
                     <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                     </div>
                     <input type="number" required readonly class="form-control" name="newBalance" id="newBalance" value="<?php echo $user['balance']; ?>" >
                  </div>
               </div>
               <hr/>
               <?php if(isset($_POST['needFunds'])){?>
                  <input type="text" hidden name="returnAddress" value="<?php echo $_POST['needFunds'] ?>">
               <?php } ?>
               <div class="form-group form-inline mb-0">
                  <label class="col col-form-label"><?php echo $errors['balance'];?></label>                  
                  <button type="submit" class="btn btn-success btn-lg float-right">Submit</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>