<?php 
  session_start();

  function pathTo($destination) {
    echo "<script>window.location.href = './$destination.php'</script>";
  }

  /* Set status to invalid */
  $_SESSION['status'] = 'invalid';

  /* Unset user data */
  unset($_SESSION['Username']);

  /* Redirect to login page */
  pathTo('index');
?>