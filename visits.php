<?php
require_once './db.php';
$visits = $db->fetch('SELECT * FROM visits WHERE user_agent NOT LIKE "%bot%" ORDER BY id DESC LIMIT 500');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
    <table class="table table-bordered table-striped table-hover table-responsive align-middle">
        <thead class="table-dark">
            <tr>
                <th> id </th>
                <th> user_agent </th>
                <th> ip </th>
                <th> url </th>
                <th> visited_at </th>
                <th> request </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visits as $visit) { ?>
                <tr>
                    <td> <?= htmlspecialchars($visit['id'], ENT_QUOTES, 'UTF-8') ?> </td>
                    <td> <?= htmlspecialchars($visit['user_agent'], ENT_QUOTES, 'UTF-8') ?> </td>
                    <td> <?= htmlspecialchars($visit['ip'], ENT_QUOTES, 'UTF-8') ?> </td>
                    <td> <?= htmlspecialchars($visit['url'], ENT_QUOTES, 'UTF-8') ?> </td>
                    <td> <?= htmlspecialchars($visit['visited_at'], ENT_QUOTES, 'UTF-8') ?> </td>
                    <td> <?= htmlspecialchars($visit['request'], ENT_QUOTES, 'UTF-8') ?> </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>