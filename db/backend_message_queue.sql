CREATE TABLE `backend_message_queue` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `cmd` varchar(255) NOT NULL,
 `arg` varchar(255) NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `cmd` (`cmd`,`arg`)
) ENGINE=MEMORY AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
