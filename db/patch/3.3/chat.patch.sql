ALTER TABLE `chat`
ADD `channel_id` int(10) unsigned NOT NULL DEFAULT '0',
DROP `channel`,
DROP `private`;

CREATE TABLE IF NOT EXISTS `chat_channels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` enum('public','alliance','private','') NOT NULL,
  `permanent` int(11) NOT NULL,
  `topic` varchar(255) NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `alliance_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;