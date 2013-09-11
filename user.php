<?php
    include_once 'common/base.php';
    $user = trim($_REQUEST['user']);
    $title = "User: " . $user;
    include_once 'common/header.php';

    if (empty($user)) {
        terminalError('Need to specify a user!');
    }

    $userinfo = $users->getUserInfo($user);
    if (!isset($userinfo['user_id'])) {
        terminalError('User does not exist!');
    }

    $usergroups = $users->getUserGroups($userinfo['user_id']);
?>

<div class="page-header">
    <div class="page-tools">
        <?php
        if (in_array('admin', $mygroups)) {
            // leet admin tools

            if (in_array('blocked', $usergroups)) {
                echo '<button type="submit" id="action-unblock" class="btn btn-danger"> unblock user</button>';
            } else {
                echo '<button type="submit" id="action-block" class="btn btn-danger"><i class="icon-ban-circle icon-white"></i> block user</button>';
            }

            if (in_array('admin', $usergroups)) {
                echo ' <button type="submit" id="action-demote" class="btn btn-success"><i class="icon-remove icon-white"></i> de-admin</button>';
            } else {
                echo ' <button type="submit" id="action-promote" class="btn btn-success"><i class="icon-star icon-white"></i> make admin</button>';
            }
        }
        ?>
    </div>
    <h1>User: <?php echo $user; ?></h1>
</div>

<table class="table table-bordered">
    <tr>
        <th scope="row">User id</th>
        <td>
            <?php echo $userinfo['user_id']; ?>
        </td>
    </tr>
    <tr>
        <th scope="row">Registered</th>
        <td>
            <?php echo $userinfo['user_timestamp']; ?>
        </td>
    </tr>
    <tr>
        <th scope="row">Groups</th>
        <td>
            <?php echo $users->formatGroups($usergroups); ?>
        </td>
    </tr>
</table>

<h2>Edits</h2>
<?php
    $useredits = $users->getUserEdits($user);
    if (empty($useredits)) {
        echo '<p>User has no edits yet.</p>';
    } else {
        include_once 'common/classes/Pages.php'; // need for tags
        $pages = new Pages($db);
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th style="width: 12em;">Timestamp</th>
            <th>Page</th>
            <th>Comment</th>
            <th>Tags</th>
        </tr>
    </thead>
    <tbody>
<?php
        foreach ($useredits as $row) {
            echo '<tr>';
            echo sprintf('<td><a href="viewrev.php?revid=%1$s">%2$s</a></td>', $row['rev_id'], formatTimestamp($row['rev_timestamp']) );
            echo sprintf('<td><a href="view.php?page=%1$s">%1$s</a></td>', $row['page_title']);
            echo '<td>' . $row['rev_comment'] . '</td>';
            $revtags = $pages->getRevTags($row['rev_id']);
            echo '<td>' . $pages->formatTags($revtags) . '</td>';
            echo '</tr>';
        }
?>
    </tbody>
</table>

<?php
    }
    include_once 'common/footer.php';
?>

<script>
$('#action-block').click(function() {
    $.post(
        'common/ajax.users.php', 
        {
            action: 'block',
            userid: <?php echo $userinfo['user_id']; ?>
        },
        // success
        function(data) {
            document.location.reload(true);
        }
    );
});

$('#action-unblock').click(function() {
    $.post(
        'common/ajax.users.php', 
        {
            action: 'unblock',
            userid: <?php echo $userinfo['user_id']; ?>
        },
        // success
        function(data) {
            document.location.reload(true);
        }
    );
});

$('#action-promote').click(function() {
    $.post(
        'common/ajax.users.php', 
        {
            action: 'promote',
            userid: <?php echo $userinfo['user_id']; ?>
        },
        // success
        function(data) {
            document.location.reload(true);
        }
    );
});

$('#action-demote').click(function() {
    $.post(
        'common/ajax.users.php', 
        {
            action: 'demote',
            userid: <?php echo $userinfo['user_id']; ?>
        },
        // success
        function(data) {
            document.location.reload(true);
        }
    );
});
</script>