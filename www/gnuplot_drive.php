<?php

require("mysql_DB.php");



//usage: gnuplot_graph.php
//   required options:
//      from=YYYY-mm-dd
//      to=YYYY-mm-dd
//      table=<Database data table>
//      graphs=<Column name of database data table, comma separated for more than one>
//      sys=<system ID>
//   optional:
//      title=<title of graph>
//      graphs_axes=<line descriptions, comma separated for more than one>
//      graphs_names=<line descriptions, comma separated for more than one>
//      data_file=<filename>
//      png_file=<filename> 
//      pdf_file=<filename> 


// initialize database
open_database();


if (!array_key_exists('from',$_REQUEST) or 
    !array_key_exists('to',$_REQUEST) or
    !array_key_exists('table',$_REQUEST) or
    !array_key_exists('graphs',$_REQUEST) or
    !array_key_exists('sys',$_REQUEST)) 
     crit_error("Not all required options are defined:<br><li>from<li>to<li>table<li>graphs<li>sys");



// open files
if (array_key_exists('data_file',$_REQUEST)) {
  $tmpfilename = $_REQUEST['data_file']; 
} else {
  $tmpfilename =  tempnam("/tmp","GNUPLOT_TMP_");
}
$tmpfile = fopen( $tmpfilename,"w"); 
if (!$tmpfile) crit_error("Could not open temporary file ".$tmpfilename);

if (array_key_exists('pdf_file',$_REQUEST)) {
  $pdffilename =  $_REQUEST['pdf_file'];
  $pngfilename =  tempnam("/tmp","GNUPLOT_PNG_").".png";
}

if (array_key_exists('png_file',$_REQUEST)) 
  $pngfilename =  $_REQUEST['png_file'];

if (array_key_exists('title',$_REQUEST)) 
  $title =  $_REQUEST['title'];

$query  = "select date,read_err,write_err from ".$_REQUEST['table']." ";
$query .= "where date>='".$_REQUEST['from']."' and  date<='".$_REQUEST['to']."'";
$query .= " and drive='".$_REQUEST['sys']."' order by date";

//header of tmp file
fwrite($tmpfile,"#\n# Automatically generated by gnuplot_graph.php\n#\n#Query: ".$query."\n#\n#");

$nodata = false;
if (get_rows($query,$result) < 1)  $nodata=true;


// prints colum names in header file
$fields = array();
if (!$nodata) {
  foreach ($result[0] as $fn => $r) {
    fwrite($tmpfile,$fn."	");
    array_push($fields,$fn);
  } 
  fwrite($tmpfile,"\n#\n");
  
  //print data in file
  foreach ($result as $r) {
    foreach ($r as $f)
      fwrite($tmpfile,$f."	");
    fwrite($tmpfile,"\n");
  }
}
fclose($tmpfile);

// initialize descriptors for comminication with gnuplot
$desc = array( 0 => array("pipe", "r"), 
	       1 => array("pipe", "w"), 
	       2 => array("file", "/tmp/error-output.txt", "a")
	       );
	       
