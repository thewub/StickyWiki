<?php
    include_once 'common/base.php';
    $title = 'All users';
    include_once 'common/header.php';

    include_once 'common/classes/Users.php';
    $users = new Users($db);
    $allusers = $users->getAllUsers();
?>

<div class="page-header">
    <h1>All users</h1>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Username</th>
            <th>id</th>
            <th>Registered</th>
            <th>Groups</th>
        </tr>
    </thead>

    <tbody>
    
        <?php
            foreach ($allusers as $i) {
                echo '<tr>';
                $usergroups = $users->getUserGroups($i['user_id']);
                echo sprintf('<td><a href="user.php?user=%1$s">%1$s</a></td>', $i['user_name']);
                echo '<td>' . $i['user_id'] . '</td>';
                echo '<td>' . date(SW_DATETIME_FORMAT, strtotime($i['user_timestamp'])) . '</td>';
                echo '<td>' . $users->formatGroups($usergroups) . '</td>';
                echo '</tr>';
            }
        ?>


    </tbody>
</table>



<ul>

</ul>

<?php include_once 'common/footer.php'; ?>