<?php
define("ABSPATH", dirname(__DIR__, 2));

require_once ABSPATH . '/app/Build.php';

(new Build())->execute();

header('Location: /public/admin/index.php');
exit;