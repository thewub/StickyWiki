<?php
 
/**
 * Class to deal with users e.g. logging in, getting contributions
 *
 * PHP version 5
 *
 */
class Users {

	/**
     * The database object
     *
     * @var object
     */
    private $_db;
 
    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db
     * @return void
     */
    public function __construct($db=NULL) {
        if(is_object($db)) {
            $this->_db = $db;
        } else {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    }


    /**
     * Attempts to insert a new user into the database
     *
     * @param string $u : supplied username
     * @param string $p : supplied password
     * @return string   - a message indicating the action status
     */
    public function createAccount($u, $p) {
        // Check if username is already taken
        $sql = "SELECT COUNT(user_name) AS x
                FROM `user`
                WHERE user_name=:u";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":u", $u, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row['x']!=0) {
            return '<p class="text-error"> Sorry, that username is already taken. Please try another. </p>';
        }

        // add user to the database
        $sql = "INSERT INTO user (user_name, user_password, user_timestamp)
                VALUES (:u, SHA1(:p), NOW())";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":u", $u, PDO::PARAM_STR);
        $stmt->bindParam(":p", $p, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        // and log them in
        $users = new Users();
        $users->accountLogin($u, $p);
 
        return '<h2> Success! </h2>'
              . '<p> Your account was successfully created with the username <strong>' . $u . '</strong>.</p>';
    }


    /**
     * Checks credentials and logs in the user
     *
     * @param string $u : supplied username
     * @param string $p : supplied password
     * @return boolean    TRUE on success and FALSE on failure
     */
    public function accountLogin($u, $p) {
        $sql = "SELECT user_name, user_id
                FROM user
                WHERE user_name=:user
                AND user_password=SHA1(:pass)
                LIMIT 1";
        try {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $u, PDO::PARAM_STR);
            $stmt->bindParam(':pass', $p, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount()==1) {
                $result = $stmt->fetch();
                $_SESSION['Username'] = $result['user_name'];
                $_SESSION['UserID'] = $result['user_id'];
                $_SESSION['LoggedIn'] = 1;
                return TRUE;
            } else {
                return FALSE;
            }
        } catch(PDOException $e) {
            // something went wrong with the database
            return FALSE;
        }
    }

    /** 
     * Gets information about a user from the database
     *
     * @param string $u : username
     * @return array : info from database
     */
    public function getUserInfo($u) {
        $sql = "SELECT user_id, user_timestamp
                FROM user
                WHERE user_name=:u";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':u', $u, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }

    /** 
     * Gets a user's edits from the database
     *
     * @param string $u : username
     * @return array : rev entries from database
     */
    public function getUserEdits($u) {
        $sql = "SELECT *
                FROM rev
                JOIN page ON (rev_page=page_id)
                JOIN user ON (rev_user=user_id)
                WHERE user_name=:u
                ORDER BY rev_timestamp DESC";       
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':u', $u, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Gets user's groups
     *
     * @param int $uid : user id
     * @return array : array of user groups
     **/
    function getUserGroups($uid) {
        $sql = "SELECT ug_group
                FROM user_groups
                WHERE ug_user=:uid";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result;
    }

    /**
     * Add a user to a group
     *
     * @param int $uid : user id
     * @param string $group : group to add
     * @return void
     **/
    function addUserGroup($uid, $group) {
        $sql = "INSERT INTO user_groups (ug_user, ug_group)
                VALUES (:uid, :group)";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
        $stmt->bindParam(':group', $group, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Remove a user from a group
     *
     * @param int $uid : user id
     * @param string $group : group to remove
     * @return void
     **/
    function removeUserGroup($uid, $group) {
        $sql = "DELETE FROM user_groups 
                WHERE ug_user=:uid
                AND ug_group=:group";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_STR);
        $stmt->bindParam(':group', $group, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Loop through an array of user groups and display them with fancy label formatting
     *
     * @param array $usergroups
     * @return string : html for display
     **/
    function formatGroups($usergroups) {
        $output = '';
        foreach ($usergroups as $i) {
            if ($i==='admin') {
                $output = $output . ' <span class="badge badge-success">admin</span> ';
            } else if ($i==='blocked') {
                $output = $output . ' <span class="badge badge-important">blocked</span> ';
            } else {
                $output = $output . ' <span class="badge">' . $i . '</span>';
            }
        }
        return $output;
    }

    public function getAllUsers() {
        $sql = "SELECT user_name, user_id, user_timestamp
                FROM user
                ORDER BY user_name";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    function changePassword($username, $currentpassword, $newpassword) {
        if ( $this->accountLogin($username, $currentpassword) ) {
            $sql = "UPDATE user
                    SET user_password = SHA1( :newpass )
                    WHERE user_name = :username;";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':newpass', $newpassword, PDO::PARAM_STR);
            $stmt->execute();
            return 'Password successfully changed.';
        } else {
            terminalError('Current password incorrect!');
        }
    }

}
?>