
START TRANSACTION;

UPDATE `options` SET `value`='1|0::1' WHERE `key`='o_multi_lang';

COMMIT;