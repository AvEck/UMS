<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgotten Password - JCGroep</title>
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
                <h1 class="text-center login-title">Recover your Account</h1>
                <div class="account-wall">
            <img class="profile-img" src="img/logo.png" alt="">


<?PHP
//check if a form was used or if the user came from the EMAIL
if ($_POST) {
    //user used a form
    include ('UserManagement.php');
    $usrMngr = new UserManagement();
    if (isset($_POST['email'])&&$_POST['email']!='') {
        
        //send email to user
        
        $result = $usrMngr->sendForgotPassHash($_POST['email']); ?>
            <div class="status-message">
                <?PHP if ($result != false) { ?>
                <p>An e-mail has been sent to your account</p>
                <p>For testing purposes here's the link:<BR /><?PHP echo $result ?></p>
                <?PHP } ?>
            </form>
    <?PHP } elseif (isset($_POST['newPass'])&&$_POST['newPass']!=''&&isset($_POST['e'])&&$_POST['e']!='') {
        
        //update the new Pass
        
        $passReset = $usrMngr->editPass($_POST['e'], $_POST['newPass']);
        if ($passReset == true) { 
            $usrMngr->updateForgotPass($_POST['e']); ?>
            <div class="status-message">
                <p>Password was changed succesfully</p>
            </div>
        <?PHP } else { ?>
            <div class="status-message">
                <p>Something went wrong<BR />Please try again later.</p>
            </div>
        <?PHP }
     } else {
        
        //e-mail wasn't filled in
        
        echo 'please input your email address if you want to recover your account<br><br>';
    }
} elseif (isset($_GET['h'])&&$_GET['h']!=''&&isset($_GET['e'])&&$_GET['e']!='') {
    
    //user came from the email
    
    include ('UserManagement.php');
    $usrMngr = new UserManagement();
    if($usrMngr->checkForgotTimestamp($_GET['e'],$_GET['h'])) { ?>
        <form class="form-signin" method="post">
            <h1 class="text-center login-title"><?PHP echo $_GET['e'];?></h1> 
            <input name="newPass" class="form-control independant" placeholder="New Password" required autofocus>
            <input type="hidden" name="e" value="<?PHP echo $_GET['e'] ?>">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Reset password</button>
        </form>
    
    <?PHP } else { ?>
    <div class="status-message">
        <p>This link has expired.<BR /><a href='./forgot.php'>Try Again</a></p>
    </div>
     <?PHP }
} else {
    
    //user has not posted or didn't come from the email, thus wants to reset his password
    
?>
        <form class="form-signin" method="post">
            <input name="email" class="form-control independant" placeholder="Email" required autofocus>
            <button class="btn btn-lg btn-primary btn-block" type="submit">Send E-mail</button>
        </form>

<?PHP } ?>
        </div>
    <a href="./" class="text-center new-account">&#8592; Back to Home</a>
</div>
</div>
</div>
</body>
</body>
</html>