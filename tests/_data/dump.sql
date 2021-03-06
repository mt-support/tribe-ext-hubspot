-- Adminer 4.2.6-dev MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `wp_commentmeta`;
CREATE TABLE `wp_commentmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `comment_id` (`comment_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_comments`;
CREATE TABLE `wp_comments` (
  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',
  `comment_author` tinytext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_author_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT '0',
  `comment_approved` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_ID`),
  KEY `comment_post_ID` (`comment_post_ID`),
  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  KEY `comment_date_gmt` (`comment_date_gmt`),
  KEY `comment_parent` (`comment_parent`),
  KEY `comment_author_email` (`comment_author_email`(10)),
  KEY `woo_idx_comment_type` (`comment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_edd_customermeta`;
CREATE TABLE `wp_edd_customermeta` (
  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer_id` bigint(20) NOT NULL,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `customer_id` (`customer_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `wp_edd_customers`;
CREATE TABLE `wp_edd_customers` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` mediumtext NOT NULL,
  `purchase_value` mediumtext NOT NULL,
  `purchase_count` bigint(20) NOT NULL,
  `payment_ids` longtext NOT NULL,
  `notes` longtext NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `wp_edd_customers` (`id`, `user_id`, `email`, `name`, `purchase_value`, `purchase_count`, `payment_ids`, `notes`, `date_created`) VALUES
(1,	1,	'dev-email@flywheel.local',	'Luca Tumedei',	'4.000000',	1,	'50',	'',	'2017-12-07 13:22:27');

DROP TABLE IF EXISTS `wp_links`;
CREATE TABLE `wp_links` (
  `link_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link_url` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_image` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_target` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_description` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_visible` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) unsigned NOT NULL DEFAULT '1',
  `link_rating` int(11) NOT NULL DEFAULT '0',
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `link_notes` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `link_rss` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`link_id`),
  KEY `link_visible` (`link_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_options`;
CREATE TABLE `wp_options` (
  `option_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `option_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `autoload` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`option_id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1,	'siteurl',	'http://events.local',	'yes'),
(2,	'home',	'http://events.local',	'yes'),
(3,	'blogname',	'commerce',	'yes'),
(4,	'blogdescription',	'Just another WordPress site',	'yes'),
(5,	'users_can_register',	'0',	'yes'),
(6,	'admin_email',	'dev-email@flywheel.local',	'yes'),
(7,	'start_of_week',	'1',	'yes'),
(8,	'use_balanceTags',	'0',	'yes'),
(9,	'use_smilies',	'1',	'yes'),
(10,	'require_name_email',	'1',	'yes'),
(11,	'comments_notify',	'1',	'yes'),
(12,	'posts_per_rss',	'10',	'yes'),
(13,	'rss_use_excerpt',	'0',	'yes'),
(14,	'mailserver_url',	'mail.example.com',	'yes'),
(15,	'mailserver_login',	'login@example.com',	'yes'),
(16,	'mailserver_pass',	'password',	'yes'),
(17,	'mailserver_port',	'110',	'yes'),
(18,	'default_category',	'1',	'yes'),
(19,	'default_comment_status',	'open',	'yes'),
(20,	'default_ping_status',	'open',	'yes'),
(21,	'default_pingback_flag',	'1',	'yes'),
(22,	'posts_per_page',	'10',	'yes'),
(23,	'date_format',	'F j, Y',	'yes'),
(24,	'time_format',	'g:i a',	'yes'),
(25,	'links_updated_date_format',	'F j, Y g:i a',	'yes'),
(26,	'comment_moderation',	'0',	'yes'),
(27,	'moderation_notify',	'1',	'yes'),
(28,	'permalink_structure',	'/%postname%/',	'yes'),
(30,	'hack_file',	'0',	'yes'),
(31,	'blog_charset',	'UTF-8',	'yes'),
(32,	'moderation_keys',	'',	'no'),
(34,	'category_base',	'',	'yes'),
(35,	'ping_sites',	'http://rpc.pingomatic.com/',	'yes'),
(36,	'comment_max_links',	'2',	'yes'),
(37,	'gmt_offset',	'0',	'yes'),
(38,	'default_email_category',	'1',	'yes'),
(39,	'recently_edited',	'',	'no'),
(40,	'template',	'twentyseventeen',	'yes'),
(41,	'stylesheet',	'twentyseventeen',	'yes'),
(42,	'comment_whitelist',	'1',	'yes'),
(43,	'blacklist_keys',	'',	'no'),
(44,	'comment_registration',	'0',	'yes'),
(45,	'html_type',	'text/html',	'yes'),
(46,	'use_trackback',	'0',	'yes'),
(47,	'default_role',	'subscriber',	'yes'),
(48,	'db_version',	'44719',	'yes'),
(49,	'uploads_use_yearmonth_folders',	'1',	'yes'),
(50,	'upload_path',	'',	'yes'),
(51,	'blog_public',	'1',	'yes'),
(52,	'default_link_category',	'2',	'yes'),
(53,	'show_on_front',	'posts',	'yes'),
(54,	'tag_base',	'',	'yes'),
(55,	'show_avatars',	'1',	'yes'),
(56,	'avatar_rating',	'G',	'yes'),
(57,	'upload_url_path',	'',	'yes'),
(58,	'thumbnail_size_w',	'150',	'yes'),
(59,	'thumbnail_size_h',	'150',	'yes'),
(60,	'thumbnail_crop',	'1',	'yes'),
(61,	'medium_size_w',	'300',	'yes'),
(62,	'medium_size_h',	'300',	'yes'),
(63,	'avatar_default',	'mystery',	'yes'),
(64,	'large_size_w',	'1024',	'yes'),
(65,	'large_size_h',	'1024',	'yes'),
(66,	'image_default_link_type',	'none',	'yes'),
(67,	'image_default_size',	'',	'yes'),
(68,	'image_default_align',	'',	'yes'),
(69,	'close_comments_for_old_posts',	'0',	'yes'),
(70,	'close_comments_days_old',	'14',	'yes'),
(71,	'thread_comments',	'1',	'yes'),
(72,	'thread_comments_depth',	'5',	'yes'),
(73,	'page_comments',	'0',	'yes'),
(74,	'comments_per_page',	'50',	'yes'),
(75,	'default_comments_page',	'newest',	'yes'),
(76,	'comment_order',	'asc',	'yes'),
(77,	'sticky_posts',	'a:0:{}',	'yes'),
(78,	'widget_categories',	'a:2:{i:2;a:4:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:12:\"hierarchical\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}',	'yes'),
(79,	'widget_text',	'a:0:{}',	'yes'),
(80,	'widget_rss',	'a:0:{}',	'yes'),
(81,	'uninstall_plugins',	'a:1:{s:49:\"log-deprecated-notices/log-deprecated-notices.php\";a:2:{i:0;s:14:\"Deprecated_Log\";i:1;s:12:\"on_uninstall\";}}',	'no'),
(82,	'timezone_string',	'',	'yes'),
(83,	'page_for_posts',	'0',	'yes'),
(84,	'page_on_front',	'0',	'yes'),
(85,	'default_post_format',	'0',	'yes'),
(86,	'link_manager_enabled',	'0',	'yes'),
(87,	'finished_splitting_shared_terms',	'1',	'yes'),
(88,	'site_icon',	'0',	'yes'),
(89,	'medium_large_size_w',	'768',	'yes'),
(90,	'medium_large_size_h',	'0',	'yes'),
(91,	'initial_db_version',	'38590',	'yes'),
(92,	'wp_user_roles',	'a:10:{s:13:\"administrator\";a:2:{s:4:\"name\";s:13:\"Administrator\";s:12:\"capabilities\";a:186:{s:13:\"switch_themes\";b:1;s:11:\"edit_themes\";b:1;s:16:\"activate_plugins\";b:1;s:12:\"edit_plugins\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_files\";b:1;s:14:\"manage_options\";b:1;s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:6:\"import\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:8:\"level_10\";b:1;s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:12:\"delete_users\";b:1;s:12:\"create_users\";b:1;s:17:\"unfiltered_upload\";b:1;s:14:\"edit_dashboard\";b:1;s:14:\"update_plugins\";b:1;s:14:\"delete_plugins\";b:1;s:15:\"install_plugins\";b:1;s:13:\"update_themes\";b:1;s:14:\"install_themes\";b:1;s:11:\"update_core\";b:1;s:10:\"list_users\";b:1;s:12:\"remove_users\";b:1;s:13:\"promote_users\";b:1;s:18:\"edit_theme_options\";b:1;s:13:\"delete_themes\";b:1;s:6:\"export\";b:1;s:18:\"manage_woocommerce\";b:1;s:24:\"view_woocommerce_reports\";b:1;s:12:\"edit_product\";b:1;s:12:\"read_product\";b:1;s:14:\"delete_product\";b:1;s:13:\"edit_products\";b:1;s:20:\"edit_others_products\";b:1;s:16:\"publish_products\";b:1;s:21:\"read_private_products\";b:1;s:15:\"delete_products\";b:1;s:23:\"delete_private_products\";b:1;s:25:\"delete_published_products\";b:1;s:22:\"delete_others_products\";b:1;s:21:\"edit_private_products\";b:1;s:23:\"edit_published_products\";b:1;s:20:\"manage_product_terms\";b:1;s:18:\"edit_product_terms\";b:1;s:20:\"delete_product_terms\";b:1;s:20:\"assign_product_terms\";b:1;s:15:\"edit_shop_order\";b:1;s:15:\"read_shop_order\";b:1;s:17:\"delete_shop_order\";b:1;s:16:\"edit_shop_orders\";b:1;s:23:\"edit_others_shop_orders\";b:1;s:19:\"publish_shop_orders\";b:1;s:24:\"read_private_shop_orders\";b:1;s:18:\"delete_shop_orders\";b:1;s:26:\"delete_private_shop_orders\";b:1;s:28:\"delete_published_shop_orders\";b:1;s:25:\"delete_others_shop_orders\";b:1;s:24:\"edit_private_shop_orders\";b:1;s:26:\"edit_published_shop_orders\";b:1;s:23:\"manage_shop_order_terms\";b:1;s:21:\"edit_shop_order_terms\";b:1;s:23:\"delete_shop_order_terms\";b:1;s:23:\"assign_shop_order_terms\";b:1;s:16:\"edit_shop_coupon\";b:1;s:16:\"read_shop_coupon\";b:1;s:18:\"delete_shop_coupon\";b:1;s:17:\"edit_shop_coupons\";b:1;s:24:\"edit_others_shop_coupons\";b:1;s:20:\"publish_shop_coupons\";b:1;s:25:\"read_private_shop_coupons\";b:1;s:19:\"delete_shop_coupons\";b:1;s:27:\"delete_private_shop_coupons\";b:1;s:29:\"delete_published_shop_coupons\";b:1;s:26:\"delete_others_shop_coupons\";b:1;s:25:\"edit_private_shop_coupons\";b:1;s:27:\"edit_published_shop_coupons\";b:1;s:24:\"manage_shop_coupon_terms\";b:1;s:22:\"edit_shop_coupon_terms\";b:1;s:24:\"delete_shop_coupon_terms\";b:1;s:24:\"assign_shop_coupon_terms\";b:1;s:17:\"edit_shop_webhook\";b:1;s:17:\"read_shop_webhook\";b:1;s:19:\"delete_shop_webhook\";b:1;s:18:\"edit_shop_webhooks\";b:1;s:25:\"edit_others_shop_webhooks\";b:1;s:21:\"publish_shop_webhooks\";b:1;s:26:\"read_private_shop_webhooks\";b:1;s:20:\"delete_shop_webhooks\";b:1;s:28:\"delete_private_shop_webhooks\";b:1;s:30:\"delete_published_shop_webhooks\";b:1;s:27:\"delete_others_shop_webhooks\";b:1;s:26:\"edit_private_shop_webhooks\";b:1;s:28:\"edit_published_shop_webhooks\";b:1;s:25:\"manage_shop_webhook_terms\";b:1;s:23:\"edit_shop_webhook_terms\";b:1;s:25:\"delete_shop_webhook_terms\";b:1;s:25:\"assign_shop_webhook_terms\";b:1;s:17:\"view_shop_reports\";b:1;s:24:\"view_shop_sensitive_data\";b:1;s:19:\"export_shop_reports\";b:1;s:21:\"manage_shop_discounts\";b:1;s:20:\"manage_shop_settings\";b:1;s:18:\"view_product_stats\";b:1;s:15:\"import_products\";b:1;s:17:\"edit_shop_payment\";b:1;s:17:\"read_shop_payment\";b:1;s:19:\"delete_shop_payment\";b:1;s:18:\"edit_shop_payments\";b:1;s:25:\"edit_others_shop_payments\";b:1;s:21:\"publish_shop_payments\";b:1;s:26:\"read_private_shop_payments\";b:1;s:20:\"delete_shop_payments\";b:1;s:28:\"delete_private_shop_payments\";b:1;s:30:\"delete_published_shop_payments\";b:1;s:27:\"delete_others_shop_payments\";b:1;s:26:\"edit_private_shop_payments\";b:1;s:28:\"edit_published_shop_payments\";b:1;s:25:\"manage_shop_payment_terms\";b:1;s:23:\"edit_shop_payment_terms\";b:1;s:25:\"delete_shop_payment_terms\";b:1;s:25:\"assign_shop_payment_terms\";b:1;s:23:\"view_shop_payment_stats\";b:1;s:20:\"import_shop_payments\";b:1;s:18:\"edit_shop_discount\";b:1;s:18:\"read_shop_discount\";b:1;s:20:\"delete_shop_discount\";b:1;s:19:\"edit_shop_discounts\";b:1;s:26:\"edit_others_shop_discounts\";b:1;s:22:\"publish_shop_discounts\";b:1;s:27:\"read_private_shop_discounts\";b:1;s:21:\"delete_shop_discounts\";b:1;s:29:\"delete_private_shop_discounts\";b:1;s:31:\"delete_published_shop_discounts\";b:1;s:28:\"delete_others_shop_discounts\";b:1;s:27:\"edit_private_shop_discounts\";b:1;s:29:\"edit_published_shop_discounts\";b:1;s:26:\"manage_shop_discount_terms\";b:1;s:24:\"edit_shop_discount_terms\";b:1;s:26:\"delete_shop_discount_terms\";b:1;s:26:\"assign_shop_discount_terms\";b:1;s:24:\"view_shop_discount_stats\";b:1;s:21:\"import_shop_discounts\";b:1;s:31:\"read_private_aggregator-records\";b:1;s:23:\"edit_aggregator-records\";b:1;s:30:\"edit_others_aggregator-records\";b:1;s:31:\"edit_private_aggregator-records\";b:1;s:33:\"edit_published_aggregator-records\";b:1;s:25:\"delete_aggregator-records\";b:1;s:32:\"delete_others_aggregator-records\";b:1;s:33:\"delete_private_aggregator-records\";b:1;s:35:\"delete_published_aggregator-records\";b:1;s:26:\"publish_aggregator-records\";b:1;}}s:6:\"editor\";a:2:{s:4:\"name\";s:6:\"Editor\";s:12:\"capabilities\";a:44:{s:17:\"moderate_comments\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:12:\"upload_files\";b:1;s:15:\"unfiltered_html\";b:1;s:10:\"edit_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:10:\"edit_pages\";b:1;s:4:\"read\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:17:\"edit_others_pages\";b:1;s:20:\"edit_published_pages\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_pages\";b:1;s:19:\"delete_others_pages\";b:1;s:22:\"delete_published_pages\";b:1;s:12:\"delete_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:20:\"delete_private_posts\";b:1;s:18:\"edit_private_posts\";b:1;s:18:\"read_private_posts\";b:1;s:20:\"delete_private_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"read_private_pages\";b:1;s:31:\"read_private_aggregator-records\";b:1;s:23:\"edit_aggregator-records\";b:1;s:30:\"edit_others_aggregator-records\";b:1;s:31:\"edit_private_aggregator-records\";b:1;s:33:\"edit_published_aggregator-records\";b:1;s:25:\"delete_aggregator-records\";b:1;s:32:\"delete_others_aggregator-records\";b:1;s:33:\"delete_private_aggregator-records\";b:1;s:35:\"delete_published_aggregator-records\";b:1;s:26:\"publish_aggregator-records\";b:1;}}s:6:\"author\";a:2:{s:4:\"name\";s:6:\"Author\";s:12:\"capabilities\";a:15:{s:12:\"upload_files\";b:1;s:10:\"edit_posts\";b:1;s:20:\"edit_published_posts\";b:1;s:13:\"publish_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:22:\"delete_published_posts\";b:1;s:23:\"edit_aggregator-records\";b:1;s:33:\"edit_published_aggregator-records\";b:1;s:25:\"delete_aggregator-records\";b:1;s:35:\"delete_published_aggregator-records\";b:1;s:26:\"publish_aggregator-records\";b:1;}}s:11:\"contributor\";a:2:{s:4:\"name\";s:11:\"Contributor\";s:12:\"capabilities\";a:7:{s:10:\"edit_posts\";b:1;s:4:\"read\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:12:\"delete_posts\";b:1;s:23:\"edit_aggregator-records\";b:1;s:25:\"delete_aggregator-records\";b:1;}}s:10:\"subscriber\";a:2:{s:4:\"name\";s:10:\"Subscriber\";s:12:\"capabilities\";a:2:{s:4:\"read\";b:1;s:7:\"level_0\";b:1;}}s:8:\"customer\";a:2:{s:4:\"name\";s:8:\"Customer\";s:12:\"capabilities\";a:1:{s:4:\"read\";b:1;}}s:12:\"shop_manager\";a:2:{s:4:\"name\";s:12:\"Shop manager\";s:12:\"capabilities\";a:154:{s:7:\"level_9\";b:1;s:7:\"level_8\";b:1;s:7:\"level_7\";b:1;s:7:\"level_6\";b:1;s:7:\"level_5\";b:1;s:7:\"level_4\";b:1;s:7:\"level_3\";b:1;s:7:\"level_2\";b:1;s:7:\"level_1\";b:1;s:7:\"level_0\";b:1;s:4:\"read\";b:1;s:18:\"read_private_pages\";b:1;s:18:\"read_private_posts\";b:1;s:10:\"edit_users\";b:1;s:10:\"edit_posts\";b:1;s:10:\"edit_pages\";b:1;s:20:\"edit_published_posts\";b:1;s:20:\"edit_published_pages\";b:1;s:18:\"edit_private_pages\";b:1;s:18:\"edit_private_posts\";b:1;s:17:\"edit_others_posts\";b:1;s:17:\"edit_others_pages\";b:1;s:13:\"publish_posts\";b:1;s:13:\"publish_pages\";b:1;s:12:\"delete_posts\";b:1;s:12:\"delete_pages\";b:1;s:20:\"delete_private_pages\";b:1;s:20:\"delete_private_posts\";b:1;s:22:\"delete_published_pages\";b:1;s:22:\"delete_published_posts\";b:1;s:19:\"delete_others_posts\";b:1;s:19:\"delete_others_pages\";b:1;s:17:\"manage_categories\";b:1;s:12:\"manage_links\";b:1;s:17:\"moderate_comments\";b:1;s:12:\"upload_files\";b:1;s:6:\"export\";b:1;s:6:\"import\";b:1;s:10:\"list_users\";b:1;s:18:\"manage_woocommerce\";b:1;s:24:\"view_woocommerce_reports\";b:1;s:12:\"edit_product\";b:1;s:12:\"read_product\";b:1;s:14:\"delete_product\";b:1;s:13:\"edit_products\";b:1;s:20:\"edit_others_products\";b:1;s:16:\"publish_products\";b:1;s:21:\"read_private_products\";b:1;s:15:\"delete_products\";b:1;s:23:\"delete_private_products\";b:1;s:25:\"delete_published_products\";b:1;s:22:\"delete_others_products\";b:1;s:21:\"edit_private_products\";b:1;s:23:\"edit_published_products\";b:1;s:20:\"manage_product_terms\";b:1;s:18:\"edit_product_terms\";b:1;s:20:\"delete_product_terms\";b:1;s:20:\"assign_product_terms\";b:1;s:15:\"edit_shop_order\";b:1;s:15:\"read_shop_order\";b:1;s:17:\"delete_shop_order\";b:1;s:16:\"edit_shop_orders\";b:1;s:23:\"edit_others_shop_orders\";b:1;s:19:\"publish_shop_orders\";b:1;s:24:\"read_private_shop_orders\";b:1;s:18:\"delete_shop_orders\";b:1;s:26:\"delete_private_shop_orders\";b:1;s:28:\"delete_published_shop_orders\";b:1;s:25:\"delete_others_shop_orders\";b:1;s:24:\"edit_private_shop_orders\";b:1;s:26:\"edit_published_shop_orders\";b:1;s:23:\"manage_shop_order_terms\";b:1;s:21:\"edit_shop_order_terms\";b:1;s:23:\"delete_shop_order_terms\";b:1;s:23:\"assign_shop_order_terms\";b:1;s:16:\"edit_shop_coupon\";b:1;s:16:\"read_shop_coupon\";b:1;s:18:\"delete_shop_coupon\";b:1;s:17:\"edit_shop_coupons\";b:1;s:24:\"edit_others_shop_coupons\";b:1;s:20:\"publish_shop_coupons\";b:1;s:25:\"read_private_shop_coupons\";b:1;s:19:\"delete_shop_coupons\";b:1;s:27:\"delete_private_shop_coupons\";b:1;s:29:\"delete_published_shop_coupons\";b:1;s:26:\"delete_others_shop_coupons\";b:1;s:25:\"edit_private_shop_coupons\";b:1;s:27:\"edit_published_shop_coupons\";b:1;s:24:\"manage_shop_coupon_terms\";b:1;s:22:\"edit_shop_coupon_terms\";b:1;s:24:\"delete_shop_coupon_terms\";b:1;s:24:\"assign_shop_coupon_terms\";b:1;s:17:\"edit_shop_webhook\";b:1;s:17:\"read_shop_webhook\";b:1;s:19:\"delete_shop_webhook\";b:1;s:18:\"edit_shop_webhooks\";b:1;s:25:\"edit_others_shop_webhooks\";b:1;s:21:\"publish_shop_webhooks\";b:1;s:26:\"read_private_shop_webhooks\";b:1;s:20:\"delete_shop_webhooks\";b:1;s:28:\"delete_private_shop_webhooks\";b:1;s:30:\"delete_published_shop_webhooks\";b:1;s:27:\"delete_others_shop_webhooks\";b:1;s:26:\"edit_private_shop_webhooks\";b:1;s:28:\"edit_published_shop_webhooks\";b:1;s:25:\"manage_shop_webhook_terms\";b:1;s:23:\"edit_shop_webhook_terms\";b:1;s:25:\"delete_shop_webhook_terms\";b:1;s:25:\"assign_shop_webhook_terms\";b:1;s:17:\"view_shop_reports\";b:1;s:24:\"view_shop_sensitive_data\";b:1;s:19:\"export_shop_reports\";b:1;s:20:\"manage_shop_settings\";b:1;s:21:\"manage_shop_discounts\";b:1;s:18:\"view_product_stats\";b:1;s:15:\"import_products\";b:1;s:17:\"edit_shop_payment\";b:1;s:17:\"read_shop_payment\";b:1;s:19:\"delete_shop_payment\";b:1;s:18:\"edit_shop_payments\";b:1;s:25:\"edit_others_shop_payments\";b:1;s:21:\"publish_shop_payments\";b:1;s:26:\"read_private_shop_payments\";b:1;s:20:\"delete_shop_payments\";b:1;s:28:\"delete_private_shop_payments\";b:1;s:30:\"delete_published_shop_payments\";b:1;s:27:\"delete_others_shop_payments\";b:1;s:26:\"edit_private_shop_payments\";b:1;s:28:\"edit_published_shop_payments\";b:1;s:25:\"manage_shop_payment_terms\";b:1;s:23:\"edit_shop_payment_terms\";b:1;s:25:\"delete_shop_payment_terms\";b:1;s:25:\"assign_shop_payment_terms\";b:1;s:23:\"view_shop_payment_stats\";b:1;s:20:\"import_shop_payments\";b:1;s:18:\"edit_shop_discount\";b:1;s:18:\"read_shop_discount\";b:1;s:20:\"delete_shop_discount\";b:1;s:19:\"edit_shop_discounts\";b:1;s:26:\"edit_others_shop_discounts\";b:1;s:22:\"publish_shop_discounts\";b:1;s:27:\"read_private_shop_discounts\";b:1;s:21:\"delete_shop_discounts\";b:1;s:29:\"delete_private_shop_discounts\";b:1;s:31:\"delete_published_shop_discounts\";b:1;s:28:\"delete_others_shop_discounts\";b:1;s:27:\"edit_private_shop_discounts\";b:1;s:29:\"edit_published_shop_discounts\";b:1;s:26:\"manage_shop_discount_terms\";b:1;s:24:\"edit_shop_discount_terms\";b:1;s:26:\"delete_shop_discount_terms\";b:1;s:26:\"assign_shop_discount_terms\";b:1;s:24:\"view_shop_discount_stats\";b:1;s:21:\"import_shop_discounts\";b:1;}}s:15:\"shop_accountant\";a:2:{s:4:\"name\";s:15:\"Shop Accountant\";s:12:\"capabilities\";a:8:{s:4:\"read\";b:1;s:10:\"edit_posts\";b:0;s:12:\"delete_posts\";b:0;s:13:\"edit_products\";b:1;s:21:\"read_private_products\";b:1;s:17:\"view_shop_reports\";b:1;s:19:\"export_shop_reports\";b:1;s:18:\"edit_shop_payments\";b:1;}}s:11:\"shop_worker\";a:2:{s:4:\"name\";s:11:\"Shop Worker\";s:12:\"capabilities\";a:61:{s:4:\"read\";b:1;s:10:\"edit_posts\";b:0;s:12:\"upload_files\";b:1;s:12:\"delete_posts\";b:0;s:12:\"edit_product\";b:1;s:12:\"read_product\";b:1;s:14:\"delete_product\";b:1;s:13:\"edit_products\";b:1;s:20:\"edit_others_products\";b:1;s:16:\"publish_products\";b:1;s:21:\"read_private_products\";b:1;s:15:\"delete_products\";b:1;s:23:\"delete_private_products\";b:1;s:25:\"delete_published_products\";b:1;s:22:\"delete_others_products\";b:1;s:21:\"edit_private_products\";b:1;s:23:\"edit_published_products\";b:1;s:20:\"manage_product_terms\";b:1;s:18:\"edit_product_terms\";b:1;s:20:\"delete_product_terms\";b:1;s:20:\"assign_product_terms\";b:1;s:18:\"view_product_stats\";b:1;s:15:\"import_products\";b:1;s:17:\"edit_shop_payment\";b:1;s:17:\"read_shop_payment\";b:1;s:19:\"delete_shop_payment\";b:1;s:18:\"edit_shop_payments\";b:1;s:25:\"edit_others_shop_payments\";b:1;s:21:\"publish_shop_payments\";b:1;s:26:\"read_private_shop_payments\";b:1;s:20:\"delete_shop_payments\";b:1;s:28:\"delete_private_shop_payments\";b:1;s:30:\"delete_published_shop_payments\";b:1;s:27:\"delete_others_shop_payments\";b:1;s:26:\"edit_private_shop_payments\";b:1;s:28:\"edit_published_shop_payments\";b:1;s:25:\"manage_shop_payment_terms\";b:1;s:23:\"edit_shop_payment_terms\";b:1;s:25:\"delete_shop_payment_terms\";b:1;s:25:\"assign_shop_payment_terms\";b:1;s:23:\"view_shop_payment_stats\";b:1;s:20:\"import_shop_payments\";b:1;s:18:\"edit_shop_discount\";b:1;s:18:\"read_shop_discount\";b:1;s:20:\"delete_shop_discount\";b:1;s:19:\"edit_shop_discounts\";b:1;s:26:\"edit_others_shop_discounts\";b:1;s:22:\"publish_shop_discounts\";b:1;s:27:\"read_private_shop_discounts\";b:1;s:21:\"delete_shop_discounts\";b:1;s:29:\"delete_private_shop_discounts\";b:1;s:31:\"delete_published_shop_discounts\";b:1;s:28:\"delete_others_shop_discounts\";b:1;s:27:\"edit_private_shop_discounts\";b:1;s:29:\"edit_published_shop_discounts\";b:1;s:26:\"manage_shop_discount_terms\";b:1;s:24:\"edit_shop_discount_terms\";b:1;s:26:\"delete_shop_discount_terms\";b:1;s:26:\"assign_shop_discount_terms\";b:1;s:24:\"view_shop_discount_stats\";b:1;s:21:\"import_shop_discounts\";b:1;}}s:11:\"shop_vendor\";a:2:{s:4:\"name\";s:11:\"Shop Vendor\";s:12:\"capabilities\";a:11:{s:4:\"read\";b:1;s:10:\"edit_posts\";b:0;s:12:\"upload_files\";b:1;s:12:\"delete_posts\";b:0;s:12:\"edit_product\";b:1;s:13:\"edit_products\";b:1;s:14:\"delete_product\";b:1;s:15:\"delete_products\";b:1;s:16:\"publish_products\";b:1;s:23:\"edit_published_products\";b:1;s:20:\"assign_product_terms\";b:1;}}}',	'yes'),
(93,	'fresh_site',	'0',	'yes'),
(94,	'widget_search',	'a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}',	'yes'),
(95,	'widget_recent-posts',	'a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}',	'yes'),
(96,	'widget_recent-comments',	'a:2:{i:2;a:2:{s:5:\"title\";s:0:\"\";s:6:\"number\";i:5;}s:12:\"_multiwidget\";i:1;}',	'yes'),
(97,	'widget_archives',	'a:2:{i:2;a:3:{s:5:\"title\";s:0:\"\";s:5:\"count\";i:0;s:8:\"dropdown\";i:0;}s:12:\"_multiwidget\";i:1;}',	'yes'),
(98,	'widget_meta',	'a:2:{i:2;a:1:{s:5:\"title\";s:0:\"\";}s:12:\"_multiwidget\";i:1;}',	'yes'),
(99,	'sidebars_widgets',	'a:5:{s:19:\"wp_inactive_widgets\";a:0:{}s:9:\"sidebar-1\";a:6:{i:0;s:8:\"search-2\";i:1;s:14:\"recent-posts-2\";i:2;s:17:\"recent-comments-2\";i:3;s:10:\"archives-2\";i:4;s:12:\"categories-2\";i:5;s:6:\"meta-2\";}s:9:\"sidebar-2\";a:0:{}s:9:\"sidebar-3\";a:0:{}s:13:\"array_version\";i:3;}',	'yes'),
(100,	'widget_pages',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(101,	'widget_calendar',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(102,	'widget_media_audio',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(103,	'widget_media_image',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(104,	'widget_media_gallery',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(105,	'widget_media_video',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(106,	'nonce_key',	';j?!m>(jn)LaF/S2]^(9h1dNzI=]kByDjm*k-=_;SPI3Hpo<PIDuPZ*MjDle7%Pa',	'no'),
(107,	'nonce_salt',	'OzXxZIhWVsT9?Lyu|,UYN{KdT_toN]I/KiEb=N4)}s;QAA)#Tde0WP4nzB4}=jaG',	'no'),
(108,	'widget_tag_cloud',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(109,	'widget_nav_menu',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(110,	'widget_custom_html',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(111,	'cron',	'a:12:{i:1557429396;a:1:{s:34:\"wp_privacy_delete_old_export_files\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:6:\"hourly\";s:4:\"args\";a:0:{}s:8:\"interval\";i:3600;}}}i:1557433531;a:2:{s:16:\"wp_version_check\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}s:17:\"wp_update_plugins\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1557441310;a:1:{s:16:\"wp_update_themes\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1557446400;a:1:{s:27:\"woocommerce_scheduled_sales\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1557468710;a:1:{s:28:\"woocommerce_cleanup_sessions\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:10:\"twicedaily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:43200;}}}i:1557476742;a:2:{s:19:\"wp_scheduled_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}s:25:\"delete_expired_transients\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1557477173;a:1:{s:24:\"tribe_common_log_cleanup\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1557477220;a:1:{s:30:\"wp_scheduled_auto_draft_delete\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1557493423;a:1:{s:26:\"edd_daily_scheduled_events\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1557511910;a:1:{s:30:\"woocommerce_tracker_send_event\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}i:1557512195;a:1:{s:32:\"recovery_mode_clean_expired_keys\";a:1:{s:32:\"40cd750bba9870f18aada2478b24840a\";a:3:{s:8:\"schedule\";s:5:\"daily\";s:4:\"args\";a:0:{}s:8:\"interval\";i:86400;}}}s:7:\"version\";i:2;}',	'yes'),
(112,	'theme_mods_twentyseventeen',	'a:1:{s:18:\"custom_css_post_id\";i:-1;}',	'yes'),
(140,	'tribe_events_calendar_options',	'a:67:{s:25:\"ticket-enabled-post-types\";a:2:{i:0;s:4:\"post\";i:1;s:12:\"tribe_events\";}s:31:\"previous_event_tickets_versions\";a:2:{i:0;s:1:\"0\";i:1;s:7:\"4.7dev1\";}s:28:\"latest_event_tickets_version\";s:6:\"4.10.5\";s:33:\"last-update-message-event-tickets\";s:7:\"4.7dev1\";s:34:\"ticket-authentication-requirements\";N;s:19:\"ticket-paypal-email\";s:29:\"merchant-sb@theaveragedev.com\";s:21:\"ticket-paypal-sandbox\";b:1;s:28:\"ticket-paypal-identity-token\";s:59:\"OxdDgUf3HgQQ1yhiIR2SyOPYuYX89JpAg1WZnxrAVn6q6g_XMzyyWHCLmjG\";s:26:\"ticket-paypal-success-page\";s:1:\"0\";s:27:\"ticket-paypal-currency-code\";s:3:\"EUR\";s:21:\"defaultCurrencySymbol\";s:3:\"€\";s:23:\"reverseCurrencyPosition\";b:1;s:14:\"schema-version\";s:3:\"3.9\";s:27:\"recurring_events_are_hidden\";s:7:\"exposed\";s:21:\"previous_ecp_versions\";a:4:{i:0;s:1:\"0\";i:1;s:5:\"4.6.8\";i:2;s:5:\"4.6.9\";i:3;s:6:\"4.6.10\";}s:18:\"latest_ecp_version\";s:6:\"4.6.11\";s:39:\"last-update-message-the-events-calendar\";s:6:\"4.6.11\";s:25:\"ticket-rsvp-form-location\";s:40:\"tribe_events_single_event_after_the_meta\";s:29:\"ticket-commerce-form-location\";s:40:\"tribe_events_single_event_after_the_meta\";s:29:\"tickets-woo-generation-status\";a:4:{i:0;s:10:\"wc-pending\";i:1;s:13:\"wc-processing\";i:2;s:10:\"wc-on-hold\";i:3;s:12:\"wc-completed\";}s:27:\"tickets-woo-dispatch-status\";a:3:{i:0;s:13:\"wc-processing\";i:1;s:10:\"wc-on-hold\";i:2;s:12:\"wc-completed\";}s:13:\"earliest_date\";s:19:\"2018-02-14 08:00:00\";s:21:\"earliest_date_markers\";a:1:{i:0;s:3:\"975\";}s:11:\"latest_date\";s:19:\"2018-02-28 17:00:00\";s:19:\"latest_date_markers\";a:3:{i:0;s:3:\"594\";i:1;s:3:\"596\";i:2;s:3:\"913\";}s:16:\"tribeEnableViews\";a:3:{i:0;s:4:\"list\";i:1;s:5:\"month\";i:2;s:3:\"day\";}s:11:\"donate-link\";b:0;s:12:\"postsPerPage\";s:2:\"10\";s:17:\"liveFiltersUpdate\";b:1;s:12:\"showComments\";b:0;s:20:\"showEventsInMainLoop\";b:0;s:10:\"eventsSlug\";s:6:\"events\";s:15:\"singleEventSlug\";s:5:\"event\";s:14:\"multiDayCutoff\";s:5:\"00:00\";s:15:\"embedGoogleMaps\";b:1;s:19:\"embedGoogleMapsZoom\";s:2:\"10\";s:11:\"debugEvents\";b:0;s:26:\"tribe_events_timezone_mode\";s:5:\"event\";s:32:\"tribe_events_timezones_show_zone\";b:0;s:8:\"fb_token\";s:171:\"EAAZAWBRs6YykBALAwn14KJ55T70Cpk7SNYB8TZCOWW07Tw1NZAHwlZCZAOS1ZBS0H8wkOZB1DxyQPhBRJZBxY8FbiFwPZCtcXy3UKZBIsNKoLKJPBiRgMyTuZBsvIlsjo7ZBWi9ooS6keEoco8Qm4KUQ6OTbG1ZCnEbzJ24wZD\";s:16:\"fb_token_expires\";i:1518259413;s:15:\"fb_token_scopes\";s:46:\"user_events,user_managed_groups,public_profile\";s:29:\"disable_metabox_custom_fields\";s:4:\"show\";s:18:\"pro-schema-version\";s:3:\"3.9\";s:25:\"filter-bar-schema-version\";s:5:\"4.5.2\";s:24:\"ticket-paypal-notify-url\";s:25:\"http://a3baaf02.ngrok.io/\";s:28:\"ticket-paypal-notify-history\";N;s:21:\"ticket-paypal-disable\";b:0;s:44:\"ticket-paypal-confirmation-email-sender-name\";s:6:\"Luca T\";s:40:\"ticket-paypal-confirmation-email-subject\";s:15:\"You got tickets\";s:45:\"ticket-paypal-confirmation-email-sender-email\";s:11:\"luca@tri.be\";s:20:\"ticket-paypal-enable\";b:1;s:16:\"rest-v1-disabled\";b:0;s:38:\"ticket-confirmation-email-sender-email\";s:24:\"dev-email@flywheel.local\";s:37:\"ticket-confirmation-email-sender-name\";s:5:\"admin\";s:33:\"ticket-confirmation-email-subject\";s:27:\"Your tickets from commerce!\";s:31:\"rsvp-confirmation-email-subject\";s:25:\"Your RSVPs from commerce!\";s:29:\"ticket-commerce-currency-code\";s:3:\"USD\";s:23:\"ticket-paypal-configure\";N;s:25:\"ticket-paypal-setup-start\";N;s:23:\"ticket-paypal-setup-end\";N;s:31:\"ticket-paypal-ipn-config-status\";N;s:25:\"ticket-paypal-ipn-enabled\";s:3:\"yes\";s:29:\"ticket-paypal-ipn-address-set\";s:3:\"yes\";s:28:\"ticket-paypal-stock-handling\";s:11:\"on-complete\";s:44:\"_tribe-commmerce-paypal-oversell-show-notice\";s:1:\"1\";s:28:\"event-tickets-schema-version\";s:6:\"4.10.5\";}',	'yes'),
(194,	'recently_activated',	'a:7:{s:27:\"woocommerce/woocommerce.php\";i:1519042217;s:23:\"tribe-cli/tribe-cli.php\";i:1519042217;s:34:\"events-pro/events-calendar-pro.php\";i:1519042217;s:43:\"the-events-calendar/the-events-calendar.php\";i:1519042217;s:41:\"event-tickets-plus/event-tickets-plus.php\";i:1519042217;s:31:\"event-tickets/event-tickets.php\";i:1519042217;s:49:\"easy-digital-downloads/easy-digital-downloads.php\";i:1519042217;}',	'yes'),
(221,	'a8c_developer',	'a:1:{s:12:\"project_type\";s:5:\"wporg\";}',	'yes'),
(250,	'log_deprecated_notices',	'a:2:{s:11:\"last_viewed\";s:19:\"2017-12-06 15:28:50\";s:10:\"db_version\";i:4;}',	'yes'),
(254,	'active_plugins',	'a:2:{i:0;s:41:\"event-tickets-plus/event-tickets-plus.php\";i:1;s:31:\"event-tickets/event-tickets.php\";}',	'yes'),
(257,	'external_updates-event-tickets-plus',	'O:8:\"stdClass\":3:{s:9:\"lastCheck\";i:1557425798;s:14:\"checkedVersion\";s:6:\"4.10.4\";s:6:\"update\";O:19:\"Tribe__PUE__Utility\":11:{s:2:\"id\";i:0;s:6:\"plugin\";s:41:\"event-tickets-plus/event-tickets-plus.php\";s:4:\"slug\";s:18:\"event-tickets-plus\";s:7:\"version\";s:6:\"4.10.3\";s:8:\"homepage\";s:20:\"http://m.tri.be/18wg\";s:12:\"download_url\";s:290:\"https://puestaging.tri.be/api/plugins/v2/download?plugin=event-tickets-plus&version=4.10.3&installed_version=4.10.4&domain=events.local&multisite=0&network_activated=0&active_sites=1&wp_version=5.2&key=f0c38a187457a9bb5b1079f4556f467883a223f5&dk=f0c38a187457a9bb5b1079f4556f467883a223f5&o=m\";s:8:\"sections\";O:8:\"stdClass\":3:{s:11:\"description\";s:154:\"Event Tickets Plus adds features and functionality onto the core Event Tickets plugin, so you can sell tickets with WooCommerce or Easy Digital Downloads.\";s:12:\"installation\";s:348:\"Installing Events Tickets Plus is easy: just back up your site, download/install Event Tickets from the WordPress.org repo, and download/install Events Ticket Plus from theeventscalendar.com. Activate them both and you\'ll be good to go! If you\'re still confused or encounter problems, check out part 1 of our new user primer (http://m.tri.be/18ve).\";s:9:\"changelog\";s:1536:\"<p>= [4.10.3] 2019-04-23 =</p>\r\n\r\n<ul>\r\n<li>Tweak - Changed minimum supported version of The Events Calendar to 4.9</li>\r\n<li>Tweak - Allow menu order to be saved when saving tickets [121703]</li>\r\n<li>Tweak - Add hooks before WooCommerce and EDD Attendees are generated <code>tribe_tickets_plus_woo_before_generate_tickets</code> and <code>tribe_tickets_plus_edd_before_generate_tickets</code> [124675]</li>\r\n<li>Tweak - Add CSS for <code>tribe_events_modal</code> class when query string is present in URL [123818]</li>\r\n<li>Tweak - Modify the attendee meta to remove empty values, but keep zero and improve the escaping of data [123892]</li>\r\n<li>Tweak - Add support for display 0 when it is a value in attendee meta [123892]</li>\r\n<li>Tweak - Change Attendee Registration page options to use ID instead of page slug [124997]</li>\r\n<li>Tweak - Use new <code>tribe_attendee_registration_form_classes</code> hook to add form classes for EDD/WooCommerce [124997]</li>\r\n<li>Tweak - Added actions: <code>tribe_tickets_plus_woo_before_generate_tickets</code>, <code>tribe_tickets_plus_woo_before_generate_tickets</code></li>\r\n<li>Fix - Add Deleted Attendees Count to EDD and add checks for EDD/WooCommerce Tickets to only increase counter once per attendee [122083]</li>\r\n<li>Fix - Filter the Attendee Registration display to only show tickets for the current provider. Add functions to add provider to cart and Attendee Registration URL [122317]</li>\r\n<li>Language - 0 new strings added, 16 updated, 1 fuzzied, and 0 obsoleted</li>\r\n</ul>\";}s:14:\"upgrade_notice\";s:0:\"\";s:13:\"custom_update\";O:8:\"stdClass\":1:{s:5:\"icons\";O:8:\"stdClass\":1:{s:3:\"svg\";s:92:\"https://theeventscalendar.com/content/themes/tribe-ecp/img/svg/product-icons/ticketsplus.svg\";}}s:11:\"api_expired\";b:0;s:11:\"api_upgrade\";b:0;}}',	'no'),
(258,	'tribe_pue_key_notices',	'a:0:{}',	'yes'),
(296,	'woocommerce_store_address',	'100 rue de Turenne',	'yes'),
(297,	'woocommerce_store_address_2',	'apt 12 etage 4, code 6541',	'yes'),
(298,	'woocommerce_store_city',	'Paris',	'yes'),
(299,	'woocommerce_default_country',	'FR',	'yes'),
(300,	'woocommerce_store_postcode',	'75003',	'yes'),
(301,	'woocommerce_allowed_countries',	'all',	'yes'),
(302,	'woocommerce_all_except_countries',	'a:0:{}',	'yes'),
(303,	'woocommerce_specific_allowed_countries',	'a:0:{}',	'yes'),
(304,	'woocommerce_ship_to_countries',	'',	'yes'),
(305,	'woocommerce_specific_ship_to_countries',	'a:0:{}',	'yes'),
(306,	'woocommerce_default_customer_address',	'geolocation',	'yes'),
(307,	'woocommerce_calc_taxes',	'no',	'yes'),
(308,	'woocommerce_demo_store',	'no',	'yes'),
(309,	'woocommerce_demo_store_notice',	'This is a demo store for testing purposes &mdash; no orders shall be fulfilled.',	'no'),
(310,	'woocommerce_currency',	'GTQ',	'yes'),
(311,	'woocommerce_currency_pos',	'right',	'yes'),
(312,	'woocommerce_price_thousand_sep',	' ',	'yes'),
(313,	'woocommerce_price_decimal_sep',	',',	'yes'),
(314,	'woocommerce_price_num_decimals',	'2',	'yes'),
(315,	'woocommerce_weight_unit',	'kg',	'yes'),
(316,	'woocommerce_dimension_unit',	'cm',	'yes'),
(317,	'woocommerce_enable_reviews',	'yes',	'yes'),
(318,	'woocommerce_review_rating_verification_label',	'yes',	'no'),
(319,	'woocommerce_review_rating_verification_required',	'no',	'no'),
(320,	'woocommerce_enable_review_rating',	'yes',	'yes'),
(321,	'woocommerce_review_rating_required',	'yes',	'no'),
(322,	'woocommerce_shop_page_id',	'111',	'yes'),
(323,	'woocommerce_shop_page_display',	'',	'yes'),
(324,	'woocommerce_category_archive_display',	'',	'yes'),
(325,	'woocommerce_default_catalog_orderby',	'menu_order',	'yes'),
(326,	'woocommerce_cart_redirect_after_add',	'no',	'yes'),
(327,	'woocommerce_enable_ajax_add_to_cart',	'yes',	'yes'),
(328,	'shop_catalog_image_size',	'a:3:{s:5:\"width\";s:3:\"300\";s:6:\"height\";s:3:\"300\";s:4:\"crop\";i:1;}',	'yes'),
(329,	'shop_single_image_size',	'a:3:{s:5:\"width\";s:3:\"600\";s:6:\"height\";s:3:\"600\";s:4:\"crop\";i:1;}',	'yes'),
(330,	'shop_thumbnail_image_size',	'a:3:{s:5:\"width\";s:3:\"180\";s:6:\"height\";s:3:\"180\";s:4:\"crop\";i:1;}',	'yes'),
(331,	'woocommerce_manage_stock',	'yes',	'yes'),
(332,	'woocommerce_hold_stock_minutes',	'60',	'no'),
(333,	'woocommerce_notify_low_stock',	'yes',	'no'),
(334,	'woocommerce_notify_no_stock',	'yes',	'no'),
(335,	'woocommerce_stock_email_recipient',	'dev-email@flywheel.local',	'no'),
(336,	'woocommerce_notify_low_stock_amount',	'2',	'no'),
(337,	'woocommerce_notify_no_stock_amount',	'0',	'yes'),
(338,	'woocommerce_hide_out_of_stock_items',	'no',	'yes'),
(339,	'woocommerce_stock_format',	'',	'yes'),
(340,	'woocommerce_file_download_method',	'force',	'no'),
(341,	'woocommerce_downloads_require_login',	'no',	'no'),
(342,	'woocommerce_downloads_grant_access_after_payment',	'yes',	'no'),
(343,	'woocommerce_prices_include_tax',	'no',	'yes'),
(344,	'woocommerce_tax_based_on',	'shipping',	'yes'),
(345,	'woocommerce_shipping_tax_class',	'inherit',	'yes'),
(346,	'woocommerce_tax_round_at_subtotal',	'no',	'yes'),
(347,	'woocommerce_tax_classes',	'Reduced rate\nZero rate',	'yes'),
(348,	'woocommerce_tax_display_shop',	'excl',	'yes'),
(349,	'woocommerce_tax_display_cart',	'excl',	'no'),
(350,	'woocommerce_price_display_suffix',	'',	'yes'),
(351,	'woocommerce_tax_total_display',	'itemized',	'no'),
(352,	'woocommerce_enable_shipping_calc',	'yes',	'no'),
(353,	'woocommerce_shipping_cost_requires_address',	'no',	'no'),
(354,	'woocommerce_ship_to_destination',	'billing',	'no'),
(355,	'woocommerce_shipping_debug_mode',	'no',	'no'),
(356,	'woocommerce_enable_coupons',	'yes',	'yes'),
(357,	'woocommerce_calc_discounts_sequentially',	'no',	'no'),
(358,	'woocommerce_enable_guest_checkout',	'yes',	'no'),
(359,	'woocommerce_force_ssl_checkout',	'no',	'yes'),
(360,	'woocommerce_unforce_ssl_checkout',	'no',	'yes'),
(361,	'woocommerce_cart_page_id',	'112',	'yes'),
(362,	'woocommerce_checkout_page_id',	'113',	'yes'),
(363,	'woocommerce_terms_page_id',	'',	'no'),
(364,	'woocommerce_checkout_pay_endpoint',	'order-pay',	'yes'),
(365,	'woocommerce_checkout_order_received_endpoint',	'order-received',	'yes'),
(366,	'woocommerce_myaccount_add_payment_method_endpoint',	'add-payment-method',	'yes'),
(367,	'woocommerce_myaccount_delete_payment_method_endpoint',	'delete-payment-method',	'yes'),
(368,	'woocommerce_myaccount_set_default_payment_method_endpoint',	'set-default-payment-method',	'yes'),
(369,	'woocommerce_myaccount_page_id',	'114',	'yes'),
(370,	'woocommerce_enable_signup_and_login_from_checkout',	'yes',	'no'),
(371,	'woocommerce_enable_myaccount_registration',	'no',	'no'),
(372,	'woocommerce_enable_checkout_login_reminder',	'yes',	'no'),
(373,	'woocommerce_registration_generate_username',	'yes',	'no'),
(374,	'woocommerce_registration_generate_password',	'no',	'no'),
(375,	'woocommerce_myaccount_orders_endpoint',	'orders',	'yes'),
(376,	'woocommerce_myaccount_view_order_endpoint',	'view-order',	'yes'),
(377,	'woocommerce_myaccount_downloads_endpoint',	'downloads',	'yes'),
(378,	'woocommerce_myaccount_edit_account_endpoint',	'edit-account',	'yes'),
(379,	'woocommerce_myaccount_edit_address_endpoint',	'edit-address',	'yes'),
(380,	'woocommerce_myaccount_payment_methods_endpoint',	'payment-methods',	'yes'),
(381,	'woocommerce_myaccount_lost_password_endpoint',	'lost-password',	'yes'),
(382,	'woocommerce_logout_endpoint',	'customer-logout',	'yes'),
(383,	'woocommerce_email_from_name',	'commerce',	'no'),
(384,	'woocommerce_email_from_address',	'dev-email@flywheel.local',	'no'),
(385,	'woocommerce_email_header_image',	'',	'no'),
(386,	'woocommerce_email_footer_text',	'commerce',	'no'),
(387,	'woocommerce_email_base_color',	'#96588a',	'no'),
(388,	'woocommerce_email_background_color',	'#f7f7f7',	'no'),
(389,	'woocommerce_email_body_background_color',	'#ffffff',	'no'),
(390,	'woocommerce_email_text_color',	'#3c3c3c',	'no'),
(391,	'woocommerce_api_enabled',	'yes',	'yes'),
(397,	'woocommerce_admin_notices',	'a:0:{}',	'yes'),
(399,	'widget_woocommerce_widget_cart',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(400,	'widget_woocommerce_layered_nav_filters',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(401,	'widget_woocommerce_layered_nav',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(402,	'widget_woocommerce_price_filter',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(403,	'widget_woocommerce_product_categories',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(404,	'widget_woocommerce_product_search',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(405,	'widget_woocommerce_product_tag_cloud',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(406,	'widget_woocommerce_products',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(407,	'widget_woocommerce_recently_viewed_products',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(408,	'widget_woocommerce_top_rated_products',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(409,	'widget_woocommerce_recent_reviews',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(410,	'widget_woocommerce_rating_filter',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(413,	'woocommerce_meta_box_errors',	'a:0:{}',	'yes'),
(416,	'woocommerce_product_type',	'both',	'yes'),
(417,	'woocommerce_allow_tracking',	'no',	'yes'),
(418,	'woocommerce_stripe_settings',	'a:3:{s:7:\"enabled\";s:2:\"no\";s:14:\"create_account\";b:0;s:5:\"email\";b:0;}',	'yes'),
(419,	'woocommerce_ppec_paypal_settings',	'a:1:{s:7:\"enabled\";s:2:\"no\";}',	'yes'),
(420,	'woocommerce_paypal_settings',	'a:17:{s:7:\"enabled\";s:3:\"yes\";s:5:\"email\";s:29:\"merchant-sb@theaveragedev.com\";s:5:\"title\";s:6:\"PayPal\";s:11:\"description\";s:85:\"Pay via PayPal; you can pay with your credit card if you don\'t have a PayPal account.\";s:8:\"testmode\";s:3:\"yes\";s:5:\"debug\";s:2:\"no\";s:14:\"receiver_email\";s:29:\"merchant-sb@theaveragedev.com\";s:14:\"identity_token\";s:0:\"\";s:14:\"invoice_prefix\";s:3:\"WC-\";s:13:\"send_shipping\";s:2:\"no\";s:16:\"address_override\";s:2:\"no\";s:13:\"paymentaction\";s:4:\"sale\";s:10:\"page_style\";s:0:\"\";s:9:\"image_url\";s:0:\"\";s:12:\"api_username\";s:0:\"\";s:12:\"api_password\";s:0:\"\";s:13:\"api_signature\";s:0:\"\";}',	'yes'),
(455,	'edd_settings',	'a:11:{s:13:\"purchase_page\";i:924;s:12:\"success_page\";i:925;s:12:\"failure_page\";i:926;s:21:\"purchase_history_page\";i:927;s:9:\"test_mode\";s:1:\"1\";s:8:\"gateways\";a:1:{s:6:\"paypal\";s:1:\"1\";}s:15:\"default_gateway\";s:6:\"paypal\";s:12:\"paypal_email\";s:29:\"merchant-sb@theaveragedev.com\";s:27:\"disable_paypal_verification\";s:1:\"1\";s:8:\"currency\";s:3:\"GBP\";s:17:\"currency_position\";s:6:\"before\";}',	'yes'),
(456,	'edd_use_php_sessions',	'1',	'yes'),
(457,	'edd_version',	'2.8.18',	'yes'),
(460,	'edd_default_api_version',	'v2',	'yes'),
(461,	'wp_edd_customers_db_version',	'1.0',	'yes'),
(462,	'wp_edd_customermeta_db_version',	'1.0',	'yes'),
(465,	'edd_completed_upgrades',	'a:4:{i:0;s:21:\"upgrade_payment_taxes\";i:1;s:37:\"upgrade_customer_payments_association\";i:2;s:21:\"upgrade_user_api_keys\";i:3;s:25:\"remove_refunded_sale_logs\";}',	'yes'),
(467,	'widget_edd_cart_widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(468,	'widget_edd_categories_tags_widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(469,	'widget_edd_product_details',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(470,	'_edd_table_check',	'1519640883',	'yes'),
(471,	'edd_tracking_notice',	'1',	'yes'),
(485,	'edd_earnings_total',	'4',	'yes'),
(505,	'tribe_last_save_post',	'1519042218',	'yes'),
(506,	'widget_tribe-events-list-widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(635,	'pue_install_key_event_tickets_plus',	'f0c38a187457a9bb5b1079f4556f467883a223f5',	'yes'),
(636,	'pue_install_key_event_aggregator',	'57356dea635edffb54fb9bc821f500938f75e4bc',	'yes'),
(774,	'tribe_community_events_options',	'a:18:{s:17:\"maybeFlushRewrite\";b:0;s:25:\"allowAnonymousSubmissions\";b:0;s:18:\"prevent_new_venues\";b:0;s:22:\"prevent_new_organizers\";b:0;s:15:\"useVisualEditor\";b:0;s:13:\"defaultStatus\";s:5:\"draft\";s:20:\"communityRewriteSlug\";s:9:\"community\";s:18:\"emailAlertsEnabled\";b:0;s:15:\"emailAlertsList\";s:24:\"dev-email@flywheel.local\";s:27:\"allowUsersToEditSubmissions\";b:0;s:29:\"allowUsersToDeleteSubmissions\";b:0;s:18:\"trashItemsVsDelete\";s:1:\"1\";s:13:\"eventsPerPage\";s:2:\"10\";s:19:\"blockRolesFromAdmin\";b:0;s:14:\"blockRolesList\";N;s:18:\"blockRolesRedirect\";s:0:\"\";s:23:\"defaultCommunityVenueID\";s:1:\"0\";s:21:\"single_geography_mode\";b:0;}',	'yes'),
(775,	'WP_Router_route_hash',	'a9dc694584be3afbd3769eacbb582e16',	'yes'),
(776,	'Tribe__Events__Community__Schemaschema_version',	'3',	'yes'),
(789,	'tribe_community_events_tickets_options',	'a:17:{i:0;b:0;s:24:\"enable_community_tickets\";b:1;s:22:\"edit_event_tickets_cap\";b:1;s:20:\"enable_image_uploads\";b:0;s:14:\"purchase_limit\";s:1:\"3\";s:13:\"site_fee_type\";s:4:\"none\";s:19:\"site_fee_percentage\";s:0:\"\";s:13:\"site_fee_flat\";s:0:\"\";s:19:\"payment_fee_setting\";s:6:\"absorb\";s:21:\"enable_split_payments\";b:0;s:14:\"paypal_sandbox\";b:0;s:19:\"paypal_api_username\";s:0:\"\";s:19:\"paypal_api_password\";s:0:\"\";s:20:\"paypal_api_signature\";s:0:\"\";s:21:\"paypal_application_id\";s:0:\"\";s:21:\"paypal_receiver_email\";s:0:\"\";s:21:\"paypal_invoice_prefix\";s:3:\"CT-\";}',	'yes'),
(797,	'external_updates-events-community',	'O:8:\"stdClass\":3:{s:9:\"lastCheck\";i:1515414945;s:14:\"checkedVersion\";s:5:\"4.5.8\";s:6:\"update\";O:19:\"Tribe__PUE__Utility\":11:{s:2:\"id\";i:0;s:6:\"plugin\";s:43:\"events-community/tribe-community-events.php\";s:4:\"slug\";s:16:\"events-community\";s:7:\"version\";s:5:\"4.5.8\";s:8:\"homepage\";s:18:\"http://m.tri.be/4f\";s:12:\"download_url\";s:288:\"https://puestaging.tri.be/api/plugins/v2/download?plugin=events-community&version=4.5.8&installed_version=4.5.8&domain=commerce.dev&multisite=0&network_activated=0&active_sites=1&wp_version=4.9.1&key=5f4d13a417aa66540d390a3e61248f1fce5aa531&dk=5f4d13a417aa66540d390a3e61248f1fce5aa531&o=e\";s:8:\"sections\";O:8:\"stdClass\":3:{s:11:\"description\";s:268:\"Community Events is an extension to Modern Tribe\'s The Events Calendar, and enables frontend event submissions on your WordPress site. Site admins have complete control over submitted content and published submissions appear identical to events created on the backend.\";s:12:\"installation\";s:347:\"Installing Community Events is easy: just back up your site, download/install The Events Calendar from the WordPress.org repo, and download/install Community Events from theeventscalendar.com. Activate them both and you\'ll be good to go! If you\'re still confused or encounter problems, check out part 1 of our new user primer (http://m.tri.be/4m).\";s:9:\"changelog\";s:192:\"<p>= [4.5.8] 2017-10-04 =</p>\r\n\r\n<ul>\r\n<li>Fix - Fixed issues with the jQuery Timepicker vendor script conflicting with other plugins\' similar scripts (props: @hcny et al.) [74644]</li>\r\n</ul>\";}s:14:\"upgrade_notice\";s:321:\"This is a minor update from the previous release. All users are encouraged to backup their site before updating, and to apply the updates on a staging/test site where they can check on + fix customizations as needed before deploying to production. Existing customizations may break if you neglect to take this precaution.\";s:13:\"custom_update\";O:8:\"stdClass\":1:{s:5:\"icons\";O:8:\"stdClass\":1:{s:3:\"svg\";s:90:\"https://theeventscalendar.com/content/themes/tribe-ecp/img/svg/product-icons/community.svg\";}}s:11:\"api_expired\";b:0;s:11:\"api_upgrade\";b:0;}}',	'no'),
(798,	'external_updates-events-community-tickets',	'O:8:\"stdClass\":3:{s:9:\"lastCheck\";i:1515414945;s:14:\"checkedVersion\";s:5:\"4.5.3\";s:6:\"update\";O:19:\"Tribe__PUE__Utility\":11:{s:2:\"id\";i:0;s:6:\"plugin\";s:53:\"events-community-tickets/events-community-tickets.php\";s:4:\"slug\";s:24:\"events-community-tickets\";s:7:\"version\";s:5:\"4.5.3\";s:8:\"homepage\";s:20:\"http://m.tri.be/18m2\";s:12:\"download_url\";s:296:\"https://puestaging.tri.be/api/plugins/v2/download?plugin=events-community-tickets&version=4.5.3&installed_version=4.5.3&domain=commerce.dev&multisite=0&network_activated=0&active_sites=1&wp_version=4.9.1&key=d9bab542bb54baaa5912a84656b40e10a05bc066&dk=d9bab542bb54baaa5912a84656b40e10a05bc066&o=e\";s:8:\"sections\";O:8:\"stdClass\":3:{s:11:\"description\";s:235:\"Community Tickets is an add-on for The Events Calendar, Community Events, and WooCommerce Tickets. Users submitting events via Community Events can now add tickets to their events, and the site admin can collect fees from ticket sales.\";s:12:\"installation\";s:1806:\"1. From the dashboard of your site, navigate to Plugins --> Add New.\r\n2. Select the Upload option and hit \"Choose File.\"\r\n3. When the popup appears select the the-events-calendar-community-tickets.x.x.zip file from your desktop. (The \'x.x\' will change depending on the current version number).\r\n4. Follow the on-screen instructions and wait as the upload completes.\r\n5. When it\'s finished, activate the plugin via the prompt. A message will show confirming activation was successful.\r\n6. For access to new updates, make sure you have added your valid License Key under Events --> Settings --> Licenses.1. From the dashboard of your site, navigate to Plugins --> Add New.\r\n2. Select the Upload option and hit \"Choose File.\"\r\n3. When the popup appears select the the-events-calendar-community-tickets.x.x.zip file from your desktop. (The \'x.x\' will change depending on the current version number).\r\n4. Follow the on-screen instructions and wait as the upload completes.\r\n5. When it\'s finished, activate the plugin via the prompt. A message will show confirming activation was successful.\r\n6. For access to new updates, make sure you have added your valid License Key under Events --> Settings --> Licenses.1. From the dashboard of your site, navigate to Plugins --> Add New.\r\n2. Select the Upload option and hit \"Choose File.\"\r\n3. When the popup appears select the the-events-calendar-community-tickets.x.x.zip file from your desktop. (The \'x.x\' will change depending on the current version number).\r\n4. Follow the on-screen instructions and wait as the upload completes.\r\n5. When it\'s finished, activate the plugin via the prompt. A message will show confirming activation was successful.\r\n6. For access to new updates, make sure you have added your valid License Key under Events --> Settings --> Licenses.\";s:9:\"changelog\";s:12:\"change it up\";}s:14:\"upgrade_notice\";s:14:\"upgrade notice\";s:13:\"custom_update\";O:8:\"stdClass\":1:{s:5:\"icons\";O:8:\"stdClass\":1:{s:3:\"svg\";s:98:\"https://theeventscalendar.com/content/themes/tribe-ecp/img/svg/product-icons/community-tickets.svg\";}}s:11:\"api_expired\";b:0;s:11:\"api_upgrade\";b:0;}}',	'no'),
(902,	'edd_version_upgraded_from',	'2.8.18',	'yes'),
(1086,	'widget_tribe-events-adv-list-widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(1087,	'widget_tribe-events-countdown-widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(1088,	'widget_tribe-mini-calendar',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(1089,	'widget_tribe-events-venue-widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(1090,	'widget_tribe-this-week-events-widget',	'a:1:{s:12:\"_multiwidget\";i:1;}',	'yes'),
(1092,	'pue_install_key_tribe_filterbar',	'd0af577f31208f8cd1c166b6057d905f3104143d',	'yes'),
(1093,	'pue_install_key_events_calendar_pro',	'97f261bb1246e1c8ad0b7ace9ec6cb7764a09730',	'yes'),
(1099,	'external_updates-events-calendar-pro',	'O:8:\"stdClass\":3:{s:9:\"lastCheck\";i:1519036401;s:14:\"checkedVersion\";s:6:\"4.4.20\";s:6:\"update\";O:19:\"Tribe__PUE__Utility\":11:{s:2:\"id\";i:0;s:6:\"plugin\";s:34:\"events-pro/events-calendar-pro.php\";s:4:\"slug\";s:19:\"events-calendar-pro\";s:7:\"version\";s:3:\"4.5\";s:8:\"homepage\";s:18:\"http://m.tri.be/4d\";s:12:\"download_url\";s:290:\"https://puestaging.tri.be/api/plugins/v2/download?plugin=events-calendar-pro&version=4.5&installed_version=4.4.20&domain=commerce.dev&multisite=0&network_activated=0&active_sites=1&wp_version=4.9.4&key=97f261bb1246e1c8ad0b7ace9ec6cb7764a09730&dk=97f261bb1246e1c8ad0b7ace9ec6cb7764a09730&o=m\";s:8:\"sections\";O:8:\"stdClass\":3:{s:11:\"description\";s:220:\"Events Calendar PRO is an extension to Modern Tribe\'s The Events Calendar, and includes recurring events, additional frontend views and more. To see a full feature list please visit the product page (http://m.tri.be/4d).\";s:12:\"installation\";s:353:\"Installing Events Calendar PRO is easy: just back up your site, download/install The Events Calendar from the WordPress.org repo, and download/install Events Calendar PRO from theeventscalendar.com. Activate them both and you\'ll be good to go! If you\'re still confused or encounter problems, check out part 1 of our new user primer (http://m.tri.be/4i).\";s:9:\"changelog\";s:27:\"beautiful changelog for PRO\";}s:14:\"upgrade_notice\";s:24:\"PRO update - do it baby!\";s:13:\"custom_update\";O:8:\"stdClass\":1:{s:5:\"icons\";O:8:\"stdClass\":1:{s:3:\"svg\";s:84:\"https://theeventscalendar.com/content/themes/tribe-ecp/img/svg/product-icons/ECP.svg\";}}s:11:\"api_expired\";b:0;s:11:\"api_upgrade\";b:0;}}',	'no'),
(1100,	'external_updates-tribe-filterbar',	'O:8:\"stdClass\":3:{s:9:\"lastCheck\";i:1513160254;s:14:\"checkedVersion\";s:5:\"4.5.2\";s:6:\"update\";O:19:\"Tribe__PUE__Utility\":11:{s:2:\"id\";i:0;s:6:\"plugin\";s:52:\"events-filterbar/the-events-calendar-filter-view.php\";s:4:\"slug\";s:15:\"tribe-filterbar\";s:7:\"version\";s:5:\"4.5.1\";s:8:\"homepage\";s:18:\"http://m.tri.be/l1\";s:12:\"download_url\";s:274:\"https://puestaging.tri.be/api/plugins/v2/download?plugin=tribe-filterbar&version=4.5.1&installed_version=4.5.2&domain&multisite=0&network_activated=0&active_sites=1&wp_version=4.9.1&key=d0af577f31208f8cd1c166b6057d905f3104143d&dk=d0af577f31208f8cd1c166b6057d905f3104143d&o=m\";s:8:\"sections\";O:8:\"stdClass\":3:{s:11:\"description\";s:150:\"Filter Bar is an add-on for The Events Calendar/Events Calendar PRO, which creates an advanced filter panel on the frontend of your events list views.\";s:12:\"installation\";s:337:\"Installing Filter Bar is easy: just back up your site, download/install The Events Calendar 4.5.1 from the WordPress.org repo, and download/install Filter Bar 4.5.1 from theeventscalendar.com. Activate them both and you\'ll be good to go! If you\'re still confused or encounter problems, check out our new user primer (http://m.tri.be/fv).\";s:9:\"changelog\";s:392:\"<p>= [4.5.1] 2017-10-04 =</p>\r\n\r\n<ul>\r\n<li>Fix - Fixed some layout issues with the \"Show Filters\"/\"Collapse Filters\" toggle button in mobile views (props to @ergosom for reporting this!) [75373]</li>\r\n<li>Tweak - Improved performance by swapping out LEFT JOINs for INNER JOINs in SQL queries [88588]</li>\r\n<li>Language - 2 new strings added, 17 updated, 0 fuzzied, and 2 obsoleted</li>\r\n</ul>\";}s:14:\"upgrade_notice\";s:321:\"This is a major update from the previous release. All users are encouraged to backup their site before updating, and to apply the updates on a staging/test site where they can check on + fix customizations as needed before deploying to production. Existing customizations may break if you neglect to take this precaution.\";s:13:\"custom_update\";N;s:11:\"api_expired\";b:0;s:11:\"api_upgrade\";b:0;}}',	'no'),
(2342,	'auto_core_update_notified',	'a:4:{s:4:\"type\";s:7:\"success\";s:5:\"email\";s:24:\"dev-email@flywheel.local\";s:7:\"version\";s:5:\"4.9.2\";s:9:\"timestamp\";i:1516177418;}',	'no'),
(3791,	'woocommerce_permalinks',	'a:5:{s:12:\"product_base\";s:7:\"product\";s:13:\"category_base\";s:16:\"product-category\";s:8:\"tag_base\";s:11:\"product-tag\";s:14:\"attribute_base\";s:0:\"\";s:22:\"use_verbose_page_rules\";b:0;}',	'yes'),
(3792,	'current_theme_supports_woocommerce',	'1',	'yes'),
(3797,	'default_product_cat',	'15',	'yes'),
(3806,	'woocommerce_thumbnail_image_width',	'300',	'yes'),
(3807,	'woocommerce_thumbnail_cropping',	'1:1',	'yes'),
(3808,	'woocommerce_single_image_width',	'600',	'yes'),
(3917,	'WPLANG',	'',	'yes'),
(3918,	'new_admin_email',	'dev-email@flywheel.local',	'yes'),
(4075,	'woocommerce_version',	'3.3.1',	'yes'),
(4076,	'woocommerce_db_version',	'3.3.1',	'yes'),
(4181,	'_transient_edd_cache_excluded_uris',	'a:2:{i:0;s:5:\"p=924\";i:1;s:5:\"p=925\";}',	'yes'),
(4184,	'_transient_woocommerce_webhook_ids',	'a:0:{}',	'yes'),
(4189,	'_transient_wc_attribute_taxonomies',	'a:0:{}',	'yes'),
(4190,	'_transient_wc_count_comments',	'O:8:\"stdClass\":7:{s:14:\"total_comments\";i:0;s:3:\"all\";i:0;s:9:\"moderated\";i:0;s:8:\"approved\";i:0;s:4:\"spam\";i:0;s:5:\"trash\";i:0;s:12:\"post-trashed\";i:0;}',	'yes'),
(4200,	'_site_transient_update_plugins',	'O:8:\"stdClass\":5:{s:12:\"last_checked\";i:1557425798;s:7:\"checked\";a:37:{s:33:\"classic-editor/classic-editor.php\";s:3:\"1.5\";s:25:\"dev-plugin/dev-plugin.php\";s:2:\".5\";s:49:\"easy-digital-downloads/easy-digital-downloads.php\";s:6:\"2.9.14\";s:49:\"tribe-ext-eventbrite-additional-options/index.php\";s:5:\"1.0.1\";s:31:\"event-tickets/event-tickets.php\";s:6:\"4.10.5\";s:47:\"tribe-ext-pdf-tickets/tribe-ext-pdf-tickets.php\";s:5:\"1.2.0\";s:41:\"event-tickets-plus/event-tickets-plus.php\";s:6:\"4.10.4\";s:61:\"woocommerce-follow-up-emails/woocommerce-follow-up-emails.php\";s:6:\"4.4.19\";s:21:\"gigpress/gigpress.php\";s:6:\"2.3.23\";s:29:\"gravityforms/gravityforms.php\";s:7:\"2.2.1.2\";s:39:\"image-widget-plus/image-widget-plus.php\";s:5:\"1.0.3\";s:31:\"query-monitor/query-monitor.php\";s:5:\"3.3.4\";s:43:\"the-events-calendar/the-events-calendar.php\";s:7:\"4.9.1.1\";s:43:\"events-community/tribe-community-events.php\";s:7:\"4.6.1.2\";s:49:\"tribe-ext-community-google-maps-options/index.php\";s:5:\"1.0.0\";s:53:\"events-community-tickets/events-community-tickets.php\";s:7:\"4.6.1.2\";s:45:\"events-elasticsearch/events-elasticsearch.php\";s:5:\"1.0.2\";s:38:\"events-eventbrite/tribe-eventbrite.php\";s:5:\"4.6.2\";s:52:\"events-filterbar/the-events-calendar-filter-view.php\";s:5:\"4.8.1\";s:47:\"tribe-ext-ea-facebook/tribe-ext-ea-facebook.php\";s:5:\"1.0.1\";s:79:\"tribe-ext-instructor-linked-post-type/tribe-ext-instructor-linked-post-type.php\";s:5:\"1.0.1\";s:29:\"tribe-ext-relabeler/index.php\";s:5:\"1.0.1\";s:34:\"events-pro/events-calendar-pro.php\";s:7:\"4.7.0.1\";s:55:\"tribe-ext-map-show-all-events-with-same-venue/index.php\";s:3:\"1.1\";s:41:\"transients-manager/transients-manager.php\";s:5:\"1.7.7\";s:51:\"tribe-bulk-update-dates/tribe-bulk-update-dates.php\";s:2:\".5\";s:23:\"tribe-cli/tribe-cli.php\";s:5:\"0.2.7\";s:27:\"idea-garden/idea-garden.php\";s:5:\"0.0.1\";s:37:\"panel-builder/tribe-panel-builder.php\";s:3:\"3.1\";s:37:\"user-role-editor/user-role-editor.php\";s:6:\"4.50.2\";s:33:\"user-switching/user-switching.php\";s:5:\"1.5.0\";s:27:\"woocommerce/woocommerce.php\";s:5:\"3.6.2\";s:35:\"woocommerce-attach-me/attach-me.php\";s:4:\"16.1\";s:40:\"wordpress-beta-tester/wp-beta-tester.php\";s:5:\"2.0.4\";s:27:\"wp-crontrol/wp-crontrol.php\";s:5:\"1.7.1\";s:27:\"wp-rollback/wp-rollback.php\";s:3:\"1.6\";s:24:\"wordpress-seo/wp-seo.php\";s:6:\"11.1.1\";}s:8:\"response\";a:0:{}s:12:\"translations\";a:0:{}s:9:\"no_update\";a:14:{s:33:\"classic-editor/classic-editor.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:28:\"w.org/plugins/classic-editor\";s:4:\"slug\";s:14:\"classic-editor\";s:6:\"plugin\";s:33:\"classic-editor/classic-editor.php\";s:11:\"new_version\";s:3:\"1.5\";s:3:\"url\";s:45:\"https://wordpress.org/plugins/classic-editor/\";s:7:\"package\";s:61:\"https://downloads.wordpress.org/plugin/classic-editor.1.5.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/classic-editor/assets/icon-256x256.png?rev=1998671\";s:2:\"1x\";s:67:\"https://ps.w.org/classic-editor/assets/icon-128x128.png?rev=1998671\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:70:\"https://ps.w.org/classic-editor/assets/banner-1544x500.png?rev=1998671\";s:2:\"1x\";s:69:\"https://ps.w.org/classic-editor/assets/banner-772x250.png?rev=1998676\";}s:11:\"banners_rtl\";a:0:{}}s:49:\"easy-digital-downloads/easy-digital-downloads.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:36:\"w.org/plugins/easy-digital-downloads\";s:4:\"slug\";s:22:\"easy-digital-downloads\";s:6:\"plugin\";s:49:\"easy-digital-downloads/easy-digital-downloads.php\";s:11:\"new_version\";s:6:\"2.9.14\";s:3:\"url\";s:53:\"https://wordpress.org/plugins/easy-digital-downloads/\";s:7:\"package\";s:72:\"https://downloads.wordpress.org/plugin/easy-digital-downloads.2.9.14.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:74:\"https://ps.w.org/easy-digital-downloads/assets/icon-256x256.png?rev=971967\";s:2:\"1x\";s:66:\"https://ps.w.org/easy-digital-downloads/assets/icon.svg?rev=971968\";s:3:\"svg\";s:66:\"https://ps.w.org/easy-digital-downloads/assets/icon.svg?rev=971968\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:78:\"https://ps.w.org/easy-digital-downloads/assets/banner-1544x500.png?rev=1728279\";s:2:\"1x\";s:77:\"https://ps.w.org/easy-digital-downloads/assets/banner-772x250.png?rev=1728282\";}s:11:\"banners_rtl\";a:0:{}}s:31:\"event-tickets/event-tickets.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:27:\"w.org/plugins/event-tickets\";s:4:\"slug\";s:13:\"event-tickets\";s:6:\"plugin\";s:31:\"event-tickets/event-tickets.php\";s:11:\"new_version\";s:8:\"4.10.4.4\";s:3:\"url\";s:44:\"https://wordpress.org/plugins/event-tickets/\";s:7:\"package\";s:65:\"https://downloads.wordpress.org/plugin/event-tickets.4.10.4.4.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:66:\"https://ps.w.org/event-tickets/assets/icon-256x256.png?rev=2071486\";s:2:\"1x\";s:66:\"https://ps.w.org/event-tickets/assets/icon-128x128.png?rev=2071486\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:69:\"https://ps.w.org/event-tickets/assets/banner-1544x500.png?rev=2048309\";s:2:\"1x\";s:68:\"https://ps.w.org/event-tickets/assets/banner-772x250.png?rev=2048309\";}s:11:\"banners_rtl\";a:0:{}}s:21:\"gigpress/gigpress.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:22:\"w.org/plugins/gigpress\";s:4:\"slug\";s:8:\"gigpress\";s:6:\"plugin\";s:21:\"gigpress/gigpress.php\";s:11:\"new_version\";s:6:\"2.3.23\";s:3:\"url\";s:39:\"https://wordpress.org/plugins/gigpress/\";s:7:\"package\";s:58:\"https://downloads.wordpress.org/plugin/gigpress.2.3.23.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:60:\"https://ps.w.org/gigpress/assets/icon-256x256.jpg?rev=979213\";s:2:\"1x\";s:60:\"https://ps.w.org/gigpress/assets/icon-128x128.jpg?rev=979213\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:63:\"https://ps.w.org/gigpress/assets/banner-1544x500.jpg?rev=979213\";s:2:\"1x\";s:62:\"https://ps.w.org/gigpress/assets/banner-772x250.jpg?rev=979213\";}s:11:\"banners_rtl\";a:0:{}}s:31:\"query-monitor/query-monitor.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:27:\"w.org/plugins/query-monitor\";s:4:\"slug\";s:13:\"query-monitor\";s:6:\"plugin\";s:31:\"query-monitor/query-monitor.php\";s:11:\"new_version\";s:5:\"3.3.4\";s:3:\"url\";s:44:\"https://wordpress.org/plugins/query-monitor/\";s:7:\"package\";s:62:\"https://downloads.wordpress.org/plugin/query-monitor.3.3.4.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:66:\"https://ps.w.org/query-monitor/assets/icon-256x256.png?rev=2056073\";s:2:\"1x\";s:58:\"https://ps.w.org/query-monitor/assets/icon.svg?rev=2056073\";s:3:\"svg\";s:58:\"https://ps.w.org/query-monitor/assets/icon.svg?rev=2056073\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:69:\"https://ps.w.org/query-monitor/assets/banner-1544x500.png?rev=1629576\";s:2:\"1x\";s:68:\"https://ps.w.org/query-monitor/assets/banner-772x250.png?rev=1731469\";}s:11:\"banners_rtl\";a:0:{}}s:43:\"the-events-calendar/the-events-calendar.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:33:\"w.org/plugins/the-events-calendar\";s:4:\"slug\";s:19:\"the-events-calendar\";s:6:\"plugin\";s:43:\"the-events-calendar/the-events-calendar.php\";s:11:\"new_version\";s:7:\"4.9.1.1\";s:3:\"url\";s:50:\"https://wordpress.org/plugins/the-events-calendar/\";s:7:\"package\";s:70:\"https://downloads.wordpress.org/plugin/the-events-calendar.4.9.1.1.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:72:\"https://ps.w.org/the-events-calendar/assets/icon-256x256.png?rev=2071468\";s:2:\"1x\";s:72:\"https://ps.w.org/the-events-calendar/assets/icon-128x128.png?rev=2071468\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:75:\"https://ps.w.org/the-events-calendar/assets/banner-1544x500.png?rev=2048291\";s:2:\"1x\";s:74:\"https://ps.w.org/the-events-calendar/assets/banner-772x250.png?rev=2048291\";}s:11:\"banners_rtl\";a:0:{}}s:41:\"transients-manager/transients-manager.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:32:\"w.org/plugins/transients-manager\";s:4:\"slug\";s:18:\"transients-manager\";s:6:\"plugin\";s:41:\"transients-manager/transients-manager.php\";s:11:\"new_version\";s:5:\"1.7.7\";s:3:\"url\";s:49:\"https://wordpress.org/plugins/transients-manager/\";s:7:\"package\";s:67:\"https://downloads.wordpress.org/plugin/transients-manager.1.7.7.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:71:\"https://ps.w.org/transients-manager/assets/icon-256x256.png?rev=1671074\";s:2:\"1x\";s:63:\"https://ps.w.org/transients-manager/assets/icon.svg?rev=1671074\";s:3:\"svg\";s:63:\"https://ps.w.org/transients-manager/assets/icon.svg?rev=1671074\";}s:7:\"banners\";a:1:{s:2:\"1x\";s:73:\"https://ps.w.org/transients-manager/assets/banner-772x250.png?rev=1671074\";}s:11:\"banners_rtl\";a:0:{}}s:37:\"user-role-editor/user-role-editor.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:30:\"w.org/plugins/user-role-editor\";s:4:\"slug\";s:16:\"user-role-editor\";s:6:\"plugin\";s:37:\"user-role-editor/user-role-editor.php\";s:11:\"new_version\";s:6:\"4.50.2\";s:3:\"url\";s:47:\"https://wordpress.org/plugins/user-role-editor/\";s:7:\"package\";s:66:\"https://downloads.wordpress.org/plugin/user-role-editor.4.50.2.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:69:\"https://ps.w.org/user-role-editor/assets/icon-256x256.jpg?rev=1020390\";s:2:\"1x\";s:69:\"https://ps.w.org/user-role-editor/assets/icon-128x128.jpg?rev=1020390\";}s:7:\"banners\";a:1:{s:2:\"1x\";s:71:\"https://ps.w.org/user-role-editor/assets/banner-772x250.png?rev=1263116\";}s:11:\"banners_rtl\";a:0:{}}s:33:\"user-switching/user-switching.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:28:\"w.org/plugins/user-switching\";s:4:\"slug\";s:14:\"user-switching\";s:6:\"plugin\";s:33:\"user-switching/user-switching.php\";s:11:\"new_version\";s:5:\"1.5.0\";s:3:\"url\";s:45:\"https://wordpress.org/plugins/user-switching/\";s:7:\"package\";s:63:\"https://downloads.wordpress.org/plugin/user-switching.1.5.0.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:67:\"https://ps.w.org/user-switching/assets/icon-256x256.png?rev=2031882\";s:2:\"1x\";s:59:\"https://ps.w.org/user-switching/assets/icon.svg?rev=2032062\";s:3:\"svg\";s:59:\"https://ps.w.org/user-switching/assets/icon.svg?rev=2032062\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:70:\"https://ps.w.org/user-switching/assets/banner-1544x500.png?rev=1364546\";s:2:\"1x\";s:69:\"https://ps.w.org/user-switching/assets/banner-772x250.png?rev=1216665\";}s:11:\"banners_rtl\";a:1:{s:2:\"2x\";s:74:\"https://ps.w.org/user-switching/assets/banner-1544x500-rtl.png?rev=2032062\";}}s:27:\"woocommerce/woocommerce.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:25:\"w.org/plugins/woocommerce\";s:4:\"slug\";s:11:\"woocommerce\";s:6:\"plugin\";s:27:\"woocommerce/woocommerce.php\";s:11:\"new_version\";s:5:\"3.6.2\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/woocommerce/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/plugin/woocommerce.3.6.2.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:64:\"https://ps.w.org/woocommerce/assets/icon-256x256.png?rev=2075035\";s:2:\"1x\";s:64:\"https://ps.w.org/woocommerce/assets/icon-128x128.png?rev=2075035\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/woocommerce/assets/banner-1544x500.png?rev=2075035\";s:2:\"1x\";s:66:\"https://ps.w.org/woocommerce/assets/banner-772x250.png?rev=2075035\";}s:11:\"banners_rtl\";a:0:{}}s:40:\"wordpress-beta-tester/wp-beta-tester.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:35:\"w.org/plugins/wordpress-beta-tester\";s:4:\"slug\";s:21:\"wordpress-beta-tester\";s:6:\"plugin\";s:40:\"wordpress-beta-tester/wp-beta-tester.php\";s:11:\"new_version\";s:5:\"2.0.4\";s:3:\"url\";s:52:\"https://wordpress.org/plugins/wordpress-beta-tester/\";s:7:\"package\";s:70:\"https://downloads.wordpress.org/plugin/wordpress-beta-tester.2.0.4.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:74:\"https://ps.w.org/wordpress-beta-tester/assets/icon-256x256.png?rev=2074117\";s:2:\"1x\";s:74:\"https://ps.w.org/wordpress-beta-tester/assets/icon-128x128.png?rev=2074117\";}s:7:\"banners\";a:0:{}s:11:\"banners_rtl\";a:0:{}}s:27:\"wp-crontrol/wp-crontrol.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:25:\"w.org/plugins/wp-crontrol\";s:4:\"slug\";s:11:\"wp-crontrol\";s:6:\"plugin\";s:27:\"wp-crontrol/wp-crontrol.php\";s:11:\"new_version\";s:5:\"1.7.1\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/wp-crontrol/\";s:7:\"package\";s:60:\"https://downloads.wordpress.org/plugin/wp-crontrol.1.7.1.zip\";s:5:\"icons\";a:1:{s:7:\"default\";s:62:\"https://s.w.org/plugins/geopattern-icon/wp-crontrol_dfc98b.svg\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:67:\"https://ps.w.org/wp-crontrol/assets/banner-1544x500.jpg?rev=2051757\";s:2:\"1x\";s:66:\"https://ps.w.org/wp-crontrol/assets/banner-772x250.jpg?rev=2051757\";}s:11:\"banners_rtl\";a:0:{}}s:27:\"wp-rollback/wp-rollback.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:25:\"w.org/plugins/wp-rollback\";s:4:\"slug\";s:11:\"wp-rollback\";s:6:\"plugin\";s:27:\"wp-rollback/wp-rollback.php\";s:11:\"new_version\";s:3:\"1.6\";s:3:\"url\";s:42:\"https://wordpress.org/plugins/wp-rollback/\";s:7:\"package\";s:58:\"https://downloads.wordpress.org/plugin/wp-rollback.1.6.zip\";s:5:\"icons\";a:2:{s:2:\"2x\";s:64:\"https://ps.w.org/wp-rollback/assets/icon-256x256.jpg?rev=1159170\";s:2:\"1x\";s:64:\"https://ps.w.org/wp-rollback/assets/icon-128x128.jpg?rev=1159170\";}s:7:\"banners\";a:1:{s:2:\"1x\";s:66:\"https://ps.w.org/wp-rollback/assets/banner-772x250.jpg?rev=1948781\";}s:11:\"banners_rtl\";a:0:{}}s:24:\"wordpress-seo/wp-seo.php\";O:8:\"stdClass\":9:{s:2:\"id\";s:27:\"w.org/plugins/wordpress-seo\";s:4:\"slug\";s:13:\"wordpress-seo\";s:6:\"plugin\";s:24:\"wordpress-seo/wp-seo.php\";s:11:\"new_version\";s:6:\"11.1.1\";s:3:\"url\";s:44:\"https://wordpress.org/plugins/wordpress-seo/\";s:7:\"package\";s:63:\"https://downloads.wordpress.org/plugin/wordpress-seo.11.1.1.zip\";s:5:\"icons\";a:3:{s:2:\"2x\";s:66:\"https://ps.w.org/wordpress-seo/assets/icon-256x256.png?rev=1834347\";s:2:\"1x\";s:58:\"https://ps.w.org/wordpress-seo/assets/icon.svg?rev=1946641\";s:3:\"svg\";s:58:\"https://ps.w.org/wordpress-seo/assets/icon.svg?rev=1946641\";}s:7:\"banners\";a:2:{s:2:\"2x\";s:69:\"https://ps.w.org/wordpress-seo/assets/banner-1544x500.png?rev=1843435\";s:2:\"1x\";s:68:\"https://ps.w.org/wordpress-seo/assets/banner-772x250.png?rev=1843435\";}s:11:\"banners_rtl\";a:2:{s:2:\"2x\";s:73:\"https://ps.w.org/wordpress-seo/assets/banner-1544x500-rtl.png?rev=1843435\";s:2:\"1x\";s:72:\"https://ps.w.org/wordpress-seo/assets/banner-772x250-rtl.png?rev=1843435\";}}}}',	'no'),
(4201,	'_site_transient_update_themes',	'O:8:\"stdClass\":4:{s:12:\"last_checked\";i:1557425798;s:7:\"checked\";a:9:{s:5:\"Avada\";s:5:\"5.4.2\";s:4:\"Divi\";s:6:\"3.0.51\";s:13:\"genesis-child\";s:5:\"1.0.8\";s:7:\"genesis\";s:6:\"2.10.1\";s:13:\"twentyfifteen\";s:3:\"2.5\";s:15:\"twentyseventeen\";s:3:\"2.2\";s:13:\"twentysixteen\";s:3:\"2.0\";s:13:\"weaver-xtreme\";s:7:\"4.3.1.4\";s:1:\"x\";s:5:\"5.2.3\";}s:8:\"response\";a:0:{}s:12:\"translations\";a:0:{}}',	'no'),
(4222,	'_transient_is_multi_author',	'0',	'yes'),
(4229,	'rewrite_rules',	'a:107:{s:21:\"tickets/([0-9]{1,})/?\";s:43:\"index.php?p=$matches[1]&tribe-edit-orders=1\";s:29:\"(?:attendee\\-registration)/?$\";s:33:\"index.php?attendee-registration=1\";s:11:\"^wp-json/?$\";s:22:\"index.php?rest_route=/\";s:14:\"^wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:21:\"^index.php/wp-json/?$\";s:22:\"index.php?rest_route=/\";s:24:\"^index.php/wp-json/(.*)?\";s:33:\"index.php?rest_route=/$matches[1]\";s:22:\"tribe-promoter-auth/?$\";s:37:\"index.php?tribe-promoter-auth-check=1\";s:47:\"category/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:42:\"category/(.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:52:\"index.php?category_name=$matches[1]&feed=$matches[2]\";s:23:\"category/(.+?)/embed/?$\";s:46:\"index.php?category_name=$matches[1]&embed=true\";s:35:\"category/(.+?)/page/?([0-9]{1,})/?$\";s:53:\"index.php?category_name=$matches[1]&paged=$matches[2]\";s:17:\"category/(.+?)/?$\";s:35:\"index.php?category_name=$matches[1]\";s:44:\"tag/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:39:\"tag/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?tag=$matches[1]&feed=$matches[2]\";s:20:\"tag/([^/]+)/embed/?$\";s:36:\"index.php?tag=$matches[1]&embed=true\";s:32:\"tag/([^/]+)/page/?([0-9]{1,})/?$\";s:43:\"index.php?tag=$matches[1]&paged=$matches[2]\";s:14:\"tag/([^/]+)/?$\";s:25:\"index.php?tag=$matches[1]\";s:45:\"type/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:40:\"type/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?post_format=$matches[1]&feed=$matches[2]\";s:21:\"type/([^/]+)/embed/?$\";s:44:\"index.php?post_format=$matches[1]&embed=true\";s:33:\"type/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?post_format=$matches[1]&paged=$matches[2]\";s:15:\"type/([^/]+)/?$\";s:33:\"index.php?post_format=$matches[1]\";s:48:\"ticket-meta-fieldset/[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:58:\"ticket-meta-fieldset/[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:78:\"ticket-meta-fieldset/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:73:\"ticket-meta-fieldset/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:73:\"ticket-meta-fieldset/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:54:\"ticket-meta-fieldset/[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:37:\"ticket-meta-fieldset/([^/]+)/embed/?$\";s:53:\"index.php?ticket-meta-fieldset=$matches[1]&embed=true\";s:41:\"ticket-meta-fieldset/([^/]+)/trackback/?$\";s:47:\"index.php?ticket-meta-fieldset=$matches[1]&tb=1\";s:49:\"ticket-meta-fieldset/([^/]+)/page/?([0-9]{1,})/?$\";s:60:\"index.php?ticket-meta-fieldset=$matches[1]&paged=$matches[2]\";s:56:\"ticket-meta-fieldset/([^/]+)/comment-page-([0-9]{1,})/?$\";s:60:\"index.php?ticket-meta-fieldset=$matches[1]&cpage=$matches[2]\";s:45:\"ticket-meta-fieldset/([^/]+)(?:/([0-9]+))?/?$\";s:59:\"index.php?ticket-meta-fieldset=$matches[1]&page=$matches[2]\";s:37:\"ticket-meta-fieldset/[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:47:\"ticket-meta-fieldset/[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:67:\"ticket-meta-fieldset/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:62:\"ticket-meta-fieldset/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:62:\"ticket-meta-fieldset/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:43:\"ticket-meta-fieldset/[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:12:\"robots\\.txt$\";s:18:\"index.php?robots=1\";s:48:\".*wp-(atom|rdf|rss|rss2|feed|commentsrss2)\\.php$\";s:18:\"index.php?feed=old\";s:20:\".*wp-app\\.php(/.*)?$\";s:19:\"index.php?error=403\";s:18:\".*wp-register.php$\";s:23:\"index.php?register=true\";s:32:\"feed/(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:27:\"(feed|rdf|rss|rss2|atom)/?$\";s:27:\"index.php?&feed=$matches[1]\";s:8:\"embed/?$\";s:21:\"index.php?&embed=true\";s:20:\"page/?([0-9]{1,})/?$\";s:28:\"index.php?&paged=$matches[1]\";s:41:\"comments/feed/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:36:\"comments/(feed|rdf|rss|rss2|atom)/?$\";s:42:\"index.php?&feed=$matches[1]&withcomments=1\";s:17:\"comments/embed/?$\";s:21:\"index.php?&embed=true\";s:44:\"search/(.+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:39:\"search/(.+)/(feed|rdf|rss|rss2|atom)/?$\";s:40:\"index.php?s=$matches[1]&feed=$matches[2]\";s:20:\"search/(.+)/embed/?$\";s:34:\"index.php?s=$matches[1]&embed=true\";s:32:\"search/(.+)/page/?([0-9]{1,})/?$\";s:41:\"index.php?s=$matches[1]&paged=$matches[2]\";s:14:\"search/(.+)/?$\";s:23:\"index.php?s=$matches[1]\";s:47:\"author/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:42:\"author/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:50:\"index.php?author_name=$matches[1]&feed=$matches[2]\";s:23:\"author/([^/]+)/embed/?$\";s:44:\"index.php?author_name=$matches[1]&embed=true\";s:35:\"author/([^/]+)/page/?([0-9]{1,})/?$\";s:51:\"index.php?author_name=$matches[1]&paged=$matches[2]\";s:17:\"author/([^/]+)/?$\";s:33:\"index.php?author_name=$matches[1]\";s:69:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:64:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:80:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]\";s:45:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/embed/?$\";s:74:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&embed=true\";s:57:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:81:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]\";s:39:\"([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$\";s:63:\"index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]\";s:56:\"([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:51:\"([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$\";s:64:\"index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]\";s:32:\"([0-9]{4})/([0-9]{1,2})/embed/?$\";s:58:\"index.php?year=$matches[1]&monthnum=$matches[2]&embed=true\";s:44:\"([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$\";s:65:\"index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]\";s:26:\"([0-9]{4})/([0-9]{1,2})/?$\";s:47:\"index.php?year=$matches[1]&monthnum=$matches[2]\";s:43:\"([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:38:\"([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?year=$matches[1]&feed=$matches[2]\";s:19:\"([0-9]{4})/embed/?$\";s:37:\"index.php?year=$matches[1]&embed=true\";s:31:\"([0-9]{4})/page/?([0-9]{1,})/?$\";s:44:\"index.php?year=$matches[1]&paged=$matches[2]\";s:13:\"([0-9]{4})/?$\";s:26:\"index.php?year=$matches[1]\";s:27:\".?.+?/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\".?.+?/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\".?.+?/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\".?.+?/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\".?.+?/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"(.?.+?)/embed/?$\";s:41:\"index.php?pagename=$matches[1]&embed=true\";s:20:\"(.?.+?)/trackback/?$\";s:35:\"index.php?pagename=$matches[1]&tb=1\";s:40:\"(.?.+?)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:35:\"(.?.+?)/(feed|rdf|rss|rss2|atom)/?$\";s:47:\"index.php?pagename=$matches[1]&feed=$matches[2]\";s:28:\"(.?.+?)/page/?([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&paged=$matches[2]\";s:35:\"(.?.+?)/comment-page-([0-9]{1,})/?$\";s:48:\"index.php?pagename=$matches[1]&cpage=$matches[2]\";s:24:\"(.?.+?)(?:/([0-9]+))?/?$\";s:47:\"index.php?pagename=$matches[1]&page=$matches[2]\";s:27:\"[^/]+/attachment/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:37:\"[^/]+/attachment/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:57:\"[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:52:\"[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:33:\"[^/]+/attachment/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";s:16:\"([^/]+)/embed/?$\";s:37:\"index.php?name=$matches[1]&embed=true\";s:20:\"([^/]+)/trackback/?$\";s:31:\"index.php?name=$matches[1]&tb=1\";s:40:\"([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:35:\"([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:43:\"index.php?name=$matches[1]&feed=$matches[2]\";s:28:\"([^/]+)/page/?([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&paged=$matches[2]\";s:35:\"([^/]+)/comment-page-([0-9]{1,})/?$\";s:44:\"index.php?name=$matches[1]&cpage=$matches[2]\";s:24:\"([^/]+)(?:/([0-9]+))?/?$\";s:43:\"index.php?name=$matches[1]&page=$matches[2]\";s:16:\"[^/]+/([^/]+)/?$\";s:32:\"index.php?attachment=$matches[1]\";s:26:\"[^/]+/([^/]+)/trackback/?$\";s:37:\"index.php?attachment=$matches[1]&tb=1\";s:46:\"[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$\";s:49:\"index.php?attachment=$matches[1]&feed=$matches[2]\";s:41:\"[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$\";s:50:\"index.php?attachment=$matches[1]&cpage=$matches[2]\";s:22:\"[^/]+/([^/]+)/embed/?$\";s:43:\"index.php?attachment=$matches[1]&embed=true\";}',	'yes'),
(4233,	'_transient_timeout_tribe_feature_detection',	'1558030596',	'no'),
(4234,	'_transient_tribe_feature_detection',	'a:1:{s:22:\"supports_async_process\";b:1;}',	'no'),
(4236,	'_site_transient_timeout_theme_roots',	'1557427598',	'no'),
(4237,	'_site_transient_theme_roots',	'a:9:{s:5:\"Avada\";s:7:\"/themes\";s:4:\"Divi\";s:7:\"/themes\";s:13:\"genesis-child\";s:7:\"/themes\";s:7:\"genesis\";s:7:\"/themes\";s:13:\"twentyfifteen\";s:7:\"/themes\";s:15:\"twentyseventeen\";s:7:\"/themes\";s:13:\"twentysixteen\";s:7:\"/themes\";s:13:\"weaver-xtreme\";s:7:\"/themes\";s:1:\"x\";s:7:\"/themes\";}',	'no'),
(4238,	'recovery_keys',	'a:0:{}',	'yes'),
(4239,	'wp_page_for_privacy_policy',	'0',	'yes'),
(4240,	'show_comments_cookies_opt_in',	'1',	'yes'),
(4241,	'db_upgraded',	'',	'yes');

DROP TABLE IF EXISTS `wp_postmeta`;
CREATE TABLE `wp_postmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `post_id` (`post_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_posts`;
CREATE TABLE `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_author` bigint(20) unsigned NOT NULL DEFAULT '0',
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_title` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_excerpt` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'open',
  `post_password` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `post_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `to_ping` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `pinged` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content_filtered` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `post_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT '0',
  `post_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `post_name` (`post_name`(191)),
  KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  KEY `post_parent` (`post_parent`),
  KEY `post_author` (`post_author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_termmeta`;
CREATE TABLE `wp_termmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `term_id` (`term_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_terms`;
CREATE TABLE `wp_terms` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `term_group` bigint(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_id`),
  KEY `slug` (`slug`(191)),
  KEY `name` (`name`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_terms` (`term_id`, `name`, `slug`, `term_group`) VALUES
(1,	'Uncategorized',	'uncategorized',	0);

DROP TABLE IF EXISTS `wp_term_relationships`;
CREATE TABLE `wp_term_relationships` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_taxonomy_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  KEY `term_taxonomy_id` (`term_taxonomy_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_term_taxonomy`;
CREATE TABLE `wp_term_taxonomy` (
  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `taxonomy` varchar(32) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `count` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`term_taxonomy_id`),
  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  KEY `taxonomy` (`taxonomy`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_term_taxonomy` (`term_taxonomy_id`, `term_id`, `taxonomy`, `description`, `parent`, `count`) VALUES
(1,	1,	'category',	'',	0,	1);

DROP TABLE IF EXISTS `wp_usermeta`;
CREATE TABLE `wp_usermeta` (
  `umeta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`umeta_id`),
  KEY `user_id` (`user_id`),
  KEY `meta_key` (`meta_key`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_usermeta` (`umeta_id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1,	1,	'nickname',	'admin'),
(2,	1,	'first_name',	'Luca'),
(3,	1,	'last_name',	'Tumedei'),
(4,	1,	'description',	''),
(5,	1,	'rich_editing',	'true'),
(6,	1,	'syntax_highlighting',	'true'),
(7,	1,	'comment_shortcuts',	'false'),
(8,	1,	'admin_color',	'fresh'),
(9,	1,	'use_ssl',	'0'),
(10,	1,	'show_admin_bar_front',	'true'),
(11,	1,	'locale',	''),
(12,	1,	'wp_capabilities',	'a:1:{s:13:\"administrator\";b:1;}'),
(13,	1,	'wp_user_level',	'10'),
(14,	1,	'dismissed_wp_pointers',	'attendees_filters'),
(15,	1,	'show_welcome_panel',	'0'),
(16,	1,	'session_tokens',	'a:1:{s:64:\"69d0e3a6d91cf85082331b2cdf240e351836bfa9381c35012f8e1c1d96ef2b6b\";a:4:{s:10:\"expiration\";i:1557598596;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:105:\"Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.108 Safari/537.36\";s:5:\"login\";i:1557425796;}}'),
(17,	1,	'wp_dashboard_quick_press_last_post_id',	'1119'),
(18,	1,	'community-events-location',	'a:1:{s:2:\"ip\";s:12:\"192.168.92.0\";}'),
(20,	1,	'_edd_nginx_redirect_dismissed',	'1'),
(21,	1,	'last_update',	'1512654182'),
(22,	1,	'_edd_user_address',	'a:0:{}'),
(23,	1,	'billing_first_name',	'Luca'),
(24,	1,	'billing_last_name',	'Tumedei'),
(25,	1,	'billing_address_1',	'100 rue de Turenne'),
(26,	1,	'billing_address_2',	'apt 12 etage 4, code 6541'),
(27,	1,	'billing_city',	'Paris'),
(28,	1,	'billing_postcode',	'75003'),
(29,	1,	'billing_country',	'FR'),
(30,	1,	'billing_email',	'luca.tumedei@gmail.com'),
(31,	1,	'billing_phone',	'+33633810141'),
(32,	1,	'shipping_method',	''),
(34,	1,	'_woocommerce_persistent_cart_1',	'a:1:{s:4:\"cart\";a:0:{}}'),
(35,	1,	'paying_customer',	'1'),
(36,	1,	'_order_count',	'1'),
(37,	1,	'_money_spent',	'9'),
(38,	1,	'wp_user-settings',	'libraryContent=browse'),
(39,	1,	'wp_user-settings-time',	'1512754218'),
(40,	1,	'closedpostboxes_post',	'a:0:{}'),
(41,	1,	'metaboxhidden_post',	'a:7:{i:0;s:11:\"postexcerpt\";i:1;s:13:\"trackbacksdiv\";i:2;s:10:\"postcustom\";i:3;s:16:\"commentstatusdiv\";i:4;s:11:\"commentsdiv\";i:5;s:7:\"slugdiv\";i:6;s:9:\"authordiv\";}'),
(42,	1,	'meta-box-order_post',	'a:3:{s:4:\"side\";s:61:\"submitdiv,formatdiv,categorydiv,tagsdiv-post_tag,postimagediv\";s:6:\"normal\";s:96:\"tribetickets,postexcerpt,trackbacksdiv,postcustom,commentstatusdiv,commentsdiv,slugdiv,authordiv\";s:8:\"advanced\";s:0:\"\";}'),
(43,	1,	'screen_layout_post',	'2'),
(49,	1,	'tribe-dismiss-notice',	'event-tickets-plus-missing-woocommerce-support'),
(50,	1,	'event_tickets_attendees_per_page',	'50'),
(51,	1,	'event_tickets_paypal_orders_per_page',	'50'),
(52,	1,	'closedpostboxes_tribe_events',	'a:5:{i:0;s:26:\"tribe_events_event_details\";i:1;s:11:\"postexcerpt\";i:2;s:10:\"postcustom\";i:3;s:16:\"commentstatusdiv\";i:4;s:9:\"authordiv\";}'),
(53,	1,	'metaboxhidden_tribe_events',	'a:1:{i:0;s:7:\"slugdiv\";}'),
(54,	1,	'meta-box-order_tribe_events',	'a:4:{s:4:\"side\";s:86:\"submitdiv,tagsdiv-post_tag,tribe_events_catdiv,tribe_events_event_options,postimagediv\";s:6:\"normal\";s:122:\"tribetickets,tribe_events_event_details,postexcerpt,postcustom,commentstatusdiv,slugdiv,authordiv,revisionsdiv,commentsdiv\";s:5:\"tribe\";s:16:\"field-1132171157\";s:8:\"advanced\";s:0:\"\";}'),
(55,	1,	'screen_layout_tribe_events',	'2'),
(56,	2,	'nickname',	'editor'),
(57,	2,	'first_name',	'edi'),
(58,	2,	'last_name',	'tor'),
(59,	2,	'description',	''),
(60,	2,	'rich_editing',	'true'),
(61,	2,	'syntax_highlighting',	'true'),
(62,	2,	'comment_shortcuts',	'false'),
(63,	2,	'admin_color',	'fresh'),
(64,	2,	'use_ssl',	'0'),
(65,	2,	'show_admin_bar_front',	'true'),
(66,	2,	'locale',	''),
(67,	2,	'wp_capabilities',	'a:1:{s:6:\"editor\";b:1;}'),
(68,	2,	'wp_user_level',	'7'),
(69,	2,	'dismissed_wp_pointers',	''),
(70,	2,	'session_tokens',	'a:11:{s:64:\"85149c07eec2c0c3ce2bc5515fcc9148cce638e543538d50d95cfd9cc966b98f\";a:4:{s:10:\"expiration\";i:1515321311;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515148511;}s:64:\"1c8fb916afdb6530fe535de0b114a838432f68d5ffb4a8491dea0dddfe77410d\";a:4:{s:10:\"expiration\";i:1515321939;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149139;}s:64:\"1e5f63158130b298fa29f95853a5674b3d6498c85019e480ea6f3a3a3e13bff6\";a:4:{s:10:\"expiration\";i:1515322342;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149542;}s:64:\"c6fbab5ba6895b9611a40d1dbcef472660ec53e510dcc0dee7023146cd60deca\";a:4:{s:10:\"expiration\";i:1515322370;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149570;}s:64:\"8151e36bc8e042973a2c84ae4b412afa70605a2a196b83a149f73069ca971dbe\";a:4:{s:10:\"expiration\";i:1515322466;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149666;}s:64:\"fc0373829d0027c4388deb545b41267e2269be01a0a40cf11c9f7d646077561d\";a:4:{s:10:\"expiration\";i:1515322630;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149830;}s:64:\"1e6e0b2ddc4415329cff1301df53d13e630802614c5030519d791874cea56c3c\";a:4:{s:10:\"expiration\";i:1515322665;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149865;}s:64:\"c6c0e8dd28adc7cc5e9fa0c8035b5b05e24de7fa93fa63778d3ebcea4ac70715\";a:4:{s:10:\"expiration\";i:1515322673;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515149873;}s:64:\"9093ff3caa9cdf49c1e4c55f464dcb8e67abb6ee0c4d08df244ba301216b41bb\";a:4:{s:10:\"expiration\";i:1515322899;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515150099;}s:64:\"20fdb331b003aa28e64de3319922d4263c2e87ec2137f15986474d84d1ee2616\";a:4:{s:10:\"expiration\";i:1515323475;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515150675;}s:64:\"b16397c356d2d25903671f584198115bf5eaa5e16dab92da14af9f11d2af6c3b\";a:4:{s:10:\"expiration\";i:1515328169;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:120:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36\";s:5:\"login\";i:1515155369;}}'),
(71,	3,	'nickname',	'customer'),
(72,	3,	'first_name',	'John'),
(73,	3,	'last_name',	'Doe'),
(74,	3,	'description',	''),
(75,	3,	'rich_editing',	'true'),
(76,	3,	'syntax_highlighting',	'true'),
(77,	3,	'comment_shortcuts',	'false'),
(78,	3,	'admin_color',	'fresh'),
(79,	3,	'use_ssl',	'0'),
(80,	3,	'show_admin_bar_front',	'true'),
(81,	3,	'locale',	''),
(82,	3,	'wp_capabilities',	'a:1:{s:10:\"subscriber\";b:1;}'),
(83,	3,	'wp_user_level',	'0'),
(84,	3,	'dismissed_wp_pointers',	''),
(85,	3,	'session_tokens',	'a:1:{s:64:\"38dfa7e7434e833002ca0450157f94241e3a138c11137e7563035fea56eabcd0\";a:4:{s:10:\"expiration\";i:1516901887;s:2:\"ip\";s:10:\"172.17.0.1\";s:2:\"ua\";s:121:\"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36\";s:5:\"login\";i:1516729087;}}'),
(86,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-151'),
(87,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-673e230ddc3c51305-151'),
(88,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-3e63e9b7562746203-151'),
(89,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-ed3fa713aa163c977-151'),
(90,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-dc278a63daa20c2f7-151'),
(91,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-0e20bc938c5aaf73e-151'),
(92,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-d452cb42f44c53470-151'),
(93,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-ebd93b6574ec6ff9b-151'),
(94,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-43ea21e9b01293b2f-151'),
(95,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-107ce0926b105d71f-151'),
(96,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-d69ecd98aa6557ef1-151'),
(97,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-344b3ef5863ebe81f-151'),
(98,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-b271863ec07efb19e-151'),
(99,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-196e8bcace5ba650e-151'),
(100,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-877928b1a7c1835d7-151'),
(101,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-69cab007a0f7cf02f-151'),
(102,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-151e25fe035304906-151'),
(103,	1,	'tribe-dismiss-notice',	'tickets-commerce-paypal-oversell-4334f83b1ac20cede-151'),
(104,	1,	'tribe-dismiss-notice',	'tickets-paypal-oversell-62211cdfbe6ea9341-151'),
(105,	1,	'closedpostboxes_dashboard',	'a:5:{i:0;s:19:\"dashboard_right_now\";i:1;s:18:\"dashboard_activity\";i:2;s:22:\"tribe_dashboard_widget\";i:3;s:21:\"dashboard_quick_press\";i:4;s:17:\"dashboard_primary\";}'),
(106,	1,	'metaboxhidden_dashboard',	'a:0:{}'),
(107,	1,	'tribe-dismiss-notice',	'tickets-paypal-oversell-e6bbc212a420184c4-151'),
(108,	1,	'dismissed_store_notice_setting_moved_notice',	'1'),
(109,	1,	'dismissed_update_notice',	'1');

DROP TABLE IF EXISTS `wp_users`;
CREATE TABLE `wp_users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(60) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_pass` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_nicename` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_email` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_url` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_activation_key` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT '0',
  `display_name` varchar(250) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`),
  KEY `user_login_key` (`user_login`),
  KEY `user_nicename` (`user_nicename`),
  KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_users` (`ID`, `user_login`, `user_pass`, `user_nicename`, `user_email`, `user_url`, `user_registered`, `user_activation_key`, `user_status`, `display_name`) VALUES
(1,	'admin',	'$P$B.pAkfqZpJYBF0mbOf95Peob0RYRq20',	'admin',	'dev-email@flywheel.local',	'',	'2017-12-06 08:25:30',	'',	0,	'admin'),
(2,	'editor',	'$P$BQU4J6/7Udjl8Zd6/GBmDnYMUWbf891',	'editor',	'editor@commerce.dev',	'http://ed.to.r',	'2018-01-05 10:34:58',	'',	0,	'edi tor'),
(3,	'customer',	'$P$BBpWL/eTl/yQQ5cyEilwH4mGfXFiBx.',	'customer',	'customer@commerce.dev',	'http://theaveragedev.com',	'2018-01-09 11:17:18',	'',	0,	'John Doe');

DROP TABLE IF EXISTS `wp_wc_download_log`;
CREATE TABLE `wp_wc_download_log` (
  `download_log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `user_ip_address` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '',
  PRIMARY KEY (`download_log_id`),
  KEY `permission_id` (`permission_id`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_wc_webhooks`;
CREATE TABLE `wp_wc_webhooks` (
  `webhook_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `delivery_url` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `secret` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `topic` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_created_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `api_version` smallint(4) NOT NULL,
  `failure_count` smallint(10) NOT NULL DEFAULT '0',
  `pending_delivery` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`webhook_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_api_keys`;
CREATE TABLE `wp_woocommerce_api_keys` (
  `key_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `description` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `permissions` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `consumer_key` char(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `consumer_secret` char(43) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `nonces` longtext COLLATE utf8mb4_unicode_520_ci,
  `truncated_key` char(7) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_access` datetime DEFAULT NULL,
  PRIMARY KEY (`key_id`),
  KEY `consumer_key` (`consumer_key`),
  KEY `consumer_secret` (`consumer_secret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_attribute_taxonomies`;
CREATE TABLE `wp_woocommerce_attribute_taxonomies` (
  `attribute_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attribute_label` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `attribute_type` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attribute_orderby` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `attribute_public` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`attribute_id`),
  KEY `attribute_name` (`attribute_name`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_downloadable_product_permissions`;
CREATE TABLE `wp_woocommerce_downloadable_product_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `download_id` varchar(36) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `order_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `order_key` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_email` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `downloads_remaining` varchar(9) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `access_granted` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access_expires` datetime DEFAULT NULL,
  `download_count` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`permission_id`),
  KEY `download_order_key_product` (`product_id`,`order_id`,`order_key`(16),`download_id`),
  KEY `download_order_product` (`download_id`,`order_id`,`product_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_log`;
CREATE TABLE `wp_woocommerce_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `level` smallint(4) NOT NULL,
  `source` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `message` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `context` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`log_id`),
  KEY `level` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_order_itemmeta`;
CREATE TABLE `wp_woocommerce_order_itemmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `meta_key` (`meta_key`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_woocommerce_order_itemmeta` (`meta_id`, `order_item_id`, `meta_key`, `meta_value`) VALUES
(1,	1,	'_product_id',	'41'),
(2,	1,	'_variation_id',	'0'),
(3,	1,	'_qty',	'3'),
(4,	1,	'_tax_class',	''),
(5,	1,	'_line_subtotal',	'9'),
(6,	1,	'_line_subtotal_tax',	'0'),
(7,	1,	'_line_total',	'9'),
(8,	1,	'_line_tax',	'0'),
(9,	1,	'_line_tax_data',	'a:2:{s:5:\"total\";a:0:{}s:8:\"subtotal\";a:0:{}}'),
(10,	1,	'_tribe_wooticket_attendee_optout',	'');

DROP TABLE IF EXISTS `wp_woocommerce_order_items`;
CREATE TABLE `wp_woocommerce_order_items` (
  `order_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_item_name` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `order_item_type` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `order_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_woocommerce_order_items` (`order_item_id`, `order_item_name`, `order_item_type`, `order_id`) VALUES
(1,	'Woo ticket 1',	'line_item',	53);

DROP TABLE IF EXISTS `wp_woocommerce_payment_tokenmeta`;
CREATE TABLE `wp_woocommerce_payment_tokenmeta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_token_id` bigint(20) unsigned NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,
  PRIMARY KEY (`meta_id`),
  KEY `payment_token_id` (`payment_token_id`),
  KEY `meta_key` (`meta_key`(32))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_payment_tokens`;
CREATE TABLE `wp_woocommerce_payment_tokens` (
  `token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `gateway_id` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `token` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`token_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_sessions`;
CREATE TABLE `wp_woocommerce_sessions` (
  `session_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `session_key` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `session_value` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `session_expiry` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`session_key`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_woocommerce_sessions` (`session_id`, `session_key`, `session_value`, `session_expiry`) VALUES
(32,	'1',	'a:7:{s:4:\"cart\";s:6:\"a:0:{}\";s:11:\"cart_totals\";s:367:\"a:15:{s:8:\"subtotal\";i:0;s:12:\"subtotal_tax\";i:0;s:14:\"shipping_total\";i:0;s:12:\"shipping_tax\";i:0;s:14:\"shipping_taxes\";a:0:{}s:14:\"discount_total\";i:0;s:12:\"discount_tax\";i:0;s:19:\"cart_contents_total\";i:0;s:17:\"cart_contents_tax\";i:0;s:19:\"cart_contents_taxes\";a:0:{}s:9:\"fee_total\";i:0;s:7:\"fee_tax\";i:0;s:9:\"fee_taxes\";a:0:{}s:5:\"total\";i:0;s:9:\"total_tax\";i:0;}\";s:15:\"applied_coupons\";s:6:\"a:0:{}\";s:22:\"coupon_discount_totals\";s:6:\"a:0:{}\";s:26:\"coupon_discount_tax_totals\";s:6:\"a:0:{}\";s:21:\"removed_cart_contents\";s:6:\"a:0:{}\";s:8:\"customer\";s:834:\"a:26:{s:2:\"id\";s:1:\"1\";s:13:\"date_modified\";s:25:\"2017-12-07T13:43:02+00:00\";s:8:\"postcode\";s:5:\"75003\";s:4:\"city\";s:5:\"Paris\";s:9:\"address_1\";s:18:\"100 rue de Turenne\";s:7:\"address\";s:18:\"100 rue de Turenne\";s:9:\"address_2\";s:25:\"apt 12 etage 4, code 6541\";s:5:\"state\";s:0:\"\";s:7:\"country\";s:2:\"FR\";s:17:\"shipping_postcode\";s:0:\"\";s:13:\"shipping_city\";s:0:\"\";s:18:\"shipping_address_1\";s:0:\"\";s:16:\"shipping_address\";s:0:\"\";s:18:\"shipping_address_2\";s:0:\"\";s:14:\"shipping_state\";s:0:\"\";s:16:\"shipping_country\";s:2:\"FR\";s:13:\"is_vat_exempt\";s:0:\"\";s:19:\"calculated_shipping\";s:0:\"\";s:10:\"first_name\";s:4:\"Luca\";s:9:\"last_name\";s:7:\"Tumedei\";s:7:\"company\";s:0:\"\";s:5:\"phone\";s:12:\"+33633810141\";s:5:\"email\";s:22:\"luca.tumedei@gmail.com\";s:19:\"shipping_first_name\";s:0:\"\";s:18:\"shipping_last_name\";s:0:\"\";s:16:\"shipping_company\";s:0:\"\";}\";}',	1519208888);

DROP TABLE IF EXISTS `wp_woocommerce_shipping_zones`;
CREATE TABLE `wp_woocommerce_shipping_zones` (
  `zone_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `zone_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `zone_order` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`zone_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_woocommerce_shipping_zones` (`zone_id`, `zone_name`, `zone_order`) VALUES
(1,	'France',	0);

DROP TABLE IF EXISTS `wp_woocommerce_shipping_zone_locations`;
CREATE TABLE `wp_woocommerce_shipping_zone_locations` (
  `location_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `zone_id` bigint(20) unsigned NOT NULL,
  `location_code` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `location_type` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `location_id` (`location_id`),
  KEY `location_type_code` (`location_type`(10),`location_code`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_woocommerce_shipping_zone_locations` (`location_id`, `zone_id`, `location_code`, `location_type`) VALUES
(1,	1,	'FR',	'country');

DROP TABLE IF EXISTS `wp_woocommerce_shipping_zone_methods`;
CREATE TABLE `wp_woocommerce_shipping_zone_methods` (
  `zone_id` bigint(20) unsigned NOT NULL,
  `instance_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `method_id` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `method_order` bigint(20) unsigned NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`instance_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

INSERT INTO `wp_woocommerce_shipping_zone_methods` (`zone_id`, `instance_id`, `method_id`, `method_order`, `is_enabled`) VALUES
(1,	1,	'free_shipping',	1,	1),
(0,	2,	'free_shipping',	1,	1);

DROP TABLE IF EXISTS `wp_woocommerce_tax_rates`;
CREATE TABLE `wp_woocommerce_tax_rates` (
  `tax_rate_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tax_rate_country` varchar(2) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate_state` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate` varchar(8) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate_name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  `tax_rate_priority` bigint(20) unsigned NOT NULL,
  `tax_rate_compound` int(1) NOT NULL DEFAULT '0',
  `tax_rate_shipping` int(1) NOT NULL DEFAULT '1',
  `tax_rate_order` bigint(20) unsigned NOT NULL,
  `tax_rate_class` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`tax_rate_id`),
  KEY `tax_rate_country` (`tax_rate_country`),
  KEY `tax_rate_state` (`tax_rate_state`(2)),
  KEY `tax_rate_class` (`tax_rate_class`(10)),
  KEY `tax_rate_priority` (`tax_rate_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


DROP TABLE IF EXISTS `wp_woocommerce_tax_rate_locations`;
CREATE TABLE `wp_woocommerce_tax_rate_locations` (
  `location_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `location_code` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `tax_rate_id` bigint(20) unsigned NOT NULL,
  `location_type` varchar(40) COLLATE utf8mb4_unicode_520_ci NOT NULL,
  PRIMARY KEY (`location_id`),
  KEY `tax_rate_id` (`tax_rate_id`),
  KEY `location_type_code` (`location_type`(10),`location_code`(20))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;


-- 2019-05-09 18:18:10
