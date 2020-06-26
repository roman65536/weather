local mqttclient = require("luamqttc/client")

--local test="Xd=200c,Xm=29c,Tu=180,Tx=120db,Vi=120,Vi=120.1V"

val={}
val["Ta"]='outTopic/sensor/temp'
val["Dm"]='outTopic/sensor/winddir'
val["Pa"]='outTopic/sensor/airpres'
val["Sm"]='outTopic/sensor/windspeed'
val["Ua"]='outTopic/sensor/humi'
val["Ri"]='outTopic/sensor/rain'
val["Sx"]='outTopic/sensor/windspeedMax'



fd=io.open("/dev/ttyUSB0","r")
--fd=io.open("/tmp/wetter.pipe","r")
if fd == nil then
		print("Couldn't open file ")
		os.exit()
	end

function split3(str, pattern)
    pattern = pattern or "[^%s]+"
    if pattern:len() == 0 then pattern = "[^%s]+" end
    local parts = {__index = insert}
    setmetatable(parts, parts)
    str:gsub(pattern, parts)
    print(parts)
    setmetatable(parts, nil)
    parts.__index = nil
    return parts
end


function split2(str, delimiter)
    if (delimiter=='') then return false end
    local pos,array = 0, {}
    -- for each divider found
    for st,sp in function() return string.find(str, delimiter, pos, true) end do
        table.insert(array, string.sub(str, pos, st - 1))
	--print(string.sub(str,pos,st-1))

        pos = sp + 1
    end
    table.insert(array, string.sub(str, pos))
    return array
end


function split(str, pat)
   local t = {}  -- NOTE: use {n = 0} in Lua-5.0
   local fpat = "(.-)" .. pat
   local last_end = 1
   local s, e, cap = str:find(fpat, 1)
   while s do
	   print(cap)
      if s ~= 1 or cap ~= "" then
         table.insert(t,cap)
      end
      last_end = e+1
      s, e, cap = str:find(fpat, last_end)
   end
   if last_end <= #str then
      cap = str:sub(last_end)
      table.insert(t, cap)
   end
   return t
end

function dump(o)
   if type(o) == 'table' then
      local s = '{ '
      for k,v in pairs(o) do
         if type(k) ~= 'number' then k = '"'..k..'"' end
         s = s .. '['..k..'] = ' .. dump(v) .. ','
      end
      return s .. '} '
   else
      return tostring(o)
   end
end



srv=mqttclient.new("Sensors")
timeout=5
host="localhost"
srv:connect(host,1883,{timeout=timeout})

while true do

test=fd:read("*line")
if test==nil  then  break end

k1=split2(test,",") 
--print(dump(k1))
--print(k1)
nr= table.getn(k1)
for i = 1,nr do
--print("test "..type(k1[i]))

for k,v in string.gmatch(k1[i], "(%w+)=([%w%.]+)&*") do

print("test2: "..k.." "..v)


mstr=string.match(v,"[0-9]+")
--print(mstr)
num=tonumber(string.match(v,"(%d[,.%d]*)"))
--print(num)
--num=tonumber(v)
--print(num)
if (val[k])  then 
		print (val[k].. " val ".. num) 
		print(srv:publish(val[k],num))

	      end
end

end

end
