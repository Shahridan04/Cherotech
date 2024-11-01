-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2024 at 07:08 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raisethebar`
--

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `age` int(11) NOT NULL,
  `membership` enum('Monthly','3-Month','Yearly') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `name`, `email`, `phone`, `gender`, `age`, `membership`, `file_path`, `registration_date`) VALUES
(11, 'ナルト うずまき', 'naruto.uzumaki@example.com', '090-1234-5678', 'Male', 16, 'Monthly', '/files/naruto_uzumaki.pdf', '2024-10-29 17:00:07'),
(12, 'さくら はなみ', 'sakura.hanami@example.com', '090-2345-6789', 'Female', 15, '3-Month', '/files/sakura_hanami.pdf', '2024-10-29 17:00:07'),
(13, 'ルフィ モンキー', 'luffy.monkey@example.com', '090-3456-7890', 'Male', 19, 'Yearly', '/files/luffy_monkey.pdf', '2024-10-29 17:00:07'),
(14, 'アスナ ゆうき', 'asuna.yuuki@example.com', '090-4567-8901', 'Female', 17, 'Monthly', '/files/asuna_yuuki.pdf', '2024-10-29 17:00:07'),
(15, 'エレン イェーガー', 'eren.yeager@example.com', '090-5678-9012', 'Male', 19, '3-Month', '/files/eren_yeager.pdf', '2024-10-29 17:00:07'),
(16, 'ミカサ アッカーマン', 'mikasa.ackerman@example.com', '090-6789-0123', 'Female', 19, 'Yearly', '/files/mikasa_ackerman.pdf', '2024-10-29 17:00:07'),
(17, '光 みさき', 'hikari.misaki@example.com', '090-7890-1234', 'Female', 18, 'Monthly', '/files/hikari_misaki.pdf', '2024-10-29 17:00:07'),
(18, '剣心 薫', 'kenshin.kaworu@example.com', '090-8901-2345', 'Male', 28, '3-Month', '/files/kenshin_kaworu.pdf', '2024-10-29 17:00:07'),
(19, 'トトロ', 'totoro@example.com', '090-9012-3456', 'Other', 25, 'Yearly', '/files/totoro.pdf', '2024-10-29 17:00:07'),
(20, 'シンジ いくさ', 'shinji.ikusa@example.com', '090-0123-4567', 'Male', 14, 'Monthly', '/files/shinji_ikusa.pdf', '2024-10-29 17:00:07'),
(21, 'リュウ うえき', 'ryu.ueki@example.com', '080-1234-5678', 'Male', 15, 'Monthly', '/files/ryu_ueki.pdf', '2024-10-29 17:20:05'),
(22, 'ユイ いちか', 'yui.ichika@example.com', '080-2345-6789', 'Female', 16, '3-Month', '/files/yui_ichika.pdf', '2024-10-29 17:20:05'),
(23, 'セイバー', 'saber@example.com', '080-3456-7890', 'Female', 23, 'Yearly', '/files/saber.pdf', '2024-10-29 17:20:05'),
(24, '銀時 たま', 'gintoki.tama@example.com', '080-4567-8901', 'Male', 29, 'Monthly', '/files/gintoki_tama.pdf', '2024-10-29 17:20:05'),
(25, '悟空 かめはめ', 'goku.kamehameha@example.com', '080-5678-9012', 'Male', 30, '3-Month', '/files/goku_kamehameha.pdf', '2024-10-29 17:20:05'),
(26, 'しずか ちゃん', 'shizuka-chan@example.com', '080-6789-0123', 'Female', 14, 'Yearly', '/files/shizuka_chan.pdf', '2024-10-29 17:20:05'),
(27, 'カカシ はなみ', 'kakashi.hanami@example.com', '080-7890-1234', 'Male', 30, 'Monthly', '/files/kakashi_hanami.pdf', '2024-10-29 17:20:05'),
(28, 'ミト かごめ', 'mito.kagome@example.com', '080-8901-2345', 'Female', 18, '3-Month', '/files/mito_kagome.pdf', '2024-10-29 17:20:05'),
(29, 'アカリ かがみ', 'akari.kagami@example.com', '080-9012-3456', 'Female', 17, 'Yearly', '/files/akari_kagami.pdf', '2024-10-29 17:20:05'),
(30, 'シンカ イチゴ', 'shinka.ichigo@example.com', '080-0123-4567', 'Male', 21, 'Monthly', '/files/shinka_ichigo.pdf', '2024-10-29 17:20:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
