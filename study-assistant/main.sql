SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




--
-- Database: `timetable`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE events (
    id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    start DATETIME NOT NULL,
    end DATETIME NOT NULL
);

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