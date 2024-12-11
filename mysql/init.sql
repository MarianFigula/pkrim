CREATE DATABASE IF NOT EXISTS `pkrim-art-gallery`;
USE `pkrim-art-gallery`;

-- Create the `user` table
CREATE TABLE IF NOT EXISTS `user`
(
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(36) NOT NULL,
    `email` VARCHAR(256) NOT NULL,
    `password` VARCHAR(512) NOT NULL,
    `security_question` VARCHAR(512) NOT NULL,
    `security_answer` VARCHAR(512) NOT NULL,
    `role` CHAR(1) NOT NULL
    );

-- Insert sample data into `user` table
INSERT INTO `user` (`id`, `username`, `email`, `password`, `security_question`, `security_answer`, `role`)
VALUES
    ('1', 'admin', 'adminGallery@admin.com', '$2y$10$sbHuKYKYDFNPMKJ4K42tQ.SxUQy.MpLXEQNXWYHkGmlXwL1rmCnfO', 'What is your pet''s name?', 'Max', 'S'), /*ZE47ZX0aCntutXiTc2NU+*/
    ('2', 'alice', 'alicebobova@gmail.com', '$2y$10$sbHuKYKYDFNPMKJ4K42tQ.SxUQy.MpLXEQNXWYHkGmlXwL1rmCnfO', 'What was your first car?', 'Honda Civic', 'U'),/*0QkbFaa3WbyWFCLLFdAT+*/
    ('3', 'bob', 'bobalicovy@gmail.com', '$2y$10$sbHuKYKYDFNPMKJ4K42tQ.SxUQy.MpLXEQNXWYHkGmlXwL1rmCnfO', 'What was your first car?', 'Toyota Corolla', 'U'),/*Zgyv8tvUgZt7fGr4mQ6Q+*/
    ('4', 'charlie', 'charliebrown@gmail.com', '$2y$10$N8dF1j5pTQvmlSLrU/3k8Q0dcLd5z9fJKSzUd3QZ9nPb8TcH6nNOe', 'What is your grandmother''s name?', 'Anna', 'U'),/*5Hjs8Tyt2WbwZgZZVfAR+*/
    ('5', 'david', 'davidsmith@gmail.com', '$2y$10$3fnTlwXGJ7MkTq5K/QP/JKQJ4bKP5sHgRmchDwQh5Dq4vUOzmfNkK', 'What was the name of your first school?', 'Spojena skola Tilgnerova', 'U'),/*X7hdYlvUg7z8rJd8mS6P+*/
    ('6', 'eva', 'evajones@gmail.com', '$2y$10$Tn8jPKlJZuN6L5kdrI/XjOxG9zZbThXgLyP5xFsMkl1O3oE5DQIXy', 'What was the name of your first school', 'Paneuropska sukromna zakladna skola', 'U'),/*9HfkVaa4XcyWFHL9LdFT+*/
    ('7', 'frank', 'frankwilliams@gmail.com', '$2y$10$U9ldRj7qNz3dkSmXzQ/Hl1KQZRm2EPqfT3W4c7Zj9NpxnO7vBkM3A', 'What is your pet''s name?', 'Muro', 'U'),/*J8fkL6fUkQz4gGr9mQ5L+*/
    ('8', 'sheldon', 'sheldoncooper@gmail.com', '$2y$10$3pn9LlNvT9Pq1dNmXzJ/BzOPq7YPdErfK9zd8WTZmMkPqv8NrYMQs', 'What is your pet''s name?', 'Simona', 'U'),/*Bazinga123!*/
    ('9', 'george', 'georgewilson@gmail.com', '$2y$10$W9opSLvJZu3k9NmC4/DkEKOw4RMpzRdJ8ZTf9Zd6Gjk3mPbVvAXNa', 'What is your pet''s name?', 'Bella', 'U'),/*X9fvR6cVkTq7fFr6mS4Q+*/
    ('10', 'anna', 'annakovacova@gmail.com', '$2y$10$Zj8F1p7rVlLs9nYzS/Hl9Z5eQfDkJ6Fs9Rtq0TpG9kLp5mCbLnZGK', 'What was your first car?', 'Skoda Felicia', 'U'),/*7KlfH3sLgQz5rDg7nS8A+*/
    ('11', 'betty', 'bettysmith@gmail.com', '$2y$10$U8hkWm4tNx3p8LmVY/Xj7NQ4sQkL9mXfR5bR7TxHkQp4mD9nGnMqE', 'What is your grandmother''s name?', 'Sofia', 'U'),/*3FgkR9pVbWp9cTl3mQ6D+*/
    ('12', 'clara', 'claramiller@gmail.com', '$2y$10$P9qlRlMvT6Nq5kZnV/Hm7LQ3dKpJ7sKgS4qT5UwF9mPp9lCbLnFGA', 'What is your pet''s name?', 'Rudolf', 'U');/*8FglJ8nVkWp7sHf7lS5C+*/


