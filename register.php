<?PHP 
session_start();

if ((isset($_SESSION['login']) && $_SESSION['login'] != '')) {
    //check if user is admin, generally only admin is logged in and registering
    if($_SESSION['role']==1) {
        $isAdmin = true;
    }    
} else {
    //the role will be NOT admin because is a normal user
    $role = 0;
}

//check if the user can be created
if ($_POST) {
    include_once ('UserManagement.php');
    include_once ('functions.php');
    $usrMngr = new UserManagement();
    $startTime = startTime();
    if (isset($_POST['username'])&&$_POST['username']!=''&&
        isset($_POST['first_name'])&&$_POST['first_name']!=''
        &&isset($_POST['last_name'])&&$_POST['last_name']!=''
        &&isset($_POST['email'])&&$_POST['email']!=''
        &&isset($_POST['pass'])&&$_POST['pass']!='') {
            if (isset($_POST['role'])&&$_POST['role']!='') {
                
                //update the role if it is set by an ADMIN!
                
                $role = $_POST['role'];
            }
            //update userInfo
        $usrMngr->createUser($role, $_POST['username'],$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['pass'], 0);
        endTime($startTime,$_POST['username'], 'User created');
        if (isset($isAdmin)&&$isAdmin==true) {
            
            //user is admin, because normal user would never be logged in and be in the the register screen
            
            header ("Location: profile.php");
            die();
        } else {
            header ("Location: index.php");
            die();
        }
    } else {
    echo 'Please fill all the fields';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - JC Groep</title>
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
                <h1 class="text-center login-title">Register with JC Groep</h1>
                <div class="account-wall">
                    <img class="profile-img" src="img/logo.png" alt="">
                    <form class="form-signin" method="post" id="create-user">
                        <?PHP if (isset($isAdmin)&&$isAdmin == true) { ?>
                            <select class="form-control" name="role" form="create-user">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select><br>
                        <?PHP } ?>
                    <input name="username" class="form-control independant" placeholder="Username" required>
                    <input name="first_name" type="text" class="form-control" placeholder="First Name" required autofocus>
                    <input name="last_name" type="text" class="form-control top-join" placeholder="Last Name" required>
                    <input name="email" class="form-control independant" placeholder="Email" required>
                    <input name="pass" type="password" class="form-control" placeholder="Password" required>
                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Register</button>
                    </form>
                </div>
                <a href="./" class="text-center new-account">&#8592; Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>