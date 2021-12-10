<?php

session_start();

include_once('../connection/connect.php');


    if(isset($_SESSION['login'])){
        if(isset($_GET['id'])){

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id || empty($id)) {
                echo '<script>alert("Incorrect ID passed!!!")</script>';
                exit;
            }

            $query = $db->prepare("SELECT * FROM user WHERE user_id = ?");
            $query->bindValue(1, $id);
            $query->execute();
            $users = $query->fetch();
        }

            if(isset($_POST['username'], $_POST['username'], $_POST['fullname'], $_POST['email'])){

                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $username = filter_input(INPUT_POST, 'user', FILTER_SANITIZE_STRING);
                $fullname = filter_input(INPUT_POST, 'fullname', FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $role = filter_input(INPUT_POST, 'role', FILTER_VALIDATE_INT);


                if(empty($username) || empty($fullname) || empty($email)){
                    $error = "All fields are required!";

                    $query = $db->prepare("SELECT * FROM user WHERE user_id = ?");
                    $query->bindValue(1, $id);
                    $query->execute();
                    $users = $query->fetch();
                }
                else{
                    $query1 = $db->prepare("UPDATE user SET username=?, full_name=?, email=?, role=? WHERE user_id = ?;");
                    $query1->bindValue(1, $username);
                    $query1->bindValue(2, $fullname);
                    $query1->bindValue(3, $email);
                    $query1->bindValue(4, $role);
                    $query1->bindValue(5, $id);

                    $query1->execute();

                    header("Location: manage_users.php");
                }
            }


?>

<!DOCTYPE html>
<html lang=en>
<head>
	<meta charset="utf-8">
	<title>Edit Genre</title>
	<link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div>
        <a href="../index.php">Home</a>
        <a href="index.php">Dashboard</a>
        <a href="../admin/logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
    </div>
    
    <div class="container">
        <h2>Edit User</h2><br />
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>

        <form action="edit_users.php" method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?= $users['user_id'] ?>" />
            <input class="w3-input" type="text" name="username" value="<?= $users['username'] ?>" /><br /><br />
            <input class="w3-input" type="text" name="fullname" value="<?= $users['full_name'] ?>" /><br /><br />
            <input class="w3-input" type="text" name="email" value="<?= $users['email'] ?>" /><br /><br />
            <select class="w3-select" name="role">
                <option value="0" selected>User</option>
                <option value="1">Admin</option>
            </select><br /><br />
            <input type="submit" value="Edit User" />
        </form><br /> <br />

        <a href="manage_users.php">Back</a>
    </div>
</body>
</html>

<?php

    }
    else{
        header("Location: ../admin/index.php");
    }

?>