
Linux connection for the Vaisala WXT530 Weather station

Weather station is connected via USB to Linux, this few Lua scripts receive the messages and convert those to more readable form.
Then sends the messages to mqtt, where everybody can subscribe for the particalur values.

Prerequisites:

https://github.com/Yongke/luamqttc   MQTT Lua Client
luasql postgress
apache2 + php + php postgress
Gnuplot


