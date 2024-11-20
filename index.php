<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style1.css">
    <link rel="shortcut icon"  href="logo.png" type="image/x-icon">
    <title>Sign In</title>
</head>

<body>
    <!-- cek pesan notifikasi -->
    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "gagal") { ?>
            <div class="alert alert-danger" role="alert">
                Login Failed! Your username or password is wrong!
            </div>
        <?php } else if ($_GET['pesan'] == "logout") { ?>
            <div class="alert alert-success" role="alert">
                Logout success!
            </div>
        <?php } else if ($_GET['pesan'] == "belum login") { ?>
            <div class="alert alert-danger" role="alert">
                You have to sign in to access main page!
            </div>
        <?php } else if ($_GET['pesan'] == "terdaftar") { ?>
            <div class="alert alert-success" role="alert">
                Your Registration is complete!
            </div>
        <?php } else if ($_GET['pesan'] == "berhasil") { ?>
            <div class="alert alert-success" role="alert">
                Your Password is changed! Try Now!
            </div>
    <?php }
    }
    ?>
    <center>
        <div class="form-container" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width:500px;margin:auto;">
        <div class="logo-container">    
        <div class="row mb-4">
                <center>
                    <h1>Login Archieve Secured</h1>
                </center>
            </div>
            <div class="form">
                <form method="POST" action="cek_login.php">
                <label for="floatingInput">Username</label>
                    <div class="form-group" style="margin-bottom:10px;">
                        <input type="text" class="form-control" id="floatingInput" placeholder="Admin Name" name="username">
                    </div>
                    <label for="floatingPassword">Password</label>
                    <div class="form-group" style="margin-bottom:10px">
                        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
                    </div>
                    <button class="form-submit-btn" type="submit">Sign in</button>
                </form>
                <a href="signup.php" class="nav-link text-primary" style="margin-top:10px;">Sign Up</a>
            </div>
        </div>
    </center>
    </script>
</body>

</html>