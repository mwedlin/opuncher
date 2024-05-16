-- Create the database tables for Opuncher
--
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

--
-- Table `games`
--

CREATE TABLE `games` (
  `id` int NOT NULL,
  `name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table `punch`
--

CREATE TABLE `punch` (
  `user` int NOT NULL,
  `transmitter` int NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table `transmitters`
--

CREATE TABLE `transmitters` (
  `game` int NOT NULL,
  `id` int NOT NULL,
  `name` text NOT NULL,
  `code` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `ident` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `game` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` text NOT NULL,
  `full_name` text NOT NULL,
  `running` tinyint(1) NOT NULL DEFAULT '0',
  `start` timestamp NULL DEFAULT NULL,
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  `finish` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

COMMIT;
