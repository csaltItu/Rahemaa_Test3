-- this script is called by FreeSWITCH Dialplan upon receiving an inbound call on Application extensions, Service Numbers of Rahemma.
-- Script will handover the Incoming-call-session to WebRTC based SIP controller of Application (Test 1, Test 2, Test 3, Test 4, MVP).

-- For Test 1, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test1.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test1.php
-- For Test 2, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test2.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test2.php
-- For Test 3, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test3.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test3.php
-- For Test 4, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_Test4.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_Test4.php
-- For MVP, call-flow and IVR (Interactive Voice Response) functionality controller is Rahemaa_MVP.php and it's absolute location is [XAMP Server Root = D:/xampp/htdocs/] Web_Root/Controller/Core/Rahemaa_MVP.php

-- Currently, MVP is running, so Rahemaa_MVP.php will be called. To run previous four test, replace Rahemaa_MVP.php with the Rahemaa_Test[1|2|3|4]

pathsep = '\\'
uuida = session:get_uuid()
session:setAutoHangup(false)

uuida1 = string.sub(uuida,1)
freeswitch.consoleLog("INFO","UUIDA1:  " .. uuida1 .. "\n")
session:preAnswer()

web_url = "http://127.0.0.1/Web_Root/Controller/Core/Rahemaa_MVP.php?uuid=" ..uuida1
freeswitch.consoleLog("INFO","web_url:  " .. web_url .. "\n")

-- Get a FreeSWITCH API object and call respective controller, mentioned above, in WEB ROOT for call manipulation
api = freeswitch.API()
raw_data = api:execute("curl",web_url)
freeswitch.consoleLog("INFO","Url :\n" .. web_url .. "\n\n")
freeswitch.consoleLog("INFO","Raw data:\n" .. raw_data .. "\n\n")