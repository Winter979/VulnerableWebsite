<!-- 
 * @Author: zazu
 * @Date:   2018-08-30 01:51:42
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:22:31
-->

<?php 
   require_once("php/server.php");

   $page = "login";
   if(isset($_POST['registerForm'])){
      $page = "register";
   }

   /*If trying to register then show the register form instead*/
   if($page == "login"){
      $loginContStyle = "block";
      $registerContStyle = "none";   
   }else{
      $loginContStyle = "none";
      $registerContStyle = "block";  
   }

 ?>

<!DOCTYPE html>
<html>
<head>
   <title>World Wide Weebs - Login</title>
   
   <meta charset="UTF-8"> 

   <?php include("php/imports.php") ?>

   <script type="text/javascript">
      function changeTab(tabName)
      {
         switch(tabName)
         {
            case 'register':
               $('#registerTab').show();
               $('#loginTab').hide();
               break;
            case 'login':
               $('#loginTab').show();
               $('#registerTab').hide();
               break;
         }
      }
   </script>

   <style type="text/css">
         
      body,html{
         height: 100%;
      }

   </style>
</head>
<body>
   <div class="container h-100">
      <div class="row h-100 justify-content-center align-items-center">
         <div class="col-6" id="loginTab" style="display: <?php echo $loginContStyle ?>">
            <div class="card bg-dark">
               <div class="card-header">
                  <h3>Log in</h3>
               </div>
               <div class="card-body">
                  <form class="form" role="form" id="formLogin" method="POST">
                     <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control form-control-lg" name="username" id="username" value="<?php echo $username ?>" required <?php if($page == "login") {echo "autofocus";} ?>>
                     </div>
                     <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" class="form-control form-control-lg" name="password" id="password">
                     </div>
                     <div class="form-group text-danger text-center">
                        <?php if($loginErrors != "") echo $loginErrors; ?>
                     </div>
                     <div class="form-group">
                        <button type="button" class="btn btn-primary float-left" id="btnLogin" onclick="changeTab('register')" style="margin-top: 5px">Dont have an account</button>
                        <button type="submit" class="btn btn-success btn-lg float-right" name="loginForm" id="btnLogin">Login</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <div class="col-6" id="registerTab" style="display: <?php echo $registerContStyle ?>">
            <div class="card bg-dark">
               <div class="card-header">
                  <h3>Register</h3>
               </div>
               <div class="card-body">
                  <form class="form" role="form" id="formRegister" method="POST">
                     <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" class="form-control form-control-lg" name="username" id="username" required value=<?php echo $username; ?> <?php if($page == "server") {echo "autofocus";} ?>>
                     </div>
                     <div class="form-group">
                        <label for="firstname">Name:</label>
                        <input type="text" class="form-control form-control-lg" name="name" id="name" required value=<?php echo $name; ?>>
                     </div>
                     <div class="form-group">
                        <label for="password1">Password:</label>
                        <input type="password" class="form-control form-control-lg" name="password1" id="password1">
                     </div>
                     <div class="form-group">
                        <label for="password2">Confirm Password:</label>
                        <input type="password" class="form-control form-control-lg" name="password2" id="password2">
                     </div>
                     <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control form-control-lg" name="email" id="email" required value=<?php echo $email; ?>>
                     </div>
                     <div class="form-group text-danger text-center">
                        <?php if(count($registerErrors) != 0):?>
                           <label>
                              <?php 
                                 foreach ($registerErrors as $e) {
                                    echo $e."<br>";
                                 }
                              ?>    
                           </label>
                        <?php endif; ?>
                     </div>
                     <button for="registerForm" type="button" class="btn btn-primary btn float-left" id="btnLogin" onclick="changeTab('login')" style="margin-top: 5px">Already have an account</button>
                     <button type="submit" class="btn btn-success btn-lg float-right" name="registerForm" id="btnLogin">Register</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php include('php/errorModal.php') ?>
</body>
</html>
