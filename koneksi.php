<?php

$konek = new mysqli("localhost", "root", "", "db_projek_kripto");

if ($konek->connect_error) {
    die("Maaf Koneksi gagal!" . $konek->connect_error);
}
