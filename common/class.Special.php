<?php
/**
 * Class to handle special pages, and other stuff
 *
 * PHP version 5
 *
 */
class Special {

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
     * Gets information about the current revision of a page
     *
     * @return array - an array of recent changes
     */
    public function getRecentChanges() {
        $sql = "SELECT *
                FROM rev
                JOIN page ON (rev_page=page_id)
                JOIN user ON (rev_user=user_id)
                ORDER BY rev_timestamp DESC";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

}
?>