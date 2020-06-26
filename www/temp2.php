<?php


require("conf.php");
// initialize database
//open_database();

//$host = "192.168.0.90"; 
//$user = "postgres"; 
//$pass = "postgres"; 
//$db = "sensors"; 

$con = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die ("Could not connect to server\n"); 

header ('Content-type: text/html; charset=utf-8');
?>
<html>
    <head>
<?php


 if ((!array_key_exists('date_von',$_REQUEST)) or
     (!array_key_exists('date_bis', $_REQUEST)) )
   {
     $today=time();
     $date_bis=date('d-m-Y', $today);
     $date_von=date('d-m-Y', strtotime('-2 day'));
   }
 else
  {
    $date_von=$_POST['date_von'];
    $date_bis=$_POST['date_bis'];
  }



   echo 'From date is: "'.$date_von.'"';
   echo 'To date is: "'.$date_bis.'"';
//   echo 'The date is: <pre>"'.$_POST['comment'].'</pre>"';
// printf("len comment %d \n ",strlen($_POST['comment']));
 $mystr=$_POST['comment'];
 for ($a=0;$a<strlen($_POST['comment']);$a++)
   printf(" %02x", ord( $mystr[$a]));

 list($tag_v,$monat_v,$jahr_v)=split('[/.-]',$date_von);
 list($tag_b,$monat_b,$jahr_b)=split('[/.-]',$date_bis);
//  echo "  tag  $tag_v monat $monat_v jahr $jahr_v Test<br />";



 file_put_contents("/tmp/roman.txt",$mystr);
?>

<!-- Jquery resources-->



 <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="/jquery/jquery-1.9.1.js"></script>
<script src="/jquery/jquery-ui.js"></script>

<!-- end of Jquery resources-->


 <script>
$(function() {
$( "#datepicker" ).datepicker({
showOn: "button",
buttonImage: "/jquery/calendar.gif",
buttonImageOnly: true,
dateFormat: "dd-mm-yy", 
showWeek: true,
changeMonth: true,
changeYear: true,
setDate: "<?php echo $date_von ?>"
});
});


$(function() {
$( "#datepicker1" ).datepicker({
showOn: "button",
buttonImage: "/jquery/calendar.gif",
buttonImageOnly: true,
dateFormat: "dd-mm-yy",
setDate: "<?php echo $date_bis ?>"
});
});

</script>

</head>

 <body>
        <form  action="<?php echo $_SERVER['REQUEST_URI'];?>"  method="post">
            Date From: <input type="text" name="date_von" id="datepicker" value="<?php echo $date_von?>" /> 
            Date To: <input type="text" name="date_bis" id="datepicker1" value="<?php echo $date_bis?>" /> <br/>
            <input type="submit" value="Send date" />
        </form>

<?php

$from =  sprintf("%s-%s-%s 00:00:00",$jahr_v,$monat_v,$tag_v);
$to =  sprintf("%s-%s-%s 23:59:59",$jahr_b,$monat_b,$tag_b);


$query="select sensor from sensors  where datetime >= '".$from."' AND datetime <= '".$to."'  group by 1 order by 1"; 


echo "<pre>".$query."</pre>";
$result=pg_query($con,$query);
$num=pg_num_rows($result);
printf("<pre>%d</pre>\n",$num);



echo "<b><center>Sensor Data</center></b><br><br>";

?>
<table border="1" cellspacing="2" cellpadding="2">
<tr>
<th><font face="Arial, Helvetica, sans-serif">Sensor</font></th>

</tr>

<?php
$i=0;
while ($i < $num) {
$sensor=pg_fetch_row($result,$i);

?>

<tr>
<td><font face="Arial, Helvetica, sans-serif"><?php echo "$sensor[0]"; ?></font></td>

</tr>
<?php
++$i;
}
echo "</table>";






$i=0;
while ($i < $num) {
$sensor=pg_fetch_row($result,$i);
	$params  ="?sys=".$sensor[0]."&from=".$from."&to=".$to;
	$params .="&table=sensors";
	$params .="&title=Sensor Data ".$sensor[0];
	$params .="&graphs=value";
	$params .="&graphs_names=values";
	print "<img src=\"pg_gnuplot_temp.php".str_replace(array(" ","\"","+"),array("%20","%22","%2B"),$params)."\"><br>\n";

++$i;
}



$params  ="?sys=".$sensor[0]."&from=".$from."&to=".$to;
        $params .="&table=in_out";
        $params .="&title=Temperature Data ";
        $params .="&graphs=in1_v,in2_v,out_v";
        $params .="&graphs_names=inside1,inside2,outside";
        print "<img src=\"pg_gnuplot_multi.php".str_replace(array(" ","\"","+"),array("%20","%22","%2B"),$params)."\"><br>\n";






?>

</body>
</html>

