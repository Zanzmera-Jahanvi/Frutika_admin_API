-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2023 at 02:55 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.0.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `frutikadb`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `addAddress` (IN `u_id` INT(11), IN `u_city` VARCHAR(50), IN `u_state` VARCHAR(50), IN `u_country` VARCHAR(25), IN `u_add_1` VARCHAR(255), IN `u_add_2` VARCHAR(255), IN `zip_code` INT(10), OUT `is_done` TINYINT(4), OUT `last_user_id` INT(11))   BEGIN

SET is_done = 0;
SET last_user_id = 0;

INSERT INTO `tbl_address` (`user_id`,`city`,`state`,`country`,`addressLine1`,`addressLine2`,`postal_code`) VALUES (u_id,u_city,u_state,u_country,u_add_1,u_add_2,zip_code);

IF Row_Count() > 0 THEN
SET last_user_id  = last_insert_id();
SET is_done = 1;
END IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addCategory` (IN `c_name` VARCHAR(50), IN `c_cat_description` TEXT, IN `c_creator_id` INT(11), IN `c_cat_type` VARCHAR(50), IN `season_info` TEXT, IN `nut_info` TEXT, IN `storage_info` TEXT, IN `qty` INT(11), OUT `last_cat_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN
set last_cat_id = 0;
set is_done = 0 ;
INSERT INTO `tbl_category` (`cat_name`,`cat_description`,`cat_creator_id`,`cat_type`,`season`,`nutritional_information`,`storage_desc`,`quantity`)
VALUES (c_name,c_cat_description,c_creator_id,c_cat_type,season_info,nut_info,storage_info,qty);


