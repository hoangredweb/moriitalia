SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE `#__rwf_billinginfo`
	ADD `params` text NOT NULL DEFAULT '';

SET FOREIGN_KEY_CHECKS=1;