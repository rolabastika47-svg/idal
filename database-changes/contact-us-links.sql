-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: idal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `om_posts`
--
-- WHERE:  ID IN (137,457)

LOCK TABLES `om_posts` WRITE;
/*!40000 ALTER TABLE `om_posts` DISABLE KEYS */;
REPLACE INTO `om_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (137,1,'2026-08-20 17:22:03','2025-12-05 09:34:03','','Contact Us','','publish','closed','closed','','get-engaged','','','2026-08-20 17:22:03','2026-08-20 14:22:03','',0,'http://localhost/idal/?p=137',6,'nav_menu_item','',0);
REPLACE INTO `om_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES (457,1,'2026-08-20 17:21:25','2025-12-15 08:20:50','','اتصل بنا','','publish','closed','closed','','%d8%a7%d9%84%d8%a8%d9%8a%d8%a7%d9%86%d8%a7%d8%aa-%d9%88%d8%a7%d9%84%d9%85%d9%86%d8%b4%d9%88%d8%b1%d8%a7%d8%aa','','','2026-08-20 17:21:25','2026-08-20 14:21:25','',0,'http://localhost/idal/?p=457',6,'nav_menu_item','',0);
/*!40000 ALTER TABLE `om_posts` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20 17:23:50
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: idal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `om_postmeta`
--
-- WHERE:  post_id IN (137,457)

LOCK TABLES `om_postmeta` WRITE;
/*!40000 ALTER TABLE `om_postmeta` DISABLE KEYS */;
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (220,137,'_menu_item_type','custom');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (221,137,'_menu_item_menu_item_parent','0');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (222,137,'_menu_item_object_id','137');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (223,137,'_menu_item_object','custom');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (224,137,'_menu_item_target','');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (225,137,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (226,137,'_menu_item_xfn','');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (227,137,'_menu_item_url','http://localhost/idal/en/contact-us/');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (3996,137,'_wp_old_date','2025-12-05');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (11050,137,'_wp_old_date','2025-12-10');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (13594,137,'_wp_old_date','2025-12-16');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (18207,137,'_wp_old_date','2025-12-18');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (19688,137,'_wp_old_date','2025-12-22');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (21789,137,'_wp_old_date','2025-12-23');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (24405,137,'_wp_old_date','2025-12-30');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (26038,137,'_wp_old_date','2026-01-06');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (38655,137,'_wp_old_date','2026-01-08');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (116744,137,'_wp_old_date','2026-01-15');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (117580,137,'_wp_old_date','2026-02-16');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (125175,137,'_wp_old_date','2026-02-17');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (784127,137,'_wp_old_date','2026-02-24');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (793023,137,'_wp_old_date','2026-06-05');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (795945,137,'_wp_old_date','2026-07-21');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5813,457,'_menu_item_type','custom');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5814,457,'_menu_item_menu_item_parent','0');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5815,457,'_menu_item_object_id','457');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5816,457,'_menu_item_object','custom');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5817,457,'_menu_item_target','');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5818,457,'_menu_item_classes','a:1:{i:0;s:0:\"\";}');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5819,457,'_menu_item_xfn','');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (5820,457,'_menu_item_url','http://localhost/idal/contact-us-arabic/');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (35233,457,'_wp_old_date','2025-12-15');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (38120,457,'_wp_old_date','2026-01-13');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (77473,457,'_wp_old_date','2026-01-15');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (98635,457,'_wp_old_date','2026-01-29');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (104890,457,'_wp_old_date','2026-02-09');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (116710,457,'_wp_old_date','2026-02-11');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (117603,457,'_wp_old_date','2026-02-16');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (125198,457,'_wp_old_date','2026-02-17');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (719756,457,'_wp_old_date','2026-02-24');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (784159,457,'_wp_old_date','2026-03-11');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (795066,457,'_wp_old_date','2026-06-05');
REPLACE INTO `om_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES (795939,457,'_wp_old_date','2026-08-11');
/*!40000 ALTER TABLE `om_postmeta` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20 17:24:56
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: idal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `om_terms`
--
-- WHERE:  term_id=270

LOCK TABLES `om_terms` WRITE;
/*!40000 ALTER TABLE `om_terms` DISABLE KEYS */;
REPLACE INTO `om_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES (270,'pll_6a870ccf63b66','pll_6a870ccf63b66',0);
/*!40000 ALTER TABLE `om_terms` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20 17:25:31
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: idal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `om_term_taxonomy`
--
-- WHERE:  term_taxonomy_id=270

LOCK TABLES `om_term_taxonomy` WRITE;
/*!40000 ALTER TABLE `om_term_taxonomy` DISABLE KEYS */;
REPLACE INTO `om_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES (270,270,'post_translations','a:2:{s:2:\"en\";i:105;s:2:\"ar\";i:111;}',0,2);
/*!40000 ALTER TABLE `om_term_taxonomy` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20 17:25:55
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: idal
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `om_term_relationships`
--
-- WHERE:  object_id IN (105,111) AND term_taxonomy_id IN (10,13,270)

LOCK TABLES `om_term_relationships` WRITE;
/*!40000 ALTER TABLE `om_term_relationships` DISABLE KEYS */;
REPLACE INTO `om_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (105,10,0);
REPLACE INTO `om_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (105,270,0);
REPLACE INTO `om_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (111,13,0);
REPLACE INTO `om_term_relationships` (`object_id`, `term_taxonomy_id`, `term_order`) VALUES (111,270,0);
/*!40000 ALTER TABLE `om_term_relationships` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-20 17:26:22
