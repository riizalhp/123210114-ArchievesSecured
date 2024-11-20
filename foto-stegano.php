<?php
session_start();
if (empty($_SESSION['username'])) {
    header("location:index.php?pesan=belum_login");
}
include "koneksi.php";
if (isset($_SESSION['id'])) {
    $id_user = $_SESSION['id'];
} else {
    die('User ID is not set in session.');
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_file = $_FILES['foto']['name'];
    $pesan_tersembunyi = $_POST['pesan'];
    $id_user = $_SESSION['id'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($nama_file);
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        // Perform steganography to hide message in image
        steg_hide($target_file, $pesan_tersembunyi, $target_file);

        // Read the stego image as binary data
        $foto_stego = file_get_contents($target_file);

        // Insert file and message to database
        $stmt = $konek->prepare("INSERT INTO datafoto (id_user, nama_foto, foto_stego, pesan_tersembunyi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id_user, $nama_file, $foto_stego, $pesan_tersembunyi);

        if ($stmt->execute()) {
            echo "File and message successfully uploaded and saved.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

function steg_hide($inputFile, $message, $outputFile) {
    // Mengambil gambar dan mengubah ke format GD image
    $image = createImageFromFile($inputFile);
    if (!$image) {
        die('Gagal membuka gambar');
    }

    // Mengambil lebar dan tinggi gambar
    $width = imagesx($image);
    $height = imagesy($image);

    // Mengubah pesan menjadi biner
    $message .= '|'; // Tambahkan karakter akhir untuk menandai akhir pesan
    $binaryMessage = '';
    for ($i = 0; $i < strlen($message); $i++) {
        $binaryMessage .= str_pad(decbin(ord($message[$i])), 8, '0', STR_PAD_LEFT);
    }

    $messageLength = strlen($binaryMessage);
    if ($messageLength > ($width * $height)) {
        die('Pesan terlalu panjang untuk disisipkan ke dalam gambar.');
    }

    $counter = 0;
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            if ($counter < $messageLength) {
                $rgb = imagecolorat($image, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // Mengubah bit paling tidak signifikan (LSB) pada komponen biru
                $newB = ($binaryMessage[$counter] == '1') ? ($b | 1) : ($b & ~1);

                $newColor = imagecolorallocate($image, $r, $g, $newB);
                imagesetpixel($image, $x, $y, $newColor);

                $counter++;
            }
        }
    }

    // Menyimpan gambar output sesuai format aslinya
    $outputExtension = strtolower(pathinfo($outputFile, PATHINFO_EXTENSION));
    switch ($outputExtension) {
        case 'jpeg':
        case 'jpg':
            imagejpeg($image, $outputFile);
            break;
        case 'png':
            imagepng($image, $outputFile);
            break;
        case 'gif':
            imagegif($image, $outputFile);
            break;
        default:
            die('Format gambar output tidak didukung.');
    }

    imagedestroy($image);
}

function createImageFromFile($filePath) {
    // Mendapatkan informasi gambar, termasuk tipe MIME
    $imageInfo = getimagesize($filePath);

    if ($imageInfo === false) {
        // Jika file tidak bisa dibaca atau bukan gambar yang valid
        die('Gagal membuka file gambar.');
    }

    // Menentukan tipe MIME dari gambar
    $mimeType = $imageInfo['mime'];

    // Menggunakan fungsi yang sesuai untuk membuat resource gambar
    switch ($mimeType) {
        case 'image/jpeg':
            return imagecreatefromjpeg($filePath);
        case 'image/png':
            return imagecreatefrompng($filePath);
        case 'image/gif':
            return imagecreatefromgif($filePath);
        default:
            // Jika format gambar tidak didukung
            die('Format gambar tidak didukung.');
    }
}

function steg_extract($inputFile) {
    // Membuka gambar yang telah disisipkan pesan
    $image = createImageFromFile($inputFile);
    if (!$image) {
        die('Gagal membuka gambar');
    }

    // Mengambil lebar dan tinggi gambar
    $width = imagesx($image);
    $height = imagesy($image);

    $binaryMessage = '';
    for ($y = 0; $y < $height; $y++) {
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($image, $x, $y);
            $b = $rgb & 0xFF;

            // Mengambil bit paling tidak signifikan (LSB) dari komponen biru
            $binaryMessage .= ($b & 1) ? '1' : '0';

            // Memeriksa apakah kita sudah menemukan tanda akhir pesan '|'
            if (strlen($binaryMessage) % 8 == 0) {
                $character = chr(bindec(substr($binaryMessage, -8)));
                if ($character == '|') {
                    imagedestroy($image);
                    // Menghapus tanda akhir pesan dan mengembalikan pesan asli
                    return bin2str(substr($binaryMessage, 0, -8));
                }
            }
        }
    }

    imagedestroy($image);
    return bin2str($binaryMessage);
}
