INSERT INTO `payment_methods` (`title`, `type`, `description`, `online_payable`, `available_on_invoice`, `minimum_payment_amount`, `settings`, `deleted`) VALUES
('Paystack', 'paystack', 'Paystack payments', 1, 0, 0, '', 1); #

CREATE TABLE IF NOT EXISTS `paystack_ipn` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`transaction_id` TEXT NOT NULL,
`verification_code` TEXT NOT NULL,
`payment_verification_code` TEXT NOT NULL,
`invoice_id` int(11) NOT NULL,
`contact_user_id` int(11) NOT NULL,
`client_id` int(11) NOT NULL,
`payment_method_id` int(11) NOT NULL,
`deleted` tinyint(1) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;