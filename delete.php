<?php

session_start();
if (empty($_SESSION['username'])) {
    header("location:index.php?pesan=belum_login");
}

// Fungsi untuk menghapus file
function deleteFile($filePath)
{
    if (file_exists($filePath)) {
        unlink($filePath);
    } else {
        echo "File tidak ditemukan.\n";
    }
}

include "koneksi.php";

if (isset($_GET['id_text'])) {
    $id = $_GET['id_text'];
    $query = mysqli_query($konek, "DELETE FROM datatext WHERE id_text = '$id'") or die(mysqli_error($konek));
    if ($query) {
        header("location:storage.php");
    } else {
        echo "Proses hapus gagal";
    }
}

if (isset($_GET['id_file'])) {
    $id = $_GET['id_file'];
    $file = mysqli_query($konek, "SELECT * FROM datafile WHERE id_file = '$id'") or die(mysqli_error($konek));
    $datafile = mysqli_fetch_array($file);
    $query = mysqli_query($konek, "DELETE FROM datafile WHERE id_file = '$id'") or die(mysqli_error($konek));
    if ($query) {
        $encryptedFilePath = 'uploads/encrypted_' . $datafile['nama_file'];
        $decryptedFilePath = 'uploads/decrypted_' . $datafile['nama_file'];
        deleteFile($encryptedFilePath);
        deleteFile($decryptedFilePath);
        header("locaton:storage.php");
    }
}
