-- this script will try to connect to user and upon connection result (call success/failure) handover the call-session to WebRTC based SIP controller of the Application (Test 1, Test 2, Test 3, Test 4, MVP) who called this script for callout to user

-- For Test 1, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test1.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test1.php
-- For Test 2, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test2.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test2.php
-- For Test 3, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test3.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test3.php
-- For Test 4, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test4.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test4.php
-- For MVP, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_MVP.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_MVP.php

-- Currently, as MVP is running, therefore Rahemaa_MVP.php will be called in both call success/failure case. To run one of the previous test, replace Rahemaa_MVP.php with the repective test controller name, Rahemaa_Test[1|2|3|4].php, in lines 29, 39, 48.

callerid = argv[13];
phno = argv[1];
deployment=argv[2]
calltype=argv[3]
To_Whom=argv[4]
oreqid=argv[5]
recIDtoPlay=argv[6]
Effect_Chosen=argv[7]
ocallid=argv[8]
ouserid=argv[9]
testcall=argv[10]
ch=argv[11]
app=argv[12]
From=argv[14]

api = freeswitch.API()

hcause = ""

if tonumber(To_Whom) == nil then
	web_url = "http://127.0.0.1/Web_Root/Controller/Core/Rahemaa_MVP.php?error=".."NO_ROUTE_DESTINATION".."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app
	freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")
    raw_data = api:execute("curl", web_url)
else
	this_sess = freeswitch.Session("{ignore_early_media=true,setAutoHangup=false,originate_timeout=25,origination_caller_id_number="..callerid.."}sofia/gateway/proxy-301/"..phno)
    if (this_sess:ready()) then

		uuida = this_sess:get_uuid()
		this_sess:setAutoHangup(false)
		uuida1 = string.sub(uuida,1)
		web_url = "bgapi curl http://127.0.0.1/Web_Root/Controller/Core/Rahemaa_MVP.php?uuid=" ..uuida1.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app.."&From="..From
		freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")

		api:executeString(web_url)


	else
		obCause = this_sess:hangupCause()
	    freeswitch.consoleLog("info", To_Whom.." hangupCause(),"..ouserid..", = " .. obCause )
		web_url = "http://127.0.0.1/Web_Root/Controller/Core/Rahemaa_MVP.php?error="..obCause.."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app

		freeswitch.consoleLog("INFO","URL:  " .. web_url .. "\n")
		raw_data = api:execute("curl", web_url)

	end
end