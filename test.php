<?php require 'includes/db.php'; print_r($pdo->query('SELECT * FROM course_details LIMIT 1')->fetch(PDO::FETCH_ASSOC)); ?>
