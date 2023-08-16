

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `lift_calls` (
  `call_id` int(11) NOT NULL,
  `call_first_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `call_last_name` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `call_phone` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `call_email` varchar(200) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `call_department` int(11) NOT NULL DEFAULT 0,
  `call_request` int(11) NOT NULL DEFAULT 0,
  `call_group` int(11) NOT NULL DEFAULT 0,
  `call_adres` varchar(100) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `address_city` int(3) NOT NULL DEFAULT 0,
  `address_street` int(11) NOT NULL DEFAULT 0,
  `address_home` int(11) NOT NULL DEFAULT 0,
  `address_lift` int(11) NOT NULL DEFAULT 0,
  `call_details` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `call_date` int(11) NOT NULL DEFAULT 0,
  `call_date2` int(11) NOT NULL DEFAULT 0,
  `call_status` int(11) NOT NULL DEFAULT 0,
  `call_solution` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `call_user` int(11) NOT NULL DEFAULT 0,
  `call_staff` int(11) NOT NULL DEFAULT 0,
  `call_staff_status` int(11) DEFAULT NULL,
  `call_staff_date` int(11) DEFAULT NULL,
  `expected_repair_time` int(11) DEFAULT 0,
  `read_md5` varchar(32) COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `call_fullhistory` text COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '' COMMENT 'хранит всю историю изменений по заявке после ее закрытия',
  `call_full_note_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '{}' COMMENT 'Данные по всем заметкам к заявке' CHECK (json_valid(`call_full_note_history`))
) ENGINE=InnoDB AVG_ROW_LENGTH=2340 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Дамп данных таблицы `lift_calls`
--

INSERT INTO `lift_calls` (`call_id`, `call_first_name`, `call_last_name`, `call_phone`, `call_email`, `call_department`, `call_request`, `call_group`, `call_adres`, `address_city`, `address_street`, `address_home`, `address_lift`, `call_details`, `call_date`, `call_date2`, `call_status`, `call_solution`, `call_user`, `call_staff`, `call_staff_status`, `call_staff_date`, `expected_repair_time`, `read_md5`, `call_fullhistory`, `call_full_note_history`) VALUES
(1, 'Диспетчер1', '', '', '', 22, 2, 6, 'г. Ростов-на-Дону - Комарова бул-р. дом № 1 - п. 4', 1, 4, 1, 4, 'Стоит на 1 этаже на кнопки не реагирует', 1673813387, 0, 0, '  58854884545', 0, 3, 0, 0, 1673849987, 'cda26351a41182e978dc5dc4600eeb72', 'Дата изменений:14.01.2023@22:58 -Диспетчер1 - внес(ла) следующие изменения: Назначен ответственный: Иванов Иван . <br>', '{}');

-- --------------------------------------------------------

--
-- Структура таблицы `lift_city`
--

