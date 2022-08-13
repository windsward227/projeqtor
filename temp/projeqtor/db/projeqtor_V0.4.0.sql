
-- ///////////////////////////////////////////////////////////
-- // PROJECTOR EXPORT                                      //
-- //-------------------------------------------------------//
-- // Version : V0.4.0                                      //
-- // Date : 2009-10-06                                     //
-- ///////////////////////////////////////////////////////////

--
-- Structure de la table `${prefix}resource`
--

ALTER TABLE `${prefix}resource` ADD phone VARCHAR(20),
 ADD mobile VARCHAR(20),
 ADD fax VARCHAR(20),
 ADD idTeam INT(12) UNSIGNED;

--
-- Structure de la TABLE `${prefix}team`
--

CREATE TABLE `${prefix}team` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `idle` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_general_ci ;

--
-- Contenu de la table `${prefix}menu`
--
TRUNCATE TABLE `${prefix}menu` ;
INSERT INTO `${prefix}menu` (`id`, `name`, `idMenu`, `type`, `sortOrder`, `level`, `idle`) VALUES
(1, 'menuToday', 0, 'item', 100, NULL, 0),
(2, 'menuWork', 0, 'menu', 110, 'Project', 1),
(3, 'menuRisk', 43, 'object', 310, 'Project', 0),
(4, 'menuAction', 43, 'object', 320, 'Project', 0),
(5, 'menuIssue', 43, 'object', 330, 'Project', 0),
(6, 'menuMeeting', 0, 'class', 260, 'Project', 1),
(7, 'menuFollowup', 0, 'menu', 200, NULL, 1),
(8, 'menuImputation', 7, 'item', 210, NULL, 1),
(9, 'menuPlanning', 7, 'item', 220, NULL, 0),
(10, 'menuComponent', 0, 'class', 400, NULL, 1),
(11, 'menuTool', 0, 'menu', 500, NULL, 1),
(12, 'menuRequestor', 11, 'item', 501, NULL, 1),
(13, 'menuParameter', 0, 'menu', 900, NULL, 1),
(14, 'menuEnvironmentalParameter', 13, 'menu', 910, NULL, 1),
(15, 'menuClient', 14, 'object', 912, NULL, 0),
(16, 'menuProject', 14, 'object', 914, 'Project', 0),
(17, 'menuUser', 14, 'object', 916, NULL, 0),
(18, 'menuGlobalParameter', 13, 'item', 980, NULL, 1),
(19, 'menuProjectParameter', 13, 'item', 985, NULL, 1),
(20, 'menuUserParameter', 13, 'item', 990, '', 0),
(21, 'menuHabilitation', 37, 'item', 966, NULL, 0),
(22, 'menuTicket', 2, 'object', 120, 'Project', 0),
(25, 'menuActivity', 2, 'object', 135, 'Project', 0),
(26, 'menuMilestone', 2, 'object', 145, 'Project', 0),
(34, 'menuStatus', 36, 'object', 932, NULL, 0),
(36, 'menuListOfValues', 13, 'menu', 930, NULL, 1),
(37, 'menuHabilitationParameter', 13, 'menu', 960, NULL, 1),
(38, 'menuSeverity', 36, 'object', 934, NULL, 0),
(39, 'menuLikelihood', 36, 'object', 936, NULL, 0),
(40, 'menuCriticality', 36, 'object', 938, NULL, 0),
(41, 'menuPriority', 36, 'object', 942, NULL, 0),
(42, 'menuUrgency', 36, 'object', 940, NULL, 0),
(43, 'menuRiskManagementPlan', 0, 'menu', 300, '', 1),
(44, 'menuResource', 14, 'object', 918, NULL, 0),
(45, 'menuRiskType', 36, 'object', 950, NULL, 0),
(46, 'menuIssueType', 36, 'object', 952, NULL, 0),
(47, 'menuAccessProfile', 37, 'object', 964, NULL, 0),
(48, 'menuAccessRight', 37, 'item', 968, NULL, 0),
(49, 'menuProfile', 37, 'object', 962, NULL, 0),
(50, 'menuAffectation', 14, 'object', 920, 'Project', 0),
(51, 'menuMessage', 11, 'object', 510, 'Project', 0),
(52, 'menuMessageType', 36, 'object', 954, NULL, 0),
(53, 'menuTicketType', 36, 'object', 944, NULL, 0),
(55, 'menuActivityType', 36, 'object', 946, NULL, 0),
(56, 'menuMilestoneType', 36, 'object', 948, NULL, 0),
(57, 'menuTeam', 14, 'object', 917, NULL, 0);

--
-- Contenu de la table `${prefix}accessright`
--
TRUNCATE TABLE `${prefix}accessright` ;
INSERT INTO `${prefix}accessright` (`id`, `idProfile`, `idMenu`, `idAccessProfile`) VALUES
(1, 1, 3, 8),
(2, 2, 3, 2),
(3, 3, 3, 7),
(4, 4, 3, 1),
(5, 6, 3, 1),
(6, 7, 3, 1),
(7, 5, 3, 1),
(8, 1, 4, 8),
(9, 2, 4, 4),
(10, 3, 4, 7),
(11, 4, 4, 3),
(12, 6, 4, 3),
(13, 7, 4, 1),
(14, 5, 4, 1),
(15, 1, 5, 8),
(16, 2, 5, 2),
(17, 3, 5, 7),
(18, 4, 5, 1),
(19, 6, 5, 1),
(20, 7, 5, 1),
(21, 5, 5, 1),
(22, 1, 50, 8),
(23, 2, 50, 2),
(24, 3, 50, 7),
(25, 4, 50, 1),
(26, 6, 50, 9),
(27, 7, 50, 9),
(28, 5, 50, 9),
(29, 1, 22, 8),
(30, 2, 22, 2),
(31, 3, 22, 7),
(32, 4, 22, 7),
(33, 6, 22, 7),
(34, 7, 22, 5),
(35, 5, 22, 1),
(36, 1, 51, 8),
(37, 2, 51, 9),
(38, 3, 51, 7),
(39, 4, 51, 9),
(40, 6, 51, 9),
(41, 7, 51, 9),
(42, 5, 51, 9),
(43, 1, 25, 8),
(44, 2, 25, 2),
(45, 3, 25, 7),
(46, 4, 25, 3),
(47, 6, 25, 1),
(48, 7, 25, 1),
(49, 5, 25, 1),
(50, 1, 26, 8),
(51, 2, 26, 2),
(52, 3, 26, 7),
(53, 4, 26, 3),
(54, 6, 26, 1),
(55, 7, 26, 1),
(56, 5, 26, 1),
(57, 1, 16, 8),
(58, 2, 16, 2),
(59, 3, 16, 7),
(60, 4, 16, 9),
(61, 6, 16, 9),
(62, 7, 16, 9),
(63, 5, 16, 9);

--
-- Contenu de la table `${prefix}habilitation`
--

TRUNCATE TABLE `${prefix}habilitation` ;
INSERT INTO `${prefix}habilitation` (`id`, `idProfile`, `idMenu`, `allowAccess`) VALUES
(1, 1, 14, 1),
(2, 1, 13, 1),
(3, 1, 21, 1),
(4, 1, 17, 1),
(5, 2, 20, 1),
(6, 1, 1, 1),
(7, 2, 1, 1),
(8, 3, 1, 1),
(9, 4, 1, 1),
(10, 6, 1, 1),
(11, 7, 1, 1),
(12, 5, 1, 1),
(13, 1, 2, 1),
(14, 2, 2, 0),
(15, 3, 2, 1),
(16, 4, 2, 1),
(17, 6, 2, 1),
(18, 7, 2, 1),
(19, 5, 2, 0),
(20, 1, 3, 1),
(21, 2, 3, 1),
(22, 3, 3, 1),
(23, 4, 3, 0),
(24, 6, 3, 1),
(25, 7, 3, 0),
(26, 5, 3, 0),
(27, 1, 4, 1),
(28, 2, 4, 1),
(29, 3, 4, 1),
(30, 4, 4, 1),
(31, 6, 4, 1),
(32, 7, 4, 1),
(33, 5, 4, 1),
(34, 1, 5, 1),
(35, 2, 5, 1),
(36, 3, 5, 1),
(37, 4, 5, 0),
(38, 6, 5, 0),
(39, 7, 5, 0),
(40, 5, 5, 0),
(41, 1, 6, 0),
(42, 2, 6, 0),
(43, 3, 6, 0),
(44, 4, 6, 0),
(45, 6, 6, 0),
(46, 7, 6, 0),
(47, 5, 6, 0),
(48, 1, 7, 1),
(49, 2, 7, 1),
(50, 3, 7, 1),
(51, 4, 7, 1),
(52, 6, 7, 1),
(53, 7, 7, 1),
(54, 5, 7, 1),
(55, 1, 8, 0),
(56, 2, 8, 0),
(57, 3, 8, 0),
(58, 4, 8, 0),
(59, 6, 8, 0),
(60, 7, 8, 0),
(61, 5, 8, 0),
(62, 1, 9, 1),
(63, 2, 9, 1),
(64, 3, 9, 1),
(65, 4, 9, 1),
(66, 6, 9, 1),
(67, 7, 9, 1),
(68, 5, 9, 1),
(69, 1, 10, 0),
(70, 2, 10, 0),
(71, 3, 10, 0),
(72, 4, 10, 0),
(73, 6, 10, 0),
(74, 7, 10, 0),
(75, 5, 10, 0),
(76, 1, 11, 1),
(77, 2, 11, 0),
(78, 3, 11, 1),
(79, 4, 11, 0),
(80, 6, 11, 0),
(81, 7, 11, 0),
(82, 5, 11, 0),
(83, 1, 12, 0),
(84, 2, 12, 0),
(85, 3, 12, 0),
(86, 4, 12, 0),
(87, 6, 12, 0),
(88, 7, 12, 0),
(89, 5, 12, 0),
(90, 2, 13, 1),
(91, 3, 13, 1),
(92, 4, 13, 1),
(93, 6, 13, 1),
(94, 7, 13, 1),
(95, 5, 13, 1),
(96, 2, 14, 1),
(97, 3, 14, 1),
(98, 4, 14, 0),
(99, 6, 14, 0),
(100, 7, 14, 0),
(101, 5, 14, 0),
(102, 1, 15, 1),
(103, 2, 15, 0),
(104, 3, 15, 0),
(105, 4, 15, 0),
(106, 6, 15, 0),
(107, 7, 15, 0),
(108, 5, 15, 0),
(109, 1, 16, 1),
(110, 2, 16, 1),
(111, 3, 16, 1),
(112, 4, 16, 0),
(113, 6, 16, 0),
(114, 7, 16, 0),
(115, 5, 16, 0),
(116, 2, 17, 0),
(117, 3, 17, 0),
(118, 4, 17, 0),
(119, 6, 17, 0),
(120, 7, 17, 0),
(121, 5, 17, 0),
(122, 2, 21, 0),
(123, 3, 21, 0),
(124, 4, 21, 0),
(125, 6, 21, 0),
(126, 7, 21, 0),
(127, 5, 21, 0),
(128, 1, 18, 0),
(129, 2, 18, 0),
(130, 3, 18, 0),
(131, 4, 18, 0),
(132, 6, 18, 0),
(133, 7, 18, 0),
(134, 5, 18, 0),
(135, 1, 19, 0),
(136, 2, 19, 0),
(137, 3, 19, 0),
(138, 4, 19, 0),
(139, 6, 19, 0),
(140, 7, 19, 0),
(141, 5, 19, 0),
(142, 1, 20, 1),
(143, 3, 20, 1),
(144, 4, 20, 1),
(145, 6, 20, 1),
(146, 7, 20, 1),
(147, 5, 20, 1),
(148, 1, 22, 1),
(149, 2, 22, 1),
(150, 3, 22, 1),
(151, 4, 22, 1),
(152, 6, 22, 0),
(153, 7, 22, 0),
(154, 5, 22, 0),
(155, 1, 23, 0),
(156, 2, 23, 0),
(157, 3, 23, 0),
(158, 4, 23, 0),
(159, 6, 23, 0),
(160, 7, 23, 0),
(161, 5, 23, 0),
(162, 1, 24, 0),
(163, 2, 24, 0),
(164, 3, 24, 0),
(165, 4, 24, 0),
(166, 6, 24, 0),
(167, 7, 24, 0),
(168, 5, 24, 0),
(169, 1, 25, 1),
(170, 2, 25, 1),
(171, 3, 25, 1),
(172, 4, 25, 1),
(173, 6, 25, 1),
(174, 7, 25, 1),
(175, 5, 25, 0),
(176, 1, 26, 1),
(177, 2, 26, 1),
(178, 3, 26, 1),
(179, 4, 26, 1),
(180, 6, 26, 1),
(181, 7, 26, 1),
(182, 5, 26, 0),
(183, 1, 32, 0),
(184, 2, 32, 0),
(185, 3, 32, 0),
(186, 4, 32, 0),
(187, 6, 32, 0),
(188, 7, 32, 0),
(189, 5, 32, 0),
(190, 1, 33, 0),
(191, 2, 33, 0),
(192, 3, 33, 0),
(193, 4, 33, 0),
(194, 6, 33, 0),
(195, 7, 33, 0),
(196, 5, 33, 0),
(197, 1, 34, 1),
(198, 2, 34, 0),
(199, 3, 34, 0),
(200, 4, 34, 0),
(201, 6, 34, 0),
(202, 7, 34, 0),
(203, 5, 34, 0),
(204, 1, 36, 1),
(205, 2, 36, 0),
(206, 3, 36, 0),
(207, 4, 36, 0),
(208, 6, 36, 0),
(209, 7, 36, 0),
(210, 5, 36, 0),
(211, 1, 37, 1),
(212, 2, 37, 0),
(213, 3, 37, 0),
(214, 4, 37, 0),
(215, 6, 37, 0),
(216, 7, 37, 0),
(217, 5, 37, 0),
(218, 1, 38, 1),
(219, 2, 38, 0),
(220, 3, 38, 0),
(221, 4, 38, 0),
(222, 6, 38, 0),
(223, 7, 38, 0),
(224, 5, 38, 0),
(225, 1, 39, 1),
(226, 2, 39, 0),
(227, 3, 39, 0),
(228, 4, 39, 0),
(229, 6, 39, 0),
(230, 7, 39, 0),
(231, 5, 39, 0),
(232, 1, 40, 1),
(233, 2, 40, 0),
(234, 3, 40, 0),
(235, 4, 40, 0),
(236, 6, 40, 0),
(237, 7, 40, 0),
(238, 5, 40, 0),
(239, 1, 42, 1),
(240, 2, 42, 0),
(241, 3, 42, 0),
(242, 4, 42, 0),
(243, 6, 42, 0),
(244, 7, 42, 0),
(245, 5, 42, 0),
(246, 1, 41, 1),
(247, 2, 41, 0),
(248, 3, 41, 0),
(249, 4, 41, 0),
(250, 6, 41, 0),
(251, 7, 41, 0),
(252, 5, 41, 0),
(253, 1, 43, 1),
(254, 2, 43, 1),
(255, 3, 43, 1),
(256, 4, 43, 1),
(257, 6, 43, 1),
(258, 7, 43, 1),
(259, 5, 43, 1),
(260, 1, 44, 1),
(261, 2, 44, 0),
(262, 3, 44, 1),
(263, 4, 44, 0),
(264, 6, 44, 0),
(265, 7, 44, 0),
(266, 5, 44, 0),
(267, 1, 45, 1),
(268, 2, 45, 0),
(269, 3, 45, 0),
(270, 4, 45, 0),
(271, 6, 45, 0),
(272, 7, 45, 0),
(273, 5, 45, 0),
(274, 1, 46, 1),
(275, 2, 46, 0),
(276, 3, 46, 0),
(277, 4, 46, 0),
(278, 6, 46, 0),
(279, 7, 46, 0),
(280, 5, 46, 0),
(281, 1, 50, 1),
(282, 2, 50, 0),
(283, 3, 50, 1),
(284, 4, 50, 0),
(285, 6, 50, 0),
(286, 7, 50, 0),
(287, 5, 50, 0),
(288, 1, 49, 1),
(289, 2, 49, 0),
(290, 3, 49, 0),
(291, 4, 49, 0),
(292, 6, 49, 0),
(293, 7, 49, 0),
(294, 5, 49, 0),
(295, 1, 47, 1),
(296, 2, 47, 0),
(297, 3, 47, 0),
(298, 4, 47, 0),
(299, 6, 47, 0),
(300, 7, 47, 0),
(301, 5, 47, 0),
(302, 1, 48, 1),
(303, 2, 48, 0),
(304, 3, 48, 0),
(305, 4, 48, 0),
(306, 6, 48, 0),
(307, 7, 48, 0),
(308, 5, 48, 0),
(309, 1, 51, 1),
(310, 2, 51, 0),
(311, 3, 51, 1),
(312, 4, 51, 0),
(313, 6, 51, 0),
(314, 7, 51, 0),
(315, 5, 51, 0),
(316, 1, 52, 1),
(317, 2, 52, 0),
(318, 3, 52, 0),
(319, 4, 52, 0),
(320, 6, 52, 0),
(321, 7, 52, 0),
(322, 5, 52, 0),
(323, 1, 53, 1),
(324, 2, 53, 0),
(325, 3, 53, 0),
(326, 4, 53, 0),
(327, 6, 53, 0),
(328, 7, 53, 0),
(329, 5, 53, 0),
(330, 1, 55, 1),
(331, 2, 55, 0),
(332, 3, 55, 0),
(333, 4, 55, 0),
(334, 6, 55, 0),
(335, 7, 55, 0),
(336, 5, 55, 0),
(337, 1, 56, 1),
(338, 2, 56, 0),
(339, 3, 56, 0),
(340, 4, 56, 0),
(341, 6, 56, 0),
(342, 7, 56, 0),
(343, 5, 56, 0),
(344, 1, 57, 1),
(345, 2, 57, 0),
(346, 3, 57, 1),
(347, 4, 57, 0),
(348, 6, 57, 0),
(349, 7, 57, 0),
(350, 5, 57, 0);
