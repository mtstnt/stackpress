<?php
    defined("ABSPATH") or die("You are not allowed to access this file.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - My Blog</title>
    <link rel="stylesheet" href="/public/common/style.css">
    <link rel="stylesheet" href="/public/admin/common/admin-style.css">
</head>
<body>
    <header>
        <h1>Admin Panel</h1>
        <nav>
            <ul>
                <li><a href="/public/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/public/admin/posts.php">Posts</a></li>
                <li><a href="/public/admin/users.php">Users</a></li>
                <li><a href="/public/admin/settings.php">Settings</a></li>
                <li><a href="/public/index.php" target="_blank">View Site</a></li>
                <li><a href="/public/admin/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
