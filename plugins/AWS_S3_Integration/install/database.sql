CREATE TABLE IF NOT EXISTS `aws_s3_integration_settings` (
  `setting_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `setting_value` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'app',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; #

INSERT INTO `aws_s3_integration_settings` (`setting_name`, `setting_value`, `deleted`) VALUES ('aws_s3_integration_item_purchase_code', 'AWS_S3_Integration-ITEM-PURCHASE-CODE', 0); #

