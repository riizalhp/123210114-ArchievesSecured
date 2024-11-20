<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style1.css">
    <link rel="shortcut icon"  href="logo.png" type="image/x-icon">
    <title>Sign Up</title>
    
</head>

<body>
    <!-- cek pesan notifikasi -->
    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "gagal") { ?>
    <div class="alert alert-danger" role="alert">
        Your Registration is failed!
    </div>
    <?php }
    }
    ?>
    <center>
    <div class="form-container" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width:500px;margin:auto;">
    <div class="logo-container">       
    <div class="row">
                <center>
                    <h1>SMS Secured Apps</h1>
                </center>
                <h2 style="margin-bottom:30px;">Regist Here</h2>
            </div>
            <div class="row">
                <form method="POST" action="cek_signup.php">
                    <div class="form-floating" style="margin-bottom:10px;">
                    <div class="form-group" style="margin-bottom:10px;"> 
                    <label for="floatingInput">Username</label>   
                    <input type="text" class="form-control" id="floatingInput" placeholder="Username"
                            name="username">
                            
                    </div>
                    <div class="form-floating" style="margin-bottom:10px">
                    <div class="form-group" style="margin-bottom:10px;">    
                    <label for="floatingPassword">Password</label>
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password"
                            name="password">
                        
                    </div>
                    <div class="form-floating" style="margin-bottom:10px">
                    <div class="form-group" style="margin-bottom:10px;">        
                    <label for="floatingPassword">Confirm Password</label>
                    <input type="password" class="form-control" id="floatingPassword" placeholder="Password"
                            name="confirmpassword">
                        
                    </div>
                    <div class="checkbox mb-3">
                    </div>
                    <button class="form-submit-btn" type="submit">Sign Up</button>
                    <label style="margin:20px">Already have an account? <a href="index.php"
                            class="nav-link text-primary">Sign in</a></label>
                </form>
            </div>
        </div>
    </center>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
</body>

</html>