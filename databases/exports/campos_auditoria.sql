ALTER TABLE `biblioneet_dev`.`` 
ADD COLUMN `createdby` INT(10) UNSIGNED NOT NULL COMMENT 'User creator ID',
ADD COLUMN `createdon` DATETIME NOT NULL DEFAULT NOW() COMMENT 'Record creation date' AFTER `createdby`,
ADD COLUMN `updatedby` INT(10) UNSIGNED NULL COMMENT 'User ID update record' AFTER `createdon`,
ADD COLUMN `updatedon` DATETIME NULL COMMENT 'Last update record' AFTER `updatedby`;

ALTER TABLE `biblioneet_dev`.`` 
ADD INDEX `fk__createdby_idx` (`createdby` ASC),
ADD INDEX `fk__updatedby_idx` (`updatedby` ASC);
ALTER TABLE `biblioneet_dev`.`` 
ADD CONSTRAINT `fk__createdby`
  FOREIGN KEY (`createdby`)
  REFERENCES `biblioneet_dev`.`users` (`uid`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `fk__updatedby`
  FOREIGN KEY (`updatedby`)
  REFERENCES `biblioneet_dev`.`users` (`uid`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;