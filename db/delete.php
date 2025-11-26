<?php 
  require'database.php';

  if (isset($_POST['delete'])){
    $deleteId = $_POST['deleteId'];

    $queryCreate =  "DELETE FROM userAccounts WHERE UserId = $deleteId ";
    $sqlCreate = mysqli_query($connection, $queryCreate);

    echo '<script>alert("Successfully deleted!")</script>';
    echo '<script>window.location.href = "/GlitterTails/admin/petOwner.php"</script>';
  } else {
    echo '<script>window.location.href = "/GlitterTails/admin/petOwner.php"</script>';
  }
  
?>