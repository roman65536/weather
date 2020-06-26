<?php


require("mysql_DB.php");
// initialize database
open_database();



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
     $date_von=date('d-m-Y', strtotime('-1 year'));
   }
 else
  {
    $date_von=$_POST['date_von'];
    $date_bis=$_POST['date_bis'];
  }



   echo 'The date is: "'.$date_von.'"';
   echo 'The date is: "'.$date_bis.'"';
   echo 'The date is: <pre>"'.$_POST['comment'].'</pre>"';
 printf("len comment %d \n ",strlen($_POST['comment']));
 $mystr=$_POST['comment'];
 for ($a=0;$a<strlen($_POST['comment']);$a++)
   printf(" %02x", ord( $mystr[$a]));

 list($tag_v,$monat_v,$jahr_v)=split('[/.-]',$date_von);
 list($tag_b,$monat_b,$jahr_b)=split('[/.-]',$date_bis);
  echo "  tag  $tag_v monat $monat_v jahr $jahr_v Test<br />";



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

$from =  sprintf("%s-%s-%s",$jahr_v,$monat_v,$tag_v);
$to =  sprintf("%s-%s-%s",$jahr_b,$monat_b,$tag_b);


$query="select drive, max(read_err)- min(read_err) as readerror,max(write_err)-min(write_err) as writeerror from drives  where date >= '".$from."' AND date <= '".$to."' group by drive having min(write_err) != max(write_err) OR min(read_err) != max(read_err)"; 


echo "<pre>".$query."</pre>";
$result=mysql_query($query);
$num=mysql_numrows($result);
printf("<pre>%d</pre>\n",$num);



echo "<b><center>Read/Write Errors on Drive</center></b><br><br>";

?>
<table border="1" cellspacing="2" cellpadding="2">
<tr>
<th><font face="Arial, Helvetica, sans-serif">Drive</font></th>
<th><font face="Arial, Helvetica, sans-serif">Read Errors</font></th>
<th><font face="Arial, Helvetica, sans-serif">Write Errors</font></th>
</tr>

<?
$i=0;
while ($i < $num) {
$drive=mysql_result($result,$i,"drive");
$r_err=mysql_result($result,$i,"readerror");
$w_err=mysql_result($result,$i,"writeerror");
?>

<tr>
<td><font face="Arial, Helvetica, sans-serif"><? echo "$drive"; ?></font></td>
<td><font face="Arial, Helvetica, sans-serif"><? echo "$r_err"; ?></font></td>
<td><font face="Arial, Helvetica, sans-serif"><? echo "$w_err"; ?></font></td>
</tr>
<?
++$i;
}
echo "</table>";






$i=0;
while ($i < $num) {
$drive=mysql_result($result,$i,"drive");
	$params  ="?sys=".$drive."&from=".$from."&to=".$to;
	$params .="&table=drives";
	$params .="&title=Read Errors Drive ".$drive;
	$params .="&graphs=read_err";
	$params .="&graphs_names=read_err";
	print "<img src=\"gnuplot_drive.php".str_replace(array(" ","\"","+"),array("%20","%22","%2B"),$params)."\"><br>\n";
	$params  ="?sys=".$drive."&from=".$from."&to=".$to;
	$params .="&table=drives";
	$params .="&title=Write Errors Drive ".$drive;
	$params .="&graphs=write_err";
	$params .="&graphs_names=write_err";
	print "<img src=\"gnuplot_drive.php".str_replace(array(" ","\"","+"),array("%20","%22","%2B"),$params)."\"><br>\n";
++$i;
}






//$sys="A00094";

	$params  ="?sys=".$sys."&from=".$from."&to=".$to;
	$params .="&table=tapes";
	$params .="&graphs=read_err,write_err";
	$params .="&graphs_names=read_err,write_err";
//	print "<img src=\"gnuplot_graph.php".str_replace(array(" ","\"","+"),array("%20","%22","%2B"),$params)."\"><br>\n";




$query="select label, max(read_err)- min(read_err) as readerror,max(write_err)-min(write_err) as writeerror from tapes  where date >= '".$from."' AND date <= '".$to."' group by label having min(write_err) != max(write_err) OR min(read_err) != max(read_err)";


echo "<pre>".$query."</pre>";
$result=mysql_query($query);
$num=mysql_numrows($result);
printf("<pre>%d</pre>\n",$num);



echo "<b><center>Read/Write Errors on Drive</center></b><br><br>";

?>
<table border="1" cellspacing="2" cellpadding="2">
<tr>
<th><font face="Arial, Helvetica, sans-serif">Drive</font></th>
<th><font face="Arial, Helvetica, sans-serif">Read Errors</font></th>
<th><font face="Arial, Helvetica, sans-serif">Write Errors</font></th>
</tr>

<?
$i=0;
while ($i < $num) {
$drive=mysql_result($result,$i,"label");
$r_err=mysql_result($result,$i,"readerror");
$w_err=mysql_result($result,$i,"writeerror");
?>

<tr>
<td><font face="Arial, Helvetica, sans-serif"><? printf("<a href=\"tape.php?from=%s&to=%s&tape=%s\">$drive</a>",$from,$to,$drive); ?></font></td>
<td><font face="Arial, Helvetica, sans-serif"><? echo "$r_err"; ?></font></td>
<td><font face="Arial, Helvetica, sans-serif"><? echo "$w_err"; ?></font></td>
</tr>
<?
++$i;
}
echo "</table>";




?>

</body>
</html>

