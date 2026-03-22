-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 22 2026 г., 20:03
-- Версия сервера: 8.0.30
-- Версия PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `school_diary`
--

-- --------------------------------------------------------

--
-- Структура таблицы `attendance`
--

CREATE TABLE `attendance` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'present',
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marked_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `subject_id`, `date`, `status`, `comment`, `marked_by`, `created_at`) VALUES
(1, 1, 1, '2024-11-06', 'present', NULL, 1, '2026-03-01 11:20:04'),
(2, 2, 1, '2024-11-06', 'present', NULL, 1, '2026-03-01 11:20:04'),
(3, 3, 1, '2024-11-06', 'present', NULL, 1, '2026-03-01 11:20:04'),
(4, 4, 1, '2024-11-06', 'present', NULL, 1, '2026-03-01 11:20:04'),
(5, 5, 1, '2024-11-06', 'late', 'Опоздал на 10 минут', 1, '2026-03-01 11:20:04'),
(6, 1, 1, '2024-11-08', 'present', NULL, 1, '2026-03-01 11:20:04'),
(7, 2, 1, '2024-11-08', 'present', NULL, 1, '2026-03-01 11:20:04'),
(8, 3, 1, '2024-11-08', 'absent', 'Болезнь', 1, '2026-03-01 11:20:04'),
(9, 4, 1, '2024-11-08', 'present', NULL, 1, '2026-03-01 11:20:04'),
(10, 5, 1, '2024-11-08', 'present', NULL, 1, '2026-03-01 11:20:04'),
(11, 1, 1, '2024-11-11', 'present', NULL, 1, '2026-03-01 11:20:04'),
(12, 2, 1, '2024-11-11', 'present', NULL, 1, '2026-03-01 11:20:04'),
(13, 3, 1, '2024-11-11', 'excused', 'Справка от врача', 1, '2026-03-01 11:20:04'),
(14, 4, 1, '2024-11-11', 'present', NULL, 1, '2026-03-01 11:20:04'),
(15, 5, 1, '2024-11-11', 'absent', NULL, 1, '2026-03-01 11:20:04'),
(16, 5, 1, '2026-03-01', 'present', '', NULL, '2026-03-01 14:00:02'),
(17, 2, 1, '2026-03-01', 'present', '', NULL, '2026-03-01 14:00:02'),
(18, 3, 1, '2026-03-01', 'present', '', NULL, '2026-03-01 14:00:02'),
(19, 4, 1, '2026-03-01', 'present', '', NULL, '2026-03-01 14:00:02'),
(20, 1, 1, '2026-03-01', 'present', '', NULL, '2026-03-01 14:00:02'),
(21, 2, 3, '2026-03-01', 'absent', '', 2, '2026-03-01 18:36:15'),
(22, 3, 3, '2026-03-01', 'absent', '', 2, '2026-03-01 18:36:15'),
(23, 4, 3, '2026-03-01', 'late', '', 2, '2026-03-01 18:36:15'),
(24, 1, 3, '2026-03-01', 'excused', '', 2, '2026-03-01 18:36:15'),
(25, 2, 4, '2026-03-02', 'present', '', 3, '2026-03-02 13:10:24'),
(26, 3, 4, '2026-03-02', 'late', 'на 20 минут', 3, '2026-03-02 13:10:24'),
(27, 4, 4, '2026-03-02', 'excused', 'у врача', 3, '2026-03-02 13:10:24'),
(28, 1, 4, '2026-03-02', 'absent', '', 3, '2026-03-02 13:10:24'),
(29, 2, 2, '2026-03-03', 'present', '', 2, '2026-03-03 17:38:49'),
(30, 3, 2, '2026-03-03', 'absent', '', 2, '2026-03-03 17:38:49'),
(31, 4, 2, '2026-03-03', 'present', '', 2, '2026-03-03 17:38:49'),
(32, 1, 2, '2026-03-03', 'present', '', 2, '2026-03-03 17:38:49'),
(33, 2, 3, '2026-03-03', 'present', '', 2, '2026-03-03 17:38:54'),
(34, 3, 3, '2026-03-03', 'absent', '', 2, '2026-03-03 17:38:54'),
(35, 4, 3, '2026-03-03', 'present', '', 2, '2026-03-03 17:38:54'),
(36, 1, 3, '2026-03-03', 'present', '', 2, '2026-03-03 17:38:54');

-- --------------------------------------------------------

--
-- Структура таблицы `classes`
--

