<?PHP

class userManagement {

    private $dbh;

    function __construct() {
        $this->dbh = new PDO('mysql:dbname=jcgroep;host=localhost', 'root', 'root');
    }
    
    public function createUser ($role, $user, $first_name, $last_name, $email, $pass, $lock) {
        //get the password from the database with the current user
        $sth = $this->dbh->prepare('
            INSERT INTO
                users
            SET
                role = :role,
                user = :user,
                first_name = :first_name,
                last_name = :last_name,
                email = :email,
                pass_hash = :pass,
                is_locked = :lock
        ');
        //Hash the answer and the pass for security
        $pass = $this->hashString($pass);
        $first_name = ucfirst(strtolower($first_name));
        //bind the parameters to the SQL
        $sth->bindParam(':role', $role);
        $sth->bindParam(':user', $user);
        $sth->bindParam(':first_name', $first_name);
        $sth->bindParam(':last_name', $last_name);
        $sth->bindParam(':email', $email);
        $sth->bindParam(':pass', $pass);
        $sth->bindParam(':lock', $lock);
        $sth->execute();
    }
    
    public function deleteUser ($id) {
        //get the old password if we need it
        $sth = $this->dbh->prepare('
            SELECT
            pass_hash
            FROM users
            WHERE id = :id           
        ');
        $sth->bindParam(':id',$id);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_OBJ);
        
        //delete the user
        $sth = $this->dbh->prepare('
            DELETE FROM users
            WHERE id = :id           
        ');
        $sth->bindParam(':id',$id);
        $sth->execute();
        
        return $user->pass_hash;
    }

    public function getAllUsers($query = "all") {
        //get the password from the database with the current user
        if ($query=='all') {
        $sth = $this->dbh->prepare('
          SELECT
            id, user, role, first_name, last_name, email, is_locked
          FROM users
          ORDER BY id DESC
          ');
        } else {
            $sth = $this->dbh->prepare("
              SELECT
                id, user, role, first_name, last_name, email, is_locked
              FROM users
              WHERE user LIKE CONCAT('%', :query, '%')
              OR first_name LIKE CONCAT('%', :query, '%')
              OR last_name LIKE CONCAT('%', :query, '%')
              OR email LIKE CONCAT('%', :query, '%')
              ORDER BY id DESC
            ");
            $sth->bindParam(':query',$query);
        }
        //bind the username to the Query to prevent SQL injection
        $sth->execute();
        
        $users = $sth->fetchAll(PDO::FETCH_OBJ);
        if ($users == false) {
            //no Users....
            return false;
        } else {
            //User does exist
            return $users;
        }    
    
    }
    
    public function getUser ($id) {
        //get the password from the database with the current user
        $sth = $this->dbh->prepare('
          SELECT
            role, user, first_name, last_name, email
          FROM users
          WHERE
            id = :id
          LIMIT 1
          ');
        //bind the username to the Query to prevent SQL injection
        $sth->bindParam(':id', $id);
        $sth->execute();
        
        $user = $sth->fetch(PDO::FETCH_OBJ);
        if ($user == false) {
            //User does not exist
            return false;
        } else {
            //User does exist
            return $user;
        }    
    }
    
    public function sendForgotPassHash($email) {
        //hash current date and send it to the users email
        $date = date('Y-m-d H:i:s');
        $dateHash = $this->hashString($date);
        $sth = $this->dbh->prepare('
            UPDATE users
            SET 
                needs_forgot = 1,
                forgot_timestamp = :date,
                forgot_hash = :dateHash
          WHERE
            email = :email
          ');
        //bind the username to the Query to prevent SQL injection
        $sth->bindParam(':date', $date);
        $sth->bindParam(':dateHash', $dateHash);
        $sth->bindParam(':email', $email);
        $sth->execute();
        
        //send email with a link to this script to user
        $subject = "Password Reset";
        //the body of this email should obviously be styled before it is send out to the user.
        $body = "<a href='http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?e={$email}&h={$dateHash}'>Click this link to reset</a>";
        if (mail($email, $subject, $body)) {
            return $body;
            //return true;
        } else {
            return false;
        }        
    }
    
    public function checkForgotTimestamp($email, $hash) {
        //check if password was already reset otherwise 
        $sth = $this->dbh->prepare('
          SELECT
            needs_forgot, forgot_timestamp, forgot_hash
          FROM users
          WHERE
            email = :email
          LIMIT 1
          ');
        //bind the username to the Query to prevent SQL injection
        $sth->bindParam(':email', $email);
        $sth->execute();
        
        $result = $sth->fetch(PDO::FETCH_OBJ);
        //check if user needs a reset and if it was requested within 24 hours.
        if ($result != false&&$result->needs_forgot == 1&&strtotime($result->forgot_timestamp)>=strtotime("-24 hours")) {
            //User needs reset
            //check if the hash is correct
            if ( $hash == $result->forgot_hash ) {
                //user is the legitmate owner of the account and email                
                return true;
            } else {
                //ILLEGAL RESET ATTEMPT!
                return false;  
            }
        } else {
            //User does not need reset
            return false;
        }
        //check if TimeStamp is within 24 hours of reset
        //check if Hash is the same as hash of timestamp
    }
    
    public function updateForgotPass($email) {
        //Reset the needs_forgot boolean and delete the HASH to save space
        $sth = $this->dbh->prepare('
            UPDATE users
            SET 
                need_forgot = 0,
                forgot_hash = ""
          WHERE
            email = :email
          ');
        //bind the username to the Query to prevent SQL injection
        $sth->bindParam(':email', $email);
        $sth->execute();
    }
    
    public function editInfo ($id, $first_name, $last_name, $email) {
        //Update the info from a user by ID
        $sth = $this->dbh->prepare('
            UPDATE users
            SET 
                first_name = :first_name,
                last_name = :last_name,
                email = :email
          WHERE
            id = :id
          ');
        //bind the username to the Query to prevent SQL injection
        $sth->bindParam(':id', $id);
        $sth->bindParam(':first_name', $first_name);
        $sth->bindParam(':last_name', $last_name);
        $sth->bindParam(':email', $email);
        $sth->execute();
        
        $user = $sth->fetch(PDO::FETCH_OBJ);
        if ($user == false) {
            //User does not exist
            return false;
        } else {
            //User does exist
            return $user;
        }  
    
    }
    
    public function editPass ($user_id, $pass) {
        //hash the password for storage
        $hash = $this->hashString($pass);
        
        $sth = $this->dbh->prepare('
            UPDATE users
            SET 
                pass_hash = :hash
          WHERE
            id = :user_id
            OR
            email = :user_id
          ');
        //bind the username to the Query to prevent SQL injection
        $sth->bindParam(':user_id', $user_id);
        $sth->bindParam(':hash', $hash);
        $sth->execute();
        
        return true;
        // Value:
        // $2a$10$eImiTXuWVxfM37uY4JANjOL.oTxqp7WylW7FCzx2Lc7VLmdJIddZq
    
    }
    
    private function hashString($string_hash) {
        // A higher "cost" is more secure but consumes more processing power
        $cost = 10;
        
        // Create a random salt
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        
        // Prefix information about the hash so PHP knows how to verify it later.
        // "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
        $salt = sprintf("$2a$%02d$", $cost) . $salt;
        
        // Value:
        // $2a$10$eImiTXuWVxfM37uY4JANjQ==
        
        // Hash the password with the salt
        $hash = crypt($string_hash, $salt);
        
        return $hash;
    }
    
    
}