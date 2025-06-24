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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_attendance_raw` */

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
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `tbl_students` */

insert  into `tbl_students`(`id`,`lrn`,`first_name`,`last_name`,`birthdate`,`gender`,`address`,`grade_level`,`section`,`contact_number`,`guardian_name`,`profile_picture`) values 
(11,'100000000011','Ralph Simon','Mabulay','1988-06-02','Male','357 Poplar St','Grade 7','Generosity','09171234511','Mila Bautista','img/pics/685657131b465_ralph.jpg'),
(43,'847193650214','CARL VINCENT GUIA','ABLON','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(44,'905127384612','ELMER JR. PANTILO','AGBU','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(45,'738201496570','JHALIL PADERNAL','ARIZ','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(46,'214785963401','IVAN DAVE','BASADRE','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(47,'653290741823','TRAVIS JOHN LUNA','BONBON','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(48,'184739205716','IAN MARK GUMAPON','BOOC','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(49,'579103842167','JUNREY JR. BERNADOS','BURIOL','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(50,'690214783905','CHARLES NIKEE BAHIAN','CABATO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(51,'841235709621','CHRIST DEE       ABE-ABE','CASTRO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(52,'710982364152','ABDUL RAHEM SARIP','DACO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(53,'309186472305','MOHAMMAD FAIZ SARIP','DACO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(54,'468291037562','GIAN JAMES BALIOS','DAPITON','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(55,'781306924158','PRINCE NATHANIEL JABINIAO','DUHILAG','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(56,'591803247610','JOHN VINCENT SILLOTE','GUMAPON','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(57,'827190436258','ASHTON DANE LEONES','JABENIAR','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(58,'319062748590','KHALIL VALLE','JUSAYAN','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(59,'792364180254','JUREN ACERO','LLEGO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(60,'658120974386','JEDIAH REUEL','MACABINTA','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(61,'847120963450','BENJO REYES','PACHECO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(62,'943275081263','ANTONIO JR. ACEBES','PALAMINE','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(63,'406591738209','JAY-R TAYAOTAO','PITOGO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(64,'385729106483','JAYMAR ABIOG','RAMOS','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(65,'250481937562','LEONARD FERNANDEZ','SUMAPIG','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(66,'612849073185','XIAN AVILA','SURBANO','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(67,'730162594817','ARL JACOB NAINGUE','VALDEHUEZA','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(68,'982301647528','ALGER OBEMIO','VARIACION','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(69,'371840259671','JOHN ROY BUATONA','YBAÑEZ','0000-00-00','Male',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(70,'173820946153','WENSHIEL SUMOBAY','ABIOG','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(71,'862710394512','SHERLINA BINATLAO','AJID','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(72,'920583716240','XANDRA YZABELLE WASAWAS','ANINO','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(73,'408129763580','CLICE HEAVEN','ARTAJO','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(74,'512937048621','CAMELLE ASLAG','BASADRE','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(75,'306491752830','RIXAN NADUMA','BERNADES','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(76,'758024913607','MARY PAULINE','CABALUNA','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(77,'849730265198','SOFIA NICOLE CABALLERO','DERIADA','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(78,'105984237615','LIEZEL LIMBAG','DOSDOS','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(79,'684917203519','PRINCESS MARY GAYONAN','ECOY','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(80,'742086193540','CHELLBER NICE NERI','FELECILDA','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(81,'193840726415','MARRY ITUM','JABINIAO','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(82,'836710254938','PRINCESS XZANNIA ALBIT','JAGUALING','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(83,'620384751096','SAMANTHA DUMAGAT','JORQUIA','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(84,'519684203781','MAYLYN TABAN','LABIAL','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(85,'268491730659','SHAINE MARIE','MONDRAGON','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(86,'301742985106','ALTHEA JENNEL PASQUITO','MORALES','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(87,'985613204879','WILLANE GRACE YACAPIN','OLIVERIO','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(88,'472068153209','JASMINE BONGGAY','PACULBA','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(89,'758290146302','RECHELL SERONA','PALATULON','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(90,'624091385720','RECHIEL MAY EPIS','PERMACIO','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(91,'890427615398','PRECIOUS FLAIR VALLENTE','POLINAR','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(92,'237189654032','RODELYN CAJOTOR','QUILAB','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(93,'318427690584','ROXEL KESHLEIGH KING','REAL','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(94,'671203984157','QUISHA MARIE      SANGO-AN','SALCEDO','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(95,'492830617295','HEART HERSHEY PAMULAGAN','SARAMOSING','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(96,'743982061573','CENAIDAH BOLONIAS','SARIPADA','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(97,'198604732185','JUMALYN CABANLIT','SUMAJIT','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL),
(98,'581207349106','ELLJIE HIGYAWAN','TINGABNGAB','0000-00-00','Female',NULL,'Grade 7','Generosity',NULL,NULL,NULL);

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
