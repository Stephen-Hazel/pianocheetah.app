-- shazbook database schema
-- Run once via cPanel > phpMyAdmin or SSH: mysql -u user -p db < schema.sql

CREATE TABLE IF NOT EXISTS `users` (
   `id`         INT(11)      NOT NULL AUTO_INCREMENT,
   `google_id`  VARCHAR(100) NOT NULL,
   `email`      VARCHAR(255) NOT NULL,
   `name`       VARCHAR(255) NOT NULL,
   `avatar`     TEXT         DEFAULT NULL,
   `bio`        TEXT         DEFAULT NULL,
   `created_at` DATETIME     DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY `google_id` (`google_id`),
   UNIQUE KEY `email`     (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `posts` (
   `id`         INT(11)  NOT NULL AUTO_INCREMENT,
   `user_id`    INT(11)  NOT NULL,
   `body`       TEXT     NOT NULL,
   `image`      VARCHAR(500) DEFAULT NULL,
   `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `user_id` (`user_id`),
   CONSTRAINT `fk_posts_user`
      FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `friends` (
   `id`           INT(11) NOT NULL AUTO_INCREMENT,
   `requester_id` INT(11) NOT NULL,
   `addressee_id` INT(11) NOT NULL,
   `status`       ENUM('pending','accepted') NOT NULL DEFAULT 'pending',
   `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY `unique_pair` (`requester_id`,`addressee_id`),
   KEY `addressee_id` (`addressee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `comments` (
   `id`         INT(11)  NOT NULL AUTO_INCREMENT,
   `post_id`    INT(11)  NOT NULL,
   `user_id`    INT(11)  NOT NULL,
   `body`       TEXT     DEFAULT NULL,
   `image`      VARCHAR(500) DEFAULT NULL,
   `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   KEY `post_id` (`post_id`),
   KEY `user_id` (`user_id`),
   CONSTRAINT `fk_comments_post`
      FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
      ON DELETE CASCADE,
   CONSTRAINT `fk_comments_user`
      FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `likes` (
   `id`         INT(11) NOT NULL AUTO_INCREMENT,
   `post_id`    INT(11) NOT NULL,
   `user_id`    INT(11) NOT NULL,
   `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY (`id`),
   UNIQUE KEY `unique_like` (`post_id`,`user_id`),
   KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
