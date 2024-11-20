<?php

session_start();
if (empty($_SESSION['username'])) {
    header("location:index.php?pesan=belum_login");
}

function caesarCipherEncrypt($plaintext, $shift)
{
    $result = "";
    for ($i = 0; $i < strlen($plaintext); $i++) {
        $char = $plaintext[$i];

        if (ctype_alpha($char)) {
            $offset = ord(ctype_upper($char) ? 'A' : 'a');
            $result .= chr(($offset + (ord($char) - $offset + $shift) % 26));
        } else {
            $result .= $char;
        }
    }

    return $result;
}

function aesEncrypt($plaintext, $key)
{
    $cipherText = openssl_encrypt($plaintext, 'aes-256-ecb', $key, OPENSSL_RAW_DATA);
    return base64_encode($cipherText);
}

function superEncrypt($plaintext, $caesarShift, $aesKey)
{
    $caesarEncrypted = caesarCipherEncrypt($plaintext, $caesarShift);

    $superEncrypted = aesEncrypt($caesarEncrypted, $aesKey);

    return $superEncrypted;
}

$plaintext = $_POST['plaintext'];
$caesarShift = $_POST['shift'];
$aesKey = $_POST['aeskey'];

$encryptedText = superEncrypt($plaintext, $caesarShift, $aesKey);

// echo "Plaintext: $plaintext\n";
// echo "Encrypted Text: $encryptedText\n";

include "koneksi.php";

$id = $_SESSION['id'];
$title = $_POST['title'];

$query = mysqli_query($konek, "INSERT INTO datatext (id_user, text, shift, aeskey, title) VALUES('$id','$encryptedText',
'$caesarShift','$aesKey','$title')") or die(mysqli_error($konek));

if ($query) {
    header("location:storage.php");
} else {
    header("location:main.php");
}
