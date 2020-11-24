
# Linux connection for the Vaisala WXT530 Weather station

Weather station is connected via USB to Linux, this few Lua scripts receive the messages and convert those to more readable form.
Then sends the messages to mqtt, where everybody can subscribe for the particalur values.

# Description 
The "split.lua" opens the serial connection to the weather station (default /dev/ttyUSB0) and reads the Vaisala WXT530 messages.
Messages are compared agains the "val" table, which is an associative array. When match is found, message is converted to the coresponding value in the "val" table.
For example : val["Ta"]='outTopic/sensor/temp'  the "Ta" message from the WXT530, is converted to "outTopic/sensor/temp". The "outTopic/sensor/temp" will be the Topic of the MQTT Message together with the physical value. 


The "sub.lua", subscribe to MQTT for all "outTopic" messages and received values are stored in the PostgreSQL. 

In the "www" directory, are php scripts, which are called via php to generate appropriate gnuplot graphics from the stored sensor data. In the "conf.php" are all configurations parameter for the DB connection. By calling "temp2.php" in browser, the webpage should get the stored db values and particular physical values and create the graphics.    

# Why doing it via MQTT ?
Since such an Vaisala Sensor is not a cheap thing and very precise instrument, may be more the one User/Program may be interessted for the Physical Values. 
One thing is to show historical data via DB, but what about triggering events as they Weather values get critical? For example, a small wind turbine needs to be break to hold, when wind speed reaches curtain limits. In this case, programm subscribe to wind speed data via MQTT and when ever sensor send the Wind data, the subscribe programm will get those as well. 

 
# Prerequisites:

* https://github.com/Yongke/luamqttc   MQTT Lua Client
* Postgresql
* MQTT Server such as Mosquito
* luasql postgress
* apache2 + php + php postgress
* Gnuplot


