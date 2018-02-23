<?php


set_time_limit(1800);

$time_zone="Asia/Karachi";
if(function_exists('date_default_timezone_set'))date_default_timezone_set($time_zone);

///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// GLOBAL VARIABLES ///////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

//----- Paths for resources such as scripts, recordings -----
$base_dir  				= "http://127.0.0.1/Web_Root/";
$base_dir_absolute      = "D:/xampp/htdocs/Web_Root/";
$scripts_dir 			= $base_dir."Controller/helpers/Scripts/PollyGame/";
$database_dir           = $base_dir . "Controller/helpers/DBScripts/PollyGame/";
$prompts_dir 	        = $base_dir_absolute."Audio_Resources/PollyGame/prompts/";
$praat_dir 			    = $bases_dir_absolute."Audio_Resources/PollyGame/Praat/";
$friend_name_dir        = $praat_dir . "FriendNames/";
$sender_name_dir        = $praat_dir . "UserNames/";
$feedback_dir      	    = $praat_dir."Feedback/";
$voice_recordings_dir   = $praat_dir."Recordings/";

$test3_scripts  		= $base_dir."Controller/helpers/Scripts/Raahemaa_test3/";
$test3_recs  		    = $base_dir_absolute."Audio_Resources/Raahemaa_test3/Recs/";
$test3_prompts  		= $base_dir_absolute."Audio_Resources/Raahemaa_test3/Prompts/";
$test3_fb_dir  			= $base_dir_absolute."Audio_Resources/Raahemaa_test3/Feedback/";
$test3_comments  		= $base_dir_absolute."Audio_Resources/Raahemaa_test3/Comments/";
$test3_requests  		= $base_dir_absolute."Audio_Resources/Raahemaa_test3/Requests/";

$polly_prompts_dir  	= "";
$log_file_path          = "D:\\xampp\\htdocs\\Web_Root\\Logs\\Raahemaa_test3\\";

//----- Call-Session Info variables-----
$callid 	   = "";
$thisCallStatus 	  = "Answered";    // Temporary assignment
$currentStatus 		  = "";
$destinationAppId     = "";
$useridUnCond 		  = "";
$useridUnEnc 		  = "";
$systemLanguage 	  ="Urdu";         // Language in which prompts will be played to the user
$countryCode 		  = "";
$sipProvider 	      = "WateenE1";

//----- Call Flow control -----
$phDirEnabled = "true";
$forwardedTo = array();    // Array of (EncPhNo-EffectNo, Number of times in this call)
$term = "#";
$atWhatAgeDoJobsKickIn = 0;
$atWhatAgeDoesFBKickIn = 0;
$atWhatAgeDoesClearVoiceKickIn = 0;

//----- Call Logging -----
$fh 		   = "";    // temprary variable to act as a place holder for file handle
$logEntry 	   = "";

//----- Global Voice Prompts---------
$ask_for_forwarding = "";
$what_to_do         = "";

//----- User ID's used for Testing rah-e-maa tests-----
$testers_user_ids = array(
    "3566",
    "26573",
    "1776",
    "142566",
    "142562",
    "147650"
);

// Cut-off limits, based on days, of previous calls and requests in Database
$callTableCutoff = getcallTableCutoff(5);
$reqTableCutoff  = getreqTableCutoff(5);

// Inbound ESL connection to FreeSwitch Server
$password = "Conakry2014";
$port 	  = "8021";
$host 	  = "127.0.0.1";
$fp 	  = event_socket_create($host, $port, $password); // Connection handle to FreeSwitch Server
$uuid 	  = ""; // Randomly generated value used by Freeswitch to identify one session (object for interaction with Call-legs) from another
if(isset($_REQUEST["uuid"])){  // session-id is passed from the Freeswitch
	$uuid = $_REQUEST["uuid"];
}

if(isset($_REQUEST["calltype"])){	// This is not incoming call as $calltype is set

	///////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////// OUTGOING CALLS ///////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////

	$calltype 	 = $_REQUEST["calltype"];
 	$testcall 	 = $_REQUEST["testcall"];
    $sipProvider = $_REQUEST["ch"];
	$userid 	 = $_REQUEST["phno"];
 	$oreqid 	 = $_REQUEST["oreqid"];
 	$recIDtoPlay = $_REQUEST["recIDtoPlay"];
 	$effectno 	 = $_REQUEST["effectno"];
 	$ocallid 	 = $_REQUEST["ocallid"];
 	$ouserid 	 = $_REQUEST["ouserid"];
 	$app 		 = $_REQUEST["app"];
 	$From 		 = $_REQUEST["From"];

    $currentStatus = 'InProgress';
	$useridUnEnc   = KeyToPh($userid);
	$countryCode   = getCountryCode($useridUnEnc);
	$temp 			 = getPreferredLangs($oreqid);
	$Langs 			 = explode(",", $temp);
	$systemLanguage  = $Langs[0];
	$ouserid = KeyToPh($ouserid);
	$callid  = makeNewCall($oreqid, $userid, $currentStatus, $calltype, $sipProvider);	// Create a row in the call table to store info related to current call

 	if(isset($_REQUEST["error"])){
 		$error= $_REQUEST["error"];
 		switch ($error) {
 			case 'USER_BUSY':
 			case 'CALL_REJECTED':
 				busyFCN($callid);
 				break;
 			case 'ALLOTTED_TIMEOUT':
 			case 'NO_ANSWER':
 			case 'RECOVERY_ON_TIMER_EXPIRE':
 				timeOutFCN($callid);
 				break;
 			case 'NO_ROUTE_DESTINATION':
 			case 'INCOMPATIBLE_DESTINATION':
 			case 'UNALLOCATED_NUMBER':
 				callFailureFCN($callid);
 				break;
 			default:
 				errorFCN($callid);
 				break;
 		}
 		exit(1);
 	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: ".$app.", Call Type: ".$calltype.", Phone Number: ".$userid.", Originating Request ID: ".$oreqid.", Call ID: ".$callid.", Country: PK, ouserid: ".$ouserid.", Country Code: " . $countryCode);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Call Table cutoff found at:".$callTableCutoff);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Req Table cutoff found at:".$reqTableCutoff);

	$polly_prompts_dir = $prompts_dir.$systemLanguage."/Polly/";

	sayInt($polly_prompts_dir. "sil1500.wav ".$polly_prompts_dir. "sil1500.wav ");

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PGame prompts directory set to: ". $polly_prompts_dir);

	selectDelPrompt();
	selectMainPrompt("FALSE", 1);

	if($sipProvider == "WateenE1"){
	    if($calltype=="Call-me-back"){
    		StartUpFn();
    		PollyGameAnswerCall($callid,"FALSE");
    	}else if($calltype=="Delivery"){
    		StartUpFn();
    		PollyGameMsgDelivery($callid,"FALSE");
    	}else{
	    	Prehangup();
	    }
	}
}
else{

	///////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////// INCOMING CALLS ///////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////

	$oreqid 		= "0";
	$currentStatus  = 'InProgress';

	$PollyNumber 			= "0428333112";

	$destinationAppId = calledID(); 	//which number was called in the current call?
	$destinationAppId = trim(preg_replace('/\s\s+/', ' ', $destinationAppId));

	$userid  = getCallerID();
	$userid  = trim(preg_replace('/\s\s+/', ' ', $userid));

	$ouserid = $userid;

	if(strpos($destinationAppId, $PollyNumber) !== FALSE) {

		$testcall 	 = "FALSE";
		$requestType = "Call-me-back";
		$calltype = 'Missed_Call';
		$app = 'PollyGame';
	}
	else {

		$testcall = "FALSE";
		$requestType = "";
		$calltype = 'Unknown';
		$app = 'Alien';
		Prehangupunknown();
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$useridUnCond 	= $userid;
	$useridUnEnc 	= conditionPhNo($userid, $calltype);
	$useridUnEnc	= trim(preg_replace('/\s\s+/', ' ', $useridUnEnc));
	$userid 		= PhToKeyAndStore($useridUnEnc, 0);
	$countryCode 	= getCountryCode($useridUnEnc);

	$callid = makeNewCall($oreqid, $userid, $currentStatus, $calltype, 'WateenE1');	// Create a row in the call table to store this incoming call

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PhToKeyAndStore returned :" . $userid);

	phoneNumBeforeAndAfterConditioning($useridUnCond, $useridUnEnc, $calltype, "");

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "App: ".$app.", Call Type: ".$calltype.", Phone Number: ".$userid.", Originating Request ID: ".$oreqid.", Call ID: ".$callid.", Country: PK, ouserid: ".$ouserid.", Country Code: " . $countryCode);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Call Table cutoff found at:".$callTableCutoff);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "Req Table cutoff found at:".$reqTableCutoff);

	$polly_prompts_dir = $prompts_dir.$systemLanguage."/Polly/";

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "PGame prompts directory set to: ". $polly_prompts_dir);

	selectDelPrompt();
	selectMainPrompt("FALSE", 1);

	// If its a missed call then reject() will generate 2 retries from the Cisco equipment. This is to ignore those. OR If its a self loop call... ignore it. &&$$** Added < '1000' in place of == '04238333111'
	if(searchCalls($userid)>1 || $userid < '1000') {

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Ignoring Call as it passed: searchCalls($userid)>1 || $userid < '1000' check."); 	 //&&$$** Added < '1000' in place of == '04238333111'
		rejectCall($app);
		exit(0);
	}

	if(searchCallsReq($userid) <= 0) { // Is there a pending request from this guy already which has never been retried?

		updateCallsReq($userid); // Upgrade all retry type Pending requests from this guy, if any.
		$reqid = createMissedCall('0', '0', $callid, $requestType, $userid, "Pending", $systemLanguage, "Urdu", "WateenE1", $destinationAppId);
	}
	else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Not making a request because there are already first try Call-me-back requests of status Pending from this phone number.");
	}

	$thisCallStatus = "Complete";
	$status = "Complete";
	updateCallStatus($callid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Complete. Now exiting.");
	markCallEndTime($callid);
	rejectCall($app);
	exit(0);
}

///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Polly Game /////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function PollyGameMsgDelivery(){  //  start PollyGameMsgDelivery()
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $praat_dir;
	global $polly_prompts_dir;
	global $what_to_do;
	global $userid;
	global $ouserid;
	global $recIDtoPlay;
	global $effectno;
	global $ocallid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing greetings for message delivery.");

	$Name = $praat_dir."UserNames/".getFilePath($ocallid.".wav", "TRUE")."UserName-".$ocallid.".wav";
	$promptType = "PGame Delivery Greetings";
	$breakLoop = "FALSE";
	while($breakLoop == "FALSE"){
		$breakLoop = "TRUE";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "About to listen to $promptType.");
		$result = sayInt($polly_prompts_dir."Greetings2.wav"."\n".$Name."\n".$polly_prompts_dir."Hereitis.wav"."\n");
		$breakLoop = bargeInToChangeLang($result, $breakLoop);
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Finished listening to $promptType.");
	}

	//Say out the message:
	$Path = $praat_dir."/ModifiedRecordings/".getFilePath($recIDtoPlay.".wav", "TRUE").$effectno."-s-".$recIDtoPlay.".wav";
	$repeat = "TRUE";
	$iter = 0;
	$playMsg = "TRUE";

	while($repeat == "TRUE"){
		if($playMsg == "TRUE"){
			if(!file_exists($Path)){
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "File cannot be found. Playing the prompt to wait.");
				sayInt($polly_prompts_dir."Processingplzwait.wav");//"Processing. Please wait!";

				$reps = 0;
				while(!file_exists($Path) && $reps<20){
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the clock as the user waits.");
					sayInt($polly_prompts_dir."Processingplzwait.wav");//"Processing. Please wait!";
					sayInt($praat_dir."clock_fast.wav");
					$reps = $reps+1;
				}
			}
			else{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now playing the clock sound.");
				sayInt($praat_dir."clock_fast.wav");
			}

			$iter = $iter+1;

			$presult = sayInt($Path . "\n" . $polly_prompts_dir. "sil500.wav");
			if ($presult->name == 'choice')
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$presult->value." to skip ".$Path.".");
			}

			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Played the message.");
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now playing message related options.");
			$repeat = "FALSE";	// If no action taken then loop will break
		}
		$result = gatherInput($what_to_do,
		array(
				"choices" => "[1 DIGITS], *",//Using the [1 DIGITS] to allow tracking wrong keys"rpt(1,rpt), fwd(2, fwd), cont(3,cont), feedback(8, feedback), quit(9, quit)",
				"mode" => 'dtmf',
				"bargein" => true,
				"repeat" => 2,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "keystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);

		if ($result->name == 'choice')// If User respond, then $result->name must be setted to choice
		{
			if ($result->value == 9)//'Sender's Phone Number'
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Sender's Phone number).");
				sayInt($polly_prompts_dir."senderPh.wav");	// The sender's phone number is:
				$occ = getCountryCode($ouserid);			// Sender's country code
				$oPhnoWoCC = substr($ouserid, strlen($occ));	// Sender's number wo country code

				$num12 = str_split($ouserid);//str_split($oPhnoWoCC);
				for($index1 = 0; $index1 < count($num12); $index1+=1)
				{
					$fileName = $num12[$index1].'.wav';
					$numpath = $polly_prompts_dir.$fileName;
					sayInt($numpath);
				}
				$repeat = "TRUE";
				$playMsg = "FALSE";
			}
			else if ($result->value == 1)//'rpt'
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Repeat).");
				$repeat = "TRUE";
				$playMsg = "TRUE";
			}
			else if ($result->value==2)//'fwd'
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Forward).");
				$repeat = "TRUE";
				PollyGameScheduleMsgDelivery($callid, $recIDtoPlay, $userid, $effectno, $Path);
				$playMsg = "TRUE";
			}
			else if($result->value==3)//'reply'
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Reply).");
				$repeat = "TRUE";
				$reply = "TRUE";
				PollyGameAnswerCall($callid, $reply);
				$playMsg = "TRUE";
			}
			else if($result->value==4)//'new'
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (New Recording).");

				$repeat = "TRUE";
				$reply = "FALSE";
				PollyGameAnswerCall($callid, $reply);
				$playMsg = "TRUE";
			}
			else{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (wrong key).");
				$repeat = "TRUE";
				sayInt($polly_prompts_dir."Wrongbutton.wav");
				$playMsg = "FALSE";
			}
		}
		else{
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User did not press any key.");
			sayInt($polly_prompts_dir."Nobutton.wav");
			$repeat = "FALSE";
			$playMsg = "FALSE";
		}
	}// end of main while loop while($repeat == "TRUE")

	sayInt($polly_prompts_dir."ContactDetails.wav");
	sayInt($polly_prompts_dir."Bye.wav");//"Thanks for calling. Good Bye."
	Prehangup();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging up.");
}// end PollyGameMsgDelivery()

