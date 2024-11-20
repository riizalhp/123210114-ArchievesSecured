<?php
session_start();
if (empty($_SESSION['username'])) {
    header("location:index.php?pesan=belum_login");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page</title>
    <link rel="shortcut icon"  href="logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
</head>

<body>
<div class="container">
        <header class="d-flex flex-wrap justify-content-center py-4 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center mb-4 mb-md-0 me-md-auto text-dark text-decoration-none" style="padding-right: 10px;">

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
    <div>
        <h1 style="padding-top:50px;font-family: 'Times New Roman', Times, serif; color: black;">
            <center>Save Archives with a high level of security.</center>
        </h1>
    </div>
    <div class="container" style="margin-top: 30px;">
        <p style="font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif">
            <center>Welcome to the best archive security solution!<br>We are here to ensure that your important documents and data remain safe, accessible, and protected from all risks.
            </center>
        </p>
        <center>
            <div class="dropdown mb-4">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Text or Foto /  File
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="main.php">Text</a></li>
                    <li><a class="dropdown-item" href="main-file.php">File</a></li>
                    <li><a class="dropdown-item" href="main-foto.php">Foto </a></li>
                </ul>
            </div>
        <center>
            <form action="file-encrypt.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="file" class="form-label">
                        File
                    </label>
                    <input type="file" name="fileToUpload" id="fileToUpload" class="form-control">
                </div>
                <p style="font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif">
                <br>
            <center>To secure your archive, please enter a passcode to transform it into a confidential archive.
                <br>
            </center>
            <br>
                <div class="mb-3">
                    <label for="key" class="form-label">
                        Encryption Key
                    </label>
                    <input type="text" name="key" id="key" class="form-control">
                </div>
                <input type="submit" value="Submit File" class="btn btn-dark">
            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
</body>

</html>