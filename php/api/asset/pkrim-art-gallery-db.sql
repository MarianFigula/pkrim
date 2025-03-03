-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: mysql
-- Čas generovania: Po 03.Mar 2025, 14:35
-- Verzia serveru: 9.2.0
-- Verzia PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `pkrim-art-gallery`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `art`
--

CREATE TABLE `art` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `img_url` varchar(512) NOT NULL,
  `title` varchar(512) NOT NULL,
  `description` varchar(1024) NOT NULL,
  `price` int DEFAULT NULL,
  `upload_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Sťahujem dáta pre tabuľku `art`
--

INSERT INTO `art` (`id`, `user_id`, `img_url`, `title`, `description`, `price`, `upload_date`) VALUES
(1, 2, '/asset/camera.png', 'Kamera', 'Toto je kamera', 20, '2025-02-28 14:20:19'),
(2, 7, '/asset/abstract.png', 'Abstract No.45', 'A colorful abstract composition filled with organic shapes and dynamic flow.', 150, '2025-02-28 14:20:19'),
(3, 7, '/asset/composition.png', 'Still life', 'A not so classic still life with fruit and flowers.', 56, '2025-02-28 14:20:19'),
(4, 5, '/asset/raffael.png', 'The School of Athens', 'A Renaissance masterpiece by Raphael, depicting great philosophers.', 50000000, '2025-02-28 14:20:19'),
(5, 10, '/asset/reflection.png', 'Reflection', 'Reflection on the water surface captured in painting.', 74, '2025-02-28 14:20:19'),
(6, 11, '/asset/sunset.png', 'Sunset at the beach', 'Sun setting over a sandy beach, capturing the peaceful light.', 42, '2025-02-28 14:20:19'),
(7, 4, '/asset/sunset2.png', 'Horizon', 'A minimalist landscape with a setting sun on the horizon and gentle color transitions.', 176, '2025-02-28 14:20:19'),
(8, 1, '/asset/An-undirected-graph-with-7-nodes-and-7-edges.png', 'dd', 'ds', 10, '2025-02-28 14:33:01');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cart`
--

CREATE TABLE `cart` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Sťahujem dáta pre tabuľku `cart`
--

INSERT INTO `cart` (`id`, `user_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `cart_art`
--

CREATE TABLE `cart_art` (
  `id` int UNSIGNED NOT NULL,
  `cart_id` int UNSIGNED NOT NULL,
  `art_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Sťahujem dáta pre tabuľku `cart_art`
--

INSERT INTO `cart_art` (`id`, `cart_id`, `art_id`) VALUES
(1, 3, 1),
(2, 3, 6),
(3, 3, 5),
(4, 3, 6),
(5, 3, 5),
(6, 3, 1),
(7, 3, 6),
(8, 3, 6);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `credit_card`
--

CREATE TABLE `credit_card` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `card_number` varchar(36) NOT NULL,
  `expiration_date` date NOT NULL,
  `cvc` int NOT NULL,
  `card_creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `review`
--

CREATE TABLE `review` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `art_id` int UNSIGNED NOT NULL,
  `review_text` varchar(1024) NOT NULL,
  `rating` int NOT NULL,
  `review_creation_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Sťahujem dáta pre tabuľku `review`
--

INSERT INTO `review` (`id`, `user_id`, `art_id`, `review_text`, `rating`, `review_creation_date`) VALUES
(1, 10, 1, 'The camera photo is good, but I feel it could use more contrast to make it stand out.', 3, '2025-02-28 14:20:20'),
(2, 6, 2, 'Abstract No.45 is vibrant, but the composition seems a bit chaotic and overwhelming.', 4, '2025-02-28 14:20:20'),
(3, 2, 3, 'The still life composition feels classic and serene, a beautiful piece of art.', 5, '2025-02-28 14:20:20'),
(4, 4, 4, 'The School of Athens is a masterpiece that brings Renaissance philosophy to life.', 5, '2025-02-28 14:20:20'),
(5, 2, 5, 'Reflection has a deep sense of tranquility. The symmetry is perfect.', 5, '2025-02-28 14:20:20'),
(6, 10, 6, 'Sunset at the beach is peaceful, but the picture seems a bit overexposed.', 3, '2025-02-28 14:20:20'),
(7, 7, 7, 'Horizon has a minimalist beauty, the gradients create a soothing visual effect.', 5, '2025-02-28 14:20:20'),
(8, 3, 1, 'The vintage camera photograph is stunning, with beautiful mechanical details.', 5, '2025-02-28 14:20:20'),
(9, 5, 2, 'Abstract No.45 is a fantastic blend of colors and shapes. Truly mesmerizing!', 5, '2025-02-28 14:20:20'),
(10, 9, 3, 'The still life is well done, but the colors seem a bit dull compared to other works.', 4, '2025-02-28 14:20:20'),
(11, 2, 4, 'The School of Athens is impressive piece of work.', 5, '2025-02-28 14:20:20'),
(12, 7, 5, 'Reflection is nice, but it lacks the depth I was expecting.', 3, '2025-02-28 14:20:20'),
(13, 3, 6, 'Sunset at the beach captures a calming and peaceful atmosphere. Lovely colors!', 5, '2025-02-28 14:20:20'),
(14, 3, 7, 'Horizon is minimalist, but it feels a bit too plain and lacks detail.', 3, '2025-02-28 14:20:20'),
(15, 4, 1, 'The vintage camera photo has charm, yet it could benefit from a clearer focus.', 4, '2025-02-28 14:20:20'),
(16, 9, 2, 'Abstract No.45 is colorful, but the shapes feel too disjointed.', 3, '2025-02-28 14:20:20'),
(17, 11, 3, 'The still life is decent, but it doesnâ€™t bring anything new to the genre.', 1, '2025-02-28 14:20:20'),
(18, 6, 4, 'Work of a genius mind.', 5, '2025-02-28 14:20:20'),
(19, 9, 5, 'Reflection has potential, but it seems somewhat flat.', 3, '2025-02-28 14:20:20'),
(20, 11, 6, 'Sunset at the beach is nice, though it lacks the dramatic effect I was hoping for.', 4, '2025-02-28 14:20:20'),
(21, 10, 7, 'Horizon is calming, but it feels like it could use more visual interest.', 2, '2025-02-28 14:20:20'),
(22, 12, 1, 'The vintage camera photo is beautiful, yet the background is a bit distracting.', 4, '2025-02-28 14:20:20'),
(23, 12, 2, 'This abstract piece is vibrant, though the overall composition feels a bit cluttered.', 2, '2025-02-28 14:20:20'),
(24, 6, 3, 'The still life is well-executed but lacks a bit of vibrancy.', 4, '2025-02-28 14:20:20'),
(25, 8, 4, 'The School of Athens is undeniably a masterpiece, showcasing an impressive array of Renaissance figures. However, the density of philosophers in the composition creates an overcrowded effect that detracts from the clarity and focus of the central figures. For a work of this magnitude, a more streamlined approach would better highlight the intellectual prominence of the depicted individuals.', 3, '2025-02-28 14:20:20'),
(26, 8, 7, 'Horizon presents a minimalist landscape with commendable use of color gradients. Nevertheless, the piece suffers from a lack of spatial complexity and visual intrigue. The horizon line, while aesthetically pleasing, is insufficiently detailed and lacks the dynamic elements that would elevate it to a higher level of artistic sophistication. Itâ€™s a passable work, but does not meet the high standards of visual engagement I seek.', 1, '2025-02-28 14:20:20'),
(27, 1, 4, 'l', 5, '2025-02-28 14:33:09');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user`
--

CREATE TABLE `user` (
  `id` int UNSIGNED NOT NULL,
  `username` varchar(36) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(512) NOT NULL,
  `security_question` varchar(512) NOT NULL,
  `security_answer` varchar(512) NOT NULL,
  `role` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Sťahujem dáta pre tabuľku `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `security_question`, `security_answer`, `role`) VALUES