$proc = proc_open("/usr/bin/tee /tmp/tee.out | /usr/bin/gnuplot", $desc, $pipes);
if (!is_resource($proc)) {
  crit_error("Error executing gnuplot\n");
} else {

  /*  set terminal png {small | medium | large}         */
  /*                   {transparent|notransparent}      */
  /*                   {picsize <xsize> <ysize>}        */
  /*                   {monochrome | gray |color}      */
  /*                   {<color0> <color1> <color2> ...} */

  fwrite($pipes[0], "set terminal png truecolor small size 1024,768\n");

  // set X axe
  fwrite($pipes[0], "set xdata time\n");
  fwrite($pipes[0], "set timefmt \"%Y-%m-%d %H:%M:%S\"\n");
  fwrite($pipes[0], "set grid\n");

  if ((strtotime($_REQUEST['to'])-strtotime($_REQUEST['from']))>172800) 
    fwrite($pipes[0], "set format x \" %d/%m/%Y \"\n");
  else
    fwrite($pipes[0], "set format x \" %d/%m/%y \\n%H:%M\"\n");
  
  fwrite($pipes[0], "set xrange [\"".$_REQUEST['from']."\":\"".$_REQUEST['to']."\"]\n");
 if($title)
  fwrite($pipes[0], "set title \"".$title."\"\n");


  // get other parameters from URL
  foreach ($_REQUEST as $param => $value)
    if (($param != 'PHPSESSID') && 
	($param != 'data_file') && ($param != 'png_file') && ($param != 'pdf_file') &&
	($param != 'from') && ($param != 'to') && 
	($param != 'sys') && ($param != 'table') && 
	($param != 'graphs') && ($param != 'graphs_axes') && ($param != 'graphs_names'))
      	 fwrite($pipes[0], "set ".$param."  ".str_replace("\\\"","\"",$value)."\n");

  if ($nodata)
    fwrite($pipes[0],"set label \"No data for the selected period\" at  graph 0.5,0.5 center\n");

  // plots
  $plot_query = "plot ";
  $graphs = split(",",$_REQUEST['graphs']);
  if (array_key_exists('graphs_axes',$_REQUEST))
    $graphs_a = split(",",$_REQUEST['graphs_axes']);
  if (array_key_exists('graphs_names',$_REQUEST))
    $graphs_n = split(",",$_REQUEST['graphs_names']);

  $range = array();
  foreach ($fields as $k => $v)   array_push ($range,"$".($k+1));

  foreach($graphs as $k => $graph) {
    if ($k != 0) $plot_query .=" , ";
    $plot_query .= "'".$tmpfilename."'  using 1:(";
    $plot_query .= str_replace($fields,$range,$graph);

    $plot_query .=") ";
    if (isset($graphs_a[$k])) 
      $plot_query .= "axes ".$graphs_a[$k]." ";
    if (isset($graphs_n[$k])) 
      $plot_query .= "title \"".$graphs_n[$k]."\" ";
    $plot_query .="with linespoints";
  }
  fwrite($pipes[0],$plot_query."\n");

//    $field_set = split(",",$_REQUEST['fields']);
//    foreach ($result as $r) {
//      fwrite($pipes[0],$r['date']."	");
//      foreach ($field_set as $f)
//        fwrite($pipes[0],$r[$f]."	");
//      fwrite($pipes[0],"\n");
//    }
//    fwrite($pipes[0],"e\n");

  fwrite($pipes[0], "save '/tmp/gnu.plt'\n");


  // if needed save png_file.
  if (isset($pngfilename)) {
    fwrite($pipes[0], "set output \"".$pngfilename."\"\n");
    fwrite($pipes[0], "replot\n");
  }

  
  fclose($pipes[0]);
  
  $image = "";
  while(!feof($pipes[1])) {
    $image .= fgets($pipes[1], 1024);
  }
  fclose($pipes[1]);   

  $return_value = proc_close($proc);

  if (0) {  // for debug
    print "<pre>";
    print "DB query: ".$query."\n\n";
    print "Gnuplot query: ".$plot_query."\n\n";
    print_r($graphs);
    print "</pre>";
  }else {
    header("Content-type: image/png"); 
    header("Content-length: ".strlen($image));
    print $image;
  }
}
  