if Row_Count() > 0 THEN
set last_cat_id = last_insert_id();
set is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addCoupon` (IN `code` VARCHAR(20), IN `pur_amt` INT(11), IN `off_price` INT(11), IN `cid` INT(11), IN `pid` INT(11), IN `c_type` VARCHAR(25), IN `c_status` TINYINT(4), IN `exp_date` TIMESTAMP, IN `isUsed` TINYINT(4), IN `c_creator_id` INT(11), IN `min_p_amt` INT(11), IN `max_dis_amt` INT(11), IN `u_count` INT(11), IN `u_limit` INT(11), IN `restriction` TEXT, IN `use_instructions` TEXT, OUT `is_done` TINYINT(4), OUT `last_coupon_code` INT(11))   BEGIN
SET is_done = 0;
SET last_coupon_code = 0;

INSERT INTO `tbl_coupon`(`coupon_code`,`purchase_amount`,`offer_price`,`cat_id`,`pro_id`,`coupon_type`,`coupon_status`,`expire_date`,`is_used`,`coupon_creator_id`,
                         `min_purchase_amount`,`max_discount_amount`,`usage_count`,`usage_limit`,`user_restrictions`,`instructions`)
VALUES(code,pur_amt,off_price,cid,pid,c_type,c_status,exp_date,isUsed,c_creator_id,min_p_amt,max_dis_amt,u_count,u_limit,restriction,use_instructions);

IF Row_Count() > 0 THEN
    set is_done = 1;
    set last_coupon_code = last_insert_id();
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addProduct` (IN `p_name` VARCHAR(255), IN `p_desc` TEXT, IN `price` BIGINT(11), IN `pr_qty` INT(11), IN `cid` INT(11), IN `adminID` INT(11), IN `nut_info` TEXT, IN `origin_info` TEXT, IN `season_info` TEXT, IN `weight_info` BIGINT(20), IN `storage_info` TEXT, IN `bb_date` TIMESTAMP, OUT `last_pro_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN

set last_pro_id = 0;
set is_done = 0 ;

INSERT INTO 
`tbl_product`
(`pro_name`,`pro_desc`,`pro_price`,`pro_qty`,`pro_category_id`,`pro_creator_id`,`nutrition_information`,`origin`,
 `season`,`weight`,`storage_instrustion`,`best_before_date`)
VALUES (p_name,p_desc,price,pr_qty,cid,adminID,nut_info,origin_info,season_info,weight_info,storage_info,bb_date);


if Row_Count() > 0 THEN
set last_pro_id = last_insert_id();
set is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addToCart` (IN `u_id` INT(11), IN `p_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN

SET is_done = 0;

INSERT INTO `tbl_cart` (`user_id`,`pro_id`) VALUES (u_id,p_id);

IF Row_Count() > 0 THEN
SET is_done = 1;
END IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `addTofavourite` (IN `pr_id` INT(11), IN `u_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN
set is_done = 0;

INSERT INTO `tbl_favourite` (`pro_id`,`user_id`)
VALUES (pr_id,u_id);


if Row_Count() > 0 THEN
set is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `adminChangePassword` (IN `ad_id` INT(11), IN `pass` VARCHAR(255), OUT `is_done` TINYINT(4))   BEGIN

SET is_done = 0;

UPDATE `tbl_admin`
SET password = pass,
  date_modified = NOW()
WHERE admin_id = ad_id; 

IF Row_Count() > 0 THEN
	SET is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `admin_login` (IN `admin_email` VARCHAR(50), IN `admin_password` VARCHAR(30))   BEGIN

SELECT * FROM `tbl_admin` 
WHERE
admin_email_id = admin_email AND
password = admin_password;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `changePassword` (IN `u_id` INT(11), IN `pass` VARCHAR(50), OUT `is_done` TINYINT(4))   BEGIN

SET is_done = 0;

UPDATE `tbl_user`
SET password = pass,
  date_modified = NOW()
WHERE user_id = u_id; 

IF Row_Count() > 0 THEN
	SET is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCategory` (IN `c_cat_id` INT(11), IN `c_admin_id` INT(11), OUT `is_done` TINYINT(1))   BEGIN
set is_done =  0;

DELETE FROM `tbl_category`
WHERE cat_id = c_cat_id AND
cat_creator_id = c_admin_id;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteCoupon` (IN `c_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN
set is_done =  0;

DELETE FROM `tbl_coupon`
WHERE coupon_id = c_id; 

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteProduct` (IN `p_pro_id` INT(11), IN `p_pro_creator_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN
set is_done =  0;

DELETE FROM `tbl_product`
WHERE pro_id = p_pro_id AND
pro_creator_id = p_pro_creator_id;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `editAdminProfile` (IN `ad_id` INT(11), IN `u_name` VARCHAR(50), IN `email` VARCHAR(50), IN `cont_num` VARCHAR(15), IN `admin_gender` VARCHAR(20), IN `admin_DOB` VARCHAR(20), OUT `is_done` TINYINT(4))   BEGIN

SET is_done = 0;

UPDATE `tbl_admin`
SET user_name = u_name,
	admin_email_id = email,
	contact_num = cont_num,
	gender = admin_gender,
    DOB = admin_DOB,
    date_modified = NOW()
 
 
WHERE admin_id = ad_id;

IF Row_Count() > 0 THEN
	SET is_done = 1;
end IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `EditUserProfile` (IN `u_id` INT(11), IN `u_name` VARCHAR(50), IN `contact_number` VARCHAR(15), IN `user_email` VARCHAR(50), IN `user_gender` VARCHAR(20), IN `user_DOB` VARCHAR(30), OUT `is_done` TINYINT(4))   BEGIN

SET is_done = 0;

UPDATE `tbl_user`
SET user_name = u_name,
	contact_num = contact_number,
	email = user_email,
	gender = user_gender,
    DOB = user_DOB,
    date_modified = NOW()
 
 
WHERE user_id = u_id;

IF Row_Count() > 0 THEN
	SET is_done = 1;
end IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fatchAllCategory` ()   BEGIN
SELECT * FROM `tbl_category`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fatchAllCoupon` ()   BEGIN

SELECT * FROM `tbl_coupon` ORDER by coupon_id DESC;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fatchAllOrders` ()   BEGIN

SELECT * FROM tbl_order;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fatchAllProduct` ()   BEGIN

SELECT * FROM `tbl_product`;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fatchSingleCategory` (IN `category_id` INT(11))   BEGIN

SELECT * FROM `tbl_category`
WHERE cat_id = category_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fatchSingleProduct` (IN `product_id` INT(11))   BEGIN
SELECT * FROM `tbl_product` 
WHERE `pro_id` = product_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetchSingleCoupon` (IN `coup_id` INT(11))   BEGIN

SELECT cp.*,c.cat_name,p.pro_name 
from tbl_coupon as cp,tbl_category as c,tbl_product as p 
WHERE cp.cat_id = c.cat_id and cp.pro_id = p.pro_id and cp.coupon_id = coup_id;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `fetchUserAddress` (IN `u_id` INT(11))   BEGIN

SELECT * FROM tbl_address WHERE user_id = u_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getAllUserList` ()   SELECT * FROM `tbl_user`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getCartList` (IN `u_id` INT(11))   BEGIN

SELECT p.*
from tbl_product as  p,tbl_cart as c
WHERE p.pro_id = c.pro_id and c.user_id = u_id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getFavList` (IN `uid` INT(11))   BEGIN

SELECT DISTINCT  p.* 
FROM tbl_product as p,tbl_favourite as f 
WHERE p.pro_id = f.pro_id and f.user_id = uid;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `giveFeedback` (IN `rate` INT(11), IN `uid` INT(11), IN `pr_id` INT(11), IN `feedback_text` TEXT, OUT `is_done` TINYINT(4))   BEGIN

SET is_done = 0;

INSERT INTO `tbl_feedback` (`rating`,`user_id`,`pro_id`,`feedback`,`date_submitted`) VALUES (rate,uid,pr_id,feedback_text,NOW());

IF Row_Count() > 0 THEN
SET is_done = 1;
END IF;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `placeOrder` (IN `uid` INT(11), IN `pid` INT(11), IN `p_name` VARCHAR(50), IN `p_price` INT(11), IN `p_qty` INT(11), IN `o_status` VARCHAR(50), IN `ship_add` TEXT, IN `pay_method` TEXT, IN `t_cost` BIGINT(20), IN `dis_amt` INT(11), IN `ship_cost` INT(11), IN `bill_add` TEXT, IN `d_date_time` DATETIME, IN `pick_date` DATE, IN `pick_time` TIME, IN `g_order` INT(11), IN `g_msg` TEXT, IN `pay_status` VARCHAR(50), IN `pay_date_time` DATETIME, IN `ref_status` VARCHAR(50), IN `ref_amout` INT(11), IN `ret_status` VARCHAR(50), OUT `last_order_id` INT(11), OUT `is_done` TINYINT(4))   BEGIN
SET is_done = 0;
SET last_order_id = 0;

INSERT INTO `tbl_order`(`user_id`,`pro_id`,`pro_name`,`pro_price`,`pro_qty`,`order_status`,`shipping_address`,`payment_method`,          `total_cost`,`discount_amount`,`shipping_cost`,`billing_address`,`order_tracking_no`,`delivery_date_time`,`pickup_date`,`pickup_time`,
 `gift_order`,`gift_message`,`payment_status`,`payment_date_time`,`refund_status`,`refund_amount`,`return_status`)
VALUES(uid,pid,p_name,p_price,p_qty,o_status,ship_add,pay_method,t_cost,dis_amt,ship_cost,bill_add,SUBSTR(MD5(RAND()), 1, 10),d_date_time,pick_date,pick_time,g_order,g_msg,pay_status,pay_date_time,ref_status,ref_amout,ret_status);

IF Row_Count() > 0 THEN
    set is_done = 1;
    set last_order_id = last_insert_id();
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `removeFromCart` (IN `uid` INT(11), IN `pid` INT(11), OUT `is_done` TINYINT(4))   BEGIN
SET is_done = 0;

DELETE FROM `tbl_cart` WHERE user_id = uid and pro_id = pid;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `removeFromFavourite` (IN `uid` INT(11), IN `pid` INT(11), OUT `is_done` TINYINT(4))   BEGIN
SET is_done = 0;

DELETE FROM `tbl_favourite`
WHERE pro_id = pid AND user_id = uid;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchCatogory` (IN `category_name` VARCHAR(50))   BEGIN

SELECT * FROM `tbl_category`
WHERE `cat_name` = category_name;


END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchProduct` (IN `product_name` VARCHAR(50))   BEGIN

SELECT * FROM `tbl_product`
WHERE `pro_name` = product_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `searchUser` (IN `U_user_name` VARCHAR(50))   BEGIN

SELECT * FROM `tbl_user`
WHERE user_name = U_user_name;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateAddress` (IN `uid` INT(11), IN `u_city` VARCHAR(50), IN `u_state` VARCHAR(50), IN `u_country` VARCHAR(25), IN `u_add_1` VARCHAR(255), IN `u_add_2` VARCHAR(255), IN `zip_code` INT(10), OUT `is_done` TINYINT(4))   BEGIN
set is_done =  0;

UPDATE `tbl_address`
SET city = u_city,
	state =u_state,
    country = u_country,
    addressLine1 = u_add_1,
    addressLine2 = u_add_2,
    postal_code = zip_code,
	date_modified = NOW()
    
WHERE user_id = uid;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCategory` (IN `c_cat_id` INT(11), IN `c_cat_name` VARCHAR(50), IN `c_cat_desc` TEXT, IN `c_cat_creator_id` INT(11), IN `cat_type` VARCHAR(50), IN `season_info` TEXT, IN `nut_info` TEXT, IN `storage_info` TEXT, IN `qty` INT(11), OUT `is_done` TINYINT(4))   BEGIN
set is_done =  0;

UPDATE `tbl_category`
SET cat_name = c_cat_name,
	cat_description = c_cat_desc,
    cat_creator_id = c_cat_creator_id,
    cat_type = cat_type,
	date_modified = NOW(),
    season = season_info,
    nutritional_information = nut_info,
    storage_desc = storage_info,
    quantity = qty
    
WHERE cat_id = c_cat_id;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateCoupon` (IN `cou_id` INT(11), IN `pur_amt` INT(11), IN `off_price` INT(11), IN `cid` INT(11), IN `pid` INT(11), IN `c_type` VARCHAR(25), IN `c_status` TINYINT(4), IN `exp_date` TIMESTAMP, IN `c_creator_id` INT(11), IN `min_p_amount` INT(11), IN `max_dis_amount` INT(11), IN `u_count` INT(11), IN `u_limit` INT(11), IN `us_res` TEXT, IN `use_instructions` TEXT, OUT `is_done` TINYINT(4))   BEGIN
set is_done =  0;

UPDATE `tbl_coupon`
SET purchase_amount = pur_amt,
	offer_price = off_price,
    cat_id = cid,
    pro_id = pid,
    coupon_type = c_type,
    coupon_status = c_status,
    expire_date = exp_date,
    coupon_creator_id = c_creator_id,
	date_modified = NOW(),
    min_purchase_amount = min_p_amount,
    max_discount_amount = max_dis_amount,
    usage_count = u_count,
    usage_limit = u_limit,
    user_restrictions = us_res,
    instructions = use_instructions
    
WHERE coupon_id = cou_id;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `updateProduct` (IN `p_prod_id` INT(11), IN `p_pro_name` VARCHAR(50), IN `p_pro_desc` TEXT, IN `p_pro_price` INT(11), IN `p_pro_qty` INT(11), IN `p_pro_cate_id` INT(11), IN `p_admin_id` INT(11), IN `nut_info` TEXT, IN `origin_info` TEXT, IN `season_info` TEXT, IN `weight_info` BIGINT(20), IN `store_info` TEXT, IN `bb_date` TIMESTAMP, OUT `is_done` TINYINT(4))   BEGIN
set is_done =  0;

UPDATE `tbl_product`
SET  pro_name = p_pro_name,
	 pro_desc = p_pro_desc,
     pro_price = p_pro_price,
     pro_qty = p_pro_qty,
     pro_category_id = p_pro_cate_id,
     pro_creator_id = p_admin_id,
     date_modified = NOW(),
     nutrition_information = nut_info,
     origin = origin_info,
     season = season_info,
     weight = weight_info,
     storage_instrustion = store_info,
     best_before_date = bb_date
    
WHERE pro_id = p_prod_id;

IF Row_Count() > 0 THEN
	set is_done = 1;
end IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `userLogIn` (IN `user_name` VARCHAR(50), IN `user_password` VARCHAR(255))   BEGIN

SELECT * FROM `tbl_user`
WHERE email = user_name AND
password = user_password;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `userSignUp` (IN `u_user_name` VARCHAR(50), IN `user_contact` VARCHAR(15), IN `user_email` VARCHAR(50), IN `user_password` VARCHAR(255), OUT `is_done` TINYINT(4), OUT `last_user_id` INT(11))   BEGIN
SET last_user_id = 0;
SET is_done = 0;

INSERT INTO `tbl_user` (`user_name`,`contact_num`,`email`,`password`) VALUES (u_user_name,user_contact,user_email,user_password);
	

IF Row_Count() > 0 THEN
SET last_user_id = last_insert_id();
SET is_done = 1;
END IF;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_address`
--

CREATE TABLE `tbl_address` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `addressLine1` text NOT NULL,
  `addressLine2` text NOT NULL,
  `postal_code` int(10) NOT NULL,
  `default_address` tinyint(1) DEFAULT 0 COMMENT '0 for default ',
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_address`
--

INSERT INTO `tbl_address` (`address_id`, `user_id`, `city`, `state`, `country`, `addressLine1`, `addressLine2`, `postal_code`, `default_address`, `date_modified`) VALUES
(1, 1, 'surat', 'gujarat', 'india', 'Ganga Naka', 'near by gandhi vidhyalay', 395004, 0, '2023-03-30 10:42:10'),
(2, 2, 'surat', 'gujarat', 'india', 'katar gam ', 'near by gandhi vidhyalay', 395004, 0, '2023-03-30 10:42:24'),
(3, 3, 'surat', 'gujarat', 'india', 'katar gam ', 'ved road char rasta', 395004, 0, '2023-03-30 10:42:38'),
(4, 4, 'surat', 'gujarat', 'india', 'katar gam ', 'ved road char rasta', 395004, 0, '2023-03-30 10:42:44'),
(5, 5, 'surat', 'gujarat', 'india', 'katar gam ', 'ved road char rasta ', 395004, 0, '2023-03-30 10:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `admin_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `admin_email_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_num` varchar(15) NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) NOT NULL,
  `gender` varchar(25) NOT NULL,
  `DOB` date NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`admin_id`, `user_name`, `admin_email_id`, `password`, `contact_num`, `register_date`, `profile_image`, `gender`, `DOB`, `date_modified`) VALUES
(1, 'admin', 'admin@gmail.com', 'admin123', '7845874521', '2023-03-06 04:59:27', '1678080412_img.png', 'male', '2022-09-15', '2023-03-06 05:42:20');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cart`
--

CREATE TABLE `tbl_cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cart`
--

INSERT INTO `tbl_cart` (`cart_id`, `user_id`, `pro_id`, `date_added`) VALUES
(1, 5, 2, '2023-03-30 12:28:52'),
(2, 4, 1, '2023-03-30 12:28:58'),
(3, 2, 1, '2023-03-30 12:29:00'),
(4, 4, 2, '2023-03-30 12:29:07'),
(5, 1, 1, '2023-03-30 12:36:30'),
(6, 2, 1, '2023-03-30 12:36:32'),
(7, 4, 1, '2023-03-30 12:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(50) NOT NULL,
  `cat_description` text NOT NULL,
  `cat_creator_id` int(11) NOT NULL,
  `cat_date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `cat_type` varchar(50) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `season` text NOT NULL,
  `nutritional_information` text NOT NULL,
  `available` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for availble',
  `storage_desc` text NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`cat_id`, `cat_name`, `cat_description`, `cat_creator_id`, `cat_date_created`, `cat_type`, `date_modified`, `image`, `season`, `nutritional_information`, `available`, `storage_desc`, `quantity`) VALUES
(1, 'Apples', 'A lovely red hue with hints of yellow, this species is a hybrid of the Jonathan and the Golden Delicious and bears a faint physical resemblance to both. Like the Golden Delicious, Jonagold is sweet and thin-skinned, but it takes from the Jonathan a smooth skin and tart flavor. It is versatile and can be used in any recipe that calls for apples.', 1, '2023-03-30 16:53:42', 'Jonagold Apple', '2023-03-30 11:44:52', '1680176692Apples_img.jpg', 'The fruits ripen by mid-September. They are commonly found throughout grocery stores and farmers markets across the country in mid to late fall.', 'Calories: 52 Water: 86% Protein: 0.3 grams Carbs: 13.8 grams Sugar: 10.4 grams Fiber: 2.4 grams Fat: 0.2 grams', 1, 'The ideal storage temperature is 30 to 35 degrees F. with 90 to 95 percent relative humidity. If you don\'t have a lot of apples, the refrigerator is a good option. Place them in the crisper drawer in a plastic bag with holes in it or cover the apples with a damp paper towel.', 200),
(2, 'Barries', 'Berries tend to have a good nutritional profile. They’re typically high in fiber, vitamin C, and antioxidant polyphenols. Eating berries may help prevent and reduce the symptoms of many chronic diseases', 1, '2023-03-30 16:58:30', 'Blackberry', '2023-03-30 11:51:39', '1680177099Barries_img.jpg', 'They are available all through the year with the months between June and August being the peak season.', 'Fat: 0.7g Sodium: 1mg Carbohydrates: 13.8g Fiber: 7.6g Sugars: 7g Protein: 2g​ Potassium: 233.3mg Magnesium: 28.8mg Vitamin C: 30.2mg Folate: 36mcg Vitamin E: 1.7mg Vitamin K: 28.5mcg', 1, 'Storage/Handling: Temperature/humidity recommendation for short-term storage of 7 days or less: 32-36 degrees F / 0 _ 2 degrees C. 90 _ 98% relative humidity. Store blackberry containers in a single layer in a well ventilated area.', 200),
(3, 'Melon', 'Watermelon is a must at every summer soiree, and if you’ve tasted it before, it should be no mystery why. To pick a ripe one at the grocery store or farmers market, look for a watermelon that’s deep green in color with a cream- or yellow-colored ground spot. Pick it up and give the ground spot a hard tap—if it sounds deep and hollow, it’s ready to devour.', 1, '2023-03-30 17:05:20', 'Water melon', '2023-03-30 11:35:20', NULL, 'Watermelons are the summer seasonal fruit that aids as a one-single solution to all your summer-related health issues. ', 'Calories: 30\r\nWater: 91%\r\nProtein: 0.6 grams\r\nCarbs: 7.6 grams\r\nSugar: 6.2 grams\r\nFiber: 0.4 grams\r\nFat: 0.2 grams', 1, 'Watermelon stored at 50 to 60 °F with a relative humidity of 90% will be acceptable for up to 3 weeks. Watermelons held in dry storage below 75 °F will have approximate shelf life of up to 10 days. If dry storage temperatures are above 75 °F, shelf life will decline to 5 days.', 80);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_coupon`
--

CREATE TABLE `tbl_coupon` (
  `coupon_id` int(11) NOT NULL,
  `coupon_code` varchar(20) NOT NULL,
  `purchase_amount` bigint(20) NOT NULL,
  `offer_price` bigint(22) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `coupon_type` varchar(50) NOT NULL,
  `coupon_status` tinyint(4) NOT NULL,
  `expire_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_used` tinyint(4) NOT NULL,
  `coupon_date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp(),
  `coupon_creator_id` int(11) NOT NULL,
  `min_purchase_amount` bigint(20) NOT NULL,
  `max_discount_amount` bigint(20) NOT NULL,
  `usage_count` int(11) NOT NULL,
  `usage_limit` int(11) NOT NULL,
  `user_restrictions` text NOT NULL,
  `instructions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_coupon`
--

INSERT INTO `tbl_coupon` (`coupon_id`, `coupon_code`, `purchase_amount`, `offer_price`, `cat_id`, `pro_id`, `coupon_type`, `coupon_status`, `expire_date`, `is_used`, `coupon_date_created`, `date_modified`, `coupon_creator_id`, `min_purchase_amount`, `max_discount_amount`, `usage_count`, `usage_limit`, `user_restrictions`, `instructions`) VALUES
(1, 'FF8574521', 500, 200, 1, 1, 'free shipping', 0, '2022-12-30 18:30:00', 0, '2023-03-30 12:43:18', '2023-03-30 12:43:18', 1, 250, 100, 0, 5, 'Time-based restrictions: Coupons can be restricted based on a specific date range during which they can be redeemed, such as only being valid for a limited time period. User-based restrictions: Coupons can be restricted to specific users, such as only being valid for first-time customers, or only for users who have spent a certain amount of money with the company. Product-based restrictions: Coupons can be restricted to certain products or categories, such as only being valid for certain types of items or for items within a specific price range. Quantity-based restrictions: Coupons can be restricted based on the number of times they can be used, such as only being valid for a single use or being limited to a certain number of uses per user.', 'Follow the steps in order and don\'t skip any steps'),
(2, 'FF784521', 500, 200, 1, 2, 'free shipping', 0, '2022-12-30 18:30:00', 0, '2023-03-30 12:43:35', '2023-03-30 12:43:35', 1, 250, 100, 0, 5, 'Time-based restrictions: Coupons can be restricted based on a specific date range during which they can be redeemed, such as only being valid for a limited time period. User-based restrictions: Coupons can be restricted to specific users, such as only being valid for first-time customers, or only for users who have spent a certain amount of money with the company. Product-based restrictions: Coupons can be restricted to certain products or categories, such as only being valid for certain types of items or for items within a specific price range. Quantity-based restrictions: Coupons can be restricted based on the number of times they can be used, such as only being valid for a single use or being limited to a certain number of uses per user.', 'Follow the steps in order and don\'t skip any steps'),
(3, 'FF7685574', 500, 200, 2, 1, 'free shipping', 0, '2022-12-30 18:30:00', 0, '2023-03-30 12:43:48', '2023-03-30 12:43:48', 1, 250, 100, 0, 5, 'Time-based restrictions: Coupons can be restricted based on a specific date range during which they can be redeemed, such as only being valid for a limited time period. User-based restrictions: Coupons can be restricted to specific users, such as only being valid for first-time customers, or only for users who have spent a certain amount of money with the company. Product-based restrictions: Coupons can be restricted to certain products or categories, such as only being valid for certain types of items or for items within a specific price range. Quantity-based restrictions: Coupons can be restricted based on the number of times they can be used, such as only being valid for a single use or being limited to a certain number of uses per user.', 'Follow the steps in order and don\'t skip any steps');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_favourite`
--

CREATE TABLE `tbl_favourite` (
  `fav_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_favourite`
--

INSERT INTO `tbl_favourite` (`fav_id`, `pro_id`, `user_id`, `date_added`) VALUES
(1, 2, 2, '2023-03-30 12:28:18'),
(2, 2, 5, '2023-03-30 12:28:42');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_feedback`
--

CREATE TABLE `tbl_feedback` (
  `feedback_id` int(11) NOT NULL,
  `rating` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `visible_to_public` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 - visible to all'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_feedback`
--

INSERT INTO `tbl_feedback` (`feedback_id`, `rating`, `user_id`, `pro_id`, `feedback`, `date_submitted`, `visible_to_public`) VALUES
(1, '5', 1, 1, 'User-friendly interface: I found the app to be very easy to navigate, with clear labels and intuitive buttons. It was easy to search for specific fruits or browse through different categories.', '2023-03-30 12:27:26', 1),
(2, '5', 2, 2, 'Beautiful design: The visual design of the app is stunning, with high-quality images of each fruit and a clean, modern layout. ', '2023-03-30 12:27:43', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order`
--

CREATE TABLE `tbl_order` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pro_id` int(11) NOT NULL,
  `pro_name` varchar(75) NOT NULL,
  `pro_price` bigint(11) NOT NULL,
  `pro_qty` int(11) NOT NULL,
  `order_date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `order_status` varchar(50) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `total_cost` bigint(20) NOT NULL,
  `discount_amount` int(11) NOT NULL,
  `shipping_cost` bigint(11) NOT NULL,
  `billing_address` text NOT NULL,
  `order_tracking_no` varchar(70) DEFAULT NULL,
  `delivery_date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `pickup_date` date NOT NULL,
  `pickup_time` time NOT NULL,
  `gift_order` int(11) NOT NULL,
  `gift_message` text NOT NULL,
  `payment_status` varchar(75) NOT NULL,
  `payment_date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `refund_status` varchar(50) NOT NULL,
  `refund_amount` int(11) NOT NULL,
  `return_status` varchar(60) NOT NULL,
  `is_delivered` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 for not delivered 1 for delivered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_order`
--

INSERT INTO `tbl_order` (`order_id`, `user_id`, `pro_id`, `pro_name`, `pro_price`, `pro_qty`, `order_date_time`, `order_status`, `shipping_address`, `payment_method`, `total_cost`, `discount_amount`, `shipping_cost`, `billing_address`, `order_tracking_no`, `delivery_date_time`, `pickup_date`, `pickup_time`, `gift_order`, `gift_message`, `payment_status`, `payment_date_time`, `refund_status`, `refund_amount`, `return_status`, `is_delivered`) VALUES
(1, 1, 1, 'Cameo Apple', 80, 1, '2023-03-30 12:35:16', '0', '12, J L Nehru Road, Dharmatala', 'COD', 100, 0, 0, 'near by gandhi vidhyalay', 'fe31272242', '2022-09-11 06:40:02', '2022-09-11', '12:10:02', 1, 'god bless you', '0', '2022-09-11 06:40:02', '0', 0, '0', 0),
(2, 2, 1, 'Cameo Apple', 80, 1, '2023-03-30 12:35:39', '0', '12, J L Nehru Road, Dharmatala', 'COD', 100, 0, 0, 'near by gandhi vidhyalay', '190cba9cc2', '2022-09-11 06:40:02', '2022-09-11', '12:10:02', 1, 'god bless you', '0', '2022-09-11 06:40:02', '0', 0, '0', 0),
(3, 3, 1, 'Cameo Apple', 80, 1, '2023-03-30 12:35:41', '0', '12, J L Nehru Road, Dharmatala', 'COD', 100, 0, 0, 'near by gandhi vidhyalay', '3ecbda0d21', '2022-09-11 06:40:02', '2022-09-11', '12:10:02', 1, 'god bless you', '0', '2022-09-11 06:40:02', '0', 0, '0', 0),
(4, 4, 1, 'Cameo Apple', 80, 1, '2023-03-30 12:35:43', '0', '12, J L Nehru Road, Dharmatala', 'COD', 100, 0, 0, 'near by gandhi vidhyalay', 'e93eb91f96', '2022-09-11 06:40:02', '2022-09-11', '12:10:02', 1, 'god bless you', '0', '2022-09-11 06:40:02', '0', 0, '0', 0),
(5, 5, 1, 'Cameo Apple', 80, 1, '2023-03-30 12:35:45', '0', '12, J L Nehru Road, Dharmatala', 'COD', 100, 0, 0, 'near by gandhi vidhyalay', '4b4dde5eac', '2022-09-11 06:40:02', '2022-09-11', '12:10:02', 1, 'god bless you', '0', '2022-09-11 06:40:02', '0', 0, '0', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `pro_id` int(11) NOT NULL,
  `pro_name` varchar(50) NOT NULL,
  `pro_desc` text NOT NULL,
  `pro_price` int(11) NOT NULL,
  `pro_image` varchar(255) DEFAULT NULL,
  `pro_qty` int(11) NOT NULL,
  `pro_category_id` int(11) NOT NULL,
  `pro_date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `pro_creator_id` int(11) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp(),
  `nutrition_information` text NOT NULL,
  `origin` text NOT NULL,
  `is_fav` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 for fav',
  `is_cart` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 for cart',
  `season` text NOT NULL,
  `weight` bigint(20) NOT NULL,
  `availability` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1 for available',
  `storage_instrustion` text NOT NULL,
  `best_before_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`pro_id`, `pro_name`, `pro_desc`, `pro_price`, `pro_image`, `pro_qty`, `pro_category_id`, `pro_date_created`, `pro_creator_id`, `date_modified`, `nutrition_information`, `origin`, `is_fav`, `is_cart`, `season`, `weight`, `availability`, `storage_instrustion`, `best_before_date`) VALUES
(1, 'Cameo Apple', 'The Cameo apple is a relatively new variety of apple that was developed in Washington State, USA. It is a medium to large-sized apple with a yellow-green background and red stripes or blushes. The flesh of the Cameo apple is cream-colored, crisp, juicy, and has a sweet-tart flavor with a slightly spicy undertone. It has a firm texture that holds up well when baked or cooked, making it a popular choice for pies and sauces. The Cameo apple is typically in season from late fall through early spring and is known for its long shelf life, making it a great choice for long-term storage.', 80, '1680177724Cameo Apple_img.png', 100, 1, '2023-03-30 12:02:04', 1, '2023-03-30 12:02:04', 'Calories: 95 Total Fat: 0.3g Saturated Fat: 0g Trans Fat: 0g Cholesterol: 0mg Sodium: 1mg Total Carbohydrates: 25g Dietary Fiber: 4g Sugars: 19g Protein: 1g', 'The Cameo apple was developed by the Washington State University Tree Fruit Research Commission in the United States. It is a cross between two apple varieties, the Red Delicious and the Golden Delicious, and was first introduced to the market in 1987. The name \"Cameo\" was chosen because of the apple\'s striking appearance and unique flavor. Today, Cameo apples are grown in several regions around the world, including the United States, Chile, New Zealand, and Italy. The largest producer of Cameo apples is the state of Washington in the United States, where the apple was originally developed', 0, 0, 'The Cameo apple is a late-season apple that typically ripens in the late fall through early winter months. In the United States, the harvest season for Cameo apples usually begins in October and can extend through December or even January in some regions. The exact timing of the harvest can vary depending on the weather conditions and the location of the orchard. After being harvested, Cameo apples can be stored for several months in a cool, dry place, making them available for purchase and consumption well beyond the harvest season. Proper storage conditions are important to maintain the quality and flavor of the apples. In general, Cameo apples can be stored for up to 3-4 months in a cool (32-40°F or 0-4°C), humid environment with good ventilation.', 1, 1, 'Store the apples in a cool place: Apples should be stored at a temperature between 32-40°F (0-4°C) in a cool and dry place like a refrigerator. A cool temperature can help slow down the ripening process and prevent the apples from becoming overripe too quickly.', '1970-01-01'),
(2, 'Black Berry', 'Blackberries are a type of berry that are known for their sweet and slightly tart flavor. They are typically small and round with a deep purplish-black color and a shiny skin. Blackberries are part of the rose family and are closely related to raspberries. ', 70, '1680178027Black Berry_img.png', 120, 2, '2023-03-30 12:07:07', 1, '2023-03-30 12:07:07', 'Calories: 62 Total Fat: 0.7g Saturated Fat: 0g Trans Fat: 0g Cholesterol: 0mg Sodium: 1mg Total Carbohydrates: 14g Dietary Fiber: 8g Sugars: 7g Protein: 2g', 'Blackberries are believed to have originated in Europe, but they are now widely cultivated in many parts of the world, including North and South America, Asia, and Africa. The wild European blackberry, also known as the common blackberry (Rubus fruticosus), is thought to be the ancestor of most cultivated varieties of blackberries. Blackberries have been consumed for thousands of years and were highly valued by ancient Greeks and Romans for their medicinal properties. Today, blackberries are commonly used in a variety of culinary applications, including pies, jams, and smoothies, and are enjoyed fresh as a healthy and flavorful snack.', 0, 0, 'The blackberry season can vary depending on the region and the variety of blackberry. In general, the peak season for blackberries is during the summer months, from late May to early September in the northern hemisphere. However, the exact timing of the blackberry season can vary depending on the climate and weather conditions in each region.', 144, 1, 'Store the apples in a cool place: Apples should be stored at a temperature between 32-40°F (0-4°C) in a cool and dry place like a refrigerator. A cool temperature can help slow down the ripening process and prevent the apples from becoming overripe too quickly.', '1970-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_subcategory`
--

CREATE TABLE `tbl_subcategory` (
  `sub_cate_id` int(11) NOT NULL,
  `sub_cate_name` varchar(100) NOT NULL,
  `cat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `contact_num` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `gender` varchar(15) DEFAULT NULL,
  `DOB` varchar(20) DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`user_id`, `user_name`, `contact_num`, `email`, `password`, `register_date`, `profile_image`, `gender`, `DOB`, `date_modified`) VALUES
(1, 'kakashi', '704157541', 'kakashi@gmail.com', '3cb19a3b0a0dc95986ea8460ec006662', '2023-03-30 10:32:08', '1680172795_img.jpg', 'male', '2002/11/10', '2023-03-30 10:39:55'),
(2, 'khushi', '704157541', 'khushi@gmail.com', 'cae5161fc8156ab2de412ec4007a76e2', '2023-03-30 10:32:33', '1680172759_img.jpg', 'female', '2002/11/10', '2023-03-30 10:39:19'),
(3, 'janvi', '704157541', 'janvi@gmail.com', '3c6c3381e638b94602c57770bbea6c9b', '2023-03-30 10:32:48', '1680172728_img.jpg', 'female', '2002/11/10', '2023-03-30 10:38:48'),
(4, 'alex', '704157541', 'alex@gmail.com', 'b75bd008d5fecb1f50cf026532e8ae67', '2023-03-30 10:33:10', '1680172599_img.jpg', 'male', '2002/08/09', '2023-03-30 10:36:39'),
(5, 'itachi', '704157541', 'itachi@gmail.com', '788f052680d56ee0a63c11a34a593bed', '2023-03-30 10:33:24', '1680172520_img.jpg', 'male', '2002/08/09', '2023-03-30 10:35:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_address`
--
ALTER TABLE `tbl_address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_fk` (`user_id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cart_fk` (`user_id`),
  ADD KEY `cart_pro_fk` (`pro_id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`cat_id`),
  ADD KEY `fk` (`cat_creator_id`);

--
-- Indexes for table `tbl_coupon`
--
ALTER TABLE `tbl_coupon`
  ADD PRIMARY KEY (`coupon_id`),
  ADD KEY `admin_fk` (`coupon_creator_id`),
  ADD KEY `cate_fk` (`cat_id`),
  ADD KEY `prod_fk` (`pro_id`);

--
-- Indexes for table `tbl_favourite`
--
ALTER TABLE `tbl_favourite`
  ADD PRIMARY KEY (`fav_id`),
  ADD KEY `pro_fk` (`pro_id`),
  ADD KEY `u_fk` (`user_id`);

--
-- Indexes for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `feed_user-fk` (`user_id`),
  ADD KEY `feed_pro-fk` (`pro_id`);

--
-- Indexes for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `order_user_fk` (`user_id`),
  ADD KEY `order_product_fk` (`pro_id`);

--
-- Indexes for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD PRIMARY KEY (`pro_id`),
  ADD KEY `product_fk` (`pro_creator_id`),
  ADD KEY `category_fk` (`pro_category_id`);

--
-- Indexes for table `tbl_subcategory`
--
ALTER TABLE `tbl_subcategory`
  ADD PRIMARY KEY (`sub_cate_id`),
  ADD KEY `sub_cate_fk` (`cat_id`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_address`
--
ALTER TABLE `tbl_address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_coupon`
--
ALTER TABLE `tbl_coupon`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_favourite`
--
ALTER TABLE `tbl_favourite`
  MODIFY `fav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_order`
--
ALTER TABLE `tbl_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_product`
--
ALTER TABLE `tbl_product`
  MODIFY `pro_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_subcategory`
--
ALTER TABLE `tbl_subcategory`
  MODIFY `sub_cate_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_address`
--
ALTER TABLE `tbl_address`
  ADD CONSTRAINT `user_fk` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_cart`
--
ALTER TABLE `tbl_cart`
  ADD CONSTRAINT `cart_pro_fk` FOREIGN KEY (`pro_id`) REFERENCES `tbl_product` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_user_fk` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD CONSTRAINT `fk` FOREIGN KEY (`cat_creator_id`) REFERENCES `tbl_admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_coupon`
--
ALTER TABLE `tbl_coupon`
  ADD CONSTRAINT `coupon_admin_fk` FOREIGN KEY (`coupon_creator_id`) REFERENCES `tbl_admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coupon_cat_ck` FOREIGN KEY (`cat_id`) REFERENCES `tbl_category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coupon_pro_fk` FOREIGN KEY (`pro_id`) REFERENCES `tbl_product` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_favourite`
--
ALTER TABLE `tbl_favourite`
  ADD CONSTRAINT `fav_pro_fk` FOREIGN KEY (`pro_id`) REFERENCES `tbl_product` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fav_user_fk` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_feedback`
--
ALTER TABLE `tbl_feedback`
  ADD CONSTRAINT `feedback_pro_pk` FOREIGN KEY (`pro_id`) REFERENCES `tbl_product` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `feedback_user_fk` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD CONSTRAINT `order_pro_fk` FOREIGN KEY (`pro_id`) REFERENCES `tbl_product` (`pro_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_user_fk` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_product`
--
ALTER TABLE `tbl_product`
  ADD CONSTRAINT `pro_admin_fk` FOREIGN KEY (`pro_creator_id`) REFERENCES `tbl_admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pro_cate_fk` FOREIGN KEY (`pro_category_id`) REFERENCES `tbl_category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_subcategory`
--
ALTER TABLE `tbl_subcategory`
  ADD CONSTRAINT `sub_cate_fk` FOREIGN KEY (`cat_id`) REFERENCES `tbl_category` (`cat_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
