<?php
header ('Content-type: text/html; charset=utf-8');
?>
<html>
    <head>
<?php

   echo 'The date is: "'.$_POST['date_von'].'"';
   echo 'The date is: "'.$_POST['date_bis'].'"';
   echo 'The date is: <pre>"'.$_POST['comment'].'</pre>"';
 printf("len comment %d \n ",strlen($_POST['comment']));
 $mystr=$_POST['comment'];
 for ($a=0;$a<strlen($_POST['comment']);$a++)
   printf(" %02x", ord( $mystr[$a]));

 list($tag_v,$monat_v,$jahr_v)=split('[/.-]',$_POST['date_von']);
 list($tag_b,$monat_b,$jahr_b)=split('[/.-]',$_POST['date_bis']);
  echo "  tag  $tag_v monat $monat_v jahr $jahr_v Test<br />";



 file_put_contents("/tmp/roman.txt",$mystr);
?>

<!-- Jquery resources-->



 <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<!-- end of Jquery resources-->


 <script>
$(function() {
$( "#datepicker" ).datepicker({
showOn: "button",
buttonImageOnly: false,
dateFormat: "dd-mm-yy", 
showWeek: true,
changeMonth: true,
changeYear: true,
setDate: "<?php echo $_POST['date_von'] ?>"
});
});


$(function() {
$( "#datepicker1" ).datepicker({
showOn: "button",
buttonImage: "images/calendar.gif",
buttonImageOnly: false,
dateFormat: "dd-mm-yy",
setDate: "<?php echo $_POST['date_bis'] ?>"
});
});

</script>

</head>

 <body>
        <form  action="<?php echo $_SERVER['REQUEST_URI'];?>"  method="post">
            Date From: <input type="text" name="date_von" id="datepicker" value="<?php echo $_POST['date_von'] ?>" /> 
            Date To: <input type="text" name="date_bis" id="datepicker1" value="<?php echo $_POST['date_bis'] ?>" /> <br/>
	    <textarea maxlength="2048" name="comment" rows="5" cols="75"><?php echo $_POST['comment'] ?></textarea>
            <input type="submit" value="Send date" />
        </form>

<?php


$image_file = "tmp/image.png"; 
//$image_file = tempnam("tmp/","gnuplotout");
echo $image_file;
$data_file = tempnam("/tmp","gnuplotout");
$handle = fopen($data_file, "w");
fprintf($handle, "2014/01/01 123\n");
fprintf($handle, "2014/01/02 121\n");
fprintf($handle, "2014/01/03 122\n");
fprintf($handle, "2014/01/04 123\n");
fprintf($handle, "2014/01/05 125\n");
fprintf($handle, "2014/01/06 123\n");
fclose($handle);
$gplot_start =  sprintf("%s/%s/%s",$jahr_v,$monat_v,$tag_v);
$gplot_finish =  sprintf("%s/%s/%s",$jahr_b,$monat_b,$tag_b);
//$gplot_start = date("y/m/d", $_POST['date_von']);
//$gplot_finish = date("y/m/d", $_POST['date_bis']);
$gnuplot_cmds = <<< GNUPLOTCMDS
set term png truecolor small size 800,600
set output "$image_file"
set size 1, 1
set title "Title"
set xlabel "Date"
set ylabel "EURO"
set grid
set xdata time
set timefmt "%Y/%m/%d"
set xrange ["$gplot_start":"$gplot_finish"]
set yrange [0:*]
set format x "%d/%m/%Y"
#set nokey
plot "$data_file" using 1:2 with lines t 'Kurs'
GNUPLOTCMDS;
$gnuplot_cmds .= "\n";

// Start gnuplot
if(!($pgp = popen("/usr/bin/gnuplot", "w"))){
    # TODO Handle error
    exit;
}
fputs($pgp, $gnuplot_cmds);
pclose($pgp);
//header("Content-Type: image/png");
//passthru("cat $image_file");

// Clean up and exit
//unlink($data_file);
//unlink($image_file);
echo '<p><img src="'. htmlspecialchars($image_file) . "?" . filemtime($image_file) . '"/></p>';

?>

</body>
</html>

