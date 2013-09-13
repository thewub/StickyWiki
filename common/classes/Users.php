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

        require_once('common/PasswordHash.php');
        $pwdHasher = new PasswordHash(8, FALSE);
        $hash = $pwdHasher->HashPassword($p);
        
        // add user to the database
        $sql = "INSERT INTO user (user_name, user_password, user_timestamp)
                VALUES (:u, :hash, NOW())";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":u", $u, PDO::PARAM_STR);
        $stmt->bindParam(":hash", $hash, PDO::PARAM_STR);
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
        try {
            $sql = "SELECT user_name, user_id, user_password
                FROM user
                WHERE user_name=:user
                LIMIT 1";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':user', $u, PDO::PARAM_STR);
            $stmt->execute();

            if($stmt->rowCount()==1) {
                $result = $stmt->fetch();

                require_once("common/PasswordHash.php");
                $pwdHasher = new PasswordHash(8, FALSE);
                $correctPassword = $pwdHasher->CheckPassword($p, $result['user_password']);

                if ($correctPassword) {
                    $_SESSION['Username'] = $result['user_name'];
                    $_SESSION['UserID'] = $result['user_id'];
                    $_SESSION['LoggedIn'] = 1;
                    return TRUE;
                } else {
                    // wrong password
                    return FALSE;
                }

            } else {
                // user does not exist
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
     * @param string $username : username
     * @return array : info from database
     */
    public function getUserInfo($username) {
        $sql = "SELECT user_id, user_timestamp
                FROM user
                WHERE user_name=:u";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':u', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result;
    }

    /** 
     * Gets a user's edits from the database
     *
     * @param string $username : username
     * @return array : rev entries from database
     */
    public function getUserEdits($username) {
        $sql = "SELECT *
                FROM rev
                JOIN page ON (rev_page=page_id)
                JOIN user ON (rev_user=user_id)
                WHERE user_name=:u
                ORDER BY rev_timestamp DESC";       
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':u', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Gets user's groups
     *
     * @param int $userid : user id
     * @return array : array of user groups
     **/
    function getUserGroups($userid) {
        $sql = "SELECT ug_group
                FROM user_groups
                WHERE ug_user=:userid";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result;
    }

    /**
     * Add a user to a group
     *
     * @param int $userid : user id
     * @param string $group : group to add
     * @return void
     **/
    function addUserGroup($userid, $group) {
        $sql = "INSERT INTO user_groups (ug_user, ug_group)
                VALUES (:userid, :group)";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':group', $group, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();
    }

    /**
     * Remove a user from a group
     *
     * @param int $userid : user id
     * @param string $group : group to remove
     * @return void
     **/
    function removeUserGroup($userid, $group) {
        $sql = "DELETE FROM user_groups 
                WHERE ug_user=:userid
                AND ug_group=:group";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
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

    /**
     * Returns all users on the wiki
     *
     * @return array : array of info from the database
     */
    function getAllUsers() {
        $sql = "SELECT user_name, user_id, user_timestamp
                FROM user
                ORDER BY user_name";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Change a user's password
     *
     * @param string $username
     * @param string $currentpassword
     * @param string $newpassword
     * @return string : status message
     */
    function changePassword($username, $currentpassword, $newpassword) {

        if ( $this->accountLogin($username, $currentpassword) ) {
            require_once('common/PasswordHash.php');
            $pwdHasher = new PasswordHash(8, FALSE);
            $newHash = $pwdHasher->HashPassword($newpassword);

            $sql = "UPDATE user
                    SET user_password = :newHash
                    WHERE user_name = :username;";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':newHash', $newHash, PDO::PARAM_STR);
            $stmt->execute();
            return 'Password successfully changed.';
        } else {
            terminalError('Current password incorrect!');
        }
    }

    /**
     * Is there an admin on the wiki? Used during setup to prevent extra admins being created
     *
     * @return boolean
     */
    function adminExists() {
        $sql = "SELECT * FROM `user_groups` WHERE ug_group='admin'";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return (!empty($result));
    }

    /**
     * Create an admin account (for example when the wiki is first set up)
     *
     * @param string $username : username
     * @param string $password : password
     * @return void
     */
    function createAdminAccount($username, $password) {
        $this->createAccount($username, $password);
        $userid = $this->getUserInfo($username)['user_id'];
        $this->addUserGroup($userid, 'admin');
    }


}
?>