<?php
 
/**
 * Class to deal with pages e.g. viewing, editing
 *
 * PHP version 5
 *
 */
class Pages {

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
     * Gets information about a page and its current revision
     *
     * @param string $pagetitle : page title
     * @return array : an array containing rev & page info
     */
    public function getPageInfo($pagetitle) {

        $sql = "SELECT * 
                FROM rev
                JOIN page ON (page_current_rev=rev_id)
                JOIN user ON (user_id=rev_user)
                WHERE page_title=:p";

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":p", $pagetitle, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }

    /**
     * Gets information about a revision and the page it belongs to
     *
     * @param string $revid : revid
     * @return array : an array containing rev & page info
     */
    public function getRevInfo($revid) {

        $sql = "SELECT * 
                FROM rev
                JOIN page ON (page_id=rev_page)
                JOIN user ON (user_id=rev_user)
                WHERE rev_id=:revid";

        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":revid", $revid, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row;
    }

    /**
     * Gets info about previous revisions to the page
     *
     * @param string $pagetitle : page title
     * @return array : an array of revisions, each containing an array of info
     */
    public function getPageHistory($pagetitle) {
        $sql = "SELECT *
                FROM rev
                JOIN page ON (rev_page=page_id)
                JOIN user ON (rev_user=user_id)
                WHERE page_title=:p
                ORDER BY rev_timestamp DESC";
        
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":p", $pagetitle, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Saves new content to a page (creating the page if it doesn't already exist)
     *
     * @param int $pageid : pageid (empty if page does not yet exist)
     * @param string $pagetitle : page title
     * @param string $newcontent : the new markup to save
     * @param string $comment : the edit comment
     * @return void
     */
    public function saveEdit($pageid, $pagetitle, $newcontent, $comment) {
        if (empty($pageid)) {
            // page doesn't exist yet - need to insert it
            $newpage = true;
            $sql = "INSERT INTO page (page_title, page_current_rev)
                    VALUES (:pagetitle, 0)";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":pagetitle", $pagetitle, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            $pageid = $this->_db->lastInsertId();
        } else {
            $newpage = false;
        }

        // insert the new revision...
        $sql = "INSERT INTO rev (rev_page, rev_content, rev_comment, rev_user, rev_timestamp)
                VALUES (:page, :content, :comment, :user, NOW())";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":page", $pageid, PDO::PARAM_STR);
        $stmt->bindParam(":content", $newcontent, PDO::PARAM_STR);
        $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
        $stmt->bindParam(":user", $_SESSION['UserID'], PDO::PARAM_STR);
        $stmt->execute();
        $stmt->closeCursor();

        // ...then change page_current_rev
        $newrevid = $this->_db->lastInsertId();
        $sql = "UPDATE page 
                SET page_current_rev = :newrevid
                WHERE page_id = :page";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":newrevid", $newrevid, PDO::PARAM_STR);
        $stmt->bindParam(":page", $pageid, PDO::PARAM_STR);
        $stmt->execute();

        if ($newpage) {
            // tag rev as a page creation
            $tag = "new page";
            $sql = "INSERT INTO rev_tags (rt_rev, rt_tag)
                    VALUES (:newrevid, :tag)";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":newrevid", $newrevid, PDO::PARAM_STR);
            $stmt->bindParam(":tag", $tag, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
        }

    }

    /**
     * Deletes a page - and all revisions associated with it!
     *
     * @param int $pageid : id of the page
     * @return void
     */
    public function deletePage($pageid) {
        // delete revisions
        $sql = "DELETE FROM rev WHERE rev_page = :pageid";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":pageid", $pageid, PDO::PARAM_STR);
        $stmt->execute();

        // delete the page
        $sql = "DELETE FROM page WHERE page_id = :pageid";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(":pageid", $pageid, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Returns all page titles on the wiki
     *
     * @return array : array of page titles
     */
    public function getPageList() {
        $sql = "SELECT page_title
                FROM page
                ORDER BY page_title";
        $stmt = $this->_db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }

    /**
     * Gets tags on a rev
     *
     * @param int $revid : rev id
     * @return array : array of tags
     **/
    function getRevTags($revid) {
        $sql = "SELECT rt_tag
                FROM rev_tags
                WHERE rt_rev=:revid";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':revid', $revid, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        return $result;
    }

    /**
     * Loop through an array of tags and display them with fancy label formatting
     *
     * @param array $tags
     * @return string : html for display
     **/
    function formatTags($tags) {
        $output = '';
        foreach ($tags as $i) {
            if ($i==='new page') {
                $output = $output . ' <span class="badge badge-success">new page</span> ';
            } else {
                $output = $output . ' <span class="badge">' . $i . '</span>';
            }
        }
        return $output;
    }

}
?>