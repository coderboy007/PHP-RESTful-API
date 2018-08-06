-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2018 at 02:17 PM
-- Server version: 5.6.26
-- PHP Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restdemo`
--

-- --------------------------------------------------------

--
-- Table structure for table `imagedata`
--

CREATE TABLE IF NOT EXISTS `imagedata` (
  `image_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `images` text NOT NULL,
  `image_name` varchar(250) NOT NULL,
  `image_description` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `imagedata`
--

INSERT INTO `imagedata` (`image_id`, `user_id`, `images`, `image_name`, `image_description`, `created_date`) VALUES
(1, 19, '152992573734829.png', 'HELLO', 'bAS TESTING', '2018-06-25 16:52:31'),
(7, 1, '152992713123797.png', 'HELLO 1', 'bAS TESTING 1', '2018-06-25 17:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `user_fullname` varchar(25) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `user_image` text NOT NULL,
  `user_token` varchar(250) NOT NULL,
  `user_status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_fullname`, `email`, `password`, `user_image`, `user_token`, `user_status`) VALUES
(1, 'amit', 'admin@amit.com', '202cb962ac59075b964b07152d234b70', '', '', 1),
(4, 'A 2', 'a2@amit.com', '202cb962ac59075b964b07152d234b70', '', '', 1),
(5, 'A 3', 'a3@amit.com', '202cb962ac59075b964b07152d234b70', '', '', 1),
(19, 'Amit Dudhat', 'amit@amit.com', '202cb962ac59075b964b07152d234b70', 'abc.png', 'UG0yl6mYrF1KFKZa7DGJb6woiGpwtGBucFFOq4nJcvjjfXeXo6yTNEonfqGSYDTOXiDPWHQnynvcib7N3fiumaP5nm38s7eGF48XtFPphPneiwhELiMeTs8FXPcam9Hl', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `imagedata`
--
ALTER TABLE `imagedata`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `imagedata`
--
ALTER TABLE `imagedata`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