CREATE TABLE `classes` (
  `id` int NOT NULL,
  `name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int NOT NULL,
  `class_teacher_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `classes`
--

INSERT INTO `classes` (`id`, `name`, `year`, `class_teacher_id`) VALUES
(1, '9А', 2025, 1),
(2, '9Б', 2025, 3),
(3, '10А', 2025, NULL),
(4, '10Б', 2025, NULL),
(5, '11А', 2025, NULL),
(6, '8А', 2025, NULL),
(7, '1А', 2025, NULL),
(8, '1Б', 2025, NULL),
(9, '2А', 2025, NULL),
(10, '2Б', 2025, NULL),
(11, '3А', 2025, NULL),
(12, '3Б', 2025, NULL),
(13, '4А', 2025, NULL),
(14, '4Б', 2025, NULL),
(15, '5А', 2025, NULL),
(16, '5Б', 2025, NULL),
(17, '6А', 2025, NULL),
(18, '6Б', 2025, NULL),
(19, '7А', 2025, NULL),
(20, '7Б', 2025, NULL),
(21, '8Б', 2025, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `final_grades`
--

CREATE TABLE `final_grades` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `term_id` int NOT NULL,
  `grade` decimal(3,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `grades`
--

CREATE TABLE `grades` (
  `id` int NOT NULL,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `grade` tinyint NOT NULL,
  `date` date NOT NULL,
  `comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'current' COMMENT 'current, test, exam, homework',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `teacher_id`, `grade`, `date`, `comment`, `grade_type`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 5, '2024-11-06', 'Отличная работа', 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(2, 1, 1, 1, 4, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(3, 1, 1, 1, 5, '2024-11-11', NULL, 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(4, 1, 1, 1, 4, '2024-11-13', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(5, 1, 1, 1, 5, '2024-11-15', 'Контрольная', 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(6, 2, 1, 1, 4, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(7, 2, 1, 1, 4, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(8, 2, 1, 1, 3, '2024-11-11', 'Ошибки в вычислениях', 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(9, 2, 1, 1, 4, '2024-11-13', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(10, 2, 1, 1, 5, '2024-11-15', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(11, 3, 1, 1, 3, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(12, 3, 1, 1, 3, '2024-11-08', 'Нужно подтянуть', 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(13, 3, 1, 1, 4, '2024-11-11', NULL, 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(14, 3, 1, 1, 3, '2024-11-13', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(15, 3, 1, 1, 3, '2024-11-15', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(16, 4, 1, 1, 5, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(17, 4, 1, 1, 5, '2024-11-08', 'Превосходно', 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(18, 4, 1, 1, 5, '2024-11-11', NULL, 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(19, 4, 1, 1, 4, '2024-11-13', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(20, 4, 1, 1, 5, '2024-11-15', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(21, 5, 1, 1, 2, '2024-11-06', 'Не выполнил задание', 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(22, 5, 1, 1, 3, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(23, 5, 1, 1, 3, '2024-11-11', NULL, 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(24, 5, 1, 1, 2, '2024-11-13', 'Неудовлетворительно', 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(25, 5, 1, 1, 3, '2024-11-15', NULL, 'test', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(26, 1, 2, 2, 4, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(27, 1, 2, 2, 3, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(28, 1, 2, 2, 4, '2024-11-12', NULL, 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(29, 2, 2, 2, 5, '2024-11-06', 'Отлично!', 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(30, 2, 2, 2, 5, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(31, 2, 2, 2, 4, '2024-11-12', NULL, 'homework', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(32, 3, 2, 2, 3, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(33, 3, 2, 2, 4, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(34, 4, 2, 2, 5, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(35, 4, 2, 2, 5, '2024-11-08', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(36, 5, 2, 2, 3, '2024-11-06', NULL, 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(37, 5, 2, 2, 2, '2024-11-08', 'Много ошибок', 'current', '2026-03-01 11:20:04', '2026-03-01 11:20:04'),
(38, 5, 9, 1, 5, '2026-03-01', '', 'test', '2026-03-01 12:16:31', '2026-03-01 12:16:31'),
(39, 6, 3, 2, 4, '2026-03-01', 'Молодец', 'test', '2026-03-01 12:31:37', '2026-03-01 12:31:37'),
(40, 7, 3, 2, 5, '2026-03-01', '', 'current', '2026-03-01 12:32:43', '2026-03-01 12:32:43'),
(41, 7, 3, 2, 2, '2026-03-17', 'Поведение', 'current', '2026-03-01 12:33:08', '2026-03-01 12:33:08'),
(43, 6, 3, 2, 5, '2026-03-01', 'молодец', 'homework', '2026-03-01 12:33:45', '2026-03-01 12:33:45'),
(44, 2, 4, 3, 5, '2026-03-02', '', 'test', '2026-03-02 05:57:03', '2026-03-02 05:57:03'),
(45, 3, 4, 3, 4, '2026-03-02', '', 'homework', '2026-03-02 05:57:08', '2026-03-02 05:57:08'),
(46, 4, 4, 3, 4, '2026-03-02', '', 'test', '2026-03-02 05:57:14', '2026-03-02 05:57:14'),
(47, 1, 4, 3, 2, '2026-03-02', '', 'test', '2026-03-02 05:57:20', '2026-03-02 05:57:20'),
(48, 2, 5, 3, 5, '2026-03-02', '', 'current', '2026-03-02 05:57:38', '2026-03-02 05:57:38'),
(49, 3, 5, 3, 3, '2026-03-02', '', 'test', '2026-03-02 05:57:43', '2026-03-02 05:57:43'),
(50, 4, 5, 3, 4, '2026-03-02', '', 'test', '2026-03-02 05:57:49', '2026-03-02 05:57:49'),
(51, 1, 5, 3, 4, '2026-03-02', '', 'test', '2026-03-02 05:57:54', '2026-03-02 05:57:54'),
(52, 2, 2, 2, 4, '2026-03-02', '', 'homework', '2026-03-02 12:56:37', '2026-03-02 12:56:37'),
(53, 3, 2, 2, 5, '2026-03-02', '', 'current', '2026-03-02 12:56:41', '2026-03-02 12:56:41'),
(54, 4, 2, 2, 3, '2026-03-02', '', 'current', '2026-03-02 12:56:44', '2026-03-02 12:56:44'),
(55, 4, 2, 2, 3, '2026-03-02', '', 'current', '2026-03-02 12:56:44', '2026-03-02 12:56:44'),
(56, 1, 2, 2, 5, '2026-03-02', '', 'current', '2026-03-02 12:56:49', '2026-03-02 12:56:49'),
(57, 2, 2, 2, 5, '2026-03-01', '', 'current', '2026-03-02 12:57:05', '2026-03-02 12:57:05'),
(58, 1, 2, 2, 3, '2026-02-27', '', 'current', '2026-03-02 12:57:15', '2026-03-02 12:57:15'),
(59, 1, 2, 2, 4, '2026-02-26', '', 'current', '2026-03-02 12:57:25', '2026-03-02 12:57:25'),
(60, 4, 2, 2, 5, '2026-03-03', '', 'current', '2026-03-02 12:57:36', '2026-03-02 12:57:36'),
(61, 2, 5, 3, 4, '2026-02-25', '', 'homework', '2026-03-02 13:34:05', '2026-03-02 13:34:05');

-- --------------------------------------------------------

--
-- Структура таблицы `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `subject`, `message`, `is_read`, `read_at`, `created_at`) VALUES
(1, 4, 18, 'Родительское собрание', 'Уважаемая Ольга Николаевна! Приглашаем вас на родительское собрание, которое состоится 20 ноября в 18:00 в кабинете 301. С уважением, Михайлова И.А.', 0, NULL, '2024-11-10 07:00:00'),
(2, 4, 19, 'Родительское собрание', 'Уважаемый Сергей Петрович! Приглашаем вас на родительское собрание, которое состоится 20 ноября в 18:00 в кабинете 301. С уважением, Михайлова И.А.', 1, NULL, '2024-11-10 07:01:00'),
(3, 5, 18, 'Успеваемость по русскому языку', 'Уважаемая Ольга Николаевна! Хочу обратить ваше внимание на то, что Алексей немного снизил успеваемость по русскому языку. Рекомендую дополнительные занятия. С уважением, Петрова А.М.', 0, NULL, '2024-11-12 11:30:00'),
(4, 18, 4, 'Re: Родительское собрание', 'Ирина Александровна, спасибо за приглашение! Обязательно буду. С уважением, О.Н. Смирнова', 0, NULL, '2024-11-10 15:00:00'),
(5, 2, 5, 'Методический совет', 'Анна Михайловна, напоминаю о заседании методического совета в пятницу в 15:00. Просьба подготовить отчёт по успеваемости.', 1, NULL, '2024-11-09 06:00:00'),
(7, 1, 2, 'тест', 'тест', 1, '2026-03-01 18:31:55', '2026-03-01 17:50:42'),
(8, 1, 3, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(9, 1, 4, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(10, 1, 5, 'тест', 'тест', 1, '2026-03-03 17:38:56', '2026-03-01 17:50:42'),
(11, 1, 6, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(12, 1, 7, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(13, 1, 8, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(14, 1, 9, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(15, 1, 10, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(16, 1, 11, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(17, 1, 12, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(18, 1, 13, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(19, 1, 14, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(20, 1, 15, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(21, 1, 16, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(22, 1, 17, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(23, 1, 18, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(24, 1, 19, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(25, 1, 20, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(26, 1, 21, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(27, 1, 22, 'тест', 'тест', 0, NULL, '2026-03-01 17:50:42'),
(29, 5, 2, 'Re: Методический совет', 'Хорошо! Отчет уже готов.', 1, '2026-03-04 15:33:54', '2026-03-04 15:32:57');

-- --------------------------------------------------------

--
-- Структура таблицы `parent_student`
--

CREATE TABLE `parent_student` (
  `id` int NOT NULL,
  `parent_user_id` int NOT NULL,
  `student_id` int NOT NULL,
  `relationship` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Отец, Мать, Опекун и т.д.',
  `is_primary` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `parent_student`
--

INSERT INTO `parent_student` (`id`, `parent_user_id`, `student_id`, `relationship`, `is_primary`) VALUES
(1, 18, 1, 'Мать', 1),
(2, 19, 2, 'Отец', 1),
(3, 20, 3, 'Мать', 1),
(4, 21, 4, 'Отец', 1),
(5, 22, 5, 'Мать', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'JSON с параметрами отчёта',
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`) VALUES
(1, 'admin', 'Администратор'),
(2, 'director', 'Директор'),
(3, 'head_teacher', 'Завуч'),
(4, 'class_teacher', 'Классный руководитель'),
(5, 'teacher', 'Учитель'),
(6, 'student', 'Ученик'),
(7, 'parent', 'Родитель');

-- --------------------------------------------------------

--
-- Структура таблицы `schedule`
--

CREATE TABLE `schedule` (
  `id` int NOT NULL,
  `class_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `day_of_week` tinyint NOT NULL COMMENT '1=Пн, 2=Вт, 3=Ср, 4=Чт, 5=Пт, 6=Сб',
  `lesson_order` tinyint NOT NULL COMMENT 'Номер урока 1-10',
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `room` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `schedule`
--

INSERT INTO `schedule` (`id`, `class_id`, `subject_id`, `teacher_id`, `day_of_week`, `lesson_order`, `time_start`, `time_end`, `room`) VALUES
(1, 1, 1, 1, 1, 1, '08:30:00', '09:15:00', '301'),
(2, 1, 2, 2, 1, 2, '09:25:00', '10:10:00', '205'),
(3, 1, 4, 3, 1, 3, '10:25:00', '11:10:00', '312'),
(4, 1, 7, 4, 1, 4, '11:25:00', '12:10:00', '210'),
(5, 1, 9, 1, 1, 5, '12:20:00', '13:05:00', '401'),
(6, 1, 1, 1, 2, 1, '08:30:00', '09:15:00', '301'),
(7, 1, 3, 2, 2, 2, '09:25:00', '10:10:00', '205'),
(8, 1, 5, 3, 2, 3, '10:25:00', '11:10:00', '314'),
(9, 1, 11, 4, 2, 4, '11:25:00', '12:10:00', '210'),
(10, 1, 2, 2, 3, 1, '08:30:00', '09:15:00', '205'),
(11, 1, 1, 1, 3, 2, '09:25:00', '10:10:00', '301'),
(12, 1, 4, 3, 3, 3, '10:25:00', '11:10:00', '312'),
(13, 1, 7, 4, 3, 4, '11:25:00', '12:10:00', '210'),
(14, 1, 1, 1, 4, 1, '08:30:00', '09:15:00', '301'),
(15, 1, 3, 2, 4, 2, '09:25:00', '10:10:00', '205'),
(16, 1, 5, 3, 4, 3, '10:25:00', '11:10:00', '314'),
(17, 1, 9, 1, 4, 4, '11:25:00', '12:10:00', '401'),
(18, 1, 2, 2, 5, 1, '08:30:00', '09:15:00', '205'),
(19, 1, 1, 1, 5, 2, '09:25:00', '10:10:00', '301'),
(20, 1, 4, 3, 5, 3, '10:25:00', '11:10:00', '312'),
(21, 1, 11, 4, 5, 4, '11:25:00', '12:10:00', '210'),
(22, 2, 2, 2, 1, 1, '08:30:00', '09:15:00', '206'),
(23, 2, 1, 1, 1, 2, '09:25:00', '10:10:00', '302'),
(24, 2, 7, 4, 1, 3, '10:25:00', '11:10:00', '211'),
(25, 2, 3, 2, 1, 4, '11:25:00', '12:10:00', '206'),
(26, 2, 1, 1, 2, 1, '08:30:00', '09:15:00', '302'),
(27, 2, 2, 2, 2, 2, '09:25:00', '10:10:00', '206'),
(28, 2, 7, 4, 2, 3, '10:25:00', '11:10:00', '211'),
(29, 1, 8, 3, 6, 1, '08:30:00', '09:15:00', '312'),
(30, 7, 4, 3, 1, 2, '09:25:00', '10:10:00', ''),
(31, 7, 7, 37, 3, 1, '08:30:00', '09:15:00', '312'),
(32, 18, 11, 37, 5, 3, '10:25:00', '11:10:00', ''),
(33, 18, 12, 36, 2, 4, '11:25:00', '12:10:00', ''),
(34, 18, 9, 38, 1, 2, '09:25:00', '10:10:00', ''),
(35, 8, 1, 1, 1, 1, '08:30:00', '09:15:00', '301'),
(36, 8, 2, 2, 1, 2, '09:25:00', '10:10:00', '205'),
(37, 8, 4, 3, 1, 3, '10:25:00', '11:10:00', '312'),
(38, 8, 7, 4, 1, 4, '11:25:00', '12:10:00', '210'),
(39, 8, 9, 1, 1, 5, '12:20:00', '13:05:00', '401'),
(40, 8, 1, 1, 2, 1, '08:30:00', '09:15:00', '301'),
(41, 8, 3, 2, 2, 2, '09:25:00', '10:10:00', '205'),
(42, 8, 5, 3, 2, 3, '10:25:00', '11:10:00', '314'),
(43, 8, 11, 4, 2, 4, '11:25:00', '12:10:00', '210'),
(44, 8, 2, 2, 3, 1, '08:30:00', '09:15:00', '205'),
(45, 8, 1, 1, 3, 2, '09:25:00', '10:10:00', '301'),
(46, 8, 4, 3, 3, 3, '10:25:00', '11:10:00', '312'),
(47, 8, 7, 4, 3, 4, '11:25:00', '12:10:00', '210'),
(48, 8, 1, 1, 4, 1, '08:30:00', '09:15:00', '301'),
(49, 8, 3, 2, 4, 2, '09:25:00', '10:10:00', '205'),
(50, 8, 5, 3, 4, 3, '10:25:00', '11:10:00', '314'),
(51, 8, 9, 1, 4, 4, '11:25:00', '12:10:00', '401'),
(52, 8, 2, 2, 5, 1, '08:30:00', '09:15:00', '205'),
(53, 8, 1, 1, 5, 2, '09:25:00', '10:10:00', '301'),
(54, 8, 4, 3, 5, 3, '10:25:00', '11:10:00', '312'),
(55, 8, 11, 4, 5, 4, '11:25:00', '12:10:00', '210'),
(56, 8, 8, 3, 6, 1, '08:30:00', '09:15:00', '312');

-- --------------------------------------------------------

--
-- Структура таблицы `students`
--

CREATE TABLE `students` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `class_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `students`
--

INSERT INTO `students` (`id`, `user_id`, `class_id`) VALUES
(1, 8, 1),
(2, 9, 1),
(3, 10, 1),
(4, 11, 1),
(5, 12, 2),
(6, 13, 2),
(7, 14, 2),
(8, 15, 3),
(9, 16, 3),
(10, 17, 3),
(12, 64, 7);

-- --------------------------------------------------------

--
-- Структура таблицы `subjects`
--

CREATE TABLE `subjects` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `subjects`
--

INSERT INTO `subjects` (`id`, `name`) VALUES
(8, 'Английский язык'),
(6, 'Биология'),
(10, 'География'),
(9, 'Информатика'),
(7, 'История'),
(3, 'Литература'),
(1, 'Математика'),
(11, 'Обществознание'),
(2, 'Русский язык'),
(4, 'Физика'),
(12, 'Физкультура'),
(5, 'Химия');

-- --------------------------------------------------------

--
-- Структура таблицы `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `is_class_teacher` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `is_class_teacher`) VALUES
(1, 4, 1),
(2, 5, 0),
(3, 6, 1),
(4, 7, 0),
(36, 65, 0),
(37, 66, 0),
(38, 67, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `teacher_class_subjects`
--

CREATE TABLE `teacher_class_subjects` (
  `id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `class_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `teacher_class_subjects`
--

INSERT INTO `teacher_class_subjects` (`id`, `teacher_id`, `subject_id`, `class_id`) VALUES
(23, 1, 1, 1),
(24, 1, 1, 2),
(19, 1, 1, 3),
(20, 1, 1, 4),
(21, 1, 1, 5),
(22, 1, 1, 6),
(18, 1, 9, 1),
(17, 1, 9, 6),
(4, 2, 2, 1),
(5, 2, 2, 2),
(6, 2, 3, 1),
(7, 2, 3, 2),
(8, 3, 4, 1),
(9, 3, 4, 3),
(10, 3, 5, 1),
(11, 3, 5, 3),
(12, 4, 7, 1),
(13, 4, 7, 2),
(14, 4, 7, 3),
(15, 4, 11, 1),
(16, 4, 11, 3),
(104, 36, 12, 1),
(105, 36, 12, 2),
(106, 36, 12, 3),
(107, 36, 12, 4),
(108, 36, 12, 5),
(102, 36, 12, 6),
(88, 36, 12, 7),
(89, 36, 12, 8),
(90, 36, 12, 9),
(91, 36, 12, 10),
(92, 36, 12, 11),
(93, 36, 12, 12),
(94, 36, 12, 13),
(95, 36, 12, 14),
(96, 36, 12, 15),
(97, 36, 12, 16),
(98, 36, 12, 17),
(99, 36, 12, 18),
(100, 36, 12, 19),
(101, 36, 12, 20),
(103, 36, 12, 21),
(146, 37, 7, 1),
(147, 37, 7, 2),
(148, 37, 7, 3),
(149, 37, 7, 4),
(150, 37, 7, 5),
(144, 37, 7, 6),
(130, 37, 7, 7),
(131, 37, 7, 8),
(132, 37, 7, 9),
(133, 37, 7, 10),
(134, 37, 7, 11),
(135, 37, 7, 12),
(136, 37, 7, 13),
(137, 37, 7, 14),
(138, 37, 7, 15),
(139, 37, 7, 16),
(140, 37, 7, 17),
(141, 37, 7, 18),
(142, 37, 7, 19),
(143, 37, 7, 20),
(145, 37, 7, 21),
(167, 37, 11, 1),
(168, 37, 11, 2),
(169, 37, 11, 3),
(170, 37, 11, 4),
(171, 37, 11, 5),
(165, 37, 11, 6),
(151, 37, 11, 7),
(152, 37, 11, 8),
(153, 37, 11, 9),
(154, 37, 11, 10),
(155, 37, 11, 11),
(156, 37, 11, 12),
(157, 37, 11, 13),
(158, 37, 11, 14),
(159, 37, 11, 15),
(160, 37, 11, 16),
(161, 37, 11, 17),
(162, 37, 11, 18),
(163, 37, 11, 19),
(164, 37, 11, 20),
(166, 37, 11, 21),
(125, 38, 9, 1),
(126, 38, 9, 2),
(127, 38, 9, 3),
(128, 38, 9, 4),
(129, 38, 9, 5),
(123, 38, 9, 6),
(109, 38, 9, 7),
(110, 38, 9, 8),
(111, 38, 9, 9),
(112, 38, 9, 10),
(113, 38, 9, 11),
(114, 38, 9, 12),
(115, 38, 9, 13),
(116, 38, 9, 14),
(117, 38, 9, 15),
(118, 38, 9, 16),
(119, 38, 9, 17),
(120, 38, 9, 18),
(121, 38, 9, 19),
(122, 38, 9, 20),
(124, 38, 9, 21);

-- --------------------------------------------------------

--
-- Структура таблицы `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `subject_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`) VALUES
(10, 1, 1),
(9, 1, 9),
(3, 2, 2),
(4, 2, 3),
(5, 3, 4),
(6, 3, 5),
(7, 4, 7),
(8, 4, 11),
(11, 36, 12),
(13, 37, 7),
(14, 37, 11),
(12, 38, 9);

-- --------------------------------------------------------

--
-- Структура таблицы `teacher_workload`
--

CREATE TABLE `teacher_workload` (
  `id` int NOT NULL,
  `teacher_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `class_id` int NOT NULL,
  `hours_per_week` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `teacher_workload`
--

INSERT INTO `teacher_workload` (`id`, `teacher_id`, `subject_id`, `class_id`, `hours_per_week`) VALUES
(1, 1, 1, 1, 5),
(2, 1, 1, 2, 5),
(3, 1, 9, 1, 2),
(4, 2, 2, 1, 4),
(5, 2, 2, 2, 4),
(6, 2, 3, 1, 3),
(7, 2, 3, 2, 3),
(8, 3, 4, 1, 3),
(9, 3, 4, 3, 3),
(10, 3, 5, 1, 2),
(11, 3, 5, 3, 2),
(12, 4, 7, 1, 2),
(13, 4, 7, 2, 2),
(14, 4, 7, 3, 2),
(15, 4, 11, 1, 1),
(16, 4, 11, 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `terms`
--

CREATE TABLE `terms` (
  `id` int NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `year` int NOT NULL,
  `is_current` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `terms`
--

INSERT INTO `terms` (`id`, `name`, `start_date`, `end_date`, `year`, `is_current`) VALUES
(1, 'I четверть', '2024-09-01', '2024-10-27', 2024, 0),
(2, 'II четверть', '2024-11-06', '2024-12-29', 2024, 1),
(3, 'III четверть', '2025-01-09', '2025-03-23', 2024, 0),
(4, 'IV четверть', '2025-04-02', '2025-05-25', 2024, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` int NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password_hash`, `full_name`, `email`, `phone`, `role_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Администратор Системы', 'admin@school.ru', '+7(999)000-00-01', 1, 1, '2026-03-22 17:02:11', '2026-03-01 11:20:04', '2026-03-22 17:02:11'),
(2, 'director', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Соколов Виктор Иванович', 'director@school.ru', '+7(999)000-00-02', 2, 1, '2026-03-22 16:56:00', '2026-03-01 11:20:04', '2026-03-22 16:56:00'),
(3, 'headteacher', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Васильева Елена Петровна', 'headteacher@school.ru', '+7(999)000-00-03', 3, 1, '2026-03-22 13:20:11', '2026-03-01 11:20:04', '2026-03-22 13:20:11'),
(4, 'classteacher', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Михайлова Ирина Александровна', 'classteacher@school.ru', '+7(999)000-00-04', 4, 1, '2026-03-22 16:56:08', '2026-03-01 11:20:04', '2026-03-22 16:56:08'),
(5, 'teacher1', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Петрова Анна Михайловна', 'petrova@school.ru', '+7(999)000-00-05', 5, 1, '2026-03-22 16:55:31', '2026-03-01 11:20:04', '2026-03-22 16:55:31'),
(6, 'teacher2', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Козлов Дмитрий Сергеевич', 'kozlov@school.ru', '+7(999)000-00-06', 4, 1, '2026-03-02 13:33:42', '2026-03-01 11:20:04', '2026-03-03 17:35:13'),
(7, 'teacher3', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Новикова Светлана Васильевна', 'novikova@school.ru', '+7(999)000-00-07', 5, 1, '2026-03-02 06:01:18', '2026-03-01 11:20:04', '2026-03-02 06:01:18'),
(8, 'student1', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Смирнов Алексей Дмитриевич', 'smirnov@school.ru', '', 6, 1, '2026-03-22 13:19:33', '2026-03-01 11:20:04', '2026-03-22 16:20:45'),
(9, 'student2', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Иванова Мария Сергеевна', 'ivanova@school.ru', NULL, 6, 1, '2026-03-22 13:19:49', '2026-03-01 11:20:04', '2026-03-22 13:19:49'),
(10, 'student3', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Кузнецов Павел Андреевич', 'kuznetsov@school.ru', NULL, 6, 1, '2026-03-22 13:19:55', '2026-03-01 11:20:04', '2026-03-22 13:19:55'),
(11, 'student4', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Попова Екатерина Олеговна', 'popova@school.ru', NULL, 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(12, 'student5', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Волков Артём Игоревич', 'volkov@school.ru', '', 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 17:55:50'),
(13, 'student6', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Лебедева Дарья Владимировна', 'lebedeva@school.ru', '', 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-22 13:08:19'),
(14, 'student7', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Морозов Никита Алексеевич', 'morozov@school.ru', NULL, 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(15, 'student8', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Соколова Анастасия Петровна', 'sokolova_s@school.ru', NULL, 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(16, 'student9', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Федоров Илья Константинович', 'fedorov@school.ru', NULL, 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(17, 'student10', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Николаева Виктория Денисовна', 'nikolaeva@school.ru', NULL, 6, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(18, 'parent1', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Смирнова Ольга Николаевна', 'smirnova@mail.ru', '+7(999)100-00-01', 7, 1, '2026-03-04 15:37:26', '2026-03-01 11:20:04', '2026-03-04 15:37:26'),
(19, 'parent2', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Иванов Сергей Петрович', 'ivanov_s@mail.ru', '+7(999)100-00-02', 7, 1, '2026-03-02 06:00:37', '2026-03-01 11:20:04', '2026-03-02 06:00:37'),
(20, 'parent3', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Кузнецова Елена Викторовна', 'kuznetsova_e@mail.ru', '+7(999)100-00-03', 7, 1, '2026-03-01 12:28:10', '2026-03-01 11:20:04', '2026-03-01 12:28:10'),
(21, 'parent4', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Попов Олег Иванович', 'popov_o@mail.ru', '+7(999)100-00-04', 7, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(22, 'parent5', '$2y$10$YGVGVJ5CFLFvl6XaoCIQ5uyF5NEKI3jkxKlLej/QQI2tJBsMdObH2', 'Волкова Татьяна Сергеевна', 'volkova_t@mail.ru', '+7(999)100-00-05', 7, 1, NULL, '2026-03-01 11:20:04', '2026-03-01 12:10:38'),
(64, 'ivanov', '$2y$10$YdZdF/GfEZ9fw78FlfNvMeEsNM6eMZNQq6vr5yDm9QUX.AiHY87kW', 'Иванов Иван Иванович', '', '', 6, 1, NULL, '2026-03-01 18:11:40', '2026-03-01 18:11:40'),
(65, 'teacher4', '$2y$10$S2iERx7hkVhuqrEcrsdnXeGNpvSMe8r9cowgX5b.JGtonRY.uuj0K', 'Минтимер Шарипович Шаймиев', '', '', 5, 1, NULL, '2026-03-01 18:15:15', '2026-03-01 18:15:15'),
(66, 'putin', '$2y$10$FiRrPCt5i64hmgljKCRYEOElAyldSmzy7QNIiNwFudk4u2pvQffii', 'Владимир Владимирович Путин', '', '', 5, 1, '2026-03-02 05:55:14', '2026-03-01 18:15:31', '2026-03-02 05:55:14'),
(67, 'kolbasenko', '$2y$10$kGIRUg5ZVHMNK1gCQ6Iugue0IAOWljvQB3pBoJ1cvLCT0rowsKVcS', 'Колбасенко Данил Стандофович', '', '', 5, 0, NULL, '2026-03-01 18:16:12', '2026-03-01 18:38:51');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`subject_id`,`date`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `marked_by` (`marked_by`);

--
-- Индексы таблицы `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class` (`name`,`year`),
  ADD KEY `class_teacher_id` (`class_teacher_id`);

--
-- Индексы таблицы `final_grades`
--
ALTER TABLE `final_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_final` (`student_id`,`subject_id`,`term_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `term_id` (`term_id`);

--
-- Индексы таблицы `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `idx_grades_date` (`date`),
  ADD KEY `idx_grades_student_subject` (`student_id`,`subject_id`);

--
-- Индексы таблицы `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attempts_ip` (`ip_address`,`attempted_at`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_receiver` (`receiver_id`,`is_read`),
  ADD KEY `idx_messages_sender` (`sender_id`);

--
-- Индексы таблицы `parent_student`
--
ALTER TABLE `parent_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_parent_student` (`parent_user_id`,`student_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Индексы таблицы `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Индексы таблицы `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_class_lesson` (`class_id`,`day_of_week`,`lesson_order`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `idx_schedule_teacher` (`teacher_id`,`day_of_week`);

--
-- Индексы таблицы `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Индексы таблицы `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Индексы таблицы `teacher_class_subjects`
--
ALTER TABLE `teacher_class_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_class_subject` (`teacher_id`,`subject_id`,`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Индексы таблицы `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_teacher_subject` (`teacher_id`,`subject_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Индексы таблицы `teacher_workload`
--
ALTER TABLE `teacher_workload`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_workload` (`teacher_id`,`subject_id`,`class_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Индексы таблицы `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT для таблицы `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `final_grades`
--
ALTER TABLE `final_grades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT для таблицы `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT для таблицы `parent_student`
--
ALTER TABLE `parent_student`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT для таблицы `students`
--
ALTER TABLE `students`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT для таблицы `teacher_class_subjects`
--
ALTER TABLE `teacher_class_subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT для таблицы `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `teacher_workload`
--
ALTER TABLE `teacher_workload`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`marked_by`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`class_teacher_id`) REFERENCES `teachers` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `final_grades`
--
ALTER TABLE `final_grades`
  ADD CONSTRAINT `final_grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `final_grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `final_grades_ibfk_3` FOREIGN KEY (`term_id`) REFERENCES `terms` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `parent_student`
--
ALTER TABLE `parent_student`
  ADD CONSTRAINT `parent_student_ibfk_1` FOREIGN KEY (`parent_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parent_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE RESTRICT;

--
-- Ограничения внешнего ключа таблицы `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `teacher_class_subjects`
--
ALTER TABLE `teacher_class_subjects`
  ADD CONSTRAINT `teacher_class_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_class_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_class_subjects_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `teacher_workload`
--
ALTER TABLE `teacher_workload`
  ADD CONSTRAINT `teacher_workload_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_workload_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_workload_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
