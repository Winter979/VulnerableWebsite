<?php

/* 
 * @Author: Zazu
 * @Date:   2018-09-04 22:07:30
 * @Last Modified by:   Zazu
 * @Last Modified time: 2018-10-17 23:27:34
*/

?>

<nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
   <div class="container">
      <a class="navbar-brand" href="/index.php">World Wide Weebs - <?php echo $_SESSION['name']; ?></a>
      <div class="navbar-collapse ">
      <ul class="navbar-nav mr-auto">
         <li class="nav-item <?php if($currentPage == "index"){ echo "active";} ?>">
            <a class="nav-link" href="/">Home</a>
         </li>
         <li class="nav-item <?php if($currentPage == "movies"){ echo "active";} ?>">
            <a class="nav-link" href="/movies.php">Movies</a>
         </li>
         <li class="nav-item dropdown <?php if($currentPage == "account"){ echo "active";} ?>" style="cursor: default">
            <a class="nav-link dropdown-toggle" id="accountDropdown" data-toggle="dropdown">My Account</a>
            <div class="dropdown-menu" aria-labelledby="accountDropdown">
               <a class="dropdown-item" href="/account.php?view=details">Details</a>
               <a class="dropdown-item" href="/account.php?view=purchases">Purchases</a>
               <a class="dropdown-item" href="/account.php?view=reviews">Reviews</a> 
            </div>
         </li>
         <li class="nav-item <?php if($currentPage == "report"){ echo "active";} ?>">
            <a class="nav-link" href="/Report/report.pdf">Report</a>
         </li>
      </ul>
      <ul class="navbar-nav">
         <?php
         /*If the current sessions access level is either admin or moderator, then display the admin button*/ 
         if(in_array($_SESSION['access'], array('admin','moderator'))) : ?>
         <li class="nav-item dropdown <?php if($currentPage == "admin"){ echo "active";} ?>" style="cursor: default">
            <a class="nav-link dropdown-toggle" id="adminDropdown" data-toggle="dropdown">Admin</a>
            <div class="dropdown-menu" aria-labelledby="adminDropdown">
               <a class="dropdown-item" href="/admin/editUsers.php">Edit Users</a>
               <a class="dropdown-item" href="/admin/editMovies.php">Edit Movies</a>
               <?php if ($_SESSION['access'] == 'admin'): ?>
                  <a class="dropdown-item" href="/admin/viewTables.php">View Tables</a>
               <?php endif; ?>
            </div>
         </li>
         <?php endif; ?>
         <li class="nav-item ">
            <a class="nav-link" href="/php/logout.php">Logout</a>
         </li>
      </ul>
      </div>
  </div>
</nav>