//if needed create pdf file
if (isset($pdffilename)) {

  require ('./pdf/class.ezpdf.php');


  /*initialize pdf and dimentions*/
  $d = array(); # d = document
  $d['pdf'] = & new Cezpdf();

  $d['pdf']->ezSetCmMargins(1.5,
			    2.5,
			    1.0,
			    1.0);
  $d['pdf']->selectFont('./pdf/fonts/Helvetica.afm');

  /*page-width */     $d['w'] = $d['pdf']->ez['pageWidth'];
  /*page-height*/     $d['h'] = $d['pdf']->ez['pageHeight'];

  /*top   -margin*/   $d['tm'] = $d['pdf']->ez['topMargin'];
  /*bottom-margin*/   $d['bm'] = $d['pdf']->ez['bottomMargin'];
  /*left  -margin*/   $d['lm'] = $d['pdf']->ez['leftMargin'];
  /*right -margin*/   $d['rm'] = $d['pdf']->ez['rightMargin'];

  /*center-x*/        $d['cx'] = $d['lm'] + ($d['w'] - $d['lm'] - $d['rm'] )/2; 


  /*header & footer*/
  $hs = 10;
  $footer ="this is the footer";
  $title = "this is the title";
  $logow = $d['w'] - $d['lm']/2 - $d['rm']/2 - 2*$hs;
  $rect_b = $d['h']-3*$d['tm']/4;
  $rect_h = $d['tm']/2;
  $img_base = $d['h']-11*$d['tm']/16;
  $tit_base = $img_base - $d['pdf']->getFontDecender(16);
  $footer_hs = $d['pdf']->getTextWidth(12,$footer)/2;
  $footer_fh = $d['pdf']->getFontHeight(12);
  $all = $d['pdf']->openObject();
  $d['pdf']->saveState();
  $d['pdf']->setColor(1.0,1.0,0.0); //  set_color($d,"sun_jellow");
  $d['pdf']->filledRectangle($d['lm']/2                      ,$rect_b, 0.55*$logow ,$rect_h);
  $d['pdf']->setColor(1.0,0.0,0.0); //  set_color($d,"sun_red");
  $d['pdf']->filledRectangle($d['lm']/2 +   $hs + 0.55*$logow ,$rect_b, 0.25*$logow ,$rect_h);
  $d['pdf']->setColor(0.0,0.0,1.0); //  set_color($d,"sun_blue");
  $d['pdf']->filledRectangle($d['lm']/2 + 2*$hs + 0.80*$logow ,$rect_b, 0.20*$logow ,$rect_h);
  $d['pdf']->setColor(1.0,0.0,1.0); //  set_color($d,"doc_title");
  $d['pdf']->addText($hs+$d['lm']/2,$tit_base,16,$title);
  $d['pdf']->setColor(1.0,1.0,1.0); //  set_color($d,"white");
  $d['pdf']->addTextWrap($d['lm']/2 +0.55*$logow,$tit_base,0.25*$logow,12,date('F Y'),'right',0,0);
  $d['pdf']->addPngFromFile('./sunlogo.png',$d['lm']/2 + 2*$hs + 0.90*$logow,$img_base,0,3*$d['tm']/8);
  $d['pdf']->setStrokeColor(0.0,0.0,0.0,1); //  set_stroke_color($d,"black");
  $d['pdf']->line($d['lm']/2,$d['bm']*3/4,$d['w']-$d['rm']/2,$d['bm']*3/4);
  $d['pdf']->setColor(1.0,1.0,1.0); //  set_color($d,"black");
  $d['pdf']->addText($d['cx']-$footer_hs,$d['bm']/2-$footer_fh/2,12,$footer);
  $d['pdf']->restoreState();
  $d['pdf']->closeObject();
  // note that object can be told to appear on just odd or even pages by changing 'all' to 'odd' or 'even'.
  $d['pdf']->addObject($all,'all');
  
  /*page numeration*/
  $d['pdf']->ezStartPageNumbers($d['w']-$d['rm'],$d['bm']/4,12,'left','page {PAGENUM} of {TOTALPAGENUM}','');


  $d['pdf']->addPngFromFile($pngfilename,$d['lm']/2 ,$d['tm']+200,$d['w'] - $d['lm'] - $d['rm']);

  $pdfcode = $d['pdf']->ezOutput();

  $fp = fopen($pdffilename,'w');
  fwrite($fp,$pdfcode);
  fclose($fp);
}

//delete png temporary file if not requested in url
if (!array_key_exists('png_file',$_REQUEST) && 
    array_key_exists('pdf_file',$_REQUEST)) unlink($pngfilename);

//delete data temporary file if not requested in url
if (!array_key_exists('data_file',$_REQUEST)) unlink($tmpfilename);

// Close DB connection 
close_database();



?>
