/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - classroomsystem
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`classroomsystem` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `classroomsystem`;

/*Table structure for table `tbl_attendance_raw` */

DROP TABLE IF EXISTS `tbl_attendance_raw`;

CREATE TABLE `tbl_attendance_raw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `month` varchar(15) NOT NULL,
  `day` int(11) NOT NULL,
  `day_name` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `am_status` enum('Present','Absent') DEFAULT 'Absent',
  `pm_status` enum('Present','Absent') DEFAULT 'Absent',
  `is_tardy` tinyint(1) DEFAULT 0,
  `date_recorded` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `tbl_attendance_raw_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_attendance_raw` */

insert  into `tbl_attendance_raw`(`id`,`student_id`,`school_year`,`month`,`day`,`day_name`,`am_status`,`pm_status`,`is_tardy`,`date_recorded`) values 
(21,11,'2024-2025','June',3,'Monday','Present','Present',0,'2025-06-22 21:58:19'),
(22,11,'2024-2025','June',4,'Tuesday','Present','Present',0,'2025-06-22 21:58:19'),
(23,11,'2024-2025','June',5,'Wednesday','Present','Absent',1,'2025-06-22 21:58:19'),
(24,11,'2024-2025','June',6,'Thursday','Absent','Present',0,'2025-06-22 21:58:19'),
(25,11,'2024-2025','June',7,'Friday','Present','Present',0,'2025-06-22 21:58:19'),
(26,11,'2024-2025','June',10,'Monday','Present','Present',0,'2025-06-22 21:58:19'),
(27,11,'2024-2025','June',11,'Tuesday','Present','Present',0,'2025-06-22 21:58:19'),
(28,11,'2024-2025','June',12,'Wednesday','Absent','Absent',0,'2025-06-22 21:58:19'),
(29,11,'2024-2025','June',13,'Thursday','Absent','Present',1,'2025-06-22 21:58:19'),
(30,11,'2024-2025','June',14,'Friday','Present','Present',0,'2025-06-22 21:58:19'),
(31,11,'2024-2025','June',17,'Monday','Present','Present',0,'2025-06-22 21:58:19'),
(32,11,'2024-2025','June',18,'Tuesday','Present','Present',0,'2025-06-22 21:58:19'),
(33,11,'2024-2025','June',19,'Wednesday','Present','Absent',1,'2025-06-22 21:58:19'),
(34,11,'2024-2025','June',20,'Thursday','Present','Present',0,'2025-06-22 21:58:19'),
(35,11,'2024-2025','June',21,'Friday','Absent','Present',0,'2025-06-22 21:58:19'),
(36,11,'2024-2025','June',24,'Monday','Present','Present',0,'2025-06-22 21:58:19'),
(37,11,'2024-2025','June',25,'Tuesday','Present','Present',0,'2025-06-22 21:58:19'),
(38,11,'2024-2025','June',26,'Wednesday','Present','Present',0,'2025-06-22 21:58:19'),
(39,11,'2024-2025','June',27,'Thursday','Present','Absent',1,'2025-06-22 21:58:19'),
(40,11,'2024-2025','June',28,'Friday','Present','Present',0,'2025-06-22 21:58:19');

/*Table structure for table `tbl_student_attendance` */

DROP TABLE IF EXISTS `tbl_student_attendance`;

CREATE TABLE `tbl_student_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `school_year` varchar(9) NOT NULL,
  `month` varchar(15) NOT NULL,
  `days_present` int(11) DEFAULT 0,
  `days_absent` int(11) DEFAULT 0,
  `days_tardy` int(11) DEFAULT 0,
  `total_school_days` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `tbl_student_attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `tbl_students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_student_attendance` */

/*Table structure for table `tbl_students` */

DROP TABLE IF EXISTS `tbl_students`;

CREATE TABLE `tbl_students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lrn` varchar(12) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `grade_level` varchar(10) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lrn` (`lrn`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_students` */

insert  into `tbl_students`(`id`,`lrn`,`first_name`,`last_name`,`birthdate`,`gender`,`address`,`grade_level`,`section`,`contact_number`,`guardian_name`,`profile_picture`) values 
(1,'100000000001','Juan','Dela Cruz','2010-01-15','Male','123 Main St','Grade 6','Section 1','09171234501','Maria Dela Cruz',NULL),
(2,'100000000002','Maria','Santos','2010-02-20','Female','456 Elm St','Grade 6','Section 1','09171234502','Jose Santos',NULL),
(3,'100000000003','Pedro','Reyes','2010-03-10','Male','789 Oak St','Grade 6','Section 1','09171234503','Ana Reyes',NULL),
(4,'100000000004','Ana','Garcia','2010-04-05','Female','321 Pine St','Grade 6','Section 1','09171234504','Luis Garcia',NULL),
(5,'100000000005','Mark','Torres','2010-05-12','Male','654 Maple St','Grade 6','Section 1','09171234505','Carmen Torres',NULL),
(6,'100000000006','Liza','Mendoza','2010-06-18','Female','987 Birch St','Grade 6','Section 1','09171234506','Mario Mendoza',NULL),
(7,'100000000007','Carlos','Lopez','2010-07-22','Male','159 Cedar St','Grade 6','Section 1','09171234507','Rosa Lopez',NULL),
(8,'100000000008','Grace','Ramos','2010-08-30','Female','753 Spruce St','Grade 6','Section 1','09171234508','Pedro Ramos',NULL),
(9,'100000000009','Miguel','Cruz','2010-09-14','Male','852 Willow St','Grade 6','Section 1','09171234509','Luz Cruz',NULL),
(10,'100000000010','Ella','Flores','2010-10-25','Female','951 Aspen St','Grade 6','Section 1','09171234510','Juan Flores',NULL),
(11,'100000000011','Ralph Simon','Mabulay','1988-06-02','Male','357 Poplar St','Grade 6','Section 1','09171234511','Mila Bautista','img/pics/685657131b465_ralph.jpg'),
(12,'100000000012','Kim','Navarro','2010-12-03','Female','258 Walnut St','Grade 6','Section 1','09171234512','Rico Navarro',NULL),
(13,'100000000013','Paolo','Gutierrez','2010-01-29','Male','654 Chestnut St','Grade 6','Section 1','09171234513','Dina Gutierrez',NULL),
(14,'100000000014','Jasmine','Castro','2010-02-17','Female','147 Redwood St','Grade 6','Section 1','09171234514','Oscar Castro','img/pics/68580a58410c1_ralph.png.jpg'),
(15,'100000000015','Enzo','Morales','2010-03-23','Male','369 Magnolia St','Grade 6','Section 1','09171234515','Tina Morales',NULL);

/*Table structure for table `tbl_users` */

DROP TABLE IF EXISTS `tbl_users`;

CREATE TABLE `tbl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','teacher') NOT NULL,
  `date_created` datetime DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_users` */

insert  into `tbl_users`(`id`,`username`,`password`,`full_name`,`role`,`date_created`,`date_updated`) values 
(1,'ralphsimon.mabulay@deped.gov.ph','$2y$10$G/g8q/POiGzGwQzZHJrzNeGoo.khrDbEs5Qq28gBb0aJlP6T24POy','Ralph Simon Mabulay','teacher','2025-06-21 13:32:20','2025-06-21 13:34:12');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
