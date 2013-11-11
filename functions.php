<?PHP


function verifyUser ($username, $password) {
    //get the password from the database with the current user
    $dbh = new PDO('mysql:dbname=jcgroep;host=localhost', 'root', 'root');
    $sth = $dbh->prepare('
      SELECT
        id, role, pass_hash, is_locked
      FROM users
      WHERE
        user = :username
      LIMIT 1
      ');
    //bind the username to the Query to prevent SQL injection
    $sth->bindParam(':username', $username);
    $sth->execute();
    
    $user = $sth->fetch(PDO::FETCH_OBJ);
    if ($user == false) {
        //User does not exist
        return false;
    }
    //User does exist        
    //Hashing the password with its hash as the salt returns the same hash
    if ( crypt($password, $user->pass_hash) == $user->pass_hash ) {
        //verification is succesful :)
        return $user;
    } else {
        return false;  
    }
}
	
	
function startTime() {
	//to record the execution time.
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime;
	return $starttime;
}

function endTime($starttime,$user,$action) {
	//add the execution time to the DB
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime;
	$totaltime = ($endtime - $starttime);
    //get current dateTime
    $date = date('Y-m-d H:i:s');
    
    //get IP address of user
    if ( isset($_SERVER["REMOTE_ADDR"]) )    { 
        $ip = $_SERVER["REMOTE_ADDR"]; 
    } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    { 
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; 
    } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    { 
        $ip = $_SERVER["HTTP_CLIENT_IP"]; 
    } 
    
	$dbh = new PDO('mysql:dbname=jcgroep;host=localhost', 'root', 'root');
    $sth = $dbh->prepare('
        INSERT INTO
            log
        SET
            username = :user,
            act = :action,
            duration = :totaltime,
            timedate = :date,
            ip = :ip
    ');
    $sth->bindParam(':user', $user);
    $sth->bindParam(':action', $action);
    $sth->bindParam(':totaltime', $totaltime);
    $sth->bindParam(':date', $date);
    $sth->bindParam(':ip', $ip);
    $sth->execute();
}