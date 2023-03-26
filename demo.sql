-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: 10.0.0.135:3306
-- Время создания: Мар 26 2023 г., 21:14
-- Версия сервера: 10.3.27-MariaDB-log
-- Версия PHP: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `zamotaev_demo`
--

-- --------------------------------------------------------

--
-- Структура таблицы `lift_calls`
--

CREATE TABLE `lift_calls` (
  `call_id` int(11) NOT NULL,
  `call_first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `call_last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `call_phone` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `call_email` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `call_department` int(11) NOT NULL DEFAULT 0,
  `call_request` int(11) NOT NULL DEFAULT 0,
  `call_group` int(11) NOT NULL DEFAULT 0,
  `call_adres` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address_city` int(3) NOT NULL DEFAULT 0,
  `address_street` int(11) NOT NULL DEFAULT 0,
  `address_home` int(11) NOT NULL DEFAULT 0,
  `address_lift` int(11) NOT NULL DEFAULT 0,
  `call_details` text COLLATE utf8_unicode_ci NOT NULL,
  `call_date` int(11) NOT NULL DEFAULT 0,
  `call_date2` int(11) NOT NULL DEFAULT 0,
  `call_status` int(11) NOT NULL DEFAULT 0,
  `call_solution` text COLLATE utf8_unicode_ci NOT NULL,
  `call_user` int(11) NOT NULL DEFAULT 0,
  `call_staff` int(11) NOT NULL DEFAULT 0,
  `call_staff_status` int(11) DEFAULT NULL,
  `call_staff_date` int(11) DEFAULT NULL,
  `expected_repair_time` int(11) DEFAULT 0,
  `read_md5` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `call_fullhistory` text COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'хранит всю историю изменений по заявке после ее закрытия'
) ENGINE=InnoDB AVG_ROW_LENGTH=2340 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `lift_calls`
--

INSERT INTO `lift_calls` (`call_id`, `call_first_name`, `call_last_name`, `call_phone`, `call_email`, `call_department`, `call_request`, `call_group`, `call_adres`, `address_city`, `address_street`, `address_home`, `address_lift`, `call_details`, `call_date`, `call_date2`, `call_status`, `call_solution`, `call_user`, `call_staff`, `call_staff_status`, `call_staff_date`, `expected_repair_time`, `read_md5`, `call_fullhistory`) VALUES
(1, 'Диспетчер1', '', '', '', 22, 2, 6, 'г. Ростов-на-Дону - Комарова бул-р. дом № 1 - п. 4', 1, 4, 1, 4, 'Стоит на 1 этаже на кнопки не реагирует', 1679779802, 0, 0, ' ', 0, 3, 0, 0, 1679816402, 'cda26351a41182e978dc5dc4600eeb72', ''),
(2, 'Диспетчер1', '', '', '', 22, 2, 7, 'г. Ростов-на-Дону - Красноармейская ул. дом № 2 корп.1 - п. 1 груз.', 1, 2, 2, 7, 'Требуется замена поста приказов', 1679780102, 0, 0, ' ', 0, 5, 1, 1679780392, 1679816402, '231d4bd27d5a28dde9000488494183bc', ''),
(3, 'Диспетчер1', '', '', '', 23, 1, 4, 'г. Ростов-на-Дону - Нансена ул. дом № 989 - п. 3 прав.', 1, 6, 5, 21, 'Застряли люди', 1679779802, 0, 0, ' ', 0, 1, 2, 1679780102, 1679816402, '9f058ad1ff7778634e7379c689df2b0a', '');

-- --------------------------------------------------------

--
-- Структура таблицы `lift_city`
--

CREATE TABLE `lift_city` (
  `id` int(11) NOT NULL,
  `city_name` varchar(256) NOT NULL,
  `vis_city` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_city`
--

INSERT INTO `lift_city` (`id`, `city_name`, `vis_city`) VALUES
(1, 'г. Ростов-на-Дону', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_history`
--

CREATE TABLE `lift_history` (
  `history_id` int(11) NOT NULL,
  `history_date` int(11) DEFAULT NULL,
  `history_info` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `call_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `lift_history`
--

INSERT INTO `lift_history` (`history_id`, `history_date`, `history_info`, `call_id`) VALUES
(1, 1673726286, 'Диспетчер1 - внес(ла) следующие изменения: Назначен ответственный: Иванов Иван . ', 1),
(2, 1673726381, 'Диспетчер1 - внес(ла) следующие изменения: Предполагаемое время ремонта - 14.02.2023 . Ответственный уведомлен по телефону.  Назначен ответственный: Петров Петр . ', 2),
(3, 1673726466, 'Диспетчер1 - внес(ла) следующие изменения: Назначен ответственный: Замотаев Анатолий . ', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_home`
--

CREATE TABLE `lift_home` (
  `id` int(11) NOT NULL,
  `home_name` varchar(64) DEFAULT NULL,
  `street_id` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `vis_home` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_home`
--

INSERT INTO `lift_home` (`id`, `home_name`, `street_id`, `timestamp`, `vis_home`) VALUES
(1, '1', 4, '2023-01-14 16:44:23', 0),
(2, '2 корп.1', 2, '2023-01-14 16:45:06', 0),
(3, '2 корп.2', 2, '2023-01-14 16:45:23', 0),
(4, '3/9', 5, '2023-01-14 16:46:36', 0),
(5, '989', 6, '2023-01-14 16:47:03', 0),
(6, '10/2', 1, '2023-01-14 16:48:36', 0),
(7, '34', 3, '2023-01-14 16:49:50', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_notes`
--

CREATE TABLE `lift_notes` (
  `note_id` int(11) NOT NULL,
  `note_title` varchar(200) NOT NULL DEFAULT '',
  `note_body` text NOT NULL,
  `note_relation` int(11) NOT NULL DEFAULT 0,
  `note_type` int(1) NOT NULL DEFAULT 0,
  `note_post_date` int(11) NOT NULL DEFAULT 0,
  `note_post_ip` varchar(20) NOT NULL DEFAULT '',
  `note_post_user` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_notes`
--

INSERT INTO `lift_notes` (`note_id`, `note_title`, `note_body`, `note_relation`, `note_type`, `note_post_date`, `note_post_ip`, `note_post_user`) VALUES
(1, 'Заметка', 'Требуется замена кнопки реверса дверей', 1, 1, 1679780102, '193.160.205.86', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_object`
--

CREATE TABLE `lift_object` (
  `id` int(11) NOT NULL,
  `object_name` varchar(255) NOT NULL,
  `home_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `vis_object` tinyint(1) NOT NULL DEFAULT 0,
  `abbreviated_name` mediumint(6) NOT NULL DEFAULT 0 COMMENT 'Сокращенное наименование лифта в SPult'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_object`
--

INSERT INTO `lift_object` (`id`, `object_name`, `home_id`, `timestamp`, `vis_object`, `abbreviated_name`) VALUES
(1, 'п. 1', 1, '2023-01-14 16:44:42', 0, 0),
(2, 'п. 2', 1, '2023-01-14 16:44:42', 0, 0),
(3, 'п. 3', 1, '2023-01-14 16:44:42', 0, 0),
(4, 'п. 4', 1, '2023-01-14 16:44:42', 0, 0),
(5, 'п. 5', 1, '2023-01-14 16:44:42', 0, 0),
(6, 'п. 6', 1, '2023-01-14 16:44:42', 0, 0),
(7, 'п. 1 груз.', 2, '2023-01-14 16:45:55', 0, 0),
(8, 'п. 1 пасс.', 2, '2023-01-14 16:45:55', 0, 0),
(9, 'п. 1 лев.', 3, '2023-01-14 16:46:22', 0, 0),
(10, 'п. 1 сред.', 3, '2023-01-14 16:46:22', 0, 0),
(11, 'п. 1 прав.', 3, '2023-01-14 16:46:22', 0, 0),
(12, 'п. 1', 4, '2023-01-14 16:46:48', 0, 0),
(13, 'п. 1 лев.', 5, '2023-01-14 16:48:07', 0, 0),
(14, 'п. 1 сред.', 5, '2023-01-14 16:48:07', 0, 0),
(15, 'п. 1 прав.', 5, '2023-01-14 16:48:07', 0, 0),
(16, 'п. 2 лев.', 5, '2023-01-14 16:48:07', 0, 0),
(17, 'п. 2 сред.', 5, '2023-01-14 16:48:07', 0, 0),
(18, 'п. 2 прав.', 5, '2023-01-14 16:48:07', 0, 0),
(19, 'п. 3 лев.', 5, '2023-01-14 16:48:07', 0, 0),
(20, 'п. 3 сред.', 5, '2023-01-14 16:48:07', 0, 0),
(21, 'п. 3 прав.', 5, '2023-01-14 16:48:07', 0, 0),
(22, 'п. 1', 6, '2023-01-14 16:49:37', 0, 0),
(23, 'п. 2', 6, '2023-01-14 16:49:37', 0, 0),
(24, 'п. 3', 6, '2023-01-14 16:49:37', 0, 0),
(25, 'п. 4', 6, '2023-01-14 16:49:37', 0, 0),
(26, 'п. 1', 7, '2023-01-14 16:50:04', 0, 0),
(27, 'п. 2', 7, '2023-01-14 16:50:04', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_options`
--

CREATE TABLE `lift_options` (
  `id` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL DEFAULT '',
  `option_value` varchar(500) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_options`
--

INSERT INTO `lift_options` (`id`, `option_name`, `option_value`, `timestamp`) VALUES
(1, 'encrypted_passwords', 'yes', '2014-03-16 12:43:19'),
(2, 'newdatacall', 'no', '2021-12-31 15:00:00');

-- --------------------------------------------------------

--
-- Структура таблицы `lift_street`
--

CREATE TABLE `lift_street` (
  `id` int(11) NOT NULL,
  `street_name` varchar(255) NOT NULL DEFAULT '',
  `city_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `vis_street` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_street`
--

INSERT INTO `lift_street` (`id`, `street_name`, `city_id`, `timestamp`, `vis_street`) VALUES
(1, 'Пушкинская ул.', 1, '2023-01-14 16:42:25', 0),
(2, 'Красноармейская ул.', 1, '2023-01-14 16:42:45', 0),
(3, 'Стачки  пр-т', 1, '2023-01-14 16:43:00', 0),
(4, 'Комарова бул-р.', 1, '2023-01-14 16:43:32', 0),
(5, 'Ленина ул.', 1, '2023-01-14 16:43:46', 0),
(6, 'Нансена ул.', 1, '2023-01-14 16:44:12', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_types`
--

CREATE TABLE `lift_types` (
  `type_id` int(11) NOT NULL,
  `type` int(1) NOT NULL DEFAULT 0,
  `type_name` varchar(200) DEFAULT NULL,
  `type_report` tinyint(1) NOT NULL DEFAULT 0,
  `type_email` varchar(200) DEFAULT NULL,
  `type_location` text DEFAULT NULL,
  `type_phone` varchar(100) DEFAULT NULL
) ENGINE=InnoDB AVG_ROW_LENGTH=1170 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_types`
--

INSERT INTO `lift_types` (`type_id`, `type`, `type_name`, `type_report`, `type_email`, `type_location`, `type_phone`) VALUES
(1, 2, '1.Срочная', 0, '', 'bg-danger', ''),
(2, 2, '2.Обычная', 0, '', 'bg-warning', ''),
(3, 2, '3.Не срочная', 0, '', 'bg-secondary', ''),
(4, 3, '1.Застревание', 0, '', '', ''),
(5, 3, '2.Мелкая неисправность', 0, '', '', ''),
(6, 3, '3.Неисправность лифта', 0, '', '', ''),
(7, 3, '4.Дополнительные работы', 0, '', '', ''),
(8, 3, '5.Длительный ремонт', 0, '', '', ''),
(9, 3, '7.Прочее', 0, '', '', ''),
(22, 1, '1.Электромеханики', 1, '', '', ''),
(23, 1, '2.Аварийная служба', 0, '', '', ''),
(24, 3, '6.Неисправность дисп. об.', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_upload`
--

CREATE TABLE `lift_upload` (
  `id` int(11) NOT NULL,
  `call_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL DEFAULT '',
  `file_ext` varchar(4) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `lift_users`
--

CREATE TABLE `lift_users` (
  `user_id` int(11) NOT NULL,
  `user_login` varchar(30) NOT NULL DEFAULT '',
  `user_password` varchar(225) NOT NULL DEFAULT '',
  `user_name` varchar(200) NOT NULL DEFAULT '',
  `user_telegram` bigint(20) NOT NULL DEFAULT 0,
  `user_address` varchar(200) NOT NULL DEFAULT '',
  `user_city` varchar(100) NOT NULL DEFAULT '',
  `user_state` char(3) NOT NULL DEFAULT '',
  `user_zip` varchar(20) NOT NULL DEFAULT '',
  `user_country` char(3) NOT NULL DEFAULT '',
  `user_phone` varchar(39) NOT NULL DEFAULT '',
  `user_email` varchar(200) NOT NULL DEFAULT '',
  `user_email2` varchar(200) NOT NULL DEFAULT '',
  `user_company` varchar(100) NOT NULL DEFAULT '',
  `user_new_read` int(1) UNSIGNED NOT NULL DEFAULT 0,
  `user_im_icq` varchar(100) NOT NULL DEFAULT '',
  `user_im_msn` varchar(100) NOT NULL DEFAULT '',
  `user_im_yahoo` varchar(100) NOT NULL DEFAULT '',
  `user_im_other` varchar(200) NOT NULL DEFAULT '',
  `user_status` int(1) NOT NULL DEFAULT 0,
  `user_level` int(1) NOT NULL DEFAULT 0,
  `user_pending` int(11) NOT NULL DEFAULT 0,
  `user_date` int(11) NOT NULL DEFAULT 0,
  `last_login` int(11) NOT NULL DEFAULT 0,
  `last_ip` varchar(20) NOT NULL DEFAULT '',
  `user_msg_send` int(1) NOT NULL DEFAULT 0,
  `user_msg_subject` varchar(200) NOT NULL DEFAULT '',
  `user_protect_delete` int(1) DEFAULT 0,
  `user_protect_edit` int(11) NOT NULL DEFAULT 0,
  `user_hiden` tinyint(1) DEFAULT 0,
  `user_localadmin` tinyint(1) DEFAULT 0,
  `user_edit_obj` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'разрешение редактирования объектов',
  `user_edit_user` tinyint(1) NOT NULL DEFAULT 0,
  `user_disppermission` tinyint(1) NOT NULL DEFAULT 0,
  `user_add_call` tinyint(1) NOT NULL DEFAULT 0,
  `user_read_all_calls` tinyint(1) NOT NULL DEFAULT 0,
  `user_block` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Пользователь Заблокирован ',
  `user_token` varchar(32) NOT NULL DEFAULT '' COMMENT 'токен автоматического входа в акаунт'
) ENGINE=InnoDB AVG_ROW_LENGTH=2340 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `lift_users`
--

INSERT INTO `lift_users` (`user_id`, `user_login`, `user_password`, `user_name`, `user_telegram`, `user_address`, `user_city`, `user_state`, `user_zip`, `user_country`, `user_phone`, `user_email`, `user_email2`, `user_company`, `user_new_read`, `user_im_icq`, `user_im_msn`, `user_im_yahoo`, `user_im_other`, `user_status`, `user_level`, `user_pending`, `user_date`, `last_login`, `last_ip`, `user_msg_send`, `user_msg_subject`, `user_protect_delete`, `user_protect_edit`, `user_hiden`, `user_localadmin`, `user_edit_obj`, `user_edit_user`, `user_disppermission`, `user_add_call`, `user_read_all_calls`, `user_block`, `user_token`) VALUES
(1, 'admin', '$2a$08$/edNGVivhSJWCv.XdvuuVulHzFh7BbeqSmmwkWexHLUoOgPBrXl9a', 'Замотаев Анатолий', 1401760365, '', '', '', '', 'Рос', '+79185049042', 'zamotaev@list.ru', 'someone@example.com', '', 0, '', '', '', '', 0, 0, 0, 0, 1673725285, '193.160.205.86', 0, 'New Message', 1, 1, 0, 0, 1, 1, 1, 1, 1, 0, ''),
(2, 'disp1', '$2a$08$/edNGVivhSJWCv.XdvuuVulHzFh7BbeqSmmwkWexHLUoOgPBrXl9a', 'Диспетчер1', 0, '', '', '', '', '', '+71234567891', 'disp1@list.ru', '', 'лифтовая 1', 0, '', '', '', '', 1, 3, 0, 0, 1673726113, '193.160.205.86', 0, '', 0, 0, 1, 0, 0, 0, 1, 1, 1, 0, ''),
(3, 'meh1', '$2a$08$/edNGVivhSJWCv.XdvuuVulHzFh7BbeqSmmwkWexHLUoOgPBrXl9a', 'Иванов Иван', 0, '', '', '', '', '', '+71234567890', '', '', '', 0, '', '', '', '', 1, 2, 0, 0, 1673726566, '193.160.205.86', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, ''),
(5, 'meh2', '$2a$08$/edNGVivhSJWCv.XdvuuVulHzFh7BbeqSmmwkWexHLUoOgPBrXl9a', 'Петров Петр', 0, '', '', '', '', '', '+71234567892', '', '', '', 0, '', '', '', '', 1, 2, 0, 0, 1673367757, '192.168.0.100', 0, '', 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, '');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `lift_calls`
--
ALTER TABLE `lift_calls`
  ADD PRIMARY KEY (`call_id`),
  ADD KEY `call_department` (`call_department`),
  ADD KEY `call_request` (`call_request`),
  ADD KEY `call_staff` (`call_staff`),
  ADD KEY `call_status` (`call_status`),
  ADD KEY `call_user` (`call_user`),
  ADD KEY `call_group` (`call_group`) USING BTREE;

--
-- Индексы таблицы `lift_city`
--
ALTER TABLE `lift_city`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Индексы таблицы `lift_history`
--
ALTER TABLE `lift_history`
  ADD PRIMARY KEY (`history_id`) KEY_BLOCK_SIZE=11 USING BTREE,
  ADD KEY `call_id` (`call_id`);
ALTER TABLE `lift_history` ADD FULLTEXT KEY `historytxt` (`history_info`);

--
-- Индексы таблицы `lift_home`
--
ALTER TABLE `lift_home`
  ADD PRIMARY KEY (`id`),
  ADD KEY `street_id` (`street_id`);

--
-- Индексы таблицы `lift_notes`
--
ALTER TABLE `lift_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `note_post_user` (`note_post_user`),
  ADD KEY `note_relation` (`note_relation`),
  ADD KEY `note_type` (`note_type`);

--
-- Индексы таблицы `lift_object`
--
ALTER TABLE `lift_object`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `lift_options`
--
ALTER TABLE `lift_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_name` (`option_name`);

--
-- Индексы таблицы `lift_street`
--
ALTER TABLE `lift_street`
  ADD PRIMARY KEY (`id`),
  ADD KEY `street_name` (`street_name`);

--
-- Индексы таблицы `lift_types`
--
ALTER TABLE `lift_types`
  ADD PRIMARY KEY (`type_id`),
  ADD KEY `type` (`type`);

--
-- Индексы таблицы `lift_upload`
--
ALTER TABLE `lift_upload`
  ADD PRIMARY KEY (`id`),
  ADD KEY `call_id` (`call_id`);

--
-- Индексы таблицы `lift_users`
--
ALTER TABLE `lift_users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_level` (`user_level`),
  ADD KEY `user_msg_send` (`user_msg_send`),
  ADD KEY `user_pending` (`user_pending`),
  ADD KEY `user_protect_edit` (`user_protect_edit`),
  ADD KEY `user_status` (`user_status`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `lift_calls`
--
ALTER TABLE `lift_calls`
  MODIFY `call_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `lift_city`
--
ALTER TABLE `lift_city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `lift_history`
--
ALTER TABLE `lift_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `lift_home`
--
ALTER TABLE `lift_home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `lift_notes`
--
ALTER TABLE `lift_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `lift_object`
--
ALTER TABLE `lift_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `lift_options`
--
ALTER TABLE `lift_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `lift_street`
--
ALTER TABLE `lift_street`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `lift_types`
--
ALTER TABLE `lift_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `lift_upload`
--
ALTER TABLE `lift_upload`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `lift_users`
--
ALTER TABLE `lift_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
