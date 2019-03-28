<?php

/*  
 * @Author: zazu
 * @Date:   2018-08-30 02:09:59
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-18 21:02:36
*/

   include_once("config.php");
   session_start();

   /*Redirect if already logged in*/
   if(isset($_SESSION['userID']))
      header("location: index.php");

   /*Array to store all the errors*/
   $loginErrors = "";
   $registerErrors = array();

   $username = "";
   $name = "";
   $email = "";

   /* The user has attempted to Login */
   if(isset($_POST["loginForm"]))
   {

      $username = cleanInput($_POST['username']);
      $password = cleanInput($_POST['password']); 
      
      $sql = "SELECT * FROM Users WHERE username = '$username'";
      $results = mysqli_query($db,$sql);

      if(!$results)
         $error = true;
      else
      {
         $count = mysqli_num_rows($results);
         /*If a result has been found then the user exists*/
         if($count != 0)
         {
            $row = mysqli_fetch_assoc($results);
            if(md5($password) == $row['password'])
            {
               $_SESSION['userID'] = $row['userID'];
               /*If they were redirected to the login page then return to the page*/
               if(isset($_GET['redirect']))
                  header("location: ".urldecode($_GET['redirect']));
               else
                  header("location: index.php");
               die();
            }
            else
               $loginErrors =  "Invalid password for user: $username";
         }
         else
            $loginErrors =  "No user exists with that username";
      }
   }
   /* The user has attempted to make an account*/
   else if(isset($_POST["registerForm"]))
   {
      $username = cleanInput($_POST['username']);
      $name = cleanInput($_POST['name']);
      $email = cleanInput($_POST['email']);
      $password1 = cleanInput($_POST['password1']);
      $password2 = cleanInput($_POST['password2']);

      /*Check if the fields are invalid. If so error*/
      if(empty($username)) 
         array_push($registerErrors, "Username is required"); 
      if(empty($name)) 
         array_push($registerErrors, "Firstname is required"); 
      if(empty($email))
         array_push($registerErrors, "Email is required"); 
      if($password1 != $password2)
         array_push($registerErrors, "Your passwords do not match");

      /*Check if duplicate Username exists*/
      $dupUserCheck = "SELECT * FROM Users WHERE username = '$username'";
      
      if(!$results = mysqli_query($db, $dupUserCheck))
         $error = true;
      else
      {
         $userCheck = mysqli_fetch_assoc($results);
         
         if (mysqli_num_rows($userCheck) == 1 ) 
            array_push($registerErrors, "Username is already taken");

         /*There are no errors. So create the account and log in*/
         if(count($errors) == 0)
         {

            $hashPassord = md5($password1);

            $query = "INSERT INTO Users (username, password, email, name) 
               VALUES ('$username', '$hashPassord', '$email', '$name')";

            if(mysqli_query($db, $query))
            {
               $query = "SELECT userID FROM Users WHERE username = '$username' ";
               $results = mysqli_query($db, $query);
               $row = mysqli_fetch_assoc($results);

               $_SESSION['userID'] = $row['userID'];
               /*If they were redirected to the login page then return to the page*/
               if(isset($_GET['redirect']))
                  header("location: ".urldecode($_GET['redirect']));
               else
                  header("location: index.php");
               die();
            }
            else
               $error = true;
         }
      }
   }

?>
