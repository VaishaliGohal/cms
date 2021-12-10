<?php

    session_start();

    include_once('../connection/connect.php');

    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
        $query = $db->prepare("SELECT * FROM user  WHERE role = 0");
        $query->execute();
        $users = $query->fetchAll();

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Manage Users</title>
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
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        <main>
            <div>
                <section>
                <div>
                    <h2>All Users</h2>
                    <a href="add_users.php">Add User</a>
                    <br /><br />

                    <table>
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>User Name</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['full_name'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><a href="edit_users.php?id=<?= $user['user_id'] ?>">Edit</a></td>
                            <td><a href="delete_users.php?id=<?= $user['user_id'] ?>">Delete</a></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                    </table>
                </div>
                </section>
            </div>
        </main>
    </div>
</body>
</html>

<?php

    }
    else{
        header("Location: index.php");
    }

?>