<!DOCTYPE html>
<html>

<head>
    <title>Өгөгдөл авах хуудас</title>
    <style>
        body {
            font-family: 'Courier New';
            font-size: 20px;
            text-align: center;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        #form_container {
            width: 75%;
            margin: auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        #button_container {
            width: 500px;
            margin: auto;
            display: flex;
            margin-top: 1%;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 15px;
        }

        label {
            font-size: 20px;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            font-family: 'Courier New';
            font-size: 18px;
            padding: 10px;
            margin-top: 10px;
            border: solid #464646;
            border-radius: 5px;
            border-width: 1px;
            width: 94%;
        }

        .form_elm {
            width: 350px;
            margin: 10px;
            border: 1px solid #545454;
            border-radius: 10px;
            padding: 20px;
        }

        .form_button {
            font-family: 'Courier New';
            font-size: 18px;
            color: white;
            background-color: #4caf50;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .form_button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div id="form_container">
        <form method="post" action="index.php" class="form_elm">
            <h2>Хэрэглэгчийн мэдээлэл</h2>
            <label for="name">Нэр:</label>
            <input type="text" id="name" name="name" required autocomplete="off">

            <label for="email">И-майл:</label>
            <input type="email" id="email" name="email" required autocomplete="off">

            <label for="userId">Хэрэглэгчийн ID:</label>
            <input type="text" id="userId" name="userId" required autocomplete="off">

            <label for="imageUpload">Зурагаа сонгоно уу</label>
            <input type="file" id="imageUpload" name="imageUpload" accept="image/*">

            <input type="submit" name="submit_user" value="Оруулах" class="form_button">
        </form>
    </div>
</body>

</html>


<?php
$host = 'db';  // Docker Compose service name
$db = 'mydatabase';
$user = 'db_user';
$pass = 'db_password';
$charset = 'utf8mb4';

$mysqli = new mysqli($host, $user, $pass, $db);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Create tables if not exist
$mysqli->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100)
)");

$mysqli->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_name VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
)");

$mysqli->query("CREATE TABLE IF NOT EXISTS user_img (
    img_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    img_data LONGBLOB,
    FOREIGN KEY (user_id) REFERENCES users(id)
)");


if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['submit_user'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $userId = $_POST['userId'];

        $userInsertQuery = "INSERT INTO users (name, email, userId) VALUES ('$name', '$email', '$userId')";

        if ($mysqli->query($userInsertQuery) === TRUE) {
            echo "<br>Хэрэглэгчийн мэдээллийг амжилттай нэмлээ";
        } else {
            echo "Алдаа: " . $userInsertQuery . "<br>" . $mysqli->error;
        }
        if (isset($_FILES['imageUpload']) && $_FILES['imageUpload']['error'] === UPLOAD_ERR_OK) {
            $imgTmpName = $_FILES['imageUpload']['tmp_name'];
            $imgData = file_get_contents($imgTmpName);

            $originalImage = imagecreatefromstring(file_get_contents($imgTmpName));

            $width = imagesx($originalImage);
            $height = imagesy($originalImage);

            $newWidth = 200;
            $newHeight = 200;

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            $compressedImage = 'compressed_image.jpg';
            imagejpeg($resizedImage, $compressedImage, 75);

            $imgData = file_get_contents($compressedImage);

            $stmt = $mysqli->prepare("INSERT INTO user_img (user_id, img_data) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $imgData);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo "Зураг амжилттай нэмэгдлээ";
            } else {
                echo "Алдаа_1" . $mysqli->error;
            }

            imagedestroy($originalImage);
            imagedestroy($resizedImage);
            unlink($compressedImage);
        } else {
            echo "Алдаа_2";
        }
    }
}
$mysqli->close();