-- Create the `art` table
CREATE TABLE IF NOT EXISTS `art`
(
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `img_url` VARCHAR(512) NOT NULL,
    `title` VARCHAR(512) NOT NULL,
    `description` VARCHAR(1024) NOT NULL,
    `price` INT,
    `upload_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

ALTER TABLE `art` ADD FOREIGN KEY (`user_id`)
    REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `art` (`id`,`user_id`, `img_url`, `title`, `description`, `price`, `upload_date`)
VALUES ('1', '2', '/arts/camera.png', 'Kamera', 'Toto je kamera', '20', CURRENT_TIMESTAMP()),
('2', '7', '/arts/abstract.png', 'Abstract No.45', 'A colorful abstract composition filled with organic shapes and dynamic flow.', '150', CURRENT_TIMESTAMP()),
('3', '7', '/arts/composition.png', 'Still life', 'A not so classic still life with fruit and flowers.', '56', CURRENT_TIMESTAMP()),
('4', '5', '/arts/raffael.png', 'The School of Athens', 'A Renaissance masterpiece by Raphael, depicting great philosophers.', '50000000', CURRENT_TIMESTAMP()),
('5', '10', '/arts/reflection.png', 'Reflection', 'Reflection on the water surface captured in painting.', '74', CURRENT_TIMESTAMP()),
('6', '11', '/arts/sunset.png', 'Sunset at the beach', 'Sun setting over a sandy beach, capturing the peaceful light.', '42', CURRENT_TIMESTAMP()),
('7', '4', '/arts/sunset2.png', 'Horizon', 'A minimalist landscape with a setting sun on the horizon and gentle color transitions.', '176', CURRENT_TIMESTAMP());

-- Create the `review` table
CREATE TABLE IF NOT EXISTS `review`
(
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `art_id` INT UNSIGNED NOT NULL,
    `review_text` VARCHAR(1024) NOT NULL,
    `rating` INT NOT NULL,
    `review_creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

ALTER TABLE `review` ADD FOREIGN KEY (`user_id`)
    REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `review` ADD FOREIGN KEY (`art_id`)
    REFERENCES `art`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `review` (`id`, `user_id`,`art_id`, `review_text`, `rating`, `review_creation_date`)
VALUES ('1', '10', '1', 'The camera photo is good, but I feel it could use more contrast to make it stand out.', '3', CURRENT_TIMESTAMP()),
       ('2', '6', '2', 'Abstract No.45 is vibrant, but the composition seems a bit chaotic and overwhelming.', '4', CURRENT_TIMESTAMP()),
       ('3', '2', '3', 'The still life composition feels classic and serene, a beautiful piece of art.', '5', CURRENT_TIMESTAMP()),
       ('4', '4', '4', 'The School of Athens is a masterpiece that brings Renaissance philosophy to life.', '5', CURRENT_TIMESTAMP()),
       ('5', '2', '5', 'Reflection has a deep sense of tranquility. The symmetry is perfect.', '5', CURRENT_TIMESTAMP()),
       ('6', '10', '6', 'Sunset at the beach is peaceful, but the picture seems a bit overexposed.', '3', CURRENT_TIMESTAMP()),
       ('7', '7', '7', 'Horizon has a minimalist beauty, the gradients create a soothing visual effect.', '5', CURRENT_TIMESTAMP()),
       ('8', '3', '1', 'The vintage camera photograph is stunning, with beautiful mechanical details.', '5', CURRENT_TIMESTAMP()),
       ('9', '5', '2', 'Abstract No.45 is a fantastic blend of colors and shapes. Truly mesmerizing!', '5', CURRENT_TIMESTAMP()),
       ('10', '9', '3', 'The still life is well done, but the colors seem a bit dull compared to other works.', '4', CURRENT_TIMESTAMP()),
       ('11', '2', '4', 'The School of Athens is impressive piece of work.', '5', CURRENT_TIMESTAMP()),
       ('12', '7', '5', 'Reflection is nice, but it lacks the depth I was expecting.', '3', CURRENT_TIMESTAMP()),
       ('13', '3', '6', 'Sunset at the beach captures a calming and peaceful atmosphere. Lovely colors!', '5', CURRENT_TIMESTAMP()),
       ('14', '3', '7', 'Horizon is minimalist, but it feels a bit too plain and lacks detail.', '3', CURRENT_TIMESTAMP()),
       ('15', '4', '1', 'The vintage camera photo has charm, yet it could benefit from a clearer focus.', '4', CURRENT_TIMESTAMP()),
       ('16', '9', '2', 'Abstract No.45 is colorful, but the shapes feel too disjointed.', '3', CURRENT_TIMESTAMP()),
       ('17', '11', '3', 'The still life is decent, but it doesn’t bring anything new to the genre.', '1', CURRENT_TIMESTAMP()),
       ('18', '6', '4', 'Work of a genius mind.', '5', CURRENT_TIMESTAMP()),
       ('19', '9', '5', 'Reflection has potential, but it seems somewhat flat.', '3', CURRENT_TIMESTAMP()),
       ('20', '11', '6', 'Sunset at the beach is nice, though it lacks the dramatic effect I was hoping for.', '4', CURRENT_TIMESTAMP()),
       ('21', '10', '7', 'Horizon is calming, but it feels like it could use more visual interest.', '2', CURRENT_TIMESTAMP()),
       ('22', '12', '1', 'The vintage camera photo is beautiful, yet the background is a bit distracting.', '4', CURRENT_TIMESTAMP()),
       ('23', '12', '2', 'This abstract piece is vibrant, though the overall composition feels a bit cluttered.', '2', CURRENT_TIMESTAMP()),
       ('24', '6', '3', 'The still life is well-executed but lacks a bit of vibrancy.', '4', CURRENT_TIMESTAMP()),
       ('25', '8', '4', 'The School of Athens is undeniably a masterpiece, showcasing an impressive array of Renaissance figures. However, the density of philosophers in the composition creates an overcrowded effect that detracts from the clarity and focus of the central figures. For a work of this magnitude, a more streamlined approach would better highlight the intellectual prominence of the depicted individuals.', '3', CURRENT_TIMESTAMP()),
       ('26', '8', '7', 'Horizon presents a minimalist landscape with commendable use of color gradients. Nevertheless, the piece suffers from a lack of spatial complexity and visual intrigue. The horizon line, while aesthetically pleasing, is insufficiently detailed and lacks the dynamic elements that would elevate it to a higher level of artistic sophistication. It’s a passable work, but does not meet the high standards of visual engagement I seek.', '1', CURRENT_TIMESTAMP());




-- Create the `credit_card` table
CREATE TABLE IF NOT EXISTS `credit_card`
(
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `card_number` VARCHAR(36) NOT NULL,
    `expiration_date` DATE NOT NULL,
    `cvc` INT NOT NULL,
    `card_creation_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    );

ALTER TABLE `credit_card` ADD FOREIGN KEY (`user_id`)
    REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- Create the `cart` table
CREATE TABLE IF NOT EXISTS `cart`
(
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
    );


-- Create the `cart_art` table
CREATE TABLE IF NOT EXISTS `cart_art`
(
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cart_id` INT UNSIGNED NOT NULL,
    `art_id` INT UNSIGNED NOT NULL
);

-- Add foreign keys for `cart_art` separately
ALTER TABLE `cart_art`
    ADD FOREIGN KEY (`cart_id`) REFERENCES `cart`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `cart_art`
    ADD FOREIGN KEY (`art_id`) REFERENCES `art`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;


INSERT INTO `cart` (`user_id`)
SELECT `id`
FROM `user`;