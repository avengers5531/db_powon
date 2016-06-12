CREATE TABLE IF NOT EXISTS `region` (
  `region_id` INTEGER NOT NULL AUTO_INCREMENT,
  `country`   VARCHAR(32) NOT NULL,
  `province`  VARCHAR(32),
  `city`      VARCHAR(32),
  PRIMARY KEY (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `member` (
  `member_id` INTEGER NOT NULL AUTO_INCREMENT,
  `username`  VARCHAR(32) NOT NULL,
  `password`  VARCHAR(255) NOT NULL,
  `first_name` VARCHAR(64),
  `last_name` VARCHAR(64),
  `user_email` VARCHAR(255) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_admin` CHAR(1) NOT NULL,
  `status` CHAR(1) NOT NULL,
  `region_access` INTEGER NOT NULL DEFAULT 0,
  `lives_in` INTEGER DEFAULT NULL,
  `professions_access` INTEGER NOT NULL DEFAULT 0,
  `interests_access` INTEGER NOT NULL DEFAULT 0,
  `dob_access` INTEGER NOT NULL DEFAULT 0,
  `email_access` INTEGER NOT NULL DEFAULT 0,
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`member_id`),
  UNIQUE INDEX (`username`),
  UNIQUE (`user_email`),
  FOREIGN KEY (`lives_in`) REFERENCES `region`(`region_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `related_members` (
  `member_from` INTEGER NOT NULL,
  `member_to` INTEGER NOT NULL,
  `relation_type` CHAR(1) NOT NULL DEFAULT 'F', -- F for friends
  `request_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_date` DATETIME,
  PRIMARY KEY (`member_from`, `member_to`),
  FOREIGN KEY (`member_from`) REFERENCES `member`(`member_id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_to`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `profession` (
  `profession_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`profession_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `works_as` (
  `member_id` INTEGER NOT NULL,
  `profession_name` VARCHAR(255) NOT NULL,
  `date_started` DATE,
  `date_ended` DATE,
  PRIMARY KEY(`member_id`, `profession_name`),
  FOREIGN KEY (`member_id`) REFERENCES `member`(`member_id`) ON DELETE CASCADE,
  FOREIGN KEY(`profession_name`) REFERENCES `profession`(`profession_name`) ON DELETE CASCADE
  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `interests` (
  `interest_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY(`interest_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `has_interests` (
  `interest_name` VARCHAR(255) NOT NULL,
  `member_id` INTEGER NOT NULL,
  PRIMARY KEY (`interest_name`, `member_id`),
  FOREIGN KEY (`interest_name`) REFERENCES `interests`(`interest_name`) ON DELETE CASCADE
  ON UPDATE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` INTEGER NOT NULL AUTO_INCREMENT,
  `message_timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `subject` VARCHAR(255),
  `body` TEXT,
  `from_member` INTEGER,
  PRIMARY KEY (`message_id`),
  FOREIGN KEY (`from_member`) REFERENCES `member` (`member_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `messages_to` (
  `message_id` INTEGER NOT NULL,
  `member_id` INTEGER NOT NULL,
  `message_seen` CHAR(1) NOT NULL DEFAULT 'N',
  `message_deleted` CHAR(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`message_id`, `member_id`),
  FOREIGN KEY (`message_id`) REFERENCES `messages`(`message_id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `invoice` (
  `invoice_id` INTEGER NOT NULL AUTO_INCREMENT,
  `amount_due` REAL NOT NULL,
  `payment_deadline` DATETIME NOT NULL,
  `date_paid` DATETIME,
  `billing_period_start` DATETIME NOT NULL,
  `billing_period_end` DATETIME NOT NULL,
  `account_holder` INTEGER NOT NULL,
  PRIMARY KEY (`invoice_id`),
  FOREIGN KEY (`account_holder`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `powon_group` (
  `powon_group_id` INTEGER NOT NULL AUTO_INCREMENT,
  `group_title` VARCHAR(64),
  `description` TEXT,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `group_picture` VARCHAR(255),
  `group_owner` INTEGER,
  PRIMARY KEY (`powon_group_id`),
  FOREIGN KEY (`group_owner`) REFERENCES `member`(`member_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `is_group_member` (
  `powon_group_id` INTEGER NOT NULL,
  `member_id` INTEGER NOT NULL,
  `request_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approval_date` DATETIME,
  PRIMARY KEY (`powon_group_id`, `member_id`),
  FOREIGN KEY (`powon_group_id`) REFERENCES `powon_group`(`powon_group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `page` (
  `page_id` INTEGER NOT NULL AUTO_INCREMENT,
  `page_title` VARCHAR(64),
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `group_page` (
  `page_id` INTEGER NOT NULL,
  `page_description` TEXT,
  `access_type` CHAR(1) NOT NULL DEFAULT 'P', -- 'E' Everyone, 'P' private (only members)
  `page_owner` INTEGER,
  `page_group` INTEGER NOT NULL,
  PRIMARY KEY (`page_id`),
  FOREIGN KEY (`page_id`) REFERENCES `page`(`page_id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_owner`) REFERENCES `member`(`member_id`) ON DELETE SET NULL,
  FOREIGN KEY (`page_group`) REFERENCES `powon_group`(`powon_group_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `profile_page` (
  `page_id` INTEGER NOT NULL,
  `page_access` INTEGER NOT NULL, -- Bits represent whether friends, family, colleagues, etc have access to the page.
  `member_id` INTEGER NOT NULL,
  PRIMARY KEY (`page_id`),
  FOREIGN KEY (`page_id`) REFERENCES `page`(`page_id`) ON DELETE CASCADE,
  FOREIGN KEY (`member_id`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `member_can_access_page` (
  `page_id` INTEGER NOT NULL,
  `powon_group_id` INTEGER NOT NULL,
  `member_id` INTEGER NOT NULL,
  PRIMARY KEY (`page_id`, `powon_group_id`, `member_id`),
  FOREIGN KEY (`powon_group_id`, `member_id`) REFERENCES `is_group_member`(`powon_group_id`, `member_id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_id`) REFERENCES `page`(`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `post` (
  `post_id` INTEGER NOT NULL AUTO_INCREMENT,
  `post_date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post_type` CHAR(1) NOT NULL DEFAULT 'T', -- Text, Picture, Video
  `path_to_resource` VARCHAR(255),
  `post_body` TEXT,
  `comment_permission` CHAR(1) NOT NULL DEFAULT 'A', -- can comment 'C', can view 'V', can link 'L', can add content 'A'.
  `parent_post` INTEGER, -- it's a comment if not null
  `page_id` INTEGER NOT NULL,
  `author_id` INTEGER NOT NULL,
  PRIMARY KEY (`post_id`),
  FOREIGN KEY (`parent_post`) REFERENCES `post`(`post_id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_id`) REFERENCES `page`(`page_id`) ON DELETE CASCADE,
  FOREIGN KEY (`author_id`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event` (
  `event_id` INTEGER NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(64),
  `description` TEXT,
  `powon_group_id` INTEGER NOT NULL,
  PRIMARY KEY (`event_id`),
  FOREIGN KEY (`powon_group_id`) REFERENCES `powon_group`(`powon_group_id`) ON DELETE CASCADE
) ENGINE =InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `event_details` (
  `event_id` INTEGER NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `location` VARCHAR(255),
  PRIMARY KEY (`event_id`, `event_date`, `event_time`, `location`),
  FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `votes_on` (
  `member_id` INTEGER NOT NULL,
  `powon_group_id` INTEGER NOT NULL,
  `event_id` INTEGER NOT NULL,
  `event_date` DATE NOT NULL,
  `event_time` TIME NOT NULL,
  `location` VARCHAR(255),
  PRIMARY KEY (`member_id`, `powon_group_id`, `event_id`, `event_date`, `event_time`, `location`),
  FOREIGN KEY (`member_id`, `powon_group_id`)
  REFERENCES is_group_member(`member_id`, `powon_group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`event_id`, `event_date`, `event_time`, `location`)
  REFERENCES `event_details`(`event_id`, `event_date`, `event_time`, `location`) ON DELETE CASCADE
) ENGINE=Innodb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gift_exchange` (
  `from_member` INTEGER NOT NULL,
  `to_member` INTEGER NOT NULL,
  `gift_exchange_date` DATE NOT NULL,
  PRIMARY KEY (`from_member`, `to_member`, `gift_exchange_date`),
  FOREIGN KEY (`from_member`) REFERENCES `member`(`member_id`) ON DELETE CASCADE,
  FOREIGN KEY (`to_member`) REFERENCES `member`(`member_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
