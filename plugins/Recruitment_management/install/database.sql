CREATE TABLE IF NOT EXISTS `recruitment_application_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `key_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `sort` INT NOT NULL ,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

INSERT INTO `recruitment_application_forms` (`id`, `title`, `key_name`,`required`, `sort`,`deleted`) VALUES 
(1, 'First name', 'first_name', 1, 1, 0),
(2, 'Last name', 'last_name', 1, 2, 0),
(3, 'Email', 'email', 1, 3, 0),
(4, 'Gender', 'gender', 0, 4,0),
(5, 'Date of birth', 'date_of_birth', 0, 5,0),
(6, 'Phone', 'phone', 0, 6,0),
(7, 'Address', 'address', 0, 7,0),
(8, 'Education', 'education', 0, 8,0),
(9, 'Work experience', 'work_experience', 0, 9, 0),
(10, 'Resume', 'resume', 0, 10,0); --#

CREATE TABLE IF NOT EXISTS `recruitment_hiring_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `key_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  `sort` INT NOT NULL ,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

INSERT INTO `recruitment_hiring_stages` (`id`, `title`,`key_name`, `sort`, `deleted`) VALUES (1, 'New', 'new', 0, 0), (2, 'Hired', 'hired', 1, 0), (3, 'Disqualified', 'disqualified', 2, 0); --#

CREATE TABLE IF NOT EXISTS `recruitment_event_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

CREATE TABLE IF NOT EXISTS `recruitment_job_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

CREATE TABLE IF NOT EXISTS `recruitment_job_positions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

CREATE TABLE IF NOT EXISTS `recruitment_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

