<?php
require_once '../include/config.php';
require_once '../include/functions.php';
session_destroy();
redirect(SITE_URL . '/admin/login.php');
