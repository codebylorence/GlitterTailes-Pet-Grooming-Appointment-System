
<?php 
  require'../db/read.php';
  require'../session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <link rel="stylesheet" href="../styles/pet-owner.css">
  <style>
    h1{
      margin-bottom: 20px;
    }

    *{
      margin: 0;
      padding: 0;
    }

    body{
      margin-left: 280px;
      margin-right: 40px;
      margin-top: 20px;
      background-color: #EBEBEB;
    }

    table th{
      color: #003366;
    }
  </style>
  <title>Admin</title>
</head>
<body>
    <div>
      <section class="side-bar">
        <div class="admin-profile">
          <div class="admin-info">
             <div class="img-container">
              <div class="img-border">
                <img src="../assets/people.png" class="admin-img">
              </div>
             </div>
             <div class="admin-acc">
              <p class="admin-name"><?php echo $_SESSION['Username']; ?></p>
              <p class="admin-email">admin@sstails.com</p>
             </div>
          </div>
          <div>
          <form action="../logout.php" method="post">
            <input type="submit" value="Log out" class="logout-btn">
          </form>
        </div>
        </div>
        <hr class="line">
        <div class="sidebar-btns">
          <div class="side-btn-container dashboard-js"><div><img src="../assets/menu.png" class="img-btn"></div><div class="side-btn">Dashboard</div></div>
          <div class="side-btn-container schedule-js"><div><img src="../assets/event.png" class="img-btn"></div><div class="side-btn">Schedule</div></div>
          <div class="side-btn-container appointment-js"><div><img src="../assets/ribbon.png" class="img-btn"></div><div class="side-btn">Appointment</div></div>
          <div class="side-btn-container petowners-js"><div><img src="../assets/dog.png" class="img-btn"></div><div class="side-btn">Pet Owners</div></div>
          </div>
      </section>
      <section>
        <h1>Pet Owner's Accounts</h1>
    <table class="read-main" border="1" style="background-color: white; border: none;   border-collapse: collapse; width: 100%;">
      <tr>
        <th class="usertable-title">ID</th>
        <th class="usertable-title">FULL NAME</th>
        <th class="usertable-title">USERNAME</th>
        <th class="usertable-title">PASSWORD</th>
        <th class="usertable-title">ACTION</th>
      </tr>
      <?php while ($results = mysqli_fetch_array($sqlAccounts)) { ?>
        <tr>
          <td class="text-center"><?php echo $results['UserId'] ?></td>
          <td class="text-center"><?php echo $results['Firstname'] ?> <?php echo $results['Lastname'] ?></td>
          <td class="text-center"><?php echo $results['Username'] ?></td>
          <td class="text-center"><?php echo $results['Password'] ?></td>
          <td>
            <div class="action-btn">
              <form action="../db/update.php" method="post">
              <input type="submit" name="edit" value="Edit" class="edit-btn">
              <input type="hidden" name="editId" value="<?php echo $results['UserId'] ?>">
              <input type="hidden" name="editFirstname" value="<?php echo $results['Firstname'] ?>">
              <input type="hidden" name="editLastname" value="<?php echo $results['Lastname'] ?>">
              <input type="hidden" name="editUsername" value="<?php echo $results['Username'] ?>">
              <input type="hidden" name="editPassword" value="<?php echo $results['Password'] ?>">
            </form>
            </form>
            <form action="../db/delete.php"  method="post">
              <input type="submit" name="delete" value="Delete" class="delete-btn"  onclick="return confirm('Are you sure you want to delete this account?');">
              <input type="hidden" name="deleteId" value="<?php echo $results['UserId'] ?>">
            </form>
            </div>
        </td>
        </tr>
      <?php } ?>
    </table>
  </section>
      <script src="../script/adminRedirect.js"></script>
    </div>
</body>
</html>