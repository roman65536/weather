xpath= require "xpath"
local lom = require "lxp/lom"
local mqttclient = require("luamqttc/client")

local ref_temp=10.0


local f = assert(io.open("test2.xml", "r"))
local t = f:read("*all")
    
sensors=xpath.selectNodes(lom.parse(t),'/config/heiz/sensor/')
print(#sensors)
relay={}
ref={}
for a=1,#sensors do
if (sensors[a]["attr"]["relay"] ~= nil) then  print (sensors[a]["attr"]["id"].." "..sensors[a]["attr"]["relay"] ) end
relay[sensors[a]["attr"]["id"]]=sensors[a]["attr"]["relay"]
ref[sensors[a]["attr"]["id"]]=sensors[a]["attr"]["ref"]
end    


for a,b in pairs(relay) do
 print(a.." "..b)
 end
 
for a,b in pairs(ref) do
 print(a.." "..b)
 end
 
 print(mqttclient)
cl=mqttclient.new("myclient")
host="192.168.0.90"
timeout=2
cl:connect(host,1883,{timeout=timeout})
 
function callbc(topic,data)
if (topic == "ref/temp") then ref_temp=tonumber(data) print("New Reference Temperature set to "..ref_temp) end
print(topic.."->"..data)
t=os.date('*t')
print(t.hour..":"..t.min)
if (relay[topic] ~= nil ) then 
print ("Found it "..relay[topic])  

--if((t.hour>8) and (t.hour <18) ) then flag=0
-- else flag=1 end
flag=1


print(ref_temp)
if ((tonumber(data) < ref_temp) and (flag == 1)) then 
 print(cl:publish(relay[topic],"on"))
 print("Turning on")
 else
 print(cl:publish(relay[topic],"off"))
 print("Turning off")
 end
end
--print(topic)
end
 
 
 
cl:subscribe("#",2,callbc)


while (1) do
cl:message_loop(.5)
end

