<?php
require_once 'DBManager.php';

if (file_exists('scripts/db_ddl.sql')) {
    // file exists
    $sql = file_get_contents('scripts/db_ddl.sql');
    try {
        $dbMan = new DBManager();
        $dbMan->getConnection()->exec($sql);
        echo "\nDatabase and tables created successfully!\n";
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}