<?PHP 
session_start();

//check if user is logged in
if ((isset($_SESSION['login']) && $_SESSION['login'] != '')) {
    header ("Location: profile.php");
    die();
}

//when user tries to log in.
if ($_POST) {
    include_once ('functions.php');
    $startTime = startTime();
    if (isset($_POST['username']) && isset($_POST['password']) ) {
        //include_once ('UserManagement.php');
        //$usrMngr = new UserManagement();
        $result = verifyUser($_POST['username'], $_POST['password']);
        if($result != false) {
            //user has the right 
            //check if the users account is locked
            if ($result->is_locked==0) {
                //Thunderbirds are Go!
                $_SESSION['userId'] = $result->id;
                $_SESSION['role'] = $result->role;
                $_SESSION['login'] = true;
                endTime($startTime,$_POST['username'], 'Logged In');
                header ("Location: profile.php");
                die();  
            } else {
                //account is locked.
                endTime($startTime,$_POST['username'], 'Login Fail, Locked');
                $locked = true;
            }
 
        } else {
            //login not succesful, wrong credentials
            endTime($startTime,$_POST['username'], 'Login Fail, Wrong Credentials');
            $login = false;
        } 
    } else {
        //User didn't fill the form out correctly
        endTime($startTime,$_POST['username'], 'Login Fail, Wrong form');
        $form = false;
    }
}?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to JCGroep!</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <link rel="icon" type="image/png" href="img/favicon.png">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-md-offset-4">
                <h1 class="text-center login-title">Sign in to JC Groep</h1>
                <div class="account-wall">
                    <img class="profile-img" src="img/logo.png" alt="">
                    <form class="form-signin" method="post">
                    <?PHP if (isset($login)||isset($form)) { ?>
                        <p>Wrong password/username combination</p>
                    <?PHP } elseif(isset($locked)) { ?>
                        <p>Your account has been locked.<BR /><a href="mailto:admin@jcgroep.nl">Click to contact Admin</a> </p>
                    <?PHP } ?>
                    <input name="username" type="text" class="form-control" placeholder="Username" required autofocus>
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Sign in</button>
                    <a href="register.php" class="text-center new-account">Create an account </a>
                    </form>
                </div>
                <a href="forgot.php" class="text-center new-account">Forgot Password?</a>
            </div>
        </div>
    </div>
</body>
</html>