CREATE TABLE IF NOT EXISTS `recruitment_locations`(
   `id` INT(11) NOT NULL AUTO_INCREMENT,
   `address` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
   `deleted` TINYINT(1) NOT NULL DEFAULT '0',
   PRIMARY KEY(`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci AUTO_INCREMENT = 1; --#

CREATE TABLE IF NOT EXISTS `recruitment_jobs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `deadline` date NOT NULL,
  `job_title` text COLLATE utf8_unicode_ci NOT NULL,
  `status` enum('draft','active','inactive') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'draft',
  `job_type_id` int(11) NOT NULL DEFAULT '0',
  `job_position_id` int(11) NOT NULL DEFAULT '0',
  `department_id` int(11) NOT NULL DEFAULT '0',
  `location_id` int(11) NOT NULL DEFAULT '0',
  `description` mediumtext COLLATE utf8_unicode_ci,
  `quantity_to_be_required` double NOT NULL,
  `salary` double NULL,
  `content` TEXT NOT NULL,
  `public_key` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `recruiters` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1; --#

CREATE TABLE IF NOT EXISTS `recruitment_job_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `template` mediumtext COLLATE utf8_unicode_ci,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1 ; --#

INSERT INTO `recruitment_job_templates` (`id`, `title`, `template`, `deleted`) VALUES
(1, 'Job template 1', '<div style="display: flex; flex-direction: column; line-height: 1; font-family: calibri;"> <table style="margin-top: 0; margin-bottom: 40px"> <tbody> <tr> <td style="padding: 0;"> <div style="background-image: -webkit-linear-gradient(-50deg ,#237efd 0%,#24b0fe 46%,#24e2ff 100%); color: #fff; padding: 30px 10px 70px"> <div style="max-width: 1140px; width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto;"> <div style="text-align: center; padding-bottom: 60px;"> <p style="font-size: 40px; font-weight: 600; margin-bottom: 0;"><br></p><p style="font-size: 40px; font-weight: 600; margin-bottom: 0;">App Development Recruitment circular</p> <p style="font-size: 21px; margin-top: 10px;">We develop amazing apps for your business.<br>Here is our best offer for you.</p> </div> <div style="float: left;"> <h2 style="margin-bottom: 0; margin-top: 10px;">{JOB_TITLE}</h2> <p style="margin-top: 5px;">{JOB_POSITION}</p> <span style="margin-bottom: 0; margin-right: 10px;">{JOB_LOCATION}</span> <span style="border: 1px solid #fff;"></span> <span style="margin-left: 10px;">Posted {START_DATE}</span> </div> <div style="float: right;"> <div style="width: 180px; background-color: #23b1fe; text-align: center; padding: 15px 0;border-radius: 5px;"> <p style="font-size: 30px; ">Monthly</p> <p style="font-size: 35px; margin-top: 20px;">{SALARY}</p> </div> </div> <div style="clear: both"></div> </div> </div> </td> </tr> </tbody> </table> <table style="margin-top: 0; margin-bottom: 40px"> <tbody> <tr> <td style="padding: 0;"> <div style="max-width: 1140px;width: 100%; padding-right: 15px; padding-left: 15px; margin: 0 auto;"> <div style="display: flex; flex-wrap: wrap;"> <div style="flex: 0 0 63.666667%; max-width: 63.666667%; position: relative; width: 100%; padding-right: 15px; padding-left: 15px;"> <div style="padding: 1.5rem 2rem 3rem 2rem; box-shadow: 0 0 1.4rem rgb(191 191 191 / 24%);"> <div> <h1 style="color: #2e3438;">Job Description</h1> <p style="font-size: 18px; color: #677294; line-height: 1.8rem;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Cras sed felis eget velit aliquet. Consequat ac felis donec et odio. Egestas sed tempus urna et. Sagittis id consectetur purus ut faucibus. Massa id neque aliquam vestibulum morbi blandit. Fermentum odio eu feugiat pretium nibh ipsum consequat nisl vel. Vivamus at augue eget arcu dictum varius. Mi quis hendrerit dolor magna eget est lorem ipsum dolor. Faucibus interdum posuere lorem ipsum dolor sit amet consectetur adipiscing. Risus quis varius quam quisque id. Tellus mauris a diam maecenas sed. Gravida in fermentum et sollicitudin ac. Cursus eget nunc scelerisque viverra mauris in. Non nisi est sit amet facilisis magna. Id aliquet lectus proin nibh nisl. Porttitor eget dolor morbi non arcu.</p> </div> <div> <h1 style="color: #2e3438;">Job Responsibilities</h1> <ul style="font-size: 18px; color: #677294; line-height: 1.8rem; list-style-type: circle;"> <li>5+ years of experience with web application development</li> <li>Experience working on large-scale software projects</li> <li>Great communication skills</li> <li>Deep understanding of Object Oriented Programming and Design, data structures, and algorithms</li> <li>Deep understanding of web application development and best practices</li> <li>Good communication skills</li> </ul> <p style="font-size: 21px; color: #445760; margin-top: 10px;">Below are the skills needed for the ideal candidate:</p> <ul style="font-size: 18px; color: #677294; line-height: 1.8rem; list-style-type: circle;"> <li>PHP (CodeIgniter framework preferred)</li> <li>NodeJs basics</li> <li>Web components</li> <li>CSS &amp; HTML</li> <li>Precompilers (ie. SASS)</li> <li>Javascript</li> </ul> </div> <div> <h1 style="color: #2e3438;">About</h1> <p style="font-size: 18px; color: #677294; line-height: 1.8rem;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Nibh cras pulvinar mattis nunc sed blandit. Pretium fusce id velit ut tortor pretium viverra. Massa id neque aliquam vestibulum morbi. Varius vel pharetra vel turpis nunc eget lorem dolor. Aliquam vestibulum morbi blandit cursus risus at ultrices mi. Platea dictumst quisque sagittis purus sit amet volutpat.</p><p style="font-size: 18px; color: #677294; line-height: 1.8rem;"><br></p> </div> <div style="padding-top: 20px;"> <a href="{APPLY_JOB_LINK}" target="_blank" style="margin-top: 15px; font-size: 1.4rem; padding: 1rem 3rem; border-radius: 0.4rem; font-weight: 400; background-color: #23b1fe; text-decoration: none; color: #fff;">Apply for this job</a> </div> </div> </div> <div style="flex: 0 0 30.333333%; max-width: 30.333333%; position: relative; width: 100%; padding-right: 15px; padding-left: 15px;"> <div style="margin-bottom: 3rem; box-shadow: 0 0 1.4rem rgb(191 191 191 / 24%);"> <p style="font-size: 18px; font-weight: 500; color: #333; padding: 2rem 3rem; text-transform: uppercase; position: relative; border-bottom: 0.1rem solid #f1f1f1; margin: 0;">JOB OVERVIEW</p> <div style="padding: 1rem 0;"> <ul style="list-style-type: none;"> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Job Title <span style="color: #333;"> : {JOB_TITLE}</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Job Position<span style="color: #333;"> : {JOB_POSITION}</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Job Type<span style="color: #333;"> : {JOB_TYPE}</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Experience Type<span style="color: #333;"> : 4 Years</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Vacancy <span style="color: #333;"> : {QUANTITY_TO_BE_REQUIRED}</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Gender<span style="color: #333;"> : Male, Female</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Salary <span style="color: #333;"> : {SALARY}</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Location <span style="color: #333;"> : {JOB_LOCATION}</span></li> <li style="font-size: 18px; color: #677294; padding: .8rem 0;">Apply Deadline <span style="color: #333;"> : {DEADLINE}</span></li></ul></div></div><div style="margin-bottom: 3rem; box-shadow: 0 0 1.4rem rgb(191 191 191 / 24%);"> </div> </div> </div> </div> </td> </tr> </tbody> </table> </div>', 0); --#

CREATE TABLE IF NOT EXISTS `recruitment_settings`(
    `setting_name` VARCHAR(100) COLLATE utf8_unicode_ci NOT NULL,
    `setting_value` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
    `type` VARCHAR(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'app',
    `deleted` TINYINT(1) NOT NULL DEFAULT '0',
    UNIQUE KEY `setting_name`(`setting_name`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_unicode_ci; --#

INSERT INTO `recruitment_settings` (`setting_name`, `setting_value`, `type`, `deleted`) VALUES ('default_recruitment_job_circular_template', '1', 'app', '0'); --#

CREATE TABLE IF NOT EXISTS `recruitment_candidates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8_unicode_ci DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8_unicode_ci,
  `education` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `work_experience` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `resume` longtext COLLATE utf8_unicode_ci NOT NULL,
  `circular_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `applied_at` datetime DEFAULT NULL,
  `last_email_sent_date` date DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

CREATE TABLE IF NOT EXISTS `recruitment_candidate_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_id` text COLLATE utf8_unicode_ci NOT NULL,
  `event_type_id` text COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` MEDIUMTEXT COLLATE utf8_unicode_ci NOT NULL,
  `attendees` text COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#

CREATE TABLE IF NOT EXISTS `recruitment_candidate_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `candidate_id` text COLLATE utf8_unicode_ci NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` mediumtext COLLATE utf8_unicode_ci,
  `files` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ; --#
