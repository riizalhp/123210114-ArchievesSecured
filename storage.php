<?php
session_start();
if (empty($_SESSION['username'])) {
    header("location:index.php?pesan=belum_login");
}

function caesarCipherDecrypt($ciphertext, $shift)
{
    $result = "";

    for ($i = 0; $i < strlen($ciphertext); $i++) {
        $char = $ciphertext[$i];

        if (ctype_alpha($char)) {
            $offset = ord(ctype_upper($char) ? 'A' : 'a');
            $result .= chr(($offset + (ord($char) - $offset - $shift + 26) % 26));
        } else {
            $result .= $char;
        }
    }

    return $result;
}

function aesDecrypt($ciphertext, $key)
{
    $decipherText = openssl_decrypt(base64_decode($ciphertext), 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
    return $decipherText;
}

function superDecrypt($ciphertext, $caesarShift, $aesKey)
{
    $aesDecrypted = aesDecrypt($ciphertext, $aesKey);

    $superDecrypted = caesarCipherDecrypt($aesDecrypted, $caesarShift);

    return $superDecrypted;
}

function decryptFile($inputFile, $outputFile, $key)
{
    $fileData = file_get_contents($inputFile);
    $iv = substr($fileData, 0, 16);
    $encryptedData = substr($fileData, 16);
    $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
    file_put_contents($outputFile, $decryptedData);
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



// Fungsi untuk mengonversi biner menjadi string
function bin2str($binary) {
    $text = '';
    for ($i = 0; $i < strlen($binary); $i += 8) {
        $text .= chr(bindec(substr($binary, $i, 8)));
    }
    return $text;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page</title>
    <link rel="shortcut icon"  href="logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>
    <div class="container">
        <header class="d-flex flex-wrap justify-content-center py-4 mb-4 border-bottom">
            <a href="main.php" class="d-flex align-items-center mb-4 mb-md-0 me-md-auto text-dark text-decoration-none" style="padding-right: 15px;">

                <span class="fs-2">Archives Secured</span>
            </a>

            <div class="dropdown my-2">
                <button class="btn btn-white btn-lg dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello, <?php echo $_SESSION['username']; ?>!
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="main.php">Save Archives</a></li>
                    <li><a class="dropdown-item" href="storage.php">Saved Archives</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
    </div>
    </div>
    </header>
    </div>
    <h1 style="padding-top:50px;font-family: 'Times New Roman', Times, serif; color: black;">
            <center>Save Archives with a high level of security.</center>
        </h1>
    </div>
    <div class="container" style="margin-top: 30px;">
        <p style="font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif">
            <center><h4>Here is your Saved Archives!</h4>
        </p>
        </p>
        <center>
            <h3 class="text-dark mb-3 my-5">Encrypted Text</h3>
            <table class="table">
                <tr>
                    <th>Title</th>
                    <th>Encrypted Text</th>
                    <th>Decrypted Text</th>
                    <th>Uploaded at</th>
                    <th></th>
                </tr>
                <?php
                include "koneksi.php";

                $id = $_SESSION['id'];

                $text = mysqli_query($konek, "SELECT * FROM datatext WHERE id_user = '$id'");
                if (mysqli_num_rows($text) == 0) { ?>
                    <tr>
                        <td colspan="4">
                            <center style="margin: 20px;">Data Masih Kosong!</center>
                        </td>
                    </tr>
                <?php }
                while ($datatext = mysqli_fetch_array($text)) { ?>
                    <tr>
                        <td><?php echo $datatext['title']; ?></td>
                        <td><?php echo $datatext['text']; ?></td>
                        <td><?php echo superDecrypt($datatext['text'], $datatext['shift'], $datatext['aeskey']); ?></td>
                        <td><?php echo $datatext['uploaded_at']; ?></td>
                        <td><a href="delete.php?id_text=<?php echo $datatext['id_text']; ?>" class="btn btn-outline-dark">Delete</a></td>
                    </tr>
                <?php }
                ?>
            </table>
            <h3 class="text-dark my-5 mb-3">Encrypted File</h3>
            <table class="table">
                <tr>
                    <th>File Name</th>
                    <th>Encrypted File</th>
                    <th>Decrypted File</th>
                    <th>Uploaded at</th>
                    <th></th>
                </tr>
                <?php

                $file = mysqli_query($konek, "SELECT * FROM datafile WHERE id_user = '$id'");
                if (mysqli_num_rows($file) == 0) { ?>
                    <tr>
                        <td colspan="4">
                            <center style="margin: 20px;">Data Masih Kosong!</center>
                        </td>
                    </tr>
                <?php }
                while ($datafile = mysqli_fetch_array($file)) {
                    $encryptedFilePath = 'uploads/encrypted_' . $datafile['nama_file']; // Ganti dengan path file terenkripsi yang dihasilkan
                    $decryptedFilePath = 'uploads/decrypted_' . $datafile['nama_file']; // Ganti dengan path tempat file terdekripsi akan disimpan
                    $encryptionKey = $datafile['aeskey']; // Ganti dengan kunci enkripsi yang digunakan

                    // Mendekripsi file
                    decryptFile($encryptedFilePath, $decryptedFilePath, $encryptionKey);
                ?>
                    <tr>
                        <td><?php echo $datafile['nama_file']; ?></td>
                        <td><a href="<?php echo 'uploads/encrypted_' . $datafile['nama_file']; ?>" class="btn btn-outline-dark">Download FIle</a></td>
                        <td><a href="<?php echo 'uploads/decrypted_' . $datafile['nama_file']; ?>" class="btn btn-outline-dark">Download FIle</a></td>
                        <td><?php echo $datafile['uploaded_at']; ?></td>
                        <td><a href="delete.php?id_file=<?php echo $datafile['id_file']; ?>" class="btn btn-outline-dark">Delete</a></td>
                    </tr>
                <?php }
                ?>
            </table> 
            </table>

    <h3 class="text-dark my-5 mb-3">Steganography Photos</h3>
    <table class="table">
        <tr>
            <th>Foto Name</th>
            <th>Steganography Photos</th>
            <th>Secret Message</th>
            <th>Uploaded at</th>
            <th></th>
        </tr>
        <?php
        if (isset($_SESSION['id'])) {
            $id_user = $_SESSION['id'];
        } else {
            die('User ID is not set in session.');
        }
        
        $file = mysqli_query($konek, "SELECT * FROM datafoto WHERE id_user = '$id_user'");
        if (mysqli_num_rows($file) == 0) { ?>
            <tr>
                <td colspan="5">
                    <center style="margin: 20px;">Data Masih Kosong!</center>
                </td>
            </tr>
        <?php }
        while ($datafile = mysqli_fetch_array($file)) {
            $encryptedFilePath = 'uploads/' . $datafile['nama_foto']; // Ganti dengan path file terenkripsi yang dihasilkan
            $decryptedFilePath =  steg_extract($encryptedFilePath) ; // Ganti dengan path tempat file terdekripsi akan disimpan
           
        ?>
            <tr>
                <td><?php echo htmlspecialchars($datafile['nama_foto']); ?></td>
                <td><a href="data:image/jpeg;base64,<?php echo base64_encode($datafile['foto_stego']); ?>" download="<?php echo 'encoded_' . $datafile['nama_foto']; ?>" class="btn btn-outline-dark">Download Foto</a></td>
                <td><?php echo htmlspecialchars($decryptedFilePath); ?></td>
                <td><?php echo $datafile['uploaded_at']; ?></td>
                <td><a href="delete.php?id_foto=<?php echo $datafile['id']; ?>" class="btn btn-outline-dark">Delete</a></td>
            </tr>
        <?php }
        ?>
    </table>
        </center>
    </div>
    <div class="container">
        <footer class="py-3 my-4">
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                <li class="nav-item"><a href="main.php" class="nav-link px-2 text-muted">Save Archives</a></li>
                <li class="nav-item"><a href="storage.php" class="nav-link px-2 text-muted">Saved Archives</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link px-2 text-muted">Logout</a></li>
            </ul>
            <p class="text-center text-muted">&copy; 2022 Company, Inc</p>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
</body>

</html>