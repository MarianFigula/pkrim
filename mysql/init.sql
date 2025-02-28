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
    ('1', 'admin', 'admin.gallery@gallery.fei', 'a906449d5769fa7361d7ecc6aa3f6d28', 'What is your pet''s name?', '6a061313d22e51e0f25b7cd4dc065233', 'S'), /*123abc*/
    ('2', 'Alice Bobova', 'alice.bobova@gallery.fei', '495c6dfd597415374445c3e104646271', 'What was your first car?', '817a10a85e3d3f42fdb9ce5b434072b3', 'U'),/*T9v!3m@QpZ7**/
    ('3', 'Bob Alicovy', 'bob.alicovy@gallery.fei', 'd796b1242dc89cedffe596d14517f2ac', 'What was your first car?', 'd86d32eb99df82da2b872b9f6e0d7253', 'U'),/*iloveyou2*/
    ('4', 'Charlie Brown', 'charlie.brown@gallery.fei', 'f6f8b17841190e3bf66f6e9f2b3d6cd1', 'What is your grandmother''s name?', '97a9d330e236c8d067f01da1894a5438', 'U'),/*M8&y^R#oB1t@*/
    ('5', 'David Smith', 'david.smith@gallery.fei', 'fb5ec35d77091432228fdabca7596abb', 'What was the name of your first school?', '7576ebf1970167fcadb72a26912fc4bc', 'U'),/*qZ3*rW!mT7v@*/
    ('6', 'Eva Jones', 'eva.jones@gallery.fei', '8b5d46821f396f8572e6e739868ab3a4', 'What was the name of your first school', '5da788a0bc4f79a3e1b4ebb5dca52678', 'U'),/*pX2$Y#9NkV8^*/
    ('7', 'Frank Williams', 'frank.williams@gallery.fei', '1a7fd50291c3d26152119d6deb9da7f9', 'What is your pet''s name?', '14fb790c162be0367d27921365dab069', 'U'),/*B1t@M8y^R#oP*/
    ('8', 'Sheldon Cooper', 'sheldon.cooper@gallery.fei', '244634890f075124d2418d294d629f20', 'What is your favourite number?', '57b9cdfbafb42a79ef2c2afa8875bb9f', 'U'),/*T7v!pZ3*mQ@X*/
    ('9', 'George Wilson', 'george.wilson@gallery.fei', 'c6203afb6795ba949f78484e714736f7', 'What is your pet''s name?', 'e130fc6de9c40799c78e29ed7b77880a', 'U'),/*N9K^X2$Y#R&8*/
    ('10', 'Anna Kovacova', 'anna.kovacova@gallery.fei', '4b5987b45136a4b723c9f9cd2841a77b', 'What was your first car?', '432b2969d027397188609363dbf54b85', 'U'),/*qW!T7mZ3@pX**/
    ('11', 'Betty Smith', 'betty.smith@gallery.fei', '7c9fd2c192db77dcac0e42ee3ab6576e', 'What is your grandmother''s name?', '654cd76590cebe0ba37e8d4cce8a96ee', 'U'),/*B1y^oM8R#t@P*/
    ('12', 'Clara Miller', 'clara.miller@gallery.fei', '33580df0d0a32aed259814a9720d859f', 'What is your pet''s name?', '4a7939882a437a582bfe42754df7acd3', 'U');/*Z3*mQ@pX2$Y^*/


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