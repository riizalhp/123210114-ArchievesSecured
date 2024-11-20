<?php

session_start();
if (empty($_SESSION['username'])) {
    header("location:index.php?pesan=belum_login");
}

function encryptFile($inputFile, $outputFile, $key)
{
    $iv = random_bytes(16);
    $fileData = file_get_contents($inputFile);
    $encryptedData = openssl_encrypt($fileData, 'aes-256-cbc', $key, 0, $iv);
    file_put_contents($outputFile, $iv . $encryptedData);
}

function handleFileUpload($inputFieldName, $uploadDirectory, $encryptionKey)
{
    if (isset($_FILES[$inputFieldName])) {
        $file = $_FILES[$inputFieldName];
        $originalFileName = basename($file['name']);
        $tempFilePath = $file['tmp_name'];

        $uploadPath = $uploadDirectory . '/' . $originalFileName;

        $encryptedFilePath = $uploadDirectory . '/encrypted_' . $originalFileName;

        encryptFile($tempFilePath, $encryptedFilePath, $encryptionKey);

        include "koneksi.php";

        $id = $_SESSION['id'];
        $query = mysqli_query($konek, "INSERT INTO datafile (id_user, nama_file, aeskey) VALUES('$id','$originalFileName','$encryptionKey')") or die(mysqli_error($konek));

        header("location:storage.php");
    } else {
        echo "File tidak ditemukan.\n";
    }
}

$uploadDirectory = 'uploads';
$encryptionKey = $_POST['key'];

if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}

handleFileUpload('fileToUpload', $uploadDirectory, $encryptionKey);