function PollyGameAnswerCall($callid, $reply)//	start PollyGameAnswerCall()
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $scripts_dir;
	global $praat_dir;
	global $polly_prompts_dir;
	global $playInformedConsent;
	global $ask_for_forwarding;
	global $calltype;
	global $callid;
	global $userid;
	global $TreatmentGroup;
	global $Q;
	global $useridUnEnc;
	global $checkForQuota;
	global $recID;
	global $PGameCMBAge;
	global $atWhatAgeDoesClearVoiceKickIn;

	$firstEnc = "False";
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Inside PollyGameAnswerCall(). Calltype: ".$calltype);

	if($calltype != 'Delivery'){
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing Greetings");
			$promptType = "PGame Greetings";
			$breakLoop = "FALSE";
			while($breakLoop == "FALSE"){
				$breakLoop = "TRUE";
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "About to listen to $promptType.");
				$result = sayInt($polly_prompts_dir."Salaam.wav"." ".$polly_prompts_dir."sil1000.wav ".$polly_prompts_dir."Greetings.wav");
				$breakLoop = bargeInToChangeLang($result, $breakLoop);
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Finished listening to $promptType.");
			}
	}

	selectMainPrompt($reply, 1);
	if($calltype != 'SystemMessage' && $playInformedConsent == "TRUE" ){
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now playing Informed consent.");
		sayInt($polly_prompts_dir."InformedConsent.wav");
		$playInformedConsent = "FALSE";							// Play informed consent only once in each call
	}

	$recid = makeNewRec($callid);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Assigned Recording ID ".$recid);

	// For doing a rerecording
	$rerecordAllowed = 1; // Rerecord allowed in this call
    $rerecord = "TRUE";
	while($rerecord == "TRUE"){
		$rerecord = "FALSE";

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting the user to speak");
		if($PGameCMBAge < 0){
			$promptTheUserToSpeak = $polly_prompts_dir."Polly-intro-2.wav\n" . $polly_prompts_dir."sil500.wav\n" . $polly_prompts_dir."Ready-here-goes.wav\n";
		}
		else{
			$promptTheUserToSpeak = $polly_prompts_dir."Promptforspeaking.wav";
		}

		$result = recordAudio($promptTheUserToSpeak,		//"Just say something after the beep and Press # when done."
					array(
						"beep" => true, "timeout" => 600, "silenceTimeout" => 3, "maxTime" => 30, "bargein" => false, "terminator" => $term,
						"recordURI" => $scripts_dir."process_recording.php?recid=$recid",
						"format" => "audio/wav",
						"onHangup" => create_function("$event", "Prehangup()")
						)
					);

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Recording Complete. Result: ".$result);

		$filePath = $praat_dir;
		$fileNames[0] = "99-s";
		$fileNames[1] = "0-s";
		$fileNames[2] = "1-s";
		$fileNames[3] = "2-s";
		$fileNames[4] = "3-s";
		$fileNames[5] = "4-s";
		$fileNames[6] = "5-s";
		$fileNames[7] = "6-s";
		$fileNames[8] = "7-s";
		$fileNames[9] = "HIs";
		$fileNames[10] = "LOs";
		$fileNames[11] = "RVSs";
		$fileNames[12] = "BKMUZWHs";

		$Path = "";
		$repeat = "TRUE";

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting the user to get ready for effects.");
		sayInt($polly_prompts_dir."Readyforeffects.wav");//"Now get ready for the effects... Here it goes!!!"

		$NumOfEffects = 7;
		for($count = 0; $count <= $NumOfEffects && $repeat == "TRUE"; $count+=1)
		{
			$audioFileName = $fileNames[$count]."-".$recid.".wav";
			$Path = $filePath . "ModifiedRecordings/" . getFilePath($recid.".wav", "TRUE") . $audioFileName;

			$reps = 0;

			while((doesFileExist($audioFileName)=="0")){
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the clock as the user waits.");
				sayInt($praat_dir."clock_fast.wav");
				$reps = $reps+1;
			}
			sayInt($praat_dir."clock_fast.wav");

			$presult = sayInt($Path . "\n" . $polly_prompts_dir. "sil500.wav");
			if ($presult->name == 'choice')
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$presult->value." to skip ".$Path.".");
			}

			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Played effect number: ".$count);
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now playing effect related options.");
			$repeat = "FALSE";	// If not action taken then loop will break
			// "To Repeat, press one. To send to a friend, press two. To try another effect, press three",
			// Using the [1 DIGITS], * to allow tracking wrong keys"rpt(1,rpt), fwd(2, fwd), cont(3,cont), feedback(8, feedback), quit(9, quit)",

			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing main menu prompt to the user: ".$Ask_for_forwarding2);

			$result = gatherInput($ask_for_forwarding, array(
					"choices" => "[1 DIGITS], *",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "keystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);
			if ($result->name == 'choice'){
				if ($result->value == 1)//'rpt')
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Repeat).");
					$repeat = "TRUE";
					$count = $count - 1;
				}
				else if ($result->value==2)//'fwd')
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Forward).");
					$rerecordAllowed = 0;	// Can't rerecord in this call anymore
					selectMainPrompt($reply, 0);
					if($reply == "FALSE"){
						$repeat = "TRUE";
						PollyGameScheduleMsgDelivery($callid,$recid, $userid, $count, $Path);
					}
					else if($reply == "TRUE"){
						$repeat = "FALSE";
						PollyGameScheduleMsgReply($callid,$recid, $userid, $count, $Path);
					}
				}
				else if($result->value==3)//'cont')
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Next).");
					$repeat = "TRUE";
					if($count == $NumOfEffects){
						$count = -1;
					}
					continue;
				}
				else if ($result->value==4 && ($PGameCMBAge >= $atWhatAgeDoesClearVoiceKickIn))//'fwd' clear voice)
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Forward using unmodified voice).");
					$rerecordAllowed = 0;	// Can't rerecord in this call anymore
					selectMainPrompt($reply, 0);
					if($reply == "FALSE"){
						$repeat = "TRUE";
						PollyGameScheduleMsgDelivery($callid,$recid, $userid, 99, $Path);	// number 4 is the unmodified voice
					}
					else if($reply == "TRUE"){
						$repeat = "FALSE";
						PollyGameScheduleMsgReply($callid,$recid, $userid, 99, $Path);	// number 4 is the unmodified voice
					}
				}
				else if($result->value==5)//'ReM Hook')
				{
					{
						test3_Menu();
						$repeat = "TRUE";
					}
				}
				else if($result->value==8)//'feedback')
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Feedback).");
					$fbtype = "UInit";
					$fbid = makeNewFB($fbtype, $callid);
					$repeat = "TRUE";
					$feedBack = recordAudio($polly_prompts_dir."Recordyourfeedback.wav",
							array(
								"beep" => true, "timeout" => 600.0, "silenceTimeout" => 4.0, "maxTime" => 60, "bargein" => false, "terminator" => "#",
								"recordURI" => $scripts_dir."process_feedback.php?fbid=$fbid&fbtype=$fbtype",
								"format" => "audio/wav",
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Feedback Recording Complete. Result: ".$feedBack);
					sayInt($polly_prompts_dir."ThanksforFeedback.wav");
					$count = $count - 1;
				}
				else if ($result->value==0 && $rerecordAllowed == 1)//'rerecord')
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (Rerecord).");
					$repeat = "FALSE";
					$rerecord = "TRUE";
				}
				else
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$result->value." (wrong key).");
					$repeat = "TRUE";
					sayInt($polly_prompts_dir."Wrongbutton.wav");
					$count = $count - 1;// Dealing with bad choices as repeats.
				}
			}
			else
			{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User did not press any key.");
				sayInt($polly_prompts_dir."Nobutton.wav");
				$repeat = "FALSE";
			}
			if($count == $NumOfEffects)
			{
				$count = -1;
			}
		}//Continue in for loop: play next effect
	}// Only loop this loop if the user want to rerecord
	if($reply != "TRUE"){// If this function was being used to record a reply then don't hangup from here
		sayInt($polly_prompts_dir."Bye.wav");//"Thanks for calling. Good Bye."
		Prehangup();
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging up.");
	}
}// end PollyGameAnswerCall() function

