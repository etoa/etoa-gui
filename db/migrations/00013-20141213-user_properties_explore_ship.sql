ALTER TABLE `user_properties` ADD `exploreship_id` INT( 10 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `analyzeship_count` ,
ADD `exploreship_count` INT( 5 ) UNSIGNED NOT NULL DEFAULT '1' AFTER `exploreship_id` ;
