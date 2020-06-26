local mqttclient = require("luamqttc/client")


function callbc(topic,data)
 print("t: "..topic.." d: "..data)
     if (data == "1") then 
	os.execute("mplayer /data/Library/Dog-barking-noises.mp3")
     end
 end




print(mqttclient)
cl=mqttclient.new("myclient")
host="192.168.0.90"
timeout=2
cl:connect(host,1883,{timeout=timeout})
cl:subscribe("outTopic/sensor/pir/2",2,callbc)

while (1) do
cl:message_loop(.5)
end