function PollyGameScheduleMsgDelivery($callid, $recid, $telNumber, $count, $songpath){
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $scripts_dir;
	global $praat_dir;
	global $countryCode;
	global $systemLanguage;
	global $polly_prompts_dir;
	global $term;
	global $dlvRequestType;
	global $AlreadygivenFeedback;
	global $userid;
	global $callerPaidDel;
	global $PGameCMBAge;
	global $phDirEnabled;
	global $sipProvider;
	global $hasTheUserRecordedAName;
	global $oreqid;
	global $WaitWhileTheUserSearchesForPhNo;

	$numOfDigs = "[11-12 DIGITS]";

	//Prompt for friends' numbers
	$FriendsNumber = 'true';
	$numNewRequests = 1;
	$retryNumEntry = "false";
	while($FriendsNumber != 'false')
	{
		$phoneNumChosen = "None";
		$phoneNumber = "";
		$NoOfEntriesInDir = 0;
		$NewNumberEntered = "true";
		if($phDirEnabled == "true" && $retryNumEntry == "false"){	// Is Phone directory feature enabled? AND this is not a retry of number entry?
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Phone Directory feature is enabled.");
			$PhDir = getPhDir();
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Current Phone Directory: ".$PhDir);
			$entries = explode("-", $PhDir);
			$NoOfEntriesInDir = count($entries)-1;
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Number of directory entries: ".$NoOfEntriesInDir);
			if($NoOfEntriesInDir > 0){	// there are entries in the Phone Directory for this user
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now Giving option to select a directory entry.");

				// This is the Phone Number Directory
				// Create the prompt here
				$DirPrompt = "";
				for($j=1; $j < ($NoOfEntriesInDir+1); $j++){

					$DirPrompt = $DirPrompt . "\n" . $praat_dir . "FriendNames/".getFilePath($userid.".wav", "TRUE") . $userid . "-" . $entries[$j] . ".wav";
					$DirPrompt = $DirPrompt . "\n" . $polly_prompts_dir . "For.wav";
					$DirPrompt = $DirPrompt . "\n" . $polly_prompts_dir . "SendTo" . $j . ".wav";
				}

				$DirPrompt = $DirPrompt . "\n" . $polly_prompts_dir."NewNumber.wav";
				// Give the choice to enter a new number (0) or choose a number from the list
				$Choice = gatherInput($DirPrompt,
				array(
					"choices"=> "[1 DIGITS], *",
					"mode" => 'dtmf',
					"bargein" => true,
					"attempts" => 2,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "keystimeOutFCN",
					"timeout"=> 7,
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
				if($Choice->name == 'choice'){	// otherwise the user will be asked to enter a number.
					if($Choice->value == 0){	// Enter new number
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$Choice->value." (Enter a number manually).");
						// do nothing, as $NewNumberEntered is already true, so the user will be asked to enter a number
					}
					else if($NoOfEntriesInDir >= $Choice->value){
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$Choice->value." (".$entries[$Choice->value].").");
						$phoneNumber = $entries[$Choice->value];
						$NewNumberEntered = "false";
						$phoneNumChosen = "choice";
						updatePhDir($phoneNumber);
					}
					else{
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$Choice->value." (wrong key).");
						sayInt($polly_prompts_dir."Wrongbutton.wav");
					}
				}
				else{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User did not press any key.");
				}
			}
		}

		if($NoOfEntriesInDir == 0 || $NewNumberEntered == "true" || $retryNumEntry == "true"){
			$breakLoop = "TRUE";
			if($WaitWhileTheUserSearchesForPhNo == "TRUE"){
				$breakLoop = "FALSE";
			}
			$loopTries = 0;
			$op1 = "TRUE";
			$op2 = "FALSE";
			while($breakLoop == "FALSE" && $loopTries < 3){
				if($op1 == "TRUE"){
					$action = gatherInput($polly_prompts_dir."EHL-fwd-2.wav", array("choices" => "[1 DIGITS], *", "mode" => 'dtmf', "bargein" => true, "repeat" => 1, "timeout"=> 15, "onHangup" => create_function("$event", "Prehangup()")));//"Whenever you are ready to enter the phone number, press 1."
				}
				else if($op2 == "TRUE"){
					$action = gatherInput($polly_prompts_dir."EHL-fwd-2.wav\n".$polly_prompts_dir."EHL-fwd-4-nopress.wav\n".$polly_prompts_dir."SendTo2.wav\n", array("choices" => "[1 DIGITS], *", "mode" => 'dtmf', "bargein" => true, "repeat" => 9, "timeout"=> 20, "onHangup" => create_function("$event", "Prehangup()")));//"Whenever you are ready to enter the phone number, press 1."
				}
				if($action->name != 'choice'){
					if($op1 == "TRUE"){
						$op1 = "FALSE";
						$op2 = "TRUE";
					}
					else if($op2 == "TRUE"){
						$breakLoop = "TRUE";
						return ($numNewRequests-1);
					}
				}
				else if($action->value == 1){
					$breakLoop = "TRUE";
				}
				else if($action->value == 2){
					$breakLoop = "TRUE";
					return "FALSE";
				}
				else if($loopTries < 2){
				}
				else{
					return ($numNewRequests-1);
				}
				$loopTries++;
			}
			$retryNumEntry = "false";
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now getting friend's phone number for request number".$numNewRequests.".");
			$NumberList = gatherInput($polly_prompts_dir."FriendnopromptWOHash.wav",//"Please enter the phone number of your friend followed by the pound key",
				array(
					"choices"=>$numOfDigs,
					"mode" => 'dtmf',
					"bargein" => true,
					"attempts" => 3,
					"timeout"=> 30,
					"interdigitTimeout"=> 20,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "keystimeOutFCN",
					"terminator" => $term,
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
			if($NumberList->name == 'choice'){
				sayInt($polly_prompts_dir."Friendnorepeat.wav");				//here is the number you entered
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Friend's phone number entered: ".$NumberList->value.". Now playing it.");

				$num12 = str_split($NumberList->value);
				for($index1 = 0; $index1 < count($num12); $index1+=1)
				{
					if($index1 == 0){
						$fileName = $num12[$index1].'.wav';
						$numpath = $polly_prompts_dir.$fileName;
					}
					else{
						$fileName = $num12[$index1].'.wav';
						$numpath = $numpath . "\n" . $polly_prompts_dir.$fileName;
					}
				}
				$presult = sayInt($numpath);
				if ($presult->name == 'choice')
				{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$presult->value." to skip ".$numpath.".");
				}
			}
			else{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Timed out. No number entered. Now hanging up.");
					sayInt($polly_prompts_dir."Bye.wav");//"Thanks for calling. Good Bye."
					$FriendsNumber = 'false';
					Prehangup();
			}

			if($FriendsNumber != 'false'){
				// Number Confirmation
				$WrongButtonPressed = 'TRUE';
				while($WrongButtonPressed == 'TRUE'){
					$WrongButtonPressed = 'FALSE';
					$NumCorrect = gatherInput($polly_prompts_dir."Numberconfirm.wav",//"If this is correct, press one, otherwise, press two",
						array(
							"choices"=> "[1 DIGITS], *",
							"mode" => 'dtmf',
							"bargein" => true,
							"attempts" => 2,
							"onBadChoice" => "keysbadChoiceFCN",
							"onTimeout" => "keystimeOutFCN",
							"timeout"=> 10,
							"onHangup" => create_function("$event", "Prehangup()")
							)
						);
					if($NumCorrect->name == 'choice' && $NumCorrect->value != 1 && $NumCorrect->value != 2){	// Wrong button
						$WrongButtonPressed = 'TRUE';
						sayInt($polly_prompts_dir."Wrongbutton.wav");
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed a wrong key: ".$NumCorrect->value);
						// Repeat the phone number
						sayInt($polly_prompts_dir."Friendnorepeat.wav");				//here is the number you entered
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Friend's phone number entered: ".$NumberList->value.". Now playing it again.");

						$num12 = str_split($NumberList->value);
						for($index1 = 0; $index1 < count($num12); $index1+=1)
						{
							if($index1 == 0){
								$fileName = $num12[$index1].'.wav';
								$numpath = $polly_prompts_dir.$fileName;
							}
							else{
								$fileName = $num12[$index1].'.wav';
								$numpath = $numpath . "\n" . $polly_prompts_dir.$fileName;
							}
						}
						$presult = sayInt($numpath);
						if ($presult->name == 'choice')
						{
							writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$presult->value." to skip ".$numpath.".");
						}
					}
				}

				if($NumCorrect->name == 'choice'){
					if($NumCorrect->value == 1){//correct
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User Confirmed the number by pressing:".$NumCorrect->value);
						$phoneNumChosen = $NumberList->name;
						$phoneNumber = $NumberList->value;
						$NewNumberEntered = "true";

					}
					else if($NumCorrect->value == 2){ //If number entered is not correct
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User wants to enter the number again by pressing: ".$NumCorrect->value);
						sayInt($polly_prompts_dir."Tryagain.wav");//"Please try again"
						$retryNumEntry = "true";
					}
				}
				else{
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Timed out. User did not press any key. Now hanging up.");
					$FriendsNumber = 'false';
					sayInt($polly_prompts_dir."Bye.wav");//"Thanks for calling. Good Bye."
					Prehangup();
				}
			}
		}// if($NoOfEntriesInDir == 0 || $NewNumberEntered == "true")
		if($phoneNumChosen == 'choice'){

			if($hasTheUserRecordedAName == "FALSE"){	// Only record the name once per call
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now recording user's name.");
				// Prompt the user for his/her name
				$ownName = recordAudio($polly_prompts_dir."Recordyourname.wav",//"Please record your name, so that your friend can send you a message back",
							array(
								"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => $term,
								"recordURI" => $scripts_dir."process_UserNamerecording.php?callid=$callid",
								"format" => "audio/wav",
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);

				$hasTheUserRecordedAName = "TRUE";
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Recording of user's name complete.".$ownName);
			}
			// Create a new Request here
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now formatting the number (if required).");		// added
			$frndNoEnc = "";
			if($NewNumberEntered == "true"){
				$formattedPhNo = conditionPhNo($phoneNumber, "Delivery");
				$frndNoEnc = PhToKeyAndStore($formattedPhNo, $userid);	// added
			}
			else{
				$frndNoEnc = $phoneNumber;	// chosen from existing directory
			}
			//----->
			if(isForwardingAllowedToThisPhNo($frndNoEnc, $recid, 'msg', $count) == 'yes'){
				$reqsipProvider = whatWasThesipProviderOfTheOriginalRequest($oreqid);
				$reqid = makeNewReq($recid, $count, $callid, $dlvRequestType, $frndNoEnc, "WPending", $systemLanguage, "Urdu", $reqsipProvider);

				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Assigned request ID: ".$reqid);
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now recording user's friend's name");

				if($NewNumberEntered == "true" && $phDirEnabled == "true"){
							$FName = gatherInput($polly_prompts_dir."SaveNumber.wav",
							array(
								"choices"=> "[1 DIGITS], *",
								"mode" => 'dtmf',
								"bargein" => true,
								"attempts" => 1,
								"onBadChoice" => "keysbadChoiceFCN",
								"onTimeout" => "keystimeOutFCN",
								"timeout"=> 7,
								"onHangup" => create_function("$event", "Prehangup()")
								)
							);
							if($FName->name == 'choice'){
								if($FName->value == 1){
									writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$FName->value." (Record Friend's name).");

									$friendsName = recordAudio($polly_prompts_dir."NameOfNumber.wav",//"Okay! After the beep please record your friend's name",
										array(
											"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => $term,
											"recordURI" => $scripts_dir."process_FriendNamerecording.php?reqid=".$userid."-".$frndNoEnc,
											"format" => "audio/wav",
											"onError" => create_function("$event", 'sayInt("Wrong Input");'),
											"onTimeout" => create_function("$event", 'sayInt("No Input");'),
											"onHangup" => create_function("$event", "Prehangup()")
											)
										);
									writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User's friend's name recording complete.".$friendsName);
									updatePhDir($frndNoEnc);
							}
							else if($FName->value == 2){
								writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$FName->value." (Do not record Friend name).");
							}
							else{
								writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$FName->value." (Wrong key).");
							}
						}
						else{
							writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User did not press any key.");
						}
					}
					sayInt($polly_prompts_dir."Forward_confirmation2.wav");		// Thanks your message would be sent soon.
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User is thanked and informed that the message would be sent soon.");
				}

				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Asking the user if he wants to record another name.");
				$WrongButtonPressed = 'TRUE';
				while($WrongButtonPressed == 'TRUE'){
					$WrongButtonPressed = 'FALSE';
					$MoreNumbers = gatherInput($polly_prompts_dir."Anotherfriend-otherwise.wav",//"To add another number, press one, or if you are done, press two",
						array(
							"choices"=> "[1 DIGITS], *",
							"mode" => 'dtmf',
							"bargein" => true,
							"attempts" => 2,
							"onBadChoice" => "keysbadChoiceFCN",
							"onTimeout" => "keystimeOutFCN",
							"timeout"=> 10,
							"onHangup" => create_function("$event", "Prehangup()")
							)
						);
					if($MoreNumbers->name == 'choice' && $MoreNumbers->value != 1 && $MoreNumbers->value != 2){	// Wrong button
						$WrongButtonPressed = 'TRUE';
						sayInt($polly_prompts_dir."Wrongbutton.wav");
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed a wrong key: ".$MoreNumbers->value);
					}
				}

				if($MoreNumbers->name == 'choice'){
					if($MoreNumbers->value == 2){
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User presses ".$MoreNumbers->value." to say that he is done");
						$FriendsNumber = 'false';
					}
					else if($MoreNumbers->value == 1){
						writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User presses ".$MoreNumbers->value." to say that he wants to record another number.");
						$numNewRequests++;
					}
				}
				else{	// No key was pressed so, assume that he does not want to add another number
					writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Timed out. User did not press any key. Now proceeding.");
					$FriendsNumber = 'false';
				}
			}
			else if($retryNumEntry == "true"){
				// continue
			}
			else{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Timed out. No number entered. Now hanging up.");
				sayInt($polly_prompts_dir."Bye.wav");//"Thanks for calling. Good Bye."
				$FriendsNumber = 'false';
				Prehangup();
			}
	}//End of while($Friendsnumber != false)

	// Requesting feedback
	$previousFeedBack = gaveFeedBack($telNumber);	// Did he ever give feedback before?
	if(((($PGameCMBAge > 5 && $previousFeedBack == 0) || $PGameCMBAge % 20 == 0) && $AlreadygivenFeedback == "FALSE") && $PGameCMBAge != 0){

		$AlreadygivenFeedback = "TRUE";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Requesting System prompted feedback ".$PGameCMBAge." ".$previousFeedBack." ".$telNumber.".");
		$fbtype = $dlvRequestType . "-SPrompt";
		$fbid = makeNewFB($fbtype, $callid);

		$feedBack = recordAudio($polly_prompts_dir."Recordyourfeedback.wav",//
				array(
					"beep" => true, "timeout" => 600.0, "silenceTimeout" => 4.0, "maxTime" => 30, "bargein" => false, "terminator" => $term,
					"recordURI" => $scripts_dir."process_feedback.php?fbid=$fbid&fbtype=$fbtype",
					"format" => "audio/wav",
					"onHangup" => create_function("$event", "Prehangup()")
					)
				);
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Feedback Recording Complete. Result: ".$feedBack);
		sayInt($polly_prompts_dir."ThanksforFeedback.wav");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "System prompted feedback recording complete.");
	}

	return $numNewRequests;
}// PollyGameScheduleMsgDelivery(

