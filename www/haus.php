<?php


require("mysql_DB.php");
// initialize database
open_database();
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="refresh" content="30" />
<meta http-equiv="content-type" content="text/html; charset=UTF8">
</head>
<body>

<canvas id="myCanvas" width="800" height=600" style="solid #d3d3d3;">
Your browser does not support the HTML5 canvas tag.</canvas>

<script>
var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
var l=1*800;

ctx.font = "13px Times";

//outline
ctx.moveTo(0,0);
ctx.lineTo(l,0);
ctx.lineTo(l,l*.5357);
ctx.lineTo(0,l*.5355);

//kids
ctx.lineTo(0,0);
ctx.moveTo(l*.2035,0);
ctx.lineTo(l*.2035,l*.1696);
<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/temp/1' order by datetime desc limit 1"; 
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value");
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/temp/1' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor"; 
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="\u2191" ;
  else $delta="\u2193";
?>
ctx.fillText("Kids 1",l*.05,l*.075);
ctx.fillText("<?php echo $temp?> \xB0C <?php echo $delta?>",l*.05,l*.075+15);

<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/temp/5' order by datetime desc limit 1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value");
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/temp/5' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor"; 
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="^" ;
  else $delta="v";
?>


//schlaf
ctx.moveTo(0,l*(.5357-.2142));
ctx.lineTo(l*(.2857+.1428),l*(.5357-.2142));
ctx.fillText("Spalna 5",l*.07,l*.4);
ctx.fillText("<?php echo $temp?> \xB0C <?php echo $delta?>",l*.07,l*.4+15);

ctx.fillText("Chodba 3",l*.32,l*.2);
<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/temp/3' order by datetime desc limit 1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value");
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/temp/3' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor"; 
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="^" ;
  else $delta="v";
?>
ctx.fillText("<?php echo $temp?> \xB0C <?php echo $delta?>",l*.32,l*.2+15);


ctx.fillText("Obyvacka 0",l*.7,l*.2);
<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/temp/0' order by datetime desc limit 1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value"); 
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/temp/0' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="^" ;
  else $delta="v";

 
?>
ctx.fillText("<?php echo $temp?> \xB0C <?php echo $delta?>",l*.7,l*.2+15);
<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/humi/0' order by datetime desc limit 1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value");
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/humi/0' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="^" ;
  else $delta="v";
?>
ctx.fillText("<?php echo $temp?> % <?php echo $delta?>",l*.7,l*.2+30);



ctx.fillText("Veranda 2",l*.32,l*.6);
<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/temp/2' order by datetime desc limit 1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value");
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/temp/2' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="^" ;
  else $delta="v";
?>
ctx.fillText("<?php echo $temp?> \xB0C <?php echo $delta?>",l*.32,l*.6+15);
<?php
$query="select datetime,value from sensors where sensor='OutTopic/sensor/humi/2' order by datetime desc limit 1";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp=mysql_result($result,$i,"value");
$query="select datetime,avg(value) as value from sensors where sensor ='outTopic/sensor/humi/2' and datetime > DATE_SUB(NOW(), interval 1 HOUR) group by sensor";
$result=mysql_query($query);
$num=mysql_numrows($result);
$temp_o=mysql_result($result,$i,"value");
if ($temp > $temp_o) $delta="^" ;
  else $delta="v";
?>
ctx.fillText("<?php echo $temp?> % <?php echo $delta?>",l*.32,l*.6+30);


ctx.moveTo(l*.2857,l*.5357);
ctx.lineTo(l*.2857,l*(.5357-.2142));

ctx.moveTo(l*(.2857+.1428),l*.5357);
ctx.lineTo(l*(.2857+.1428),l*(.5357-.2142));

ctx.moveTo(l*(.2035+.1982),0);
ctx.lineTo(l*(.2035+.1982),l*.1696);

ctx.moveTo(l*(.2035+.1982+.1285),0);
ctx.lineTo(l*(.2035+.1982+.1285),l*.1696);

ctx.moveTo(l*(.2035+.1982+.1285+.0821),0);
ctx.lineTo(l*(.2035+.1982+.1285+.0821),l*.1696);

ctx.moveTo(l*(.1571),l*.1696);
ctx.lineTo(l*(.2035+.1982+.1285+.0821),l*.1696);

ctx.moveTo(l*(.1571),l*.1696);
ctx.lineTo(l*(.1571),l*(.5357-.2142));


ctx.stroke();
</script>

</body>
</html>

