CREATE TABLE `runtime_data` (
  `data_key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`data_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
