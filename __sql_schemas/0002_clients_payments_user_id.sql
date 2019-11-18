ALTER TABLE client_payments ADD user_id INT(11) NULL AFTER client_id ;
ALTER TABLE supplier_payments ADD user_id INT(11) NULL AFTER supplier_id;