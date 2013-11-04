<?PHP
session_start();

//check if user is logged in
if ((!isset($_SESSION['login']) && $_SESSION['login'] == '')) {
    header ("Location: index.php");
    die();
} else {
    include ('UserManagement.php');
    $usrMngr = new UserManagement();
    //if any info was updated
    if ($_POST) {
        if ($_POST['function']=='profile') {
            //profile update
            if (isset($_POST['first_name'])&&$_POST['first_name']!=''
                &&isset($_POST['last_name'])&&$_POST['last_name']!=''
                &&isset($_POST['email'])&&$_POST['email']!='') {
                //update userInfo
                $usrMngr->editInfo($_SESSION['userId'], $_POST['first_name'], $_POST['last_name'], $_POST['email']);
            }
            //if the pass needs to be changed
            if (isset($_POST['pass'])&&$_POST['pass']!='') {
                //update password
                $usrMngr->editPass($_SESSION['userId'], $_POST['pass']);
            }
            
        } elseif ($_POST['function']=='user') {
            //print_r($_POST);
            //ADMIN user update
            if ($_POST['submit']=="Send Pass") {
                $usrMngr->sendForgotPassHash($_POST['email']);
                $statusMessage = 'Email was Sent';
            } elseif ($_POST['submit']=="Delete") {
                $usrMngr->deleteUser($_POST['id']);
                $statusMessage = 'User was deleted';
            } elseif (isset($_POST['id'])&&$_POST['id']!=''
                &&isset($_POST['role'])&&$_POST['role']!=''
                &&isset($_POST['username'])&&$_POST['username']!=''                
                &&isset($_POST['first_name'])&&$_POST['first_name']!=''
                &&isset($_POST['last_name'])&&$_POST['last_name']!=''
                &&isset($_POST['email'])&&$_POST['email']!=''
                &&isset($_POST['lock'])&&$_POST['lock']!='') {
                //delete the user in case of new username
                $_POST['pass'] = $usrMngr->deleteUser($_POST['id']);
                //if NO password was entered use the old password
//                if ($_POST['pass']=="---") { $_POST['pass'] = $oldPass; };
                //create the new user with the old pass if we need to
                $usrMngr->createUser($_POST['role'],$_POST['username'],$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['pass'],$_POST['lock']);
                $statusMessage = 'User was updated';
            } else {
                $statusMessage = 'Please fill all the fields';
            }
        
        }
    }
    $result = $usrMngr->getUser($_SESSION['userId']);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?PHP echo $result->first_name ?> - JCGroep</title>
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
                <h1 class="text-center login-title"><?PHP echo $result->user; ?></h1>
                <div class="account-wall profile">
                    <h1 class="text-center login-title">Change your profile</h1>
                    <form class="form-signin" method="post" id="create-user">
                        <?PHP if (isset($isAdmin)&&$isAdmin == true) { ?>
                            <select class="form-control" name="role" form="create-user">
                                <option value="0">User</option>
                                <option value="1">Admin</option>
                            </select><br>
                        <?PHP } ?>
                    <input name="first_name" type="text" class="form-control" value="<?PHP echo $result->first_name ?>" required autofocus>
                    <input name="last_name" type="text" class="form-control top-join" value="<?PHP echo $result->last_name?>" required>
                    <input name="email" type="text" class="form-control independant" value="<?PHP echo $result->email ?>" required>
                    <input name="pass" type="password" class="form-control independant" placeholder="Change your Password" required>
                    <input type="hidden" name="function" value="profile">
                    <button class="btn btn-lg btn-primary btn-block" type="submit">
                        Update Profile</button>
                        <a href="./logout.php" class="text-center new-account">Log Out</a>
                    </form>
                </div>
            </div>
        </div>
        

<?PHP 
    if ($result->role==1&&$_SESSION['role']==1) {
        //current user is ADMIN!
        if(isset($_GET['q'])&&$_GET['q']!='') {
            //if there was a search, execute that
            $users = $usrMngr->getAllUsers(urldecode($_GET['q']));
            if (empty($users)||$users == false) {
                $statusMessage = 'No Users found';
            }
        } else {
            //else just get all the users
            $users = $usrMngr->getAllUsers();
        }
        if (isset($statusMessage)&&$statusMessage != '') {
            echo '<h1 class="text-center login-title status-message">'.$statusMessage.'</h1>';
        } else {
            echo '<h1 class="text-center login-title">All Users</h1>';
        } ?>
        <div class="row search">
                <form class="form-inline" role="form" method="get">
                    <div class="form-group">
                        <div style="width:75%; float: left;">
                          <label class="sr-only" for="">Enter search terms</label>
                          <input type="search" class="form-control" id="q" name="q" value="<?PHP echo (!empty($_GET['q'])?$_GET['q']:'')?>" placeholder="Enter search terms">
                        </div>
                        <div style="width: 25%; float: left; padding-left: 10px; box-sizing: border-box;">    
                          <button type="submit" id="s" class="btn btn-default">
                            <span class="glyphicon glyphicon-search"></span>
                          </button> 
                        </div>
                        <div class="clearfix"></div> 
                    </div>
                </form>
                <a href="register.php" class="btn btn-lg btn-primary btn-block newuser-list-btn">New User</a>
        </div>
        <div class="row">
        <?PHP if (!empty($users)) { ?>
        <table class="table table-striped table-bordered ">
          <thead>
            <tr>
                <th class="span1">Role</th>
                <th class="span1">Username</th>
                <th class="span1">First Name</th>
                <th class="span1">Last Name</th>
                <th class="span1">Email</th>
                <th class="span1">Locked</th>
                <th class="span1">Edit</th>
            </tr>
            </thead>
            <tbody data-link="row" class="rowlink">
                <?PHP foreach ($users as $user) { 
                    if ($user->id!=$_SESSION['userId']) { ?>
                        <tr>
                          <form method="POST">
                              <td><input  type="text" name="role" value="<?PHP echo $user->role ?>"></td>
                              <td><input type="text" name="username" value="<?PHP echo $user->user ?>"></td>
                              <td><input type="text" name="first_name" value="<?PHP echo $user->first_name ?>"></td>
                              <td><input type="text" name="last_name" value="<?PHP echo $user->last_name ?>"></td>
                              <td><input type="text" name="email" value="<?PHP echo $user->email ?>"></td>
                              <td><input type="text" name="lock" value="<?PHP echo $user->is_locked ?>"></td>
                              <td><input id="update-btn" type="submit" name="submit" value="Update">
                              <input type="submit" name="submit" value="Send Pass">
                              <input type="submit" name="submit" value="Delete"></td>
                              <input type="hidden" name="function" value="user">
                              <input type="hidden" name="id" value="<?PHP echo $user->id ?>">
                          </form>
                        </tr>
                    <?PHP }
                }?>
            
              </tbody>
        </table>
        <?PHP } ?>
        
</div>
<?PHP
    }
?>
</div>
<?PHP } ?>
</body>
</html>