<?php
    require 'config/database.php';
    session_destroy();
    header('location:/blog');
    die();
?>