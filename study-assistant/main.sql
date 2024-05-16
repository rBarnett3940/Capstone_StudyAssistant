SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




--
-- Database: `capstone`
--

-- --------------------------------------------------------

--
-- 
--
-- database schema
CREATE DATABASE capstone;

USE capstone;


-- Table for users
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reg_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

-- Table for user preferences
CREATE TABLE preferences (
  userID int(11) NOT NULL PRIMARY KEY,
  mode int,
  tod int,
  env int,
  tech int,
  retain int,
  maxHours int
);



-- Table for user events for the timetable
CREATE TABLE events (
    userID INT ,
    eventID INT PRIMARY KEY
    info VARCHAR(255) NOT NULL,
    FOREIGN KEY (userID)
        REFERENCES users(id)
        ON DELETE CASCADE
);

-- Tbale to store generated hours for each course
CREATE TABLE hours(
    userID INT PRIMARY KEY NOT NULL,
    COMP1126 INT,
    COMP1127 INT,
    COMP1161 INT,
    COMP1210 INT,
    COMP1220 INT,
    COMP2190 INT,
    COMP2140 INT,
    COMP2171 INT,
    COMP2201 INT,
    COMP2211 INT,
    COMP2340 INT,
    COMP2130 INT
);