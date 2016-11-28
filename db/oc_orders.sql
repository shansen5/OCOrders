-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 02, 2016 at 02:26 PM
-- Server version: 5.7.15-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oc_orders`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--
DROP TABLE `addresses` IF EXISTS;
CREATE TABLE `addresses` (
  `id` int(10) UNSIGNED NOT NULL,
  `street1` varchar(80) NOT NULL,
  `street2` varchar(80) NOT NULL,
  `city` varchar(80) NOT NULL,
  `state` varchar(2) NOT NULL REFERENCES `states`(`abbr`),
  `postal_code` varchar(10) NOT NULL,
  `country` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `customers`
--

DROP TABLE `customers` IF EXISTS;
CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `address_id` int(10) UNSIGNED DEFAULT NULL REFERENCES `addresses`(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `items`
--

DROP TABLE `items` IF EXISTS;
CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `item_type` varchar(20) DEFAULT NULL,
  `sub_type` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `locations`
--

DROP TABLE `locations` IF EXISTS;
CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(40) NOT NULL,
  `address_id` int(10) UNSIGNED NOT NULL REFERENCES `addresses`(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table structure for table `orders`
--

DROP TABLE `orders` IF EXISTS;
CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL REFERENCES `customers`(`id`),
  `created_on` datetime NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `frequency` enum('DAILY','WEEKLY','BI-WEEKLY','MONTHLY','ONCE') NOT NULL DEFAULT 'WEEKLY',
  `day_of_week` enum('MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY') NOT NULL,
  `pickup_location` int(10) UNSIGNED NOT NULL REFERENCES `locations`(`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE `order_items` IF EXISTS;
CREATE TABLE `order_items` (
  `order_id` int(10) UNSIGNED NOT NULL REFERENCES `orders`(`id`),
  `item_id` int(10) UNSIGNED NOT NULL REFERENCES `items`(`id`),
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `order_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `states`
--

DROP TABLE `states` IF EXISTS;
CREATE TABLE `states` (
  `name` varchar(30) DEFAULT NULL,
  `abbr` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE `order_dates` IF EXISTS;
CREATE TABLE `states` (
  `name` varchar(30) DEFAULT NULL,
  `abbr` varchar(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `states`
--
ALTER TABLE `states`
  ADD PRIMARY KEY (`abbr`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

create or replace view `order_view` as 
select customers.id as customer_id, customers.first_name as first_name, 
    customers.last_name as last_name, orders.id as order_id, 
    order_items.order_date as order_date, order_items.quantity as quantity, 
    items.item_type as item_type, items.sub_type as sub_type, 
    locations.id as location_id, locations.name as location_name 
from customers, orders, items, order_items, locations 
where orders.customer_id = customers.id 
    and orders.pickup_location = locations.id 
    and orders.id = order_items.order_id and order_items.item_id = items.id;

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

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `street1`, `street2`, `city`, `state`, `postal_code`, `country`) VALUES
(1, '2644 Donovan \r\nAve.', '', 'Bellingham', 'WA', '98225', NULL),
(2, '919 Pennington Loop', '', 'Coupeville', 'WA', '98239', NULL),
(3, '901 Grace Street', '', 'Coupeville', 'WA', '98239', NULL);

-- --------------------------------------------------------
--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`, `address_id`) VALUES
(1, 'Oystercatcher restaurant', 3);

-- --------------------------------------------------------
--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_type`, `sub_type`) VALUES
(1, 'loaf 1.5 lb', 'molasses oatmeal'),
(2, 'loaf 1.5 lb', 'rosemary olive'),
(3, 'loaf 2.0 lb', 'sourdough');

-- --------------------------------------------------------
--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `phone`, `email`, `address_id`) VALUES
(1, 'Steve', 'Hansen', '2062353486', 'shansen5@yahoo.com', 1),
(2, 'Karen', 'Sheldon', '2062353489', 'karsheldon@yahoo.com', 1),
(3, 'Sara', 'Hansen', NULL, 'sarajothompson@gmail.com', 2);

-- --------------------------------------------------------

