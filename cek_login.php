<?php

session_start();

function hashPassword($password)
{
    $hashedPassword = hash('sha256', $password);
    return $hashedPassword;
}

$username = $_POST['username'];
$password = $_POST['password'];

include "koneksi.php";

$user = mysqli_query($konek, "SELECT * FROM user WHERE username = '$username'");

if ($user) {
    $datauser = mysqli_fetch_array($user);

    $db_password = $datauser['password'];

    if (hashPassword($password) == $db_password) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = "login";
        $_SESSION['id'] = $datauser['id_user'];
        header("location:main.php");
    } else {
        header("location:index.php?pesan=gagal");
    }
} else {
    header("location:index.php?pesan=gagal");
}