CREATE TABLE `lift_city` (
  `id` int(11) NOT NULL,
  `city_name` varchar(256) NOT NULL,
  `vis` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_city`
--

INSERT INTO `lift_city` (`id`, `city_name`, `vis`) VALUES
(1, 'г. Название города', 0);


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


-- --------------------------------------------------------

--
-- Структура таблицы `lift_home`
--

CREATE TABLE `lift_home` (
  `id` int(11) NOT NULL,
  `home_name` varchar(64) DEFAULT NULL,
  `street_id` int(11) NOT NULL DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `vis` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_home`
--

INSERT INTO `lift_home` (`id`, `home_name`, `street_id`, `timestamp`, `vis`) VALUES
(1, '1', 1, '2022-08-01 18:03:57', 0);

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
  `note_post_user` int(11) NOT NULL DEFAULT 0,
  `img_name` tinytext NOT NULL DEFAULT '\'\'' COMMENT 'Имя файла изображения в завметке'
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_notes`
--

INSERT INTO `lift_notes` (`note_id`, `note_title`, `note_body`, `note_relation`, `note_type`, `note_post_date`, `note_post_ip`, `note_post_user`, `img_name`) VALUES
(1, 'Заметка', 'Пример:Требуется замена кнопки реверса дверей', 1, 1, 1673813687, '193.160.205.86', 1, '\'\'');

-- --------------------------------------------------------

--
-- Структура таблицы `lift_object`
--

CREATE TABLE `lift_object` (
  `id` int(11) NOT NULL,
  `object_name` varchar(255) NOT NULL,
  `home_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `vis` tinyint(1) NOT NULL DEFAULT 0,
  `abbreviated_name` mediumint(6) NOT NULL DEFAULT 0 COMMENT 'Сокращенное наименование лифта в SPult'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_object`
--

INSERT INTO `lift_object` (`id`, `object_name`, `home_id`, `timestamp`, `vis`, `abbreviated_name`) VALUES
(1, 'п.1 ', 1, '2022-08-26 15:50:36', 0, 0),
(2, 'п.2 ', 1, '2022-08-26 15:50:47', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_options`
--

CREATE TABLE `lift_options` (
  `id` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL DEFAULT '',
  `option_value` varchar(6000) NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(50) NOT NULL DEFAULT '''''',
  `comment` text NOT NULL DEFAULT ' ' COMMENT 'Подробно о параметре',
  `change_allowed` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Разрешено редактирование',
  `type` varchar(50) NOT NULL DEFAULT 'text' COMMENT 'тип значения'
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_options`
--

INSERT INTO `lift_options` (`id`, `option_name`, `option_value`, `timestamp`, `name`, `comment`, `change_allowed`, `type`) VALUES
(1, 'encrypted_passwords', 'yes', '2014-03-16 13:43:19', '\'\'', ' ', 0, ' '),
(2, 'newdatacall', 'no', '2021-12-31 15:00:00', '\'\'', ' ', 0, ' '),
(3, 'telegram_token', '1234567890:AAGY-B0CzWJvgOLC96EkrXYzRaqU0sA9vS4', '2023-05-25 19:53:38', 'Telegram token', ' Телеграм токен', 1, 'textarea'),
(4, 'waiting_time', '600', '2023-05-29 17:37:34', 'Ожидание сообщения', 'Время в секундах ожидания сообщения на команду в телеграм', 1, 'number'),
(5, 'repair_time', '{\n  \"0\": \"не указан\",\n  \"+30 minutes\": \"30 мин\",\n  \"today 23:00\": \"Сегодня\",\n  \"today +1day 23:00\": \"Завтра\",\n  \"today +3day 23:00\": \"Три дня\",\n  \"today+7day\": \"7 дней\",\n  \"today+10day\": \"10 дней\",\n  \"today+15day\": \"15 дней\",\n  \"today+1month\": \"1 месяц\",\n  \"today+3month\": \"3 месяца\"\n}', '2023-06-01 16:05:40', 'срок ремонта', 'Временные метки для сроков ремонта', 0, ' '),
(6, 'min_length_text', '5', '2023-06-02 19:00:53', 'Количество символов', 'Минимальное количество символов для текста в заметка, решении, описании заявки', 1, 'number'),
(7, 'login_tries', '5', '2023-06-02 20:24:53', 'Попыток авторизации', 'максимальное количество попыток авторизации', 1, 'number'),
(8, 'authorizationKey', 'yousecretkey', '2023-06-23 20:02:59', 'ключ авторизации', 'Уникальный ключ для авторизационного токена (любая комбинация символов и чисел) ', 1, ' text');

-- --------------------------------------------------------

--
-- Структура таблицы `lift_street`
--

CREATE TABLE `lift_street` (
  `id` int(11) NOT NULL,
  `street_name` varchar(255) NOT NULL DEFAULT '',
  `city_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `vis` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_street`
--

INSERT INTO `lift_street` (`id`, `street_name`, `city_id`, `timestamp`, `vis`) VALUES
(1, 'Красноармейская ул.', 1, '2022-01-17 15:24:01', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `lift_telegram`
--

CREATE TABLE `lift_telegram` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL COMMENT 'id пользователя',
  `call_id` int(10) UNSIGNED NOT NULL COMMENT 'id заявки',
  `time` int(10) UNSIGNED NOT NULL COMMENT 'unix time stamp',
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'действие на ответное сообщение'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AVG_ROW_LENGTH=1170 DEFAULT CHARSET=utf8mb3;

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
) ENGINE=InnoDB AVG_ROW_LENGTH=16384 DEFAULT CHARSET=utf8mb3;

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
  `user_protect_delete` int(1) DEFAULT 0 COMMENT 'запрет на удаление пользователя',
  `user_protect_edit` int(11) NOT NULL DEFAULT 0 COMMENT 'запрет на редактирования пользователя',
  `user_hiden` tinyint(1) DEFAULT 0,
  `user_localadmin` tinyint(1) DEFAULT 0,
  `user_edit_obj` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'разрешение редактирования объектов',
  `user_edit_user` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'разрешение на изменение пользователей',
  `user_disppermission` tinyint(1) NOT NULL DEFAULT 0,
  `user_add_call` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'разрешено формировать заявки',
  `user_read_all_calls` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'разрешение на чтение всех заявок',
  `user_block` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Пользователь Заблокирован ',
  `user_token` varchar(32) NOT NULL DEFAULT '' COMMENT 'токен автоматического входа в акаунт',
  `webgl_info` tinytext DEFAULT ' ' COMMENT 'Информация о графической подсистемы пользователя для идентификации с токеном'
) ENGINE=InnoDB AVG_ROW_LENGTH=2340 DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `lift_users`
--

INSERT INTO `lift_users` (`user_id`, `user_login`, `user_password`, `user_name`, `user_telegram`, `user_address`, `user_city`, `user_state`, `user_zip`, `user_country`, `user_phone`, `user_email`, `user_email2`, `user_company`, `user_new_read`, `user_im_icq`, `user_im_msn`, `user_im_yahoo`, `user_im_other`, `user_status`, `user_level`, `user_pending`, `user_date`, `last_login`, `last_ip`, `user_msg_send`, `user_msg_subject`, `user_protect_delete`, `user_protect_edit`, `user_hiden`, `user_localadmin`, `user_edit_obj`, `user_edit_user`, `user_disppermission`, `user_add_call`, `user_read_all_calls`, `user_block`, `user_token`, `webgl_info`) VALUES
(1, 'admin', '$2y$10$QO6yYMNijT8XrLzK9u5iLu4dYz5wKwjQYGS7suZBOXMXdML2WKEIy', 'Фамилия Имя', 1401760365, '', '', '', '', 'Рос', '+7912345678', 'zamotaev@list.ru', 'someone@example.com', '', 0, '', '', '', '', 0, 0, 0, 0, 1691614640, '127.0.0.1', 0, 'New Message', 1, 1, 0, 1, 1, 1, 1, 1, 1, 0, '', ''),
(2, 'disp', '$2y$10$QO6yYMNijT8XrLzK9u5iLu4dYz5wKwjQYGS7suZBOXMXdML2WKEIy', 'Диспетчер1', 0, '', '', '', '', '', '+71234567891', 'disp1@list.ru', '', 'лифтовая 1', 0, '', '', '', '', 1, 3, 0, 0, 1684426124, '127.0.0.1', 0, '', 0, 0, 1, 0, 0, 1, 1, 1, 0, 0, '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `users_description_row`
--

CREATE TABLE `users_description_row` (
  `id` int(11) NOT NULL,
  `name_row` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'name row from lift_users',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'тип значения',
  `text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ' ' COMMENT 'отображаемое название поля',
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ' ' COMMENT 'описание поля',
  `classification` tinyint(1) NOT NULL DEFAULT 3 COMMENT 'Классификация поля :1- авторизация, \r\n2-Основные данные \r\n3- доп. информация',
  `editable` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'возможность редактировать поле',
  `display_order` tinyint(20) NOT NULL COMMENT 'Порядок отображения'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='описание полей';

--
-- Дамп данных таблицы `users_description_row`
--

INSERT INTO `users_description_row` (`id`, `name_row`, `type`, `text`, `description`, `classification`, `editable`, `display_order`) VALUES
(3, 'user_login', 'text', ' Логин', 'Логин пользователя', 1, 1, 1),
(4, 'user_password', 'text', ' Пароль', ' Пароль пользователя', 1, 1, 2),
(5, 'user_name', 'text', ' ФИО', ' Фамилия Имя (при необходимости Отчество)', 2, 1, 3),
(6, 'user_phone', 'phone', ' Телефон', ' Телефон пользователя', 2, 1, 4),
(7, 'user_telegram', 'number', ' Телеграмм', ' ID телеграмм пользователя', 2, 1, 5),
(8, 'user_hiden', 'checkbox', ' Скрыть', ' поставьте галочку, что бы скрыть пользователя', 2, 1, 6),
(9, 'user_localadmin', 'checkbox', ' Администратор', 'Предоставить полные права администратора пользователю', 2, 1, 7),
(10, 'user_edit_obj', 'checkbox', ' Редактирует объекты ', ' Разрешить пользователю редактировать объекты (города, дома, лифты)', 2, 1, 8),
(11, 'user_edit_user', 'checkbox', 'Редактирует пользователей', ' Разрешить пользователю редактирование и добавление других пользователей', 2, 1, 9),
(12, 'user_disppermission', 'checkbox', ' Права диспетчера', ' Предоставить пользователю права диспетчера', 2, 1, 10),
(13, 'user_add_call', 'checkbox', ' Добавлять заявки', ' Разрешить пользователю добавлять заявки', 2, 1, 11),
(14, 'user_read_all_calls', 'checkbox', ' Чтение всех заявок', ' Разрешить пользователю доступ к чтению всех заявок , иначе будут доступны только те в которых он является ответственным', 2, 1, 12),
(15, 'user_block', 'checkbox', ' Заблокирован', ' Пользователь временно заблокирован и не имеет доступа к системе', 2, 1, 13),
(16, 'user_level', 'number', ' Уровень', ' Уровень пользователя от 0 до 3', 3, 1, 14),
(17, 'user_address', 'text', ' Адрес', ' улиц, номер дома, квартира  проживания пользователя', 3, 1, 15),
(18, 'user_city', 'text', ' Город', ' Город проживания пользователя', 3, 1, 16),
(19, 'user_state', 'text', ' Область', ' Область проживания пользователя', 3, 1, 17),
(20, 'user_zip', 'number', ' Индекс', ' Индекс проживания пользователя', 3, 1, 18),
(21, 'user_country', 'text', 'Страна', ' Страна проживания пользователя', 3, 1, 19),
(22, 'user_email', 'email', ' E-Mail', ' Адрес электронной почты пользователя', 3, 1, 20),
(23, 'last_ip', 'text', ' IP', ' IP адрес последней авторизации', 3, 0, 21),
(24, 'user_protect_delete', 'checkbox', ' Запрет на удаление', ' ', 3, 0, 22),
(25, 'user_protect_edit', 'checkbox', ' Запрет на изменение', ' ', 3, 0, 23),
(26, 'last_login', 'text', ' Дата авторизации', ' Дата последней авторизации пользователя', 3, 0, 24);

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
-- Индексы таблицы `lift_telegram`
--
ALTER TABLE `lift_telegram`
  ADD PRIMARY KEY (`id`);

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
-- Индексы таблицы `users_description_row`
--
ALTER TABLE `users_description_row`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_row` (`name_row`),
  ADD UNIQUE KEY `display_order` (`display_order`),
  ADD KEY `name_row_2` (`name_row`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `lift_calls`
--
ALTER TABLE `lift_calls`
  MODIFY `call_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `lift_city`
--
ALTER TABLE `lift_city`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `lift_history`
--
ALTER TABLE `lift_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `lift_home`
--
ALTER TABLE `lift_home`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `lift_notes`
--
ALTER TABLE `lift_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `lift_object`
--
ALTER TABLE `lift_object`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `lift_options`
--
ALTER TABLE `lift_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `lift_street`
--
ALTER TABLE `lift_street`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `lift_telegram`
--
ALTER TABLE `lift_telegram`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users_description_row`
--
ALTER TABLE `users_description_row`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
INSERT INTO `lift_history` (`history_id`, `history_date`, `history_info`, `call_id`) VALUES
(1, 1673726381, 'Диспетчер1 - внес(ла) следующие изменения: Предполагаемое время ремонта - 14.02.2023 . Ответственный уведомлен по телефону.  Назначен ответственный: Петров Петр . ', 1);
