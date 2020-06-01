
START TRANSACTION;

UPDATE `options` SET `tab_id`=1 WHERE `key`='o_currency';

COMMIT;