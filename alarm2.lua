local mqttclient = require("luamqttc/client")


function callbc(topic,data)
 print("t: "..topic.." d: "..data)
 end




print(mqttclient)
cl=mqttclient.new("myclient")
host="192.168.1.2"
timeout=2
cl:connect(host,1883,{timeout=timeout})
cl:subscribe("outTopic/sensor/wind",2,callbc)
--cl:subscribe("#",2,callbc)

while (1) do
cl:message_loop(.5)
end



