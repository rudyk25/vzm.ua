CREATE TABLE `reviews` (
 `id` int NOT NULL AUTO_INCREMENT,
 `author` varchar(255) NOT NULL,
 `date_added` datetime NOT NULL,
 `text` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci