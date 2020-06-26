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

 list($tag_v,$monat_v,$jahr_v)=split('[/.-]',$date_von);
 list($tag_b,$monat_b,$jahr_b)=split('[/.-]',$date_bis);
  echo "  tag  $tag_v monat $monat_v jahr $jahr_v Test<br />";



 file_put_contents("/tmp/roman.txt",$mystr);
?>

</head>

 <body>

<?php

$from =  sprintf("%s-%s-%s",$jahr_v,$monat_v,$tag_v);
$to =  sprintf("%s-%s-%s",$jahr_b,$monat_b,$tag_b);


$tape=$_REQUEST['tape'];


echo "<pre>".$tape."</pre>";



echo "<b><center>Read/Write Errors on Tape $tape</center></b><br><br>";

   $params  ="?sys=".$tape."&from=".$from."&to=".$to;
        $params .="&table=tapes";
        $params .="&graphs=read_err,write_err";
        $params .="&graphs_names=read_err,write_err";
      print "<img src=\"gnuplot_graph.php".str_replace(array(" ","\"","+"),array("%20","%22","%2B"),$params)."\"><br>\n";


?>


</body>
</html>

