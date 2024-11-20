<?php
session_start();

function hashPassword($password)
{
    $hashedPassword = hash('sha256', $password);
    return $hashedPassword;
}

$username = $_POST['username'];
$password = $_POST['password'];
$confpassword = $_POST['confirmpassword'];

if ($password == $confpassword) {
    include "koneksi.php";

    $passwordToEncrypt = $confpassword;
    $encryptedPassword = hashPassword($passwordToEncrypt);

    $query = mysqli_query($konek, "INSERT INTO user (username, password) VALUES('$username','$encryptedPassword')") or die(mysqli_error($konek));

    if ($query) {
        header("location:index.php?pesan=terdaftar");
    } else {
        header("location:signup.php?pesan=gagal");
    }
} else {
    header("location:signup.php?pesan=gagal");
}
