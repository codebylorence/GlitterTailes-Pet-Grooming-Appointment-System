<?php 
require './read.php';

if (isset($_POST['editId'])) {
    $editId = $_POST['editId'];
    $editFirstname = $_POST['editFirstname'];
    $editLastname = $_POST['editLastname'];
    $editUsername = $_POST['editUsername'];
    $editPassword = $_POST['editPassword'];
}

if (isset($_POST['update'])) {
    $updateId = $_POST['updateId'];
    $updateFirstname = $_POST['updateFirstname'];
    $updateLastname = $_POST['updateLastname'];
    $updateUsername = $_POST['updateUsername'];
    $updatePassword = $_POST['updatePassword'];

    // Hash the password before updating
    $hashedPassword = password_hash($updatePassword, PASSWORD_DEFAULT);

    // Prepare and execute the update query
    $queryUpdate = "UPDATE userAccounts 
                    SET Firstname = ?, Lastname = ?, Username = ?, Password = ? 
                    WHERE UserId = ?";
    $stmt = $connection->prepare($queryUpdate);
    $stmt->bind_param("ssssi", $updateFirstname, $updateLastname, $updateUsername, $hashedPassword, $updateId);

    if ($stmt->execute()) {
        echo '<script>alert("Successfully updated!")</script>';
        echo '<script>window.location.href = "/GlitterTails/admin/petOwner.php"</script>';
    } else {
        echo '<script>alert("Error updating account: ' . $stmt->error . '")</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Account</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: #EBEBEB;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .main {
      background: #fff;
      padding: 30px 40px;
      border-radius: 10px;
      box-shadow: 0px 10px 15px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
    }

    .main h3 {
      margin-bottom: 20px;
      font-size: 24px;
      text-align: center;
      color: #333;
    }

    .main form {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .main input[type="text"],
    .main input[type="password"] {
      padding: 10px;
      font-size: 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
      outline: none;
      transition: border-color 0.3s;
    }

    .main input[type="text"]:focus,
    .main input[type="password"]:focus {
      border-color: #66a6ff;
    }

    .main input[type="submit"] {
      background: #66a6ff;
      color: #fff;
      font-size: 16px;
      border: none;
      border-radius: 5px;
      padding: 10px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .main input[type="submit"]:hover {
      background: #4a90e2;
    }
  </style>
</head>

<body>
  <div class="main">
    <form class="update-main" action="./update.php" method="post">
      <h3>EDIT USER ACCOUNT</h3>
      <input type="text" name="updateFirstname" placeholder="Enter the firstname" value="<?php echo $editFirstname ?>" required>
      <input type="text" name="updateLastname" placeholder="Enter the lastname" value="<?php echo $editLastname ?>" required>
      <input type="text" name="updateUsername" placeholder="Enter the username" value="<?php echo $editUsername ?>" required>
      <input type="password" name="updatePassword" placeholder="Enter a new password" value="" required>
      <input type="submit" name="update" value="UPDATE">
      <input type="hidden" name="updateId" value="<?php echo $editId ?>">
    </form>
  </div>
</body>

</html>

