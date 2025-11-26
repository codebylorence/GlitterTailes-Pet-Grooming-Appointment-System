<?php 
  require'database.php';


  // $sort = 'ASC';
  // $column = 'UserId';

  // if (isset($_GET['column']) && isset($_GET['sort'])) {
  //   $column = $_GET['column'];
  //   $sort = $_GET['sort'];

  //   // Opposite
  //   $sort == 'ASC' ? $sort = 'DESC' : $sort = 'ASC'; 
  // }
  // $queryAccounts = "SELECT * FROM accounts ORDER BY $column $sort";
  $queryAccounts = "SELECT * FROM useraccounts";
  $sqlAccounts = mysqli_query($connection, $queryAccounts);

?>