(1, 'admin', 'admin.gallery@gallery.fei', 'a906449d5769fa7361d7ecc6aa3f6d28', 'What is your pet\'s name?', 'Max', 'S'),
(2, 'Alice Bobova', 'alice.bobova@gallery.fei', '495c6dfd597415374445c3e104646271', 'What was your first car?', 'Honda Civic', 'U'),
(3, 'Bob Alicovy', 'bob.alicovy@gallery.fei', 'd796b1242dc89cedffe596d14517f2ac', 'What was your first car?', 'Toyota Corolla', 'U'),
(4, 'Charlie Brown', 'charlie.brown@gallery.fei', 'f6f8b17841190e3bf66f6e9f2b3d6cd1', 'What is your grandmother\'s name?', 'Anna', 'U'),
(5, 'David Smith', 'david.smith@gallery.fei', 'fb5ec35d77091432228fdabca7596abb', 'What was the name of your first school?', 'Spojena skola Tilgnerova', 'U'),
(6, 'Eva Jones', 'eva.jones@gallery.fei', '8b5d46821f396f8572e6e739868ab3a4', 'What was the name of your first school', 'Paneuropska sukromna zakladna skola', 'U'),
(7, 'Frank Williams', 'frank.williams@gallery.fei', '1a7fd50291c3d26152119d6deb9da7f9', 'What is your pet\'s name?', 'Muro', 'U'),
(8, 'Sheldon Cooper', 'sheldon.cooper@gallery.fei', '244634890f075124d2418d294d629f20', 'What is your pet\'s name?', 'Simona', 'U'),
(9, 'George Wilson', 'george.wilson@gallery.fei', 'c6203afb6795ba949f78484e714736f7', 'What is your pet\'s name?', 'Bella', 'U'),
(10, 'Anna Kovacova', 'anna.kovacova@gallery.fei', '4b5987b45136a4b723c9f9cd2841a77b', 'What was your first car?', 'Skoda Felicia', 'U'),
(11, 'Betty Smith', 'betty.smith@gallery.fei', '7c9fd2c192db77dcac0e42ee3ab6576e', 'What is your grandmother\'s name?', 'Sofia', 'U'),
(12, 'Clara Miller', 'clara.miller@gallery.fei', '33580df0d0a32aed259814a9720d859f', 'What is your pet\'s name?', 'Rudolf', 'U');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `art`
--
ALTER TABLE `art`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexy pre tabuľku `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexy pre tabuľku `cart_art`
--
ALTER TABLE `cart_art`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `art_id` (`art_id`);

--
-- Indexy pre tabuľku `credit_card`
--
ALTER TABLE `credit_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexy pre tabuľku `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `art_id` (`art_id`);

--
-- Indexy pre tabuľku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `art`
--
ALTER TABLE `art`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pre tabuľku `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pre tabuľku `cart_art`
--
ALTER TABLE `cart_art`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pre tabuľku `credit_card`
--
ALTER TABLE `credit_card`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pre tabuľku `review`
--
ALTER TABLE `review`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT pre tabuľku `user`
--
ALTER TABLE `user`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `art`
--
ALTER TABLE `art`
  ADD CONSTRAINT `art_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `cart_art`
--
ALTER TABLE `cart_art`
  ADD CONSTRAINT `cart_art_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_art_ibfk_2` FOREIGN KEY (`art_id`) REFERENCES `art` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `credit_card`
--
ALTER TABLE `credit_card`
  ADD CONSTRAINT `credit_card_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`art_id`) REFERENCES `art` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
