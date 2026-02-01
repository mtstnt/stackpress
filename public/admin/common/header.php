<?php
defined("ABSPATH") or die("You are not allowed to access this file.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - StackPress</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif; background: #f5f7fa; line-height: 1.6; color: #333; }
        header { background: #2c3e50; color: #fff; padding: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        header .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; }
        header h1 { font-size: 1.5em; margin: 0; }
        header nav ul { display: flex; list-style: none; gap: 20px; }
        header nav a { color: #ecf0f1; text-decoration: none; padding: 8px 16px; border-radius: 4px; transition: background 0.3s; }
        header nav a:hover { background: #34495e; }
        footer { text-align: center; padding: 30px 20px; margin-top: 40px; color: #7f8c8d; font-size: 0.9em; }
        main { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .admin-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.1); border-radius: 8px; overflow: hidden; }
        .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .admin-table th { background: #34495e; color: #fff; font-weight: 600; }
        .admin-table tbody tr:hover { background: #f8f9fa; }
        .admin-table tbody tr:last-child td { border-bottom: none; }
        .admin-table a { color: #3498db; text-decoration: none; }
        .admin-table a:hover { text-decoration: underline; }
        .admin-btn { display: inline-block; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: 500; transition: background 0.3s; margin-right: 8px; }
        .admin-btn-primary { background: #3498db; color: #fff; }
        .admin-btn-primary:hover { background: #2980b9; }
        .admin-btn-success { background: #27ae60; color: #fff; }
        .admin-btn-success:hover { background: #229954; }
        .admin-action-btn { display: inline-block; padding: 4px 10px; border-radius: 3px; text-decoration: none; font-size: 0.9em; margin-right: 4px; }
        .admin-action-btn.edit { background: #3498db; color: #fff; }
        .admin-action-btn.delete { background: #e74c3c; color: #fff; }
        .admin-message { padding: 15px; margin-bottom: 20px; border-radius: 4px; font-weight: 500; }
        .admin-message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .admin-message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .admin-form { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .admin-form label { display: block; margin-bottom: 8px; font-weight: 500; color: #2c3e50; }
        .admin-form input[type="text"], .admin-form textarea { width: 100%; padding: 12px; border: 1px solid #bdc3c7; border-radius: 4px; font-size: 1em; font-family: inherit; margin-bottom: 20px; }
        .admin-form input[type="text"]:focus, .admin-form textarea:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        .admin-form textarea { min-height: 200px; resize: vertical; }
        .admin-form button { padding: 12px 24px; border: none; border-radius: 4px; font-size: 1em; cursor: pointer; font-weight: 500; transition: background 0.3s; }
        .admin-form button[type="submit"] { background: #3498db; color: #fff; }
        .admin-form button[type="submit"]:hover { background: #2980b9; }
        .admin-form button[type="submit"].delete { background: #e74c3c; }
        .admin-form button[type="submit"].delete:hover { background: #c0392b; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>StackPress Admin</h1>
            <nav>
                <ul>
                    <li><a href="/public/admin/index.php">Posts</a></li>
                    <li><a href="/public/admin/form.php">New Post</a></li>
                    <li><a href="/public/admin/publish.php">Publish</a></li>
                    <li><a href="/public/build/index.html" target="_blank">View Site</a></li>
                </ul>
            </nav>
        </div>
    </header>
