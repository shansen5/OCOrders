

-- structure

CREATE TABLE `locations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(40) NOT NULL,
    `address_id` INT UNSIGNED NOT NULL REFERENCES `addresses`.`id`
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `states` (
    `name` VARCHAR(30),
    `abbr` VARCHAR(2) PRIMARY KEY
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `addresses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `street1` VARCHAR(80) NOT NULL,
    `street2` VARCHAR(80) NOT NULL,
    `city` VARCHAR(80) NOT NULL,
    `state` VARCHAR(2) NOT NULL REFERENCES `states`.`abbr`,
    `postal_code` VARCHAR(10) NOT NULL,
    `country` VARCHAR(20)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `customers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(80) NOT NULL,
    `last_name` VARCHAR(80) NOT NULL,
    `phone` VARCHAR(20),
    `email` VARCHAR(80),
    `address_id` INT UNSIGNED REFERENCES `addresses`.`id` 
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `item_type` VARCHAR(20),
    `sub_type` VARCHAR(20)
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `order_items` (
    `order_id` INT UNSIGNED NOT NULL REFERENCES `orders`.`id`,
    `item_id` INT UNSIGNED NOT NULL REFERENCES `items`.`id`,
    `quantity` INT UNSIGNED NOT NULL DEFAULT 1,
    `order_date` DATETIME NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE `orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT UNSIGNED NOT NULL REFERENCES `customer`.`id`,
    `created_on` DATETIME NOT NULL,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `frequency` ENUM('DAILY', 'WEEKLY', 'BI-WEEKLY', 'MONTHLY') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'WEEKLY',
    `day_of_week` ENUM ('MONDAY','TUESDAY','WEDNESDAY','THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `pickup_location` INT UNSIGNED NOT NULL REFERENCES `locations`.`id`
) ENGINE = MYISAM DEFAULT CHARSET=utf8;


-- ALTER TABLE `todo` ADD INDEX (`priority`);
-- ALTER TABLE `todo` ADD INDEX (`due_on`);
-- ALTER TABLE `todo` ADD INDEX (`status`);
-- ALTER TABLE `todo` ADD INDEX (`deleted`);

-- data
INSERT INTO `states` (`name`, `abbr`)
    VALUES ('Alabama', 'AL'),
        ('Alaska', 'AK'),
        ('American Samoa', 'AS'),
        ('Arizona', 'AZ'),
        ('Arkansas', 'AR'),
        ('California', 'CA'),
        ('Colorado', 'CO'),
        ('Connecticut', 'CT'),
        ('Delaware', 'DE'),
        ('District of Columbia', 'DC'),
        ('Florida', 'FL'),
        ('Georgia', 'GA'),
        ('Guam', 'GU'),
        ('Hawaii', 'HI'),
        ('Idaho', 'ID'),
        ('Illinois', 'IL'),
        ('Iowa', 'IA'),
        ('Kansas', 'KS'),
        ('Kentucky', 'KY'),
        ('Louisiana', 'LA'),
        ('Maine', 'ME'),
        ('Maryland', 'MD'),
        ('Marshall Islands', 'MH'),
        ('Massachusetts', 'MA'),
        ('Michigan', 'MI'),
        ('Micronesia', 'FM'),
        ('Minnesota', 'MN'),
        ('Mississippi', 'MS'),
        ('Missouri', 'MO'),
        ('Montana', 'MT'),
        ('Nebraska', 'NE'),
        ('Nevada', 'NV'),
        ('New Hampshire', 'NH'),
        ('New Jersey', 'NJ'),
        ('New Mexico', 'NM'),
        ('New York', 'NY'),
        ('North Carolina', 'NC'),
        ('North Dakota', 'ND'),
        ('North Marianas', 'MP'),
        ('Ohio', 'OH'),
        ('Oklahoma', 'OK'),
        ('Oregon', 'OR'),
        ('Palau', 'PW'),
        ('Pennsylvania', 'PA'),
        ('Puerto Rico', 'PR'),
        ('Rhode Island', 'RI'),
        ('South Carolina', 'SC'),
        ('South Dakota', 'SD'),
        ('Tennessee', 'TN'),
        ('Texas', 'TX'),
        ('Utah', 'UT'),
        ('Vermont', 'VT'),
        ('Virginia', 'VA'),
        ('Washington', 'WA'),
        ('West Virginia', 'WV'),
        ('Wisconsin', 'WI'),
        ('Wyoming', 'WY');
