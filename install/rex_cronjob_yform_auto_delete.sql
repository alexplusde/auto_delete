INSERT INTO `rex_cronjob` 
(`name`, `description`, `type`, `parameters`, `interval`, `nexttime`, `environment`, `execution_moment`, `execution_start`, `status`, `createdate`, `createuser`, `updatedate`, `updateuser`) 
VALUES 
('Add-on Auto Delete: YForm', 'Automatisch erstellter Cronjob durch das Add-On auto_delete, das entsprechende Felder in YForm und der Datenbank durchsucht und automatisch löscht.', 'Alexplusde\AutoDelete\Cronjob\YFormTable', '[]', '{"minutes":"all","hours":[0],"days":[1],"weekdays":"all","months":[1]}', NOW(), '|frontend|backend|script|', 1, '0000-00-00 00:00:00', 0, NOW(), 'auto_delete', NOW(), 'auto_delete');
