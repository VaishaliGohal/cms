<?php 

    session_start();

	include_once('../connection/connect.php');

    if(isset($_SESSION['login'])){
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
	<link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

	<div>
	<a href="../index.php">Home</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
	</div>

    <div class="container">
        <ul>
            <li><a href="add.php">Create post</a></li>
            <li><a href="update.php">Update a Recipe</a></li>
            <li><a href="delete.php">Delete a Recipe</a></li>
            <li><a href="categories.php">Add or Update Cuisines</a></li>
            <li><a href="../users/manage_users.php">Manage Users</a></li>
            <li><a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a></li>
        </ul>
    </div>
</body>
</html>

<?php
    }

    else if(isset($_SESSION['user_login'])){
?>

<!DOCTYPE html>
<html lang=en>
<head>
	<meta charset="utf-8">
	<title>User Account Info</title>
	<link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

    <div>
	    <a href="../index.php">Home</a>
        <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
	</div>

    <div class="container">
        <h2>User Account Info</h2>
        <ul>
            <li><a href="users/watchlist.php">Watchlist</a></li>
            <li><a href="logout.php" onclick="return confirm('Are you sure?')">Logout</a></li>

        </ul>
    </div>
</body>
</html>

<?php
    }
    else{
        if(isset($_POST['username']) && isset($_POST['password'])){
            $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

            if(empty($username) || empty($password)){
                $error = "*All fields are required!";
            }
            else{
                $query = $db->prepare("SELECT * FROM user WHERE username = ? AND password = ?");
                $query->bindValue(1, $username);
                $query->bindValue(2, $password);

                $query->execute();
                $data = $query->fetch();

                if(password_verify($password, $data['password'])){
                    if($data['role'] == "1"){
                        $_SESSION['login'] = $username;
                        echo '<h3>Admin Login Successful</h3>';
                        header("refresh:2;url=index.php");
                        exit();
                    }
                    else if($data['role'] == "0"){
                        $_SESSION['user_login'] = $username;
                        echo '<h3>User Login Successful</h3>';
                        header("refresh:2;url=index.php");
                        exit();
                    }
                }  
                else{
                    $error = "Oops!! Your Credentials are Incorrect!";
                }
            }
        }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Admin Login</title>
	<link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    <div class="container">
        <h2>Enter Login Information:</h2><br>
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        <form action="index.php" method="post" autocomplete="off">
            <input type="text" name="username" placeholder="Username" class="w3-input w3-animate-input" style="width:40%"/><br/>
            <input type="password" name="password" placeholder="Password" class="w3-input w3-animate-input" style="width:40%"/><br/>
            <input type="submit" value="Login" />
        </form>
        <br /><br />
        <a href="../users/add_users.php">Sign Up</a>
    </div>
</body>
</html>

<?php
    }
?>
