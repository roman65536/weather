xpath= require "luaxpath"
local lom = require "lxp/lom"
local mqttclient = require("luamqttc/client")

local ref_temp=10.0

local temp_hyst=.4

local f = assert(io.open("test2.xml", "r"))
local t = f:read("*all")
    
sensors=xpath.selectNodes(lom.parse(t),'/config/heiz/sensor/')
print(#sensors)
relay={}
ref={}
ref_t={}
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
cl=mqttclient.new("Alarm")
host="192.168.1.2"
timeout=5
cl:connect(host,1883,{timeout=timeout})
srv=mqttclient.new("Alarm2")

 
function callbc(topic,data)
if (topic == "ref/temp") then ref_temp=tonumber(data) print("New Reference Temperature set to "..ref_temp) end

for a,b in pairs(ref) do
if(topic == b) then ref_tmp=tonumber(data) print("New Temperature for "..topic.." set to "..ref_tmp) ref_t[topic]=ref_tmp end
end

print(topic.."->"..data)
t=os.date('*t')
print(t.hour..":"..t.min)
if (relay[topic] ~= nil ) then 
--print ("Found it "..relay[topic])  

--if((t.hour>8) and (t.hour <18) ) then flag=0
-- else flag=1 end
flag=1


print(ref_temp)
print(ref[topic])
print(ref_t[ref[topic]])
if ( ref_t[ref[topic]] ~= nil ) then
if ((tonumber(data)+temp_hyst < ref_t[ref[topic]]) and (flag == 1)) then 
 srv:connect(host,1883,{timeout=timeout})
 print(srv:publish(relay[topic],"on"))
 srv:disconnect()
print(string.format("%d:%d : %s %s Turning on",t.hour,t.min,topic,relay[topic]))
 elseif ((tonumber(data)-temp_hyst > ref_t[ref[topic]]) and (flag == 1)) then
  srv:connect(host,1883,{timeout=timeout})
  print(srv:publish(relay[topic],"off"))
 srv:disconnect()
  print(string.format("%d:%d : %s %s Turning off",t.hour,t.min,topic,relay[topic]))
  -- print(t..":.."Turning off")
 end
 end
end
--print(topic)
end
 
 
 
cl:subscribe("#",2,callbc)


while (1) do
cl:message_loop(1)
end

