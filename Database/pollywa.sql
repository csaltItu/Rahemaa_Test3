-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.5.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pollywa`
--

-- --------------------------------------------------------

--
-- Table structure for table `call table`
--

CREATE TABLE `call table` (
  `Call_ID` int(11) NOT NULL,
  `Originating _Request_ID` int(11) NOT NULL,
  `Phone_Number` varchar(40) NOT NULL,
  `Call_Type` varchar(20) NOT NULL,
  `Call_Start_Time` datetime NOT NULL,
  `Call_End_Time` datetime NOT NULL,
  `CallStatus` varchar(20) NOT NULL DEFAULT 'unsub',
  `Channel` varchar(20) NOT NULL,
  `Attempt_Start_Time` datetime NOT NULL,
  `Xferred_To` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `feedback table`
--

CREATE TABLE `feedback table` (
  `Feedback_ID` int(11) NOT NULL,
  `Feedback_Type` varchar(30) NOT NULL,
  `Call_ID` int(11) NOT NULL,
  `Notify` varchar(3) NOT NULL DEFAULT 'No',
  `dateTime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Useful` varchar(1) NOT NULL DEFAULT 'P',
  `Annotator` varchar(10) NOT NULL DEFAULT 'Pending',
  `Annotation` text NOT NULL,
  `Gender` varchar(1) NOT NULL DEFAULT 'P'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `phone numbers`
--

CREATE TABLE `phone numbers` (
  `ph_num-id` int(11) NOT NULL,
  `unconditioned` varchar(20) NOT NULL,
  `conditioned` varchar(20) NOT NULL,
  `date_time` datetime NOT NULL,
  `call_id` int(11) NOT NULL,
  `call_type` varchar(20) NOT NULL,
  `sender` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `request table`
--

CREATE TABLE `request table` (
  `Request_ID` int(11) NOT NULL,
  `Recording_ID` int(11) NOT NULL,
  `Effect_Chosen` int(11) NOT NULL,
  `Requesting_Call_ID` int(11) NOT NULL,
  `Request_Type` varchar(20) NOT NULL,
  `From` varchar(20) NOT NULL,
  `To_Whom` varchar(20) NOT NULL,
  `Originating_Call_Time` datetime NOT NULL,
  `Request_Status` varchar(20) NOT NULL,
  `Number_of_Attempts` int(11) NOT NULL,
  `Time_of_Last_Attempt` datetime NOT NULL,
  `FollowupCall` varchar(2) NOT NULL DEFAULT 'U',
  `FollowupReqID` int(11) NOT NULL DEFAULT '-1',
  `SysLanguage` varchar(15) NOT NULL DEFAULT 'English',
  `MsgLanguage` varchar(15) NOT NULL DEFAULT 'English',
  `TestCall` varchar(5) NOT NULL DEFAULT 'FALSE',
  `Channel` varchar(20) NOT NULL,
  `Time_Req_Relayed_To_Polly` datetime NOT NULL,
  `No_of_System_Attempts_for_this_Attempt` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE `user_table` (
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `root_id` int(11) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `AdjList` varchar(400) NOT NULL,
  `PhDir` varchar(300) NOT NULL,
  `location` varchar(100) NOT NULL DEFAULT 'pending',
  `type` varchar(10) NOT NULL DEFAULT 'pending',
  `dirtybit` tinyint(1) NOT NULL DEFAULT '1',
  `MsgSurveyFlag` int(1) NOT NULL DEFAULT '0',
  `date_started_answering_calls` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_parent_started_answering_calls` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `MsgsHeard` varchar(200) NOT NULL,
  `First_Interaction_Type` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `call table`
--
ALTER TABLE `call table`
  ADD PRIMARY KEY (`Call_ID`),
  ADD KEY `Phone_Number_3` (`Phone_Number`),
  ADD KEY `Call_Type` (`Call_Type`,`CallStatus`) USING BTREE,
  ADD KEY `Phone_Number` (`Call_Type`,`CallStatus`,`Phone_Number`) USING BTREE,
  ADD KEY `Call_Type_2` (`Call_Type`,`CallStatus`,`Call_Start_Time`) USING BTREE,
  ADD KEY `Phone_Number_2` (`Call_Type`,`CallStatus`,`Call_Start_Time`,`Phone_Number`) USING BTREE;

--
-- Indexes for table `feedback table`
--
ALTER TABLE `feedback table`
  ADD PRIMARY KEY (`Feedback_ID`);

--
-- Indexes for table `phone numbers`
--
ALTER TABLE `phone numbers`
  ADD PRIMARY KEY (`ph_num-id`);

--
-- Indexes for table `request table`
--
ALTER TABLE `request table`
  ADD PRIMARY KEY (`Request_ID`),
  ADD KEY `Originating_Call_Time` (`Originating_Call_Time`),
  ADD KEY `Request_Type` (`Request_Type`,`Request_Status`),
  ADD KEY `To_Whom` (`To_Whom`),
  ADD KEY `Request_Type_2` (`Request_Type`,`Originating_Call_Time`),
  ADD KEY `Request_Type_3` (`Request_Type`),
  ADD KEY `Request_Type_4` (`Request_Type`,`To_Whom`,`Originating_Call_Time`),
  ADD KEY `Request_Type_5` (`Request_Type`,`To_Whom`),
  ADD KEY `Request_Type_6` (`Request_Type`,`Request_Status`,`Originating_Call_Time`) USING BTREE;

--
-- Indexes for table `user_table`
--
ALTER TABLE `user_table`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`,`date_added`,`First_Interaction_Type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `call table`
--
ALTER TABLE `call table`
  MODIFY `Call_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1932759;
--
-- AUTO_INCREMENT for table `feedback table`
--
ALTER TABLE `feedback table`
  MODIFY `Feedback_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5460;
--
-- AUTO_INCREMENT for table `phone numbers`
--
ALTER TABLE `phone numbers`
  MODIFY `ph_num-id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=475632;
--
-- AUTO_INCREMENT for table `request table`
--
ALTER TABLE `request table`
  MODIFY `Request_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=690141;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
