-- load driver
local driver = require "luasql.postgres"
-- create environment object
env = assert (driver.postgres())
-- connect to data source
con = assert (env:connect("dbname=sensor user=postgres password=postgres hostaddr=127.0.0.1"))


local mqttclient = require("luamqttc/client")


function callbc(topic,data)
 print("t: "..topic.." d: "..data)

 str=string.format("insert into sensors values(now(),'%s','%s')",topic,data)
 print(str)
 print(con:execute(str))
 
 end




print(mqttclient)
cl=mqttclient.new("DBSub")
host="localhost"
timeout=2
cl:connect(host,1883,{timeout=timeout})
--cl:subscribe("outTopic/sensor/wind",2,callbc)
cl:subscribe("outTopic/#",2,callbc)

while (1) do
cl:message_loop(.5)
end



