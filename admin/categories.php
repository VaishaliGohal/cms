<?php

    session_start();

    include_once('../connection/connect.php');

    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
        $query = $db->prepare("SELECT * FROM cusine");
        $query->execute();
        $cuisines = $query->fetchAll();

?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Cuisines</title>
	<link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

	<div>
	<a href="../index.php">Home</a>
    <a href="index.php">Dashboard</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
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
                    <h2>All Cuisines</h2>
                    <a href="add_category.php">Add a New Cuisine</a>
                    <br /><br />

                    <table>
                    <thead>
                        <tr>
                            <th>Cuisines</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cuisines as $cuisine): ?>
                        <tr>
                        <td><?= $cuisine['cuisines'] ?></td>
                        <td><a href="edit_category.php?id=<?= $cuisine['cuisine_id'] ?>">Edit</a></td>
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