function PollyGameScheduleMsgReply($callid, $recid, $telNumber, $count, $songpath){
	// PHP requires all globals to be called like this from within all functions. Not using this was creating access problems.
	global $scripts_dir;
	global $praat_dir;
	global $systemLanguage;
	global $polly_prompts_dir;
	global $term;
	global $dlvRequestType;
	global $sipProvider;
	global $hasTheUserRecordedAName;
	global $oreqid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Now recording user's name.");
	// Prompt the user for his/her name
	if($hasTheUserRecordedAName == "FALSE"){
		$ownName = recordAudio($polly_prompts_dir."Recordyourname.wav",//"Please record your name, so that your friend can send you a message back",
					array(
						"beep" => true, "timeout" => 600.0, "silenceTimeout" => 2.0, "maxTime" => 4, "bargein" => false, "terminator" => $term,
						"recordURI" => $scripts_dir."process_UserNamerecording.php?callid=$callid",
						"format" => "audio/wav",
						"onHangup" => create_function("$event", "Prehangup()")
						)
					);
		$hasTheUserRecordedAName = "TRUE";
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Recording of user's name complete.".$ownName);
	}
	// Create a new Request here
	$reqsipProvider = whatWasThesipProviderOfTheOriginalRequest($oreqid);
	$reqid = makeNewReq($recid, $count, $callid, $dlvRequestType, getPhNo(), "WPending", $systemLanguage, "Urdu", $reqsipProvider);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Assigned request ID: ".$reqid);

	sayInt($polly_prompts_dir."Forward_confirmation2.wav");		// Thanks your message would be sent soon.
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User is thanked and informed that the message would be sent soon.");
}// end PollyGameScheduleMsgReply()

function selectMainPrompt($reply, $rerecordAllowed){// start selectMainPrompt()
	global $PGameCMBAge;
	global $atWhatAgeDoesFBKickIn;
	global $atWhatAgeDoesClearVoiceKickIn;
	global $ask_for_forwarding;
	global $polly_prompts_dir;
	global $test1_prompts, $SA_prompts, $test4_prompts;

	$ask_for_forwarding = "";
	if($rerecordAllowed == 1){
		$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."12.wav"  . "\n" . $polly_prompts_dir."sil250.wav";		// to rerecord, press 0
	}

	$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."Hear-your-recording-nopress.wav" . "\n" . $polly_prompts_dir."SendTo1.wav";		// to relisten, press 1

	if($reply == "TRUE"){
		$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."15.wav";		// to reply using this voice, press 2
	}
	else{
		$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."14.wav";		// to send to friends, press 2
	}

	$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."16.wav";		// to go to the next effect, press 3

	if($PGameCMBAge >= $atWhatAgeDoesClearVoiceKickIn){
		$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."26.wav" . "\n" . $polly_prompts_dir."SendTo4.wav";		// to send message in your unmodified voice, press 4
	}

	//$ask_for_forwarding = $ask_for_forwarding." ". $test1_prompts."Test2hook.wav ";
	//$ask_for_forwarding = $ask_for_forwarding." ". $test4_prompts."Superabbu.wav ";

	if($PGameCMBAge >= $atWhatAgeDoesFBKickIn){
		$ask_for_forwarding = $ask_for_forwarding . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."19.wav";		// for feedback, press 8
	}
}// end selectMainPrompt()


function selectDelPrompt(){ // start selectDelPrompt()
	global $what_to_do;
	global $polly_prompts_dir;

	$what_to_do = "";
	$what_to_do = $what_to_do . "\n" . $polly_prompts_dir."21.wav";												// to relisten, press 1
	$what_to_do = $what_to_do . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."22.wav";		// to send to friends, press 2
	$what_to_do = $what_to_do . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."23.wav";		// to reply, press 3
	$what_to_do = $what_to_do . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."24.wav";		// to record a new mestest3ge, press 4
	$what_to_do = $what_to_do . "\n" . $polly_prompts_dir."sil250.wav" . "\n" . $polly_prompts_dir."20.wav";		// to listen to the phone number of the sender, press 0
}// end selectDelPrompt()



///////////////////////////////////////////////////////////////////////////////////////
//*********************************** < Suno Abbu - Test 3> *********************************//
///////////////////////////////////////////////////////////////////////////////////////

function test3_Menu(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $test3_prompts, $test3_recs, $userid;

	sayInt($test3_prompts."Test3mainprompt1.wav");

	$loop=true;

	while($loop){

		$result = gatherInput($test3_prompts."Test3mainprompt2.wav ".$test3_prompts."wapis_janay_kay_liye.wav", array(
				"choices" => "[1 DIGITS]",
				"mode" => 'dtmf',
				"bargein" => false,
				"repeat" => 2,
				"timeout"=> 10,
				"onBadChoice" => "keysbadChoiceFCN",
				"onTimeout" => "ReMkeystimeOutFCN",
				"onHangup" => create_function("$event", "Prehangup()")
			)
		);

		if($result->value == "1"){
			test3_Play("Tip");
		}

		else if($result->value == "2"){
			test3_Play("Cost");
		}

		else if($result->value == "3"){
			test3_Play("Story");
		}

		else if($result->value == "4"){
			$record_id = test3_CreateFBEntry();
			if ($record_id) {
				test3_Record("fb", $record_id);
			}
		}
		else if($result->value == "5"){
			$loop = false;
		}
	}
}

function test3_Delivery($del_id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $test3_prompts, $test3_recs;

	if ($params = test3_GetDeliveryParams($del_id)) {

		$id   = $params["file_id"];
		$fuid = $params["fuid"];
		$type = $params["type"];

		sayInt($test3_prompts."Messagereceived1.wav");
		sayInt($test3_recs."U".$fuid.".wav");
		sayInt($test3_prompts."Messagereceived2.wav");

		$loop=true;

		while($loop){

			sayInt($test3_recs.$type.$id.".wav");

			$result = gatherInput($test3_prompts."Messageoptions.wav ", array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);

			if($result->value == "1"){
				continue;
			}

			else if($result->value == "2"){
				test3_Forward($id, $type);
			}

			else if($result->value == "3"){
				test3_Menu();
			}
		}
	}
}

function test3_Play($type){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $test3_prompts;
	global $test3_recs;
	global $test3_scripts;
	global $userid;

	$files = test3_GetFile($userid, $type);

	if (!$files) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " error in fetching $type files.");
		return;
	}

	sayInt($test3_prompts.$type."intro.wav ");

	if ($type == "Story") {
		sayInt($test3_prompts."Storydisclaimer1.wav");
	}

	$count = 0;

	foreach ($files as $file) {

		if ($count > 0 && $type == "Story") {
			sayInt($test3_prompts."Nextstory.wav");
		}

		$loop        = true;
		$break_outer = false;
		$captured    = false;

		while($loop) {

			if ($break_outer) break;

			sayInt($test3_recs.$file['name']);

			if (!$captured) {
				if ($type == "Tip") {
					test3_CaptureEvent($file['file_id'], 1);
				}else if($type == "Cost") {
					test3_CaptureEvent($file['file_id'], 2);
				}else if($type == "Story") {
					if ($file["cat"] == "admin") {
						test3_CaptureEvent($file['file_id'], 3);
					}else if ($file["cat"] == "user"){
						test3_CaptureEvent($file['file_id'], 17);
					}
				}
				$captured = true;
			}

			$result = gatherInput($test3_prompts.$type."options1.wav", array(
					"choices" => "[1 DIGITS]",
					"mode" => 'dtmf',
					"bargein" => false,
					"repeat" => 2,
					"timeout"=> 10,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
				)
			);

			if($result->value == "1"){

				continue; // Repeat
			}

			else if($result->value == "2"){

				if ($type == "Story") {
					$record_id = test3_CreateRaayeEntry($file['file_id']);
					test3_Record("comment", $record_id);
				}elseif ($type == "Cost") {
					test3_Forward($file['file_id'], $type);
				}elseif ($type == "Tip") {
					$inner_loop 	= true;
					$inner_captured = false;

					while ($inner_loop) {

						test3_CaptureEvent($file['file_id'], 6);

						sayInt($test3_recs.$file['info']);

						if (!$inner_captured) {
							if ($type == "Tip") {
								test3_CaptureEvent($file['info'], 6);
							}else if($type == "Cost") {
								test3_CaptureEvent($file['info'], 5);
							}
							$inner_captured = true;
						}

						$inner_result = gatherInput($test3_prompts.$type."options2.wav", array(
								"choices" => "[1 DIGITS]",
								"mode" => 'dtmf',
								"bargein" => false,
								"repeat" => 2,
								"timeout"=> 10,
								"onBadChoice" => "keysbadChoiceFCN",
								"onTimeout" => "ReMkeystimeOutFCN",
								"onHangup" => create_function("$event", "Prehangup()")
							)
						);

						if($inner_result->value == "1"){
							continue;
						}else if($inner_result->value == "2"){
							test3_Forward($file['file_id'], $type, true);
						}else if($inner_result->value == "3"){
							if ($type == "Tip") {
								$record_id = test3_CreateRequestEntry();
								if ($record_id) {
									test3_Record("request", $record_id);
								}
							}
						}else if($inner_result->value == "4"){
							$inner_loop  = false;
							$break_outer = true;
						}else if($inner_result->value == "5"){
							return;
						}
					}
				}

			}

			else if($result->value == "3"){

				if ($type == "Story") {
					$record_id = test3_CreateStoryEntry();
					test3_Record("story", $record_id);
				}elseif ($type == "Cost") {
					$loop = false;
				}elseif ($type == "Tip") {
					test3_Forward($file['file_id'], $type);
				}
			}

			else if($result->value == "4"){

				if ($type == "Story") {
					test3_ListenToComments($file['file_id']);
				}elseif ($type == "Cost") {
					return;
				}elseif ($type == "Tip") {
					$loop = false;
				}
			}
			else if($result->value == "5"){
				if ($type == "Tip") {
					return;
				}elseif ($type == "Story") {
					test3_Forward($file['file_id'], $type);
				}
			}else if($result->value == "6"){
				if ($type == "Story") {
					$loop = false;
				}
			}else if($result->value == "7"){
				if ($type == "Story") {
					return;
				}
			}
		}
		$count++;
	}
}

function test3_GetComments($story_id){

	global $userid;
	global $test3_prompts;
	global $test3_scripts;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $test3_scripts."get_comments.php?story_id=".$story_id;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function test3_ListenToComments($story_id){

	global $callid;
	global $userid;
	global $test3_prompts;
	global $test3_scripts;
	global $test3_comments;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$result = test3_GetComments($story_id);

	if($result){

		$comments = $result["comments"];

		if($result["len"] <= 0){
			sayInt($test3_prompts."no-comments.wav");
		}else{

			sayInt($test3_prompts."Comments.wav");

			$next_comment = true;
			for($i = 0; $i < $result["len"]; $i++) {

				if (!$next_comment) break;

				$loop = true;
				while ($loop) {

					sayInt($test3_comments.$comments[$i].".wav");

					$prompt =   $test3_prompts."Commentoptions.wav";

					$choice = gatherInput($prompt, array(
				        "choices" => "[1 DIGITS], *, #",
						"mode" => 'dtmf',
						"bargein" => true,
						"repeat" => 3,
						"timeout"=> 5,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"onHangup" => create_function("$event", "Prehangup()")
				        )
				    );

					if($choice->value=="1"){
						$loop = true;
					}else if($choice->value=="2"){
						$loop = false;
					}else if($choice->value=="4"){
						$loop = false;
						$next_comment = false;
					}else if($choice->value=="3"){
						$record_id = test3_CreateRaayeEntry($story_id);
						test3_Record("comment", $record_id);
						$loop = false;
						$next_comment = true;
					}
				}
			}
		}
	}else{
		sayInt( $PQPrompts."error.wav ");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Error: Comments returned with error. Exiting.");
		//PQLog("error", "comments", "exiting");
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function test3_CaptureEvent($f_id, $action_id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $test3_scripts;
	global $userid, $callid;

	$result = doCurl($test3_scripts."set_call.php?uid=".$userid."&f_id=".$f_id."&call_id=".$callid."&action_id=".$action_id);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return true;
	}
}

function test3_Forward($file_id, $type, $info = false) {

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $test3_prompts;
	global $test3_scripts;
	global $userid;
	global $callid, $SystemLanguage, $MessageLanguage, $channel;

	$prompt = $SA_prompts."Forward1.wav";

	$loop = true;

	$number = "";

	while($loop){
		$NumberList = gatherInput($prompt, array(
				        "choices" => "[11 DIGITS]",
						"mode" => 'dtmf',
						"bargein" => true,
						"repeat" => 3,
						"timeout"=> 30,
						"interdigitTimeout"=> 20,
						"onBadChoice" => "keysbadChoiceFCN",
						"onTimeout" => "ReMkeystimeOutFCN",
						"terminator" => "#",
						"onHangup" => create_function("$event", "Prehangup()")
				    )
				);

		if($NumberList->name == 'choice'){

			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Friend's phone number entered: ".$NumberList->value.". Now playing it.");

			$num12 = str_split($NumberList->value);

			for($index1 = 0; $index1 < count($num12); $index1 += 1){
				if($index1 == 0){
					$fileName = $num12[$index1].'.wav';
					$numpath = $test3_prompts.$fileName;
				}
				else{
					$fileName = $num12[$index1].'.wav';
					$numpath = $numpath . "\n" . $test3_prompts.$fileName;
				}
			}

			sayInt($test3_prompts."Forward8.wav ");
			$presult = sayInt($numpath);

			if ($presult->name == 'choice'){
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "User pressed ".$presult->value." to skip ".$numpath.".");
			}

			$choice = gatherInput(
					$test3_prompts."Forward2.wav ", array(
			        "choices" => "[1 DIGITS],*,#",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 3,
					"timeout"=> 5,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
		        )
		    );

			if($choice->value=="1"){
				$number = $NumberList->value;
				if ($dinfo = test3_GetDeliveryInfo()) {
					//if ($dinfo["count"] <= 0)
						test3_Record("name", $userid);

				}
				$dreq = test3_CreateDeliveryRequest($file_id, $type,PhToKeyAndStore($number, $userid), $info);
				if ($dreq) {
					if ($userid == 3566) {
						if(makeNewReq($dreq["id"], 23704330, $callid, "Delivery", PhToKeyAndStore($number, $userid), "Pending", $SystemLanguage, $MessageLanguage, $channel)){
							test3_CaptureEvent($dreq["id"], 8);
							sayInt($test3_prompts."Forward6.wav ");
							$loop   = false;
						}else
							$loop   = true;
					}else{
						if(makeNewReq($dreq["id"], 23704330, $callid, "Delivery", PhToKeyAndStore($number, $userid), "Pending", $SystemLanguage, $MessageLanguage, $channel)){
						sayInt($test3_prompts."Forward6.wav ");
						$loop   = false;
					}else
						$loop   = true;
					}
				}else
					$loop   = true;
				$loop = false;
			}else if($choice->value=="2"){
				$loop = true;
			}
		}
		else{
			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Timed out. No number entered. Now hanging up.");
			$FriendsNumber = 'false';
			Prehangup();
		}
	}
}

function test3_CreateDeliveryRequest($file_id, $type, $fuid, $info = false){

	global $test3_scripts;
	global $userid;
	global $callid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $test3_scripts.'new_delivery_request.php?uid='.$userid.'&file_id='.$file_id.'&call_id='.$callid.'&fuid='.$fuid.'&type='.$type.'&info='.intval($info);
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function test3_GetDeliveryInfo(){

	global $test3_scripts;
	global $userid;
	global $callid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $test3_scripts.'get_delivery_info.php?uid='.$userid;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function test3_GetDeliveryParams($id){

	global $test3_scripts;
	global $userid;
	global $callid;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	$url    = $test3_scripts.'get_delivery_params.php?id='.$id;
	$result = doCurl($url);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=true.");
		return false;
	}else{
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning, error=false.");
		return $result;
	}
}

function test3_GetFile($uid, $type){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $test3_scripts;
	global $userid, $callid;

	$result = doCurl($test3_scripts."get_file_id.php?uid=".$uid."&type=".$type."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["files"];
	}
}

function test3_Record($type, $record_id){

	global $callid;
	global $userid;
	global $test3_prompts;
	global $test3_recs;
	global $test3_scripts;
	global $calltype;
	global $test3_comments;
	global $test3_requests;
	global $test3_fb_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	if($type == 'request'){
		$maxtime = 60;
		$prompt  = $test3_prompts."Inforequest.wav ".$test3_prompts."Beepcomment.wav ";
	}else if($type == 'name'){
		$maxtime = 10;
		$prompt  = $test3_prompts."Forward3.wav ";
	}else if($type == 'story'){
		$maxtime = 120;
		$prompt  = $test3_prompts."Apnistory.wav ".$test3_prompts."Storydisclaimer2.wav ".$test3_prompts."Beepstory.wav ";
	}else if($type == 'comment'){
		$maxtime = 60;
		$prompt  = $test3_prompts."Storycomment.wav ".$test3_prompts."Commentdisclaimer.wav ".$test3_prompts."Beepcomment.wav ";
	}else if($type == 'fb'){
		$maxtime = 60;
		$prompt  = $test3_prompts."Mainfeedback.wav ";
	}

	$uri = $test3_scripts."save_recording.php?type=".$type."&uid=".$userid."&record_id=".$record_id."&call_id=".$callid."&suno_abbu=true";

    $loop=true;

    while ($loop) {

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Prompting user to record something of type: $type");
		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "URI: $uri");

		$record_return = recordAudio($prompt , array(
		       "beep"=>true,
		       "timeout"=>30,
		       "bargein" => false,
		       "silenceTimeout"=>5,
		       "maxTime"=>$maxtime,
		       "terminator" => "#",
		       "format" => "audio/wav",
		       "recordURI" => $uri
		        )
		    );

		sayInt($test3_prompts."Replay.wav");

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Return value of record: ".$record_return->value);

		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Replaying the recording of type: $type");

		if($type == "request"){
	   		sayInt($test3_requests.$record_id.".wav");
	   		test3_CaptureEvent($record_id, 13);
		}
	   	else if($type == "name")		{
	   		sayInt($test3_recs."U".$record_id.".wav");
	   		test3_CaptureEvent($record_id, 15);
	   	}
	   	else if($type == "story"){
	   		sayInt($test3_recs.$record_id.".wav");
	   		test3_CaptureEvent($record_id, 12);
	   	}
	   	else if($type == "comment"){
	   		sayInt($test3_comments."C-".$record_id.".wav");
	   		test3_CaptureEvent($record_id, 11);
	   	}else if($type == 'fb'){
	   		$filefolder = $record_id-($record_id%1000);
		   	$path       = $test3_fb_dir.$filefolder."/".$record_id.".wav";
			$path       = str_replace("\\", "/", $path);
			sayInt($path);
			test3_CaptureEvent($record_id, 16);
		}

		$choice = gatherInput(
				$test3_prompts."Genconfirmation.wav ", array(
			        "choices" => "[1 DIGITS],*,#",
					"mode" => 'dtmf',
					"bargein" => true,
					"repeat" => 3,
					"timeout"=> 5,
					"onBadChoice" => "keysbadChoiceFCN",
					"onTimeout" => "ReMkeystimeOutFCN",
					"onHangup" => create_function("$event", "Prehangup()")
		        )
	    );

		if($choice->value=="1"){
			$loop = false;
			if($type == "request")
	   			sayInt($test3_prompts."Shukriya3.wav");
		   	// else if($type == "name")
		   	// 	sayInt($test3_prompts."Shukriya1.wav");
		   	else if($type == "story")
		   		sayInt($test3_prompts."Shukriya2.wav");
		   	else if($type == "comment"){
		   		sayInt($test3_prompts."Shukriya1.wav");
		   	}else if($type == 'fb'){
				sayInt($test3_prompts."Shukriya1.wav");
			}
		}else if($choice->value=="2") {
			$loop = true;
			if($type == "comment")
				$prompt  = $test3_prompts."Beepcomment.wav ";
			elseif($type == "story")
				$prompt  = $test3_prompts."Beepstory.wav ";
		}
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");
}

function test3_CreateFBEntry(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $test3_scripts;

	$result = doCurl($test3_scripts."create_fb_entry.php?uid=".$userid."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function test3_CreateRaayeEntry($story_id){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $test3_scripts;

	$result = doCurl($test3_scripts."create_comment.php?uid=".$userid."&call_id=".$callid."&story_id=".$story_id);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function test3_CreateStoryEntry(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $test3_scripts;

	$result = doCurl($test3_scripts."create_story.php?uid=".$userid."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

function test3_CreateRequestEntry(){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");

	global $userid, $callid;
	global $test3_scripts;

	$result = doCurl($test3_scripts."create_request.php?uid=".$userid."&call_id=".$callid);

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " JSON received: ".$result);

	$result = json_decode($result, true);
	$result = $result["result"];

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " returning.");

	if ($result["error"]) {
		return false;
	}else{
		return $result["id"];
	}
}

///////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////// Error Handlers /////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function callFailureFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thisCallStatus;

	$thisCallStatus = "Failed";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Failed.");

	Prehangup();
}

function errorFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thisCallStatus;

	$thisCallStatus = "Error";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Error.");

	Prehangup();
}

function timeOutFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thisCallStatus;

	$thisCallStatus = "TimedOut";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Call Timed Out.");

	Prehangup();
}

function busyFCN($callid){
	global $fh;
	global $oreqid;
	global $callid;
	global $thisCallStatus;

	$thisCallStatus = "Busy";
	$status = "unfulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "Destination number is busy.");

	Prehangup();
}

function keystimeOutFCN($event){
	global $polly_prompts_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the timed-out prompt.");
	sayInt($polly_prompts_dir."Nobutton.wav");
}

function ReMkeystimeOutFCN($event){
	global $test1_prompts;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the timed-out prompt.");
	sayInt($test1_prompts."Nobutton.wav");
}

function keysbadChoiceFCN($event){
	global $polly_prompts_dir;

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Playing the invalid key prompt.");
	sayInt($polly_prompts_dir."Wrongbutton.wav");
}

///////////////////////////////////////////////////////////////////////////////////////
/////////////////// Call-Session processing/logging Functions /////////////////////////
///////////////////////////////////////////////////////////////////////////////////////

function sendLogs(){
	global $database_dir;
	global $callid;
	global $logEntry;
	global $tester;//change testing

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called.");

	$LogScript = $database_dir."createLogs.php";

	$arr = explode('^', chunk_split($logEntry, 1000, '^'));		// Send logs in chunks of 1000 characters
	$i=0;
	$len = count($arr);
	while($i < $len){
		$datatopost = array (
			"callid" => $callid,
			"data" => $arr[$i]
		);

		$ch = curl_init ($LogScript);
		curl_setopt ($ch, CURLOPT_POST, true);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $datatopost);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$returndata = curl_exec ($ch);

		$i++;
	}
}

function createLogFile($id){
	global $log_file_path;

	if(!file_exists($log_file_path."\\".date('Y'))){
		mkdir($log_file_path."\\".date('Y'));
	}
	if(!file_exists($log_file_path."\\".date('Y')."\\".date('m'))){
		mkdir($log_file_path."\\".date('Y')."\\".date('m'));
	}
	if(!file_exists($log_file_path."\\".date('Y')."\\".date('m')."\\".date('d'))){
		mkdir($log_file_path."\\".date('Y')."\\".date('m')."\\".date('d'));
	}

	$now = new DateTime;
	$timestamp = $now->format('Y-m-d_H-i-s');
	$logFile = $log_file_path."\\".$now->format('Y')."\\".$now->format('m')."\\".$now->format('d')."\\log_".$timestamp."_".$id.".txt"; // Give a caller ID based name

	$handle = fopen($logFile, 'a');
	/*echo "HAH!:";
	var_dump($handle);*/
	return $handle;
}

function writeToLog($id, $handle, $tag, $str){
	global $deployment;
	global $logEntry;
	global $tester;
	global $userid;


	$writeToTropoLogs = "true";
	$spch1 = "%%";
	$spch2 = "$$";
	$del = "~";
	$colons = ":::";
	// From Apr 01, 2015: tag could be L0: System level, L1: Mixed interest, L2: User Experience
	if($tag!= 'L0' && $tag!= 'L1' && $tag!= 'L2'){
		$tag = 'L1';
	}
	if($id == "" || $id == 0){
		$id = 'UnAssigned';
	}


	//$string = $spch1 . $spch2 . $del . $id . $del . $del . date('D_Y-m-d_H-i-s') . $del . $tag . $colons . $del . $str . $spch2 . $spch1;
    // replaced the above with the following to overcome the date bug in tropo cloud. Details in email. Dec 18, 2013
    $now = new DateTime;
	$actualLogLine = $deployment . $del . $id . $del . $del . $now->format('D_Y-m-d_H-i-s') . $del . $tag . $colons . $del . $str;
    $string = $spch1 . $spch2  . $del . $actualLogLine . $spch2 . $spch1;

	$logEntry = $logEntry . $actualLogLine . $spch1 . $spch2;
}

function StartUpFn(){
	global $userid;
	global $oreqid;
	global $scripts_dir;

	$status = "InProgress";
	updateCallStatus($GLOBALS['callid'], $status);
	$status = "fulfilled";
	updateRequestStatus($oreqid, $status);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Call Answered.");
}

function Prehangup(){
	global $callid;
	global $fh;
	global $thisCallStatus;
	global $currentCall;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called.");
	/////////////////////
	updateWaitingDlvRequests($callid);
	updateCallStatus($callid, $thisCallStatus);

	$sessID = getSessID();
	$calledID = calledID();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "calledID: $calledID , SessionID: $sessID");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging Up. Call ended for callid: ".$callid);
	/////////////////////
	markCallEndTime($callid);
	sendLogs();
	//fclose($fh);
	hangupFT();
	exit(0);
}

function Prehangupunknown(){
	global $callid;
	global $fh;
	global $thisCallStatus;
	global $currentCall;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called.");

	$sessID = getSessID();
	$calledID = calledID();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", "calledID: $calledID , SessionID: $sessID");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Hanging Up. Call ended for callid: ".$callid);

	sendLogs();
	hangupFT();
	exit(0);
}

function isThisCallActive(){
	global $uuid;
	global $fp;
	global $callid;
	global $thisCallStatus;
	global $fh;

	$cmd = "api lua isActive.lua ".$uuid;
	$response = event_socket_request($fp, $cmd);
	$retVal =  trim($response);
	writeToLog($callid, $fh, "isActive", "Is the current call active? ".$retVal.". Hanging up if the call is not active.");
	if(strcmp($retVal,"false") == 0 ){
		Prehangup();
	}
	return $retVal;
}

function calledID(){
	global $fp;
	global $uuid;

	$cmd = "api lua getCalledID.lua ".$uuid;
	$response = event_socket_request($fp, $cmd);
	return $response;
}

function getSessID(){
	global $uuid;
	return $uuid;
}

function getCallerID(){
	global $uuid;
	global $fp;
	global $fh;

	$cmd = "api lua getCallerID.lua ".$uuid;
	$response = event_socket_request($fp, $cmd);
	return $response;
}

function answerCall(){
	global $uuid;
	global $fp;
	global $fh;

	$cmd = "api lua answer.lua ".$uuid;//first character is a null (0)
	$response = event_socket_request($fp, $cmd);
	return $response;
}

function rejectIfCallInactive(){
	if(isThisCallActive()=="true")
		return;
	Prehangup();
}

function rejectCall($app) {
	global $uuid;
	global $fp;
	global $fh;

    $cmd = "api lua reject.lua ".$uuid;
	$response = event_socket_request($fp, $cmd);
	Prehangup();
}

//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// DB Access Functions ////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

function makeNewRec($callid){
	global $database_dir;
	$url = $database_dir."New_Rec.php?callid=$callid";
	$result = doCurl($url);
	return $result;
}

function callRecURI($url){
	$result = doCurl($url);
	return $result;
}

function makeNewCall($reqid, $phno, $status, $calltype, $chan){
	global $database_dir;
	$url = $database_dir."New_Call.php?reqid=$reqid&phno=$phno&calltype=$calltype&status=$status&ch=$chan";
	$result = doCurl($url);
	return $result;
}

function makeNewSess($reqid, $type, $ph){
	global $database_dir;
	global $calltype;
	$url = $database_dir."New_Sess.php?calltype=$type&reqid=$reqid&ph=$ph";
	$result = doCurl($url);
	return $result;
}

function makeNewReq($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch){
	global $database_dir;
	global $userid;
	global $testcall;
	$url = $database_dir."New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=$userid&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
	$result = doCurl($url);
	return $result;
}

function createMissedCall($recid, $effect, $callid, $reqtype, $phno, $status, $syslang, $msglang, $ch, $destinationAppId){
	global $database_dir;
	global $userid;
	global $testcall;
	global $tester;
	$url = $database_dir."New_Req.php?recid=$recid&effect=$effect&callid=$callid&reqtype=$reqtype&from=$destinationAppId&phno=$phno&status=$status&syslang=$syslang&msglang=$msglang&testcall=$testcall&ch=$ch";
	$result = doCurl($url);
	return $result;
}

function makeNewFB($fbtype, $callid){
	global $database_dir;
	$url = $database_dir."New_FB.php?fbtype=$fbtype&callid=$callid";
	$result = doCurl($url);
	return $result;
}

function markCallEndTime($callid){
	global $database_dir;
	$url = $database_dir."Update_Call_Endtime.php?callid=$callid";
	$result = doCurl($url);
	return $result;
}

function updateCallStatus($callid, $status){
	global $database_dir;
	$url = $database_dir."Update_Call_Status.php?callid=$callid&status=$status";
	$result = doCurl($url);
	return $result;
}

function updateRequestStatus($reqid, $status){
	global $database_dir;
	global $sipProvider;
	$url = $database_dir."Update_Request_Status.php?reqid=$reqid&status=$status&ch=$sipProvider";
	$result = doCurl($url);
	return $result;
}

function updateWaitingDlvRequests($id){
	global $database_dir;
	$url = $database_dir."Update_Waiting_DLV_Reqs.php?rcallid=$id";
	$result = doCurl($url);
	return $result;
}

function gaveFeedBack($ph){
	global $database_dir;
	global $callTableCutoff;
	$url = $database_dir."gave_feedback.php?ph=$ph&cutoff=$callTableCutoff";
	$result = doCurl($url);
	return $result;
}

function getPhNo(){
	global $database_dir;
	global $ocallid;
	$url = $database_dir."GetPhNo.php?callID=$ocallid";
	$result = doCurl($url);
	return $result;
}

function getPreferredLangs($id){
	global $database_dir;
	$url = $database_dir."GetPreferredLangs.php?id=$id";
	$result = doCurl($url);
	return $result;
}

function getCountryCode($ph){
	global $database_dir;
	$url = $database_dir."CountryCode.php?ph=$ph";
	$result = doCurl($url);
	if($result != "Not found."){
		return explode(" - ", $result)[0];
	}
	return "";
}

function phoneNumBeforeAndAfterConditioning($before, $after, $type, $sender){
	global $callid;
	global $database_dir;
	$url = $database_dir."Update_Conditioned_PhNo.php?callid=$callid&uncond=$before&cond=$after&type=$type&sender=$sender";
	$result = doCurl($url);
	return $result;
}

function doesFileExist($fname){
	global $scripts_dir;
	$url = $scripts_dir."doesFileExist.php?fname=$fname";
	$result = doCurl($url);
	return $result;
}

function whatWasThesipProviderOfTheOriginalRequest($OrigReqID){
	global $database_dir;
	$url = $database_dir."getReqsipProvider.php?id=$OrigReqID";
	$result = doCurl($url);
	return $result;
}

//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// Cutoff based Functions /////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

function searchCalls($ph){
	global $database_dir;
	global $calltype;
	global $callTableCutoff;
	$url = $database_dir."search_calls.php?ph=$ph&type=$calltype&cutoff=$callTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchPh($ph, $application){
	global $database_dir;
	global $callTableCutoff;
	$url = $database_dir."search_phno.php?ph=$ph&app=$application&cutoff=$callTableCutoff";
	$result = doCurl($url);
	return $result;
}

function searchCallsReq($ph){
	global $database_dir;
	global $calltype;
	global $reqTableCutoff;
	$url = $database_dir."search_calls_hist.php?ph=$ph&type=$calltype&cutoff=$reqTableCutoff";
	$result = doCurl($url);
	return $result;
}

function updateCallsReq($ph){
	global $database_dir;
	global $reqTableCutoff;
	$url = $database_dir."update_calls_hist.php?ph=$ph&cutoff=$reqTableCutoff";
	$result = doCurl($url);
	return $result;
}

function getcallTableCutoff($days){
	global $database_dir;
	$url = $database_dir."getcallTableCutoff.php?days=$days";
	$result = doCurl($url);
	return $result;
}

function getreqTableCutoff($days){
	global $database_dir;
	global $userid;
	$url = $database_dir."getreqTableCutoff.php?days=$days";
	$result = doCurl($url);
	return $result;
}

//////////////////////////////////////////////////////////////////////////////////////
/////// functions to encode, decode, store, validate and clean phone numbers /////////
//////////////////////////////////////////////////////////////////////////////////////

function checkIfPhNoValid($phno){

	$phno = rtrim($phno, "*");
	$phno = ltrim($phno, "*");
	$phno = rtrim($phno, "#");

	if(sizeof($phno) < 11 || sizeof($phNo) > 15)
		return false;

	$phno = str_replace("+", "00", $phno);

	if(is_numeric($phno)){
		if ($phno[0] == 0 || $phno[0] == "0") {
			return true;
		}
		return true;
	}
	return false;
}

function conditionPhNo($phno, $type){
	global $useridUnEnc;
	global $countryCode;
	$returnNumber = $phno;
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called with ph: $phno and type: $type");

	if($type == "Missed_Call" || $type == "EMissed_Call" || $type == "AMissed_Call" || $type == "MMissed_Call" || $type == "BMissed_Call" || $type == "Unsubsidized" || $type == "FCMissed_Call"){
		if(substr($returnNumber, 0, 1) == '1'){										// 1 got prepended by mistake
			$returnNumber = substr($returnNumber, 1, strlen($returnNumber)-1);		// remove it
		}
		else if(strlen($phno) <= 10){						//local US	e.g. 4122677909
			$returnNumber = "1".$phno;						// 1 is to say that missed call is coming in from US
		}
		else{												// International
			$returnNumber = $phno;
		}
	}
	else if($type == "Delivery" || $type == "EDelivery" || $type == "BDelivery" || $type == "ADelivery" || $type == "MDelivery" || $type == "FCDelivery"){
		$returnNumber = $phno;
		if(substr($returnNumber, 0, 2) == '00'){			// A non-US sender entered an Intl. number with country code
			$returnNumber = ltrim($returnNumber, 0);
		}
		else if(substr($returnNumber, 0, 3) == '011'){		// A US sender entered an Intl. number with country code
			$returnNumber = substr($returnNumber, 3, strlen($returnNumber)-3);
		}
		else if(substr($returnNumber, 0, 1) == '0'){					// A non-US sender entered a local number
			$returnNumber = $returnNumber;	//$countryCode . ltrim($returnNumber, 0);		// Prepend the country code of the sender
		}
		else if((strlen($returnNumber)==9) && $countryCode == '224'){	// A Guinean sender entered a local number without a 0 prefix
			$returnNumber = $countryCode . ltrim($returnNumber, 0);		// Prepend the country code of Guinea
		}
		else if((strlen($useridUnEnc) == 11) && (substr($useridUnEnc, 0, 1) == 1) && (strlen($returnNumber)==10)){	// A US sender entered a 10-dig number without country code -> Assmue it is a US number
			$returnNumber = $returnNumber; //"1".$returnNumber;
		}
		phoneNumBeforeAndAfterConditioning($phno, $returnNumber, $type, $useridUnEnc);
	}
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is returning: $returnNumber");
	return $returnNumber;
}

function PhToKeyAndStore($phno, $sender){
	global $database_dir;
	global $tester;
	$url = $database_dir."insertNewPhByMatchingFromOldTable.php?sender=$sender&ph=".$phno;
	$result = doCurl($url);
	return $result;
}

function PhToKey($ph){
	global $database_dir;
	$url = $database_dir."phToKey.php?ph=$ph";
	$result = doCurl($url);
	return $result;
}

function KeyToPh($key){
	global $database_dir;
	$url = $database_dir."keyToPh.php?key=$key";
	$result = doCurl($url);
	return $result;
}

function getPhDir(){
	global $userid;
	global $database_dir;
	$url = $database_dir."getPhDir.php?user=$userid";
	$result = doCurl($url);
	return $result;
}

function updatePhDir($phoneNumber){
	global $userid;
	global $database_dir;
	$url = $database_dir."updatePhDir.php?user=$userid&friend=$phoneNumber";
	$result = doCurl($url);
	return $result;
}

//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// Misc. Functions ////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
function doCurl($url){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
		$result = curl_exec($ch);
		curl_close($ch);
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with url: $url, is returning: $result");
	return $result;
}

function getFilePath($fileName, $pathOnly = "FALSE"){
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " is called.");

	$fname = explode('.', $fileName);

	$FilePath = ($fname[0] - ($fname[0] % 1000));		// rounding down to the nearest 1000

	$File = $FilePath."/".$fileName;
	if($pathOnly == "TRUE"){
		$File = $FilePath . "/";
	}

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L0", __FUNCTION__ . " called with params: $fileName, $pathOnly, is returning: $File");

	return $File;
}

function createFilePath($filePath,$fileName, $pathOnly = "FALSE"){
	global $tester;

	$fname = explode('.', $fileName);
	$FilePathNew = $filePath.($fname[0] - ($fname[0] % 1000));		// rounding down to the nearest 1000
	//////fwrite($tester,$fname[0]." path of file.\n");

	if( is_dir($FilePathNew) === false )
	{
	    mkdir($FilePathNew);
	}
	$File = $FilePathNew."\\".$fileName;
	if($pathOnly == "TRUE"){
		$File = $FilePathNew . "\\";
	}
	return $File;
}
////////////////////////////////////////////// FreeSwitch Event Socket Library - Client Side //////////////////////

function event_socket_create($host, $port, $password) {
	global $fp;

   $fp = fsockopen($host, $port, $errno, $errdesc)
     or die("Connection to $host failed");
   socket_set_blocking($fp,false);


   if ($fp) {
       while (!feof($fp)) {
          $buffer = fgets($fp, 1024);
          usleep(50); //allow time for reponse
          if (trim($buffer) == "Content-Type: auth/request") {
             fputs($fp, "auth $password\n\n");
             break;
          }
       }
       return $fp;
    }
    else {
        return false;
    }
}

function event_socket_request($fp, $cmd) {

    if ($fp) {

        fputs($fp, $cmd."\n\n");
        usleep(50); //allow time for reponse

        $response = "";
        $i = 0;
        $contentlength = 0;
        while (!feof($fp)) {
           $buffer = fgets($fp, 4096);
           if ($contentlength > 0) {
              $response .= $buffer;
           }

           if ($contentlength == 0) { //if contentlenght is already don't process again
               if (strlen(trim($buffer)) > 0) { //run only if buffer has content
                   $temparray = explode(":", trim($buffer));
                   if ($temparray[0] == "Content-Length") {
                      $contentlength = trim($temparray[1]);
                   }
               }
           }

           usleep(50); //allow time for reponse

           //optional because of script timeout //don't let while loop become endless
           if ($i > 100000) { break; }

           if ($contentlength > 0) { //is contentlength set
               //stop reading if all content has been read.
               if (strlen($response) >= $contentlength) {
                  break;
               }
           }
           $i++;
        }

        return $response;
    }
    else {
      echo "no handle";
    }
}

function hangupFT(){
	global $uuid;
	global $fp;

	if(isset($_REQUEST["uuid"])){
		$cmd = "api lua hangup.lua ".$uuid;
		$response = event_socket_request($fp, $cmd);
		fclose($fp);
	}
}

function makechoices($choices){
	//types seen "[1 DIGITS], *" , "[1 DIGITS]" , "sometext(1,sometext)"
	$fchoices = "";
	if($choices[0] == '[')
	{
		//tokenizing all possible choices
		$pchoices = explode(",",$choices);
		$i = 1;

		while($pchoices[0][$i] != ' ' && $i < strlen($pchoices[0]))
		{
			$fchoices .= $pchoices[0][$i];
			 ++$i;
		}

		if(strlen($fchoices ) > 1)
		{
			$fchoices=str_replace("-","",$fchoices);
		}
		$j=1;

		while($j<count($pchoices))
		{
			$fchoices .= trim($pchoices[$j]);
			++$j;
		}

	}
	else
	{
		$i = 0;
		while($i< strlen($choices))
		{
			$j = $i;
			$cchoices = "";//choice in context


			while($choices[$j] != '(')
			{
				$j++;

			}
			$j++;

			while($choices[$j] != ')')
			{
				$cchoices .= $choices[$j];
				$j++;

			}
			$j++;
			$i=$j;
			$pchoices = explode(",",$cchoices);
			$fchoices .= $pchoices[0];


		}

	}

	return $fchoices;
}

function makeValidINput($choices,$fchoices){//to make regex for valid input freeswitch like [1 digits], * => \\d+ or 1234* => [1234]
	if($choices[0] == '[')
	{
		return "d" ;
	}
	else
	{
		$valid = '[';
		$i = 0;
		while($i<strlen($fchoices))
		{
			if($fchoices[$i]!='*' && $fchoices[$i]!='#' )
			{
				$valid .= $fchoices[$i];
			}
			$i++;
		}
		$valid .= ']';
		return $valid;
	}
}

function makeTerminators($fchoices,$terms){//making terms for freeswitch
	$termsin = "";
	$i = 0;
	$b = 0;
	while($i<strlen($fchoices))
	{
		if($fchoices[$i]=='*' || $fchoices[$i]=='#' )
		{
			$b = 1;
			$termsin .= $fchoices[$i];
		}
		$i++;
	}
	if($terms == "@" && $b == 1)
	{
		return $termsin;
	}
	else
	{
		$terms.=$termsin;
		return $terms;
	}
}

function mapChoice($choices,$fchoice){

	if($choices[0] == '[')
	{
		return $fchoice;
	}
	else
	{
		$i = 0;
		while($i< strlen($choices))
		{
			$j = $i;
			$cchoices = "";//choice in context


			while($choices[$j] != '(')
			{
				$j++;

			}
			$j++;

			while($choices[$j] != ')')
			{
				$cchoices .= $choices[$j];
				$j++;

			}
			$j++;
			$i=$j;
			$pchoices = explode(",",$cchoices);
			if($fchoice == $pchoices[0])
			{
				return $pchoices[1];
			}
		}
		return $fchoice;
	}
}

function calculateMaxDigits($choices,$fchoice){
	if($choices[0]=="[")
	{
		$i= strpos($choices,'-');
		if( $i !== false )
		{
			$subchoice = "";//the part 9-14 in [9-14 digits]
			$i=1;

			while($choices[$i]=="-" || is_numeric($choices[$i]))
			{

				$subchoice .= $choices[$i];
				$i++;

			}
			$subchoice = explode("-",$subchoice);
			$max=(int)($subchoice[1]);

		}
		else
		{
			$subchoice = "";//the part 9 in [9 digits]
			$i=1;
			while( is_numeric($choices[$i]))
			{
				$subchoice .= $choices[$i];
				$i++;
			}
			$max=(int)($subchoice);
		}
		return $max;
	}
	else
	{

		return 1;//in these kind of choices like notify(1,notify),donotnotify(2,donotnotify),*(*,*) at a time input numbers require is 1 always
	}
}

function calculateMinDigits($choices,$fchoice){
	if($choices[0]=="[")
	{
		$i= strpos($choices,'-');
		if( $i !== false )
		{
			$subchoice = "";//the part 9-14 in [9-14 digits]
			$i=1;

			while($choices[$i]=="-" || is_numeric($choices[$i]))
			{

				$subchoice .= $choices[$i];
				$i++;

			}
			$subchoice = explode("-",$subchoice);
			$min=(int)($subchoice[0]);

		}
		else
		{
			$subchoice = "";//the part 9 in [9 digits]
			$i=1;
			while( is_numeric($choices[$i]))
			{
				$subchoice .= $choices[$i];
				$i++;
			}
			$min=(int)($subchoice);
		}
		return $min;
	}
	else
	{
		return 1;//in these kind of choices like notify(1,notify),donotnotify(2,donotnotify),*(*,*) at a time input numbers require is 1 always
	}
}

function invalid($onBadCoice){//to handle if invalid key is entered for freeswitch
	global $polly_prompts_dir;

	if ( $onBadCoice == "keysbadChoiceFCN" )
	{
		return $polly_prompts_dir."Wrongbutton.wav";
	}
}

function onTimeOut($onTimeout){//to handle if timeout occured for freeswitch
	global $polly_prompts_dir;
	global $test1_prompts;
	if ( $onTimeout == "keystimeOutFCN" )
	{
		return $polly_prompts_dir."Nobutton.wav";
	}else if ( $onTimeout == "ReMkeystimeOutFCN" )
	{
		return $test1_prompts."Nobutton.wav";
	}
}

function gatherTnputFreeSwitch($toBeSaid,$invalidFS,$mindigitsFS,$maxdigitsFS,$maxattemptsFS,$timeoutFS,$bargein,$termFS,$validInput,$onTimeOutFS,$interdigitTimeout){

	global $uuid;
	global $fp;

	$output = (object) array('name' => 'choice', 'value' => '');
	$repeat = "TRUE";

	$kaho = "file_string://";
		$s=preg_split('/[ \n]/', $toBeSaid);
        for($i=0;$i<count($s);$i++)
        {
        	$j = 0;
        	if($s[0]=="")
        	{
        		$j = 1;
        	}
        	if($i>$j)
        	{
        		$kaho .= "!".$s[$i];
        	}
        	else
        	{
        		$kaho .= $s[$i];
        	}

        }

	while($repeat == "TRUE"){
		$repeat = "FALSE";
		$cmd = "api lua askGather.lua ".$uuid." ".$kaho." ".$invalidFS." ".$mindigitsFS." ".$maxdigitsFS." ".$maxattemptsFS." ".$timeoutFS." ".$termFS." ".$validInput." ".$onTimeOutFS." ".$interdigitTimeout;
		$response = event_socket_request($fp, $cmd);
		if(substr($response, 1)){
			$val = substr($response, 1);
			if($val[0]==' ' || $val[0]=='-'  )
			{
				$output->value= $val[1];
			}
			elseif($val[0]=='_')
			{
				$i = 1;
				while( $i < strlen($val))
				{
					$output->value .= $val[$i];
					$i++;
				}

			}
			else
			{
				$output->name = "not_Good_timeout_or_invalid";
				$output->value= "-";
			}
		}
		else{
			$output->name = "not_Good_timeout_or_invalid";
			$output->value= "-";
		}
		if($output && $output->value == "*"){	// pause the system
			pauseFT();
			$repeat = "TRUE";
		}
	}
	isThisCallActive();
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning: value: " . ($output->value) . ", name: " . ($output->name) );
	return $output;
}

function gatherInput($toBeSaid, $params){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));

	global $polly_prompts_dir;
	global $Silence;

	if(isThisCallActive()=="true")
	{
	    //making parameters
	    //parameters that should always be in ask array no matter what..choices...bargein..timeout
	    $choices=$params['choices'];//choices given by user
		$fchoices=makechoices($choices);//fchoices for freeswitch in format 123 or 1234* or 1*#..//fchoices made out of choices given by user,for freeswitch
		$mindigitsFS=calculateMinDigits($choices,$fchoices);
		$maxdigitsFS=calculateMaxDigits($choices,$fchoices);
		$bargein=$params['bargein'];
		$timeoutFS=$params['timeout'] * 1000;//to convert to millisecs as freeswitch timeout is in millisecs
		$validInput=makeValidINput($choices,$fchoices);

		//parameters that should may be in ask array..repeat || attempt...terminator..onBadChoice..onTimeout
		$termFS = "@";//default value menas no terminator
		if(checkifexists('terminator',$params)==true )
			$termFS = $params['terminator'];//intialized with passed parameter terminator
		$termFS= makeTerminators($fchoices,$termFS);//making it for freeswitch like if * is not in term but in choice it will put it in terminator
		$maxattemptsFS=0;//default value
		if(checkifexists('repeat',$params)==true )
			$maxattemptsFS=$params['repeat'] + 1;//intialized with passed parameter repeat
		if(checkifexists('attempts',$params)==true )
			$maxattemptsFS=$params['attempts'];//intialized with passed parameter attempts
		$invalidFS=$polly_prompts_dir.$Silence;//default value that is silence
		if(checkifexists('onBadChoice',$params)==true )
			$invalidFS=invalid($params['onBadChoice']);//intialized with prompt corrosponding to the onBadChoice value
		$onTimeOutFS="-";//default value that is silence
		if(checkifexists('onTimeout',$params)==true )
			$onTimeOutFS=onTimeOut($params['onTimeout']);//intialized with prompt corrosponding to the onTimeout value
		$interdigitTimeout=$timeoutFS;//default value is equal to timeout in freeswitch
		if(checkifexists('interdigitTimeout',$params)==true )
			$interdigitTimeout=$params['interdigitTimeout']*1000;//intialized with passed parameter interdigitTimeout
		return gatherTnputFreeSwitch($toBeSaid,$invalidFS,$mindigitsFS,$maxdigitsFS,$maxattemptsFS,$timeoutFS,$bargein,$termFS,$validInput,$onTimeOutFS,$interdigitTimeout);
	}
}

function checkifexists($parameter,$array){//checking if  parameter exists in array

	if(array_key_exists($parameter, $array)) {
		return true;
	}
	//else return false if parameter doesnt exist in array
	return false;
}

function recordAudio($toBeSaid, $params){

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " entered.");
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " Record Params: ".$params);

    global $uuid;
    global $fp;
    global $feedback_dir;
    global $voice_recordings_dir;
    global $tester;
    global $scripts_dir;
    global $voice_recordings_dir_Baang;
    global $UserIntro_Dir_Baang;
    global $feedback_dir_Baang;
    global $PQRecs;
    global $PQPrompts;
    global $PQScripts;

    $recid = "";
    $result = "";
    $filepathFS= "";
	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " was called with prompt: " . $toBeSaid . " and parameters: " . implode(', ', $params));

	if(isThisCallActive()=="true")
	{
   	 	if(checkifexists('silenceTimeout',$params)==true){
            $silTimeout= $params['silenceTimeout'];

        }
        else{
        	//setting default silence timeout
        	$silTimeout=5;
        }
        if(checkifexists('maxTime',$params)==true){
            $maxTime= $params['maxTime'];

        }
        else{
        	//setting default maximum time
        	$maxTime=30;
        }
        $rec_feed = 0;
        $recordURI = $params['recordURI'];

       	if( strpos($recordURI, 'process_feedback') !== false )
       		{
       			$parameterarray=explode("&", explode("?", $recordURI)[1]);

       			$fbid=explode("=", $parameterarray[0])[1];

	       			$filepathFS=$feedback_dir;
			        $filepathFS = createFilePath($filepathFS,$fbid.".wav",TRUE);
			        $filepathFS .= "Feedback-".$fbid."-";
	       			$i = strpos($recordURI, '=');
			        $i = $i +1;
			        $fbid = "";

				    while( $recordURI[$i] != '&')
			        {
			        	$fbid .= $recordURI[$i];
			        	$i = $i +1;
			        }
			        while( $recordURI[$i] != '=')
			        {
			        	$i = $i +1;
			        }
			        $i = $i +1;
			        while(  $i < strlen($recordURI))
			        {
			        	$filepathFS .= $recordURI[$i];
			        	$i = $i +1;
			        }
		        $filepathFS .= ".wav";
		        $rec_feed = 1;
       		}
        else if( strpos($recordURI, 'process_recording') !== false )
        	{
        		$rec_feed = 0;
       			$parameterarray=explode("&", explode("?", $recordURI)[1]);
       			$recid=explode("=", $parameterarray[0])[1];

       			if(strpos($recordURI, 'name') !== false){
       				$filepathFS=$UserIntro_Dir_Baang;
			        $filepathFS .= "intro-".$recid.".wav";
	        		$rec_feed = 6;
       			}else{
	        		$filepathFS=$voice_recordings_dir;
			        $filepathFS = createFilePath($filepathFS,$recid.".wav",TRUE);
			        $filepathFS .= "s-".$recid.".wav";
	        		$rec_feed = 0;
       			}
        	}
        else if( strpos($recordURI, 'save_recording') !== false )
        	{
        		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " handling save_recording");
       			$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$type           = explode("=", $parameterarray[0])[1];
       			$uid            = explode("=", $parameterarray[1])[1];
       			$record_id      = explode("=", $parameterarray[2])[1];

   				$rec_feed   = 6;

   				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " type = $type , record_id = $record_id");

		        if($type == "request"){ // QU-5972-3.wav
		        	global $SA_requests;

		        	$filepathFS = $SA_requests. $record_id.".wav";
				}else if($type == "name"){ // U1.wav
					global $SA_recs, $userid;

					$filepathFS = $SA_recs."U".$userid.".wav";
				}else if($type == "story"){ //QU-5972-3-1.wav
					global $SA_recs;

					$filepathFS = $SA_recs.$record_id.".wav";
				}else if($type == "comment"){
					global $SA_comments;

					$filepathFS = $SA_comments."C-".$record_id.".wav";
			   	}else if($type == "fb") {
			   		global $SA_fb_dir;

			        $filepathFS = createFilePath($SA_fb_dir, $record_id.".wav", TRUE);
	   				$rec_feed   = 6;
					$filepathFS .= $record_id . ".wav";
        		}
        		writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " filepathFS = $filepathFS");
        	}
        else if( strpos($recordURI, 'process_FriendNamerecording') !== false )
        	{
       			$parameterarray=explode("&", explode("?", $recordURI)[1]);
       			$reqid=explode("=", $parameterarray[0])[1];
       			$userid=explode("-", $reqid)[0];
					global $friend_name_dir;
					$filepathFS= $friend_name_dir;//"C:/xampp/htdocs/wa/Praat/FriendNames/";
			        $filepathFS = createFilePath($filepathFS,$userid.".wav",TRUE);
       				$rec_feed=6;
					$filepathFS .= $reqid.".wav";
    		}
		else if( strpos($recordURI, 'process_UserNamerecording') !== false )
    		{
   			$parameterarray=explode("&", explode("?", $recordURI)[1]);
   			$callid=explode("=", $parameterarray[0])[1];
				global $sender_name_dir;
				$filepathFS= $sender_name_dir ;//"C:/xampp/htdocs/wa/Praat/UserNames/";
		        $filepathFS = createFilePath($filepathFS,$callid.".wav",TRUE);
   				$rec_feed=6;
				$filepathFS .= "UserName-" . $callid . ".wav";
    		}
    	else if( strpos($recordURI, 'remt1_feedback') !== false )
    		{
    			global $ReMT1_fb_dir;

				$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$record_id      = explode("=", $parameterarray[0])[1];

		        $filepathFS = createFilePath($ReMT1_fb_dir, $record_id.".wav", TRUE);
   				$rec_feed   = 6;
				$filepathFS .= $record_id . ".wav";
    		}
    	else if( strpos($recordURI, 'remt2_feedback') !== false )
    		{
    			global $ReMT2_fb_dir;

				$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$record_id      = explode("=", $parameterarray[0])[1];

		        $filepathFS = createFilePath($ReMT2_fb_dir, $record_id.".wav", TRUE);
   				$rec_feed   = 6;
				$filepathFS .= $record_id . ".wav";
    		}
    	else if( strpos($recordURI, 'update_fb') !== false )
    		{
				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " update_fb entered");

    			global $WKB_fb_dir;

				$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$record_id      = explode("=", $parameterarray[0])[1];

       			writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " -- record_id = $record_id");

   				$rec_feed   = 6;
				$filepathFS = $WKB_fb_dir.$record_id . ".wav";

				writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", __FUNCTION__ . " -- file path = $filepathFS");
    		}

    	else if( strpos($recordURI, 'api/add/main_feedback') !== false )
    		{
    			global $MVP_feedbacks;

				$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$record_id      = explode("=", $parameterarray[0])[1];
		        $rec_feed   = 6;
				$filepathFS = $MVP_feedbacks.$record_id . ".wav";
    		}
    	else if( strpos($recordURI, 'api/add/question_feedback') !== false )
    		{
    			global $usercomments;

				$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$record_id      = explode("=", $parameterarray[0])[1];

   				$rec_feed   = 6;
				$filepathFS = $usercomments.$record_id . ".wav";
    		}
    	else if( strpos($recordURI, 'api/add/question_user') !== false )
    		{
    			global $MVP_recordings;

				$parameterarray = explode("&", explode("?", $recordURI)[1]);
       			$record_id      = explode("=", $parameterarray[0])[1];

   				$rec_feed   = 6;
				$filepathFS = $MVP_recordings."Q".$record_id . ".wav";
    		}
    	else if( strpos($recordURI, 'api/username') !== false )
    		{
    			global $MVP_recordings, $userid;

   				$rec_feed   = 6;
				$filepathFS = $MVP_recordings."U".$userid.".wav";
    		}

	        $kaho = "file_string://";
			$s=preg_split('/[ \n]/', $toBeSaid);
	       for($i=0;$i<count($s);$i++)
	        {
	        	$j = 0;
	        	if($s[0]=="")
	        	{
	        		$j = 1;
	        	}
	        	if($i>$j)
	        	{
	        		$kaho .= "!".$s[$i];
	        	}
	        	else
	        	{
	        		$kaho .= $s[$i];
	        	}
	        }
	        $filepathFS=str_replace("\\", "_", $filepathFS);
	        $kaho=rtrim($kaho,"!");
	       	$cmd = "api lua record.lua ".$uuid." ".$kaho." ".$filepathFS." ".$maxTime." ".$silTimeout; // some suggest using 500 as the threshold of silence
	    	$result = event_socket_request($fp, $cmd);
	    	isThisCallActive();
    		$filepathFS=str_replace("_", "\\", $filepathFS);
    		correctWavFT($filepathFS);

	    	if($rec_feed === 0)
	    	{
	    		$res = doCurl($scripts_dir."processAudFile.php?path=s-$recid".".wav");
	    	}else if($rec_feed === 2 || $rec_feed === 5 || $rec_feed === 6){
	    		$res = doCurl($recordURI);
	    	}else if($rec_feed === 1){
	    }
    }

	writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning.");
    return $result;
}

function correctWavFT($filepath){
	$rep = file_gecontents($filepath);
	$rep[20] = "\x01";
	$rep[21] = "\x00";
	file_put_contents($filepath, $rep);
}

function askFT($toBeSaid, $choices, $mode, $repeat, $bargein, $timeout, $hanguppr, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $termFS, $invalidFS)
{
    global $uuid;
    global $fp;

    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "In" . __FUNCTION__);

    $output = (object)array(
        'name' => 'choice',
        'value' => ''
    );

    $kaho = "file_string://";
    $s = preg_split('/[ \n]/', $toBeSaid);

    for ($i = 0; $i < count($s); $i++) {
        $j = 0;

        if ($s[0] == "") {
            $j = 1;
        }

        if ($i > $j) {
            $kaho.= "!" . $s[$i];
        }
        else {
            $kaho.= $s[$i];
        }
    }

    $kaho = rtrim($kaho, "!");
    $cmd = "api lua ask.lua " . $uuid . " " . $kaho . " " . $invalidFS . " " . $mindigitsFS . " " . $maxdigitsFS . " " . $maxattemptsFS . " " . $timeoutFS . " " . $termFS;
    writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L1", "FreeSwitch called to execute command : ".$cmd);
    $response = event_socket_request($fp, $cmd); //here

    if (substr($response, 1)) {

        $val = substr($response, 1);

        if ($val[0] == '_' || $val[0] == '+' || $val[0] == ' ') {
            $output->value = $val[1];
            writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", "Response of " . __FUNCTION__ . " : " . $val);
        }
        else {
            $output->name = "not_Good_timeout_or_invalid";
            $output->value = $val[1];
        }
    }
    else {
        $output->name = "not_Good_timeout_or_invalid";
        $output->value = "-";
    }
    isThisCallActive();
    return $output;
}

function sayInt($toBeSaid, $bargein = true)
{
    if (isThisCallActive() == "true") {
        $choices = "[1 DIGITS], *, #";
        $mode = 'dtmf';
        $repeatMode = 0;
        $timeout = 0.1;
        $hanguppr = "Prehangup";
        $mindigitsFS = 1;
        $maxdigitsFS = 1;
        $maxattemptsFS = 1;
        $timeoutFS = 100;
        $termFS = "*#";
        $invalidFS = "nothing";
        $repeat = "TRUE";

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " about to play: " . $toBeSaid . " using askFT function");

        while ($repeat == "TRUE") {
            $repeat = "FALSE";
            $result = askFT($toBeSaid, $choices, $mode, $repeatMode, $bargein, $timeout, $hanguppr, $mindigitsFS, $maxdigitsFS, $maxattemptsFS, $timeoutFS, $termFS, $invalidFS);

            if ($result->name == 'choice') {
                writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " prompt " . $toBeSaid . " was barged-in with " . ($result->value));
            }
        }

        writeToLog($GLOBALS['callid'], $GLOBALS['fh'], "L2", __FUNCTION__ . " complete. Now returning: value: " . ($result->value) . ", name: " . ($result->name));
        return $result;
    }
}
//////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// End of Code ////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////
?>