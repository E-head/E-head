ALTER TABLE `acl_resources` ADD `title` VARCHAR( 100 ) NULL AFTER `name` ;

UPDATE `acl_resources` SET `title` = 'Администрирование' WHERE `name` = 'admin' ;
UPDATE `acl_resources` SET `title` = 'Права доступа' WHERE `name` = 'acl' ;
UPDATE `acl_resources` SET `title` = 'Заказы' WHERE `name` = 'orders' ;
UPDATE `acl_resources` SET `title` = 'Стоимость' WHERE `name` = 'cost' ;
UPDATE `acl_resources` SET `title` = 'Поставщики' WHERE `name` = 'suppliers' ;
UPDATE `acl_resources` SET `title` = 'Адрес' WHERE `name` = 'address' ;
UPDATE `acl_resources` SET `title` = 'Производство' WHERE `name` = 'production' ;
UPDATE `acl_resources` SET `title` = 'Монтаж' WHERE `name` = 'mount' ;
UPDATE `acl_resources` SET `title` = 'Выполнено' WHERE `name` = 'success' ;
UPDATE `acl_resources` SET `title` = 'Описание' WHERE `name` = 'description' ;
UPDATE `acl_resources` SET `title` = 'Только свои заказы' WHERE `name` = 'owncheck' ;
UPDATE `acl_resources` SET `title` = 'Начало (план)' WHERE `name` = 'start_planned' ;
UPDATE `acl_resources` SET `title` = 'Конец (план)' WHERE `name` = 'end_planned' ;
UPDATE `acl_resources` SET `title` = 'Начало (факт)' WHERE `name` = 'start_fact' ;
UPDATE `acl_resources` SET `title` = 'Конец (факт)' WHERE `name` = 'end_fact' ;
UPDATE `acl_resources` SET `title` = 'План' WHERE `name` = 'planned' ;
UPDATE `acl_resources` SET `title` = 'Факт' WHERE `name` = 'fact' ;
UPDATE `acl_resources` SET `title` = 'Архив' WHERE `name` = 'archive' ;
UPDATE `acl_resources` SET `title` = 'Заказчики' WHERE `name` = 'customers' ;
UPDATE `acl_resources` SET `title` = 'Файлы' WHERE `name` = 'files' ;