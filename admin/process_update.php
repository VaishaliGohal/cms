<?php 

    session_start();

	include_once('../connection/connect.php');
	include_once('../connection/post.php');

    $post = new Post;

    $query = $db->prepare("SELECT * FROM cusine");
    $query->execute();
    $cuisines = $query->fetchAll();

    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
        if(isset($_GET['id'])){
            $id = $_GET['id'];

            $data = $post->fetch_join_data($id);
        }

        if(isset($_POST['title'], $_POST['description'])){

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = $_POST['description']; // Can not validate because of the use of WYSIWYG
            $cuisine = filter_input(INPUT_POST, 'cuisine', FILTER_VALIDATE_INT);
            $old_img = $_POST['old_image'];

            if(isset($_FILES['image'])){
                $file_name = $_FILES['image']['name'];
                $file_size = $_FILES['image']['size'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_type = $_FILES['image']['type'];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $extensions = array("jpeg", "jpg", "png", "");
                 
                if(in_array($file_ext, $extensions) === true){
                    move_uploaded_file($file_tmp, "../uploads/".$file_name);
                }
            }

            if(isset($_POST['delete'])){
                $data = $post->fetch_join_data($id);
                unlink("../uploads/".$data['image']);
            }

            if(empty($title) || empty($description) || empty($cuisine)){
                $error = "*All fields are required!";
                $data = $post->fetch_join_data($id);
            }
            else if(in_array($file_ext, $extensions) === false){
                $error = "File extension is not allowed. Allowed extension: JPG or PNG.";
                $data = $post->fetch_join_data($id);
            }
            else{
                $slug = slug($title);

                $query = $db->prepare("UPDATE post SET title=?, description=?, image=?, cuisine_id=?, slug = ?  WHERE post_id = ?");

                $query->bindValue(1, $title);
                $query->bindValue(2, $description);
                if(empty($file_name)){
                    $query->bindValue(3, $old_img);
                }
                else{
                    $query->bindValue(3, $file_name);
                }
                $query->bindValue(4, $cuisine);
                $query->bindValue(5, $slug);
                $query->bindValue(6, $id);


                $query->execute();

                if($_POST['delete'] == 'yes'){
                    $img_del = $db->prepare("UPDATE post SET image = '' WHERE post_id = $id");
                    $img_del->execute();
                }

                $query2 = $db->prepare("UPDATE cusine SET post = post + 1 WHERE cuisine_id = $cuisine");
                $query2->execute();

                header("Location: update.php");
            }
        }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Update Recipe</title>
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
        <h2>Update Recipe </h2><br />
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        <form action="process_update.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="id" value="<?= $data['post_id'] ?>" />
            <input type="text" name="title" value="<?= $data['title'] ?>" /><br /><br />
            <select name="cuisine">
                <option value="<?= $data['cuisine_id'] ?>" selected><?= $data['cuisines'] ?></option>
                <?php foreach($cuisines as $cuisine): ?>
                    <option value="<?= $cuisine['cuisine_id'] ?>"><?= $cuisine['cuisines'] ?></option>
                <?php endforeach ?>
            </select><br /><br />
            <textarea id="description" name="description"><?= $data['description'] ?></textarea><br /><br />
            <input type="file" name="image" /><br /><br />
            <p><?= $data['image'] ?></p>
            <img src="../uploads/<?= $data['image'] ?>" alt="<?= $data['image'] ?>" height="150px" />
            <br /> <br />
            <?php if(!empty($data['image'])): ?>
            <input type="checkbox" name="delete" value="yes" />
            <label>delete image</label>
            <?php endif ?>
            <br /> <br />
            <input type="hidden" name="old_image" value="<?= $data['image'] ?>"/>
            <input type="submit" value="Update Recipe" />
        </form>
    </div>

    <script src="ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>

</body>
</html>

<?php
    }
    else{
        header("Location: index.php");
    }

?>