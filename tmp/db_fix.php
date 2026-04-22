<?php
require_once __DIR__ . '/../app/Core/Config.php';
require_once __DIR__ . '/../config/database.php';

$db = Database::getInstance()->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS `inhouse_tasks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `assigned_by` int(11) NOT NULL,
    `assigned_to` int(11) NOT NULL,
    `task_name` varchar(255) NOT NULL,
    `requirements` text NOT NULL,
    `deadline` datetime NOT NULL,
    `attachment_path` varchar(255) DEFAULT NULL,
    `status` enum('Pending','Accepted','Completed','Overdue', 'Revision Requested') DEFAULT 'Pending',
    `accepted_at` datetime DEFAULT NULL,
    `acceptance_comment` text DEFAULT NULL,
    `completed_at` datetime DEFAULT NULL,
    `completion_details` text DEFAULT NULL,
    `completion_file_path` varchar(255) DEFAULT NULL,
    `manager_feedback` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$db->exec($sql);
echo "Inhouse tasks table created/verified successfully.\n";
