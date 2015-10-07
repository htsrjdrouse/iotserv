<?php 



//This is your logger
$logger = './loggerdataset';
//collecting variables for logger
include('repstrapfunctionslib.php');
//collecting variables for logger
include('cli.interface.php');

$rampx = $json['trackxyz']['x'];
$rampy = $json['trackxyz']['y'];

//echo 'RAMPX: '.$rampx.'<br>';
//echo 'RAMPY: '.$rampy.'<br>';


//for spotfinding stuff
$loadcoords = 0;
$coords1 = array("cx"=>0, "cy"=>0, "sptmean"=>0, "sptdia"=>0, "bckmean"=>0, "sn"=>0, "sr"=>0, "sc"=>0);
$coords2 = array("cx"=>0, "cy"=>0, "sptmean"=>0, "sptdia"=>0, "bckmean"=>0, "sn"=>0, "sr"=>0, "sc"=>0);
$coords3 = array("cx"=>0, "cy"=>0, "sptmean"=>0, "sptdia"=>0, "bckmean"=>0, "sn"=>0, "sr"=>0, "sc"=>0);
$coords4 = array("cx"=>0, "cy"=>0, "sptmean"=>0, "sptdia"=>0, "bckmean"=>0, "sn"=>0, "sr"=>0, "sc"=>0);


//macroscript

if ((isset($_POST['act'])) and ($_POST['act'] == 'gcodesave')){
 $opmode = $_POST['gcode'];
 $json['editgcode'] = "0";
 //here I probably need to clean the gcoderp
 // by doing this $gcoderp = preg_replace('/"/', '', $_POST['gcoderp']);
 $scriptname = $_POST['scriptname'];
 $pregcoderp = $_POST['macroscript'];
 $gcoderp = preg_split('/\r\n/', $pregcoderp);
 $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
 file_put_contents('taskjob3', json_encode($jsontasker3));
 $gcoderp = preg_replace('/"/', '', $gcoderp);
 $json['view'] = "E";
 if ($opmode == 'Delete'){
  $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
  /*
  $filenamery = $jsontasker3['filename'];
  $datary = $jsontasker3['data'];
  $ndatary = array(); 
  echo 'track '.$jsontasker3['track'].'<br>';
  $ct = -1;
  for ($i=0;$i<count($filenamery);$i++){
   $ct = $ct + 1;
   if ($jsontasker3['track'] == $ct){
	pass;
   }
   else {
    $nfilenamery[$i] = $filenamery[$i]; 
    $ndatary[$i] = $datary[$i]; 
   }
  }
  $jsontasker3['filename'] = $nfilenamery;
  $jsontasker3['data'] = $ndatary;
  $jsontasker3['track'] = count($jsontasker3['filename'])-1;
  */
  $newtrack =  (array_search($scriptname,$jsontasker3['filename'])-1);
  unset($jsontasker3['filename'][$jsontasker3['track']]);
  unset($jsontasker3['data'][$jsontasker3['track']]);
  $jsontasker3['track'] = $newtrack;
  file_put_contents('taskjob3', json_encode($jsontasker3));
 }
 else if ($opmode == 'Save'){
  $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
  //$track = array_search($scriptname,$jsontasker3['filename']);
  if (array_search($scriptname,$jsontasker3['filename'])){
   $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
   $track = array_search($scriptname,$jsontasker3['filename']);
   $jsontasker3['data'][$track] = $gcoderp;
   $jsontasker3['track'] = $track;
  }
  else {
   array_push($jsontasker3['filename'],$scriptname);
   array_push($jsontasker3['data'],$gcoderp);
   $jsontasker3['track'] =  array_search($scriptname,$jsontasker3['filename']);
  }
  file_put_contents('taskjob3', json_encode($jsontasker3));
 }
}

//<input type=hidden name="act" value="pythonsave">
if ((isset($_POST['act'])) and ($_POST['act'] == 'pythonsave')){
 $json['editgcode'] = "0";
 //echo 'savepython test: '.$_GET['savepython'].'<br>';
 $opmode = $_POST['savepython'];
 $view = $_POST['view'];
 $scriptname = $_POST['scriptname'];
 $json['view'] = "E";
 $pythoncode = $_POST['pythoncode']; 
 $jsonpython = json_decode(file_get_contents('pythoncode'), true);
 file_put_contents('pythoncode', json_encode($jsonpython));
 if ($opmode == 'Delete'){
  $jsonpython = json_decode(file_get_contents('pythoncode'), true);
  $newtrack = (array_search($scriptname,$jsonpython['filename'])-1);
  //echo 'newtrack is '.$newtrack.'<br>';
  unset($jsonpython['filename'][$jsonpython['track']]);
  unset($jsonpython['script'][$jsonpython['track']]);
  $jsonpython['track'] = $newtrack;
  file_put_contents('pythoncode', json_encode($jsonpython));
 }
 else if ($opmode == 'Save'){
  $jsonpython = json_decode(file_get_contents('pythoncode'), true);

 if(array_search($scriptname,$jsonpython['filename'])){
   $track = array_search($scriptname,$jsonpython['filename']);
   $jsonpython['track'] = $track;
   $jsonpython['script'][$track] = $pythoncode; 
   echo "Problem ... file exists<br>";
  }
 else {
    $gcodescript = preg_replace('/.py$/', '.out.g', $scriptname);
    //echo $scriptname.'<br>';
    array_push($jsonpython['filename'],$scriptname);
    array_push($jsonpython['script'],$pythoncode);
    $jsonpython['track'] =  array_search($scriptname,$jsonpython['filename']);
    $json['gfile'] = $jsonpython['track'];
   }
  }
  $json['pythoncode'] = $pythoncode;
  $track = $jsonpython['track'];
  file_put_contents('pythoncode', json_encode($jsonpython));
 }



//<input type=hidden name="act" value="pythonrun">
if ((isset($_GET['act'])) and ($_GET['act'] == 'pythonrun')){
 $view = $_GET['view'];
 $json['view'] = "E";
 $jsonpython = json_decode(file_get_contents('pythoncode'), true);
 $d = fopen('repstrap.py', 'w');
 $pythoncode = $jsonpython['script'][$jsonpython['track']];
 fwrite($d, $pythoncode);
 fclose($d);
 $cmd = 'sudo python repstrap.py';
 $result =  exec($cmd, $output);
 $outputfile = preg_replace('/\.py/','.out.g',$jsonpython['filename'][$jsonpython['track']]);
 file_put_contents('pythoncode', json_encode($jsonpython));
 $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
   if(array_search($outputfile,$jsontasker3['filename'])){
    //echo 'can load these files<br>';
    //echo $scriptname.'<br>';
    echo "Problem ... file exists<br>";
   }
   else {
    $result =  preg_replace('/\[|\]|\'/','',$result);
    $resultry = preg_split('/, /', $result);
    //var_dump($resultry);
    //echo $result.'<br>';
    array_push($jsontasker3['filename'],$outputfile);
    array_push($jsontasker3['data'],$resultry);
   }
 $jsontasker3['track'] =  array_search($outputfile,$jsontasker3['filename']);
 echo '<br>';
 //var_dump($jsontasker3['filename']);
 echo '<br>';
 file_put_contents('taskjob3', json_encode($jsontasker3));
}






//This is the for the workplate since it exceeds the GET url lengh
//------Workplate target selection -----

	//<input type=hidden name="act" value="Workplate">
	//<input type=hidden name="view" value="C">

if ((isset($_POST['act'])) and ($_POST['act'] == 'Workplate')){

	//resets the $json['workplate']['enabledtargets']
 	$json['workplate']['enabledtargets'] = [0,0,0,0,0,0,0,0,0,0];	

	$json['workplate']['enabletar'] = $_POST['enabletar'];
	$enry = array();
	for($i=0;$i<count($json['workplate']['enabletar']);$i++){
	 //"reference":{"1_1":"1","1_2":"2","1_3":"3","1_4":"4","1_5":"5","2_1":"6","2_2":"7","2_3":"8","2_4":"9","2_5":"10"}
 	 $enabledtarref = $json['workplate']['reference'][$json['workplate']['enabletar'][$i]];
	 //echo $json['workplate']['enabletar'][$i].'<br>';
 	 //echo 'Enabled targets: '.$json['workplate']['reference'][$json['workplate']['enabletar'][$i]].'<br>';
 	 //echo 'Enabled targets: '.($enabledtarref).'<br>';
	 $enry[$i] = ($enabledtarref);
	}

	 for ($j=0;$j<count($json['workplate']['enabledtargets']); $j++){
	  //echo 'j '.$j.'<br>';
	  for ($k=0;$k<count($enry);$k++){
	   //echo 'k '.$enry[$k].'<br>';
	   if ($j+1 == $enry[$k]){
		//echo ($j+1).' '.$enry[$k].' yes<br>';
		$json['workplate']['enabledtargets'][$j] = 1;
	   }
	  }
	}
	$json['view'] = $_POST['view'];

	if ($json['positioningmode'] == "imaging"){
	 $json['workplate']['imagingz'] = $_POST['imagingz'];
	 $json['workplate']['imagingzacrosstargets'] = $_POST['imagingzacrosstargets'];
	 $json['workplate']['imaginglacz'] =laccheck($_POST['imaginglacz']);
	 $json['workplate']['imagingzlacacrosstargets'] =laccheck($_POST['imagingzlacacrosstargets']);
	}
	else if ($json['positioningmode'] == "spotting"){
	 $json['workplate']['spottinglacz'] =laccheck($_POST['spottinglacz']);
	 $json['workplate']['spottingz'] = $_POST['spottingz'];
	 $json['workplate']['spottingzacrosstargets'] = $_POST['spottingzacrosstargets'];
	 $json['workplate']['spottingzlacacrosstargets'] =laccheck($_POST['spottingzlacacrosstargets']);
 	 $json['workplate']['spottingzacrossspottingposition'] = $_POST['spottingzacrossspottingposition'];
	 $json['workplate']['spottingzlacacrossspottingposition'] =laccheck($_POST['spottingzlacacrossspottingposition']);
	}

	$json['workplate']['rowsp'] = $_POST['tarrowsp'];
	$json['workplate']['colsp'] = $_POST['tarcolsp'];
	$json['workplate']['tarxdim'] = $_POST['tarxdim'];
	$json['workplate']['tarydim'] = $_POST['tarydim'];

	if ($json['positioningscheme'] == 'imaging'){
	$json['workplate']['tarxposwell'][0] = $_POST['tarxposwell0'];
	$json['workplate']['tarxposwell'][1] = $_POST['tarxposwell1'];
	$json['workplate']['tarxposwell'][2] = $_POST['tarxposwell2'];
	$json['workplate']['tarxposwell'][3] = $_POST['tarxposwell3'];
	$json['workplate']['tarxposwell'][4] = $_POST['tarxposwell4'];
	$json['workplate']['tarxposwell'][5] = $_POST['tarxposwell5'];
	$json['workplate']['tarxposwell'][6] = $_POST['tarxposwell6'];
	$json['workplate']['tarxposwell'][7] = $_POST['tarxposwell7'];
	$json['workplate']['tarxposwell'][8] = $_POST['tarxposwell8'];
	$json['workplate']['tarxposwell'][9] = $_POST['tarxposwell9'];
	$json['workplate']['taryposwell'][0] = $_POST['taryposwell0'];
	$json['workplate']['taryposwell'][1] = $_POST['taryposwell1'];
	$json['workplate']['taryposwell'][2] = $_POST['taryposwell2'];
	$json['workplate']['taryposwell'][3] = $_POST['taryposwell3'];
	$json['workplate']['taryposwell'][4] = $_POST['taryposwell4'];
	$json['workplate']['taryposwell'][5] = $_POST['taryposwell5'];
	$json['workplate']['taryposwell'][6] = $_POST['taryposwell6'];
	$json['workplate']['taryposwell'][7] = $_POST['taryposwell7'];
	$json['workplate']['taryposwell'][8] = $_POST['taryposwell8'];
	$json['workplate']['taryposwell'][9] = $_POST['taryposwell9'];
	} else {
	$json['workplate']['tarxpos'][0] = $_POST['tarxpos0'];
	$json['workplate']['tarxpos'][1] = $_POST['tarxpos1'];
	$json['workplate']['tarxpos'][2] = $_POST['tarxpos2'];
	$json['workplate']['tarxpos'][3] = $_POST['tarxpos3'];
	$json['workplate']['tarxpos'][4] = $_POST['tarxpos4'];
	$json['workplate']['tarxpos'][5] = $_POST['tarxpos5'];
	$json['workplate']['tarxpos'][6] = $_POST['tarxpos6'];
	$json['workplate']['tarxpos'][7] = $_POST['tarxpos7'];
	$json['workplate']['tarxpos'][8] = $_POST['tarxpos8'];
	$json['workplate']['tarxpos'][9] = $_POST['tarxpos9'];
	$json['workplate']['tarypos'][0] = $_POST['tarypos0'];
	$json['workplate']['tarypos'][1] = $_POST['tarypos1'];
	$json['workplate']['tarypos'][2] = $_POST['tarypos2'];
	$json['workplate']['tarypos'][3] = $_POST['tarypos3'];
	$json['workplate']['tarypos'][4] = $_POST['tarypos4'];
	$json['workplate']['tarypos'][5] = $_POST['tarypos5'];
	$json['workplate']['tarypos'][6] = $_POST['tarypos6'];
	$json['workplate']['tarypos'][7] = $_POST['tarypos7'];
	$json['workplate']['tarypos'][8] = $_POST['tarypos8'];
	$json['workplate']['tarypos'][9] = $_POST['tarypos9'];
	}



	if (isset($_POST['1_1'])){$t1_1 = $_POST['1_1'];}
	if (isset($_POST['1_2'])){$t1_2 = $_POST['1_2'];}
	if (isset($_POST['1_3'])){$t1_3 = $_POST['1_3'];}
	if (isset($_POST['1_4'])){$t1_4 = $_POST['1_4'];}
	if (isset($_POST['1_5'])){$t1_5 = $_POST['1_5'];}
	if (isset($_POST['2_1'])){$t2_1 = $_POST['2_1'];}
	if (isset($_POST['2_2'])){$t2_2 = $_POST['2_2'];}
	if (isset($_POST['2_3'])){$t2_3 = $_POST['2_3'];}
	if (isset($_POST['2_4'])){$t2_4 = $_POST['2_4'];}
	if (isset($_POST['2_5'])){$t2_5 = $_POST['2_5'];}


	$json['workplate']['targettype'][0]['leftmargin'] = $_POST['leftmargin0'];
	$json['workplate']['targettype'][1]['leftmargin'] = $_POST['leftmargin1'];
	$json['workplate']['targettype'][0]['topmargin'] = $_POST['topmargin0'];
	$json['workplate']['targettype'][1]['topmargin'] = $_POST['topmargin1'];

	$json['workplate']['targettype'][0]['blockrow'] = $_POST['blockrow0'];
	$json['workplate']['targettype'][0]['blockrowsp'] = $_POST['blockrowsp0'];
	$json['workplate']['targettype'][0]['blockcol'] = $_POST['blockcol0'];
	$json['workplate']['targettype'][0]['blockcolsp'] = $_POST['blockcolsp0'];
	$json['workplate']['targettype'][0]['spotrow'] = $_POST['spotrow0'];
	$json['workplate']['targettype'][0]['spotrowsp'] = $_POST['spotrowsp0'];
	$json['workplate']['targettype'][0]['spotcol'] = $_POST['spotcol0'];
	$json['workplate']['targettype'][0]['spotcolsp'] = $_POST['spotcolsp0'];

	$json['workplate']['targettype'][1]['blockrow'] = $_POST['blockrow1'];
	$json['workplate']['targettype'][1]['blockrowsp'] = $_POST['blockrowsp1'];
	$json['workplate']['targettype'][1]['blockcol'] = $_POST['blockcol1'];
	$json['workplate']['targettype'][1]['blockcolsp'] = $_POST['blockcolsp1'];
	$json['workplate']['targettype'][1]['spotrow'] = $_POST['spotrow1'];
	$json['workplate']['targettype'][1]['spotrowsp'] = $_POST['spotrowsp1'];
	$json['workplate']['targettype'][1]['spotcol'] = $_POST['spotcol1'];
	$json['workplate']['targettype'][1]['spotcolsp'] = $_POST['spotcolsp1'];



	$targettype = array();
	if (isset($t1_1)){$json['workplate']['arraytype'][0] = $t1_1;}else { $json['workplate']['arraytype'][0] = '';}
	if (isset($t1_2)){$json['workplate']['arraytype'][1] = $t1_2;}else { $json['workplate']['arraytype'][1] = '';}
	if (isset($t1_3)){$json['workplate']['arraytype'][2] = $t1_3;}else { $json['workplate']['arraytype'][2] = '';}
	if (isset($t1_4)){$json['workplate']['arraytype'][3] = $t1_4;}else { $json['workplate']['arraytype'][3] = '';}
	if (isset($t1_5)){$json['workplate']['arraytype'][4] = $t1_5;}else { $json['workplate']['arraytype'][4] = '';}
	if (isset($t2_1)){$json['workplate']['arraytype'][5] = $t2_1;}else { $json['workplate']['arraytype'][5] = '';}
	if (isset($t2_2)){$json['workplate']['arraytype'][6] = $t2_2;}else { $json['workplate']['arraytype'][6] = '';}
	if (isset($t2_3)){$json['workplate']['arraytype'][7] = $t2_3;}else { $json['workplate']['arraytype'][7] = '';}
	if (isset($t2_4)){$json['workplate']['arraytype'][8] = $t2_4;}else { $json['workplate']['arraytype'][8] = '';}
	if (isset($t2_5)){$json['workplate']['arraytype'][9] = $t2_5;}else { $json['workplate']['arraytype'][9] = '';}



	}



//Collecting variables
//<input type=hidden name="view" value="I">
//<input type=hidden name="act" value="Source wells">
//<input type=submit name=editlist value="Submit"><br>
if ((isset($_GET['act'])) and ($_GET['act'] == 'Source wells')){
	if ((isset($_GET['editlist'])) and ($_GET['editlist'] == 'Edit')){ 
	   if ($json['sourcewell']['edit'] == 1){
	    $json['sourcewell']['edit'] = 0;
	   }
	  else{
	   $json['sourcewell']['edit'] = 1;
	  }
	}
	if ((isset($_GET['editlist'])) and ($_GET['editlist'] == 'Submit')){ 
	  //echo "<td align=center><input type=input name=sourcewellid".$i." value='".$json['sourcewell']['anot'][$i]."' size=".strlen($json['sourcewell']['anot'][$i])."></td>";
	  for($i=0;$i<count($json['sourcewell']['anot']);$i++){
	    $tanot = 'sourcewellid'.$i;
	    $tcx = 'sourcewellx'.$i;
	    $tcy = 'sourcewelly'.$i;
	    $tcz = 'sourcewellz'.$i;
	    $tclaz = 'sourcewell_laz'.$i;
	    $json['sourcewell']['anot'][$i]= $_GET[$tanot];
	    $json['sourcewell']['x'][$i]= $_GET[$tcx];
	    $json['sourcewell']['y'][$i]= $_GET[$tcy];
	    $json['sourcewell']['z'][$i]= $_GET[$tcz];
	    $json['sourcewell']['laz'][$i]= $_GET[$tclaz];
	  }
	}
	if ((isset($_GET['editlist'])) and ($_GET['editlist'] == 'Add Tube')){ 
	  $sourcewellid = $_GET['sourcewellid'];
	  $sourcewellx = "sourcewellx".count($json['sourcewell']['x']);
	  $sourcewelly = "sourcewelly".count($json['sourcewell']['y']);
	  $sourcewellz = "sourcewellz".count($json['sourcewell']['z']);
	  $json['sourcewell']['anot'][count($json['sourcewell']['x'])] = $_GET['sourcewellid'];
	  $json['sourcewell']['x'][count($json['sourcewell']['x'])] = $_GET[$sourcewellx];
	  $json['sourcewell']['y'][count($json['sourcewell']['y'])] = $_GET[$sourcewelly];
	  $json['sourcewell']['z'][count($json['sourcewell']['z'])] = $_GET[$sourcewellz];
	}



}

//Syringe pump variables
if ((isset($_GET['act'])) and ($_GET['act'] == 'Syringepump')){
	
	if (isset($_GET['connect'])){
		$msg =  'Syringe pump socket ('.$json['servers']['webheadcampi'].') connected<br>';
		$json['syringepump']['connect'] = 1;
		$cmd = 'sudo python /home/richard/tricontsyringepump/tricontent.telnet.socket.py > /dev/null &';
		ssh04caller($cmd,$json);
		logger($logger, $msg,1);
	}
	if (isset($_GET['disconnect'])){
		$msg =  'Syringe pump socket ('.$json['servers']['webheadcampi'].') disconnected<br>';
		$json['syringepump']['connect'] = 0;
		$cmd = 'sudo python syringepump.getpidandkill.py > /dev/null &';
		ssh04caller($cmd,$json);
		logger($logger, $msg,1);
	}

	if (isset($_GET['initialization'])){
	  if ($json['syringepump']['connect'] == 1){
	    $json['syringepump']['trackaspvol'] = "0"; 
	    $cmd = 'I';
  	    $msg = 'Syringe pump initialized<br>';
	    syringesocketclient($cmd,$json);
	    logger($logger, $msg,1);
	  }
	  else {
	   logger($logger, 'Syringe socket is not connected (To start: SYsocket start)<br>',1);
  	  }

	}
	if (isset($_GET['terminate'])){
	   if ($json['syringepump']['connect'] == 1){
		 $cmd = 'T';
		 $msg = 'Terminate syringe pump process<br>';
		 syringesocketclient($cmd,$json);
		 logger($logger, $msg,1);
		}
		else {
		 logger($logger, 'Syringe socket is not connected (To start: SYsocket start)<br>',1);
		}
	   }
	}

	if (isset($_GET['filltubing'])){
	  $json['syringepump']['filltubingcycles'] = $_GET['filltubingcycles'];
	  //$cmd = "sudo python /home/richard/tricontsyringepump/socket.client.py F".$json['syringepump']['filltubingcycles']." > /dev/null &";
	 $json['syringepump']['trackaspvol'] = 0;
 	 if ($json['syringepump']['connect'] == 1){
	  $cmd = 'F'.$json['syringepump']['filltubingcycles'];
	  $msg = 'Syringe filling cycles '.$json['syringepump']['filltubingcycles'].'<br>';
	  //echo $cmd.'<br>';
	  syringesocketclient($cmd,$json);
	  logger($logger, $msg,1);
	 }
	 else {
	  logger($logger, 'Syringe socket is not connected (To start: SYsocket start)<br>',1);
	 }
        }
	//I am going to move this into a separate script
	if (isset($_GET['aspirate'])){
	   $preaspvol =  $json['syringepump']['trackaspvol'];
	   $json['syringepump']['aspirateflo'] = $_GET['aspirateflo'];
	   $json['syringepump']['aspiratevol'] = $_GET['aspiratevol'];
	   $trackaspvol = $preaspvol + $json['syringepump']['aspiratevol'];
	   $json['syringepump']['trackaspvol'] = $trackaspvol; 
	   $volrt = $json['syringepump']['trackaspvol'].'_'.$json['syringepump']['aspirateflo'];
	   $url = 'runner.php?mmmove='.$volrt.'&tcli=aspirate';
	   header('Location: '.$url);
	}
	if (isset($_GET['dispense'])){
	   $preaspvol =  $json['syringepump']['trackaspvol'];
	   $dispensevol = $_GET['dispensevol'];
	   $dispenseflo = $_GET['dispenseflo'];
	   if ($dispensevol > $preaspvol){
		echo '<font color=red>Error: Dispense volume is more then what was aspirated</font><br>';
		echo '<font color=red>There is <?php echo $preaspvol; ?> left</font><br>';
	   }
	   else{
		$preaspvol = $preaspvol - $dispensevol;
	   	$json['syringepump']['trackaspvol'] = $preaspvol;
	   	$volrt = $dispensevol.'_'.$dispenseflo; 
	   	$url = 'runner.php?mmmove='.$volrt.'&tcli=dispense';
	   	header('Location: '.$url);
	   }
	}
/*
<input type=submit name=aspirate value="Aspiration">
Volume: <input type=text name=aspiratevol value="<?php echo $json['syringepump']['aspiratevol']; ?>" size=5>&micro;l
Flow rate: <input type=text name=aspirateflo value="<?php echo $json['syringepump']['aspirateflo']; ?>" size=5>&micro;l/second
*/

if ((isset($_POST['act'])) and ($_POST['act'] == 'Delstrobimages')){
 $view = $_POST['view'];
 $json['view'] = $view;
 $pdir = $json['strobimages']['path'];
 if (strlen($pdir) > 2){
  $pdir = $pdir.'/';
 }
 else {
  $pdir = '';
 }
 $dir  = './'.$pdir;

//<input type=submit name='subval' value='Delete Selected Images'>
//<input type=submit name='subval' value='Delete All Images'>
 if ($_POST['subval'] == "Delete Selected Images"){
 $strobjsonprocessing = json_decode(file_get_contents('./strobdatasetprocessing'), true);
 for ($i=0;$i<count($_POST['imgary']);$i++){
  if (file_exists($dir.$_POST['imgary'][$i])){
   exec('sudo rm '.$dir.$_POST['imgary'][$i].' &');
   $imagefile = $_POST['imgary'][$i];
   $key = array_search($imagefile, $strobjsonprocessing['key']);
   unset($strobjsonprocessing['key'][$key]);
   unset($strobjsonprocessing['dataset'][$key]);
  }
 }
 file_put_contents('./strobdatasetprocessing', json_encode($strobjsonprocessing));
 }
 if ($_POST['subval'] == "Delete All Images"){
  $handle = opendir('strobimages');
  while (false !== ($entry = readdir($handle))) {
        //echo "$entry\n";
	if (strlen($entry) > 3){
	 if (array_search($entry, $strobjsonprocessing['key']) > -1){
   	  $key = array_search($entry, $strobjsonprocessing['key']);
   	  unset($strobjsonprocessing['key'][$key]);
   	  unset($strobjsonprocessing['dataset'][$key]);
   	  exec('sudo rm '.$dir.$entry.' &');
	 }
        }
    }
   closedir($handle);
 }
 echo 'Strob images were deleted<br>';
}




if ((isset($_POST['act'])) and ($_POST['act'] == 'Delimages')){
 $view = $_POST['view'];
 $json['view'] = $view;

 $pdir = $json['gcodefile']['path'];
 if (strlen($pdir) > 2){
  $pdir = $pdir.'/';
 }
 else {
  $pdir = '';
 }
 $dir  = './'.$pdir;


//<input type=submit name='subval' value='Delete Selected Images'>
//<input type=submit name='subval' value='Delete All Images'>
 if ($_POST['subval'] == "Delete Selected Images"){
 for ($i=0;$i<count($_POST['imgary']);$i++){
  if (file_exists($dir.$_POST['imgary'][$i])){
   exec('sudo rm '.$dir.$_POST['imgary'][$i].' &');
   //echo 'File '.$dir.$_POST['imgary'][$i].' has been deleted';
  }
 }
 }
 if ($_POST['subval'] == "Delete All Images"){
   exec('sudo rm '.$dir.'*.jpg &');
 }
 echo 'Images were deleted<br>';
}


//Uploading gcode 


$disp = 0;
if ((isset($_POST['ract'])) and ($_POST['ract'])){
  $ract = $_POST['ract'];
  //echo '<br>Test: '.$ract.'<br>';
  if ($ract == 'UPLOAD GCODE FILE'){
   //echo '<br>View: '.$_POST['view'].'<br>';
   $json['view'] = $_POST['view'];	
   //echo "Upload: " . $_FILES["gcodefile"]["name"] . "<br>";
  
   //I need to discriminate between data uploaded and data pasted in the input box so to do this I set a boolean type flag: tranferslistfile

  $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
  file_put_contents('taskjob3', json_encode($jsontasker3));
 
  if ($_FILES["gcodefile"]["error"] > 0) {
 	echo "Error: Problem uploading file (" . $_FILES["gcodefile"]["error"] . ")<br>";
  }
  else {
 	//Sanity checks to make sure the script is working
  	//echo "Upload: " . $_FILES["gcodefile"]["name"] . "<br>";
  	//echo "Type: " . $_FILES["gcodefile"]["type"] . "<br>";
  	//echo "Size: " . ($_FILES["gcodefile"]["size"] / 1024) . " kB<br>";
  	//echo "Stored in: " . $_FILES["gcodefile"]["tmp_name"];

  	$json['gcodefile']['filename'] = $_FILES["gcodefile"]["name"];

	$gcodefiledat = file($_FILES["gcodefile"]["tmp_name"]);
	//echo '<br>filenamedat: '.$gcodefiledat;
	$json = readgcodefile($gcodefiledat,$json);

  	if (array_search($scriptname,$jsontasker3['filename'])){
    	 $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
   	 $track = array_search($scriptname,$jsontasker3['filename']);
   	 $jsontasker3['track'] = $track;
  	 file_put_contents('taskjob3', json_encode($jsontasker3));
   	 echo "<br>Error: this file already exists<br>";
  	}
  	else {
    	 $jsontasker3 = json_decode(file_get_contents('taskjob3'), true);
   	 array_push($jsontasker3['filename'],$json['gcodefile']['filename']);
   	 array_push($jsontasker3['data'],$json['gcodefile']['lines']);
         array_search($scriptname,$jsontasker3['filename']);
   	 $jsontasker3['track'] = array_search($scriptname,$jsontasker3['filename']);
  	 file_put_contents('taskjob3', json_encode($jsontasker3));
  	}
  }
  $disp = 1;
 }

}




if ((isset($_GET['file'])) and ($_GET['file'])){
 $file = $_GET['file'];
 //45.57_88.11_0.00.jpg	
 $pattern = '/(.*)_(.*)_.*jpg/';
 preg_match($pattern, $file, $matches, PREG_OFFSET_CAPTURE);
 $rampx =  $matches[1][0];
 $rampy =  $matches[2][0];

 $json['positions']['file'] = $file;
 //$json['positions']['file'] = preg_replace('\?.*','',$file);
 $json['positions']['rampx'] = $rampx;
 $json['positions']['rampy'] = $rampy;
}  //passing on configuration file

if ((isset($_GET['view'])) and ($_GET['view'])){
 $view = $_GET['view'];
 $json['view'] = $view;
}


$file = $json['positions']['file'];
$file = $file.'?'.filemtime($file);
$filen = $file;
$filei = $file;



 if((isset($_GET['zact'])) and ($_GET['zact'] == 'ztrav')){
  if ((isset($_GET['ztravesub'])) and ($_GET['ztravesub'] == "Edit")){
		$json['ztravedit'] = 1;
 }
  if ((isset($_GET['ztravesub'])) and ($_GET['ztravesub'] == "Submit")){
		$json['ztravedit'] = 0;
		$json['ztrav'] = $_GET['ztrav'];
 }
}	





if ((isset($_GET['coords'])) and ($_GET['coords'])){
   $coords = $_GET['coords'];
   $cds = preg_split("/,/", $coords);
   $pbx = $cds[0];
   $pby = $cds[1];
   $ex = $cds[2];
   $ey = $cds[3];
   $bx = $pbx + $ex/2;
   $by = $pby + $ey/2;
   $json['grid']['ex'] = $ex;
   $json['grid']['ey'] = $ey;
   $json['grid']['pbx'] = $pbx;
   $json['grid']['pby'] = $pby;
   $json['grid']['bx'] = $bx;
   $json['grid']['by'] = $by;

}  //passing on configuration coords
if ((isset($_GET['rampy'])) and ($_GET['rampy'])){
	$rampy = $_GET['rampy'];
	$rampx = $_GET['rampx'];
 	$json['positions']['rampx'] = $rampx;
	$json['positions']['rampy'] = $rampy;
}  //passing on configuration rampx
if ((isset($_GET['act'])) and ($_GET['act'])){
//------Wash and Dry parameters-----
//go to work on this tomorrow
	$act = $_GET['act'];
	if ($act == 'wash'){
	  if ($json["stop"] == "0"){
	   $json['washing']['washtime'] = $_GET['washtime'];
	   $json['washing']['touchdrytime'] = $_GET['drytime'];
	   if ($_GET['dry'] == "dry"){
	     $url = 'runner.php?mmmove=&tcli=washdry';
	   }
	   else {
	     $url = 'runner.php?mmmove=&tcli=wash';
	   }
	   header('Location: '.$url);
	 }
	}
	if ($act == 'dry'){
	 $json['washing']['touchdrytime'] = $_GET['drytime'];
	 $url = 'runner.php?mmmove=&tcli=dry';
	 header('Location: '.$url);
	}
	//echo " <input type=submit name=washingedit value=Edit>";
	if(($act=="washedit") and (isset($_GET['washingedit'])) and ($_GET['washingedit'] == "Edit")){
	 if ($json['washing']['edit'] == "0"){
	  $json['washing']['edit'] = 1;
	 }
	 else {
	  $json['washing']['edit'] = 0;
	 } 
	}
	if(($act=="washedit") and (isset($_GET['washingedit'])) and ($_GET['washingedit'] == "Submit")){
	 $json['washing']['washx'] = $_GET['washingeditx'];
	 $json['washing']['washy'] = $_GET['washingedity'];
	 $json['washing']['washz'] = $_GET['washingeditz'];
	 $json['washing']['washlaz'] = $_GET['washingeditlaz'];
	 $json['washing']['syringepumpflorate'] = $_GET['washingeditsyringepumpflorate'];
	 $json['washing']['edit'] = 0;
	}
	if ($act == 'pumpon'){
	 $pumpsub = $_GET['pumpsub']; 
	 if ($pumpsub == 'Drain ON'){
		$json['washing']['drainpumpon'] = 1;
		$json = pressuresocketclient('DRYON',$json);
	 	logger($logger, 'Drain on<br>',1);
	 }
	 else if ($pumpsub == 'Drain OFF'){
		$json['washing']['drainpumpon'] = 0;
		$json = pressuresocketclient('DRYOFF',$json);
	 	logger($logger, 'Drain off<br>',1);
	 }
	 else if ($pumpsub == 'Wash ON'){
		$json['washing']['washpumpon'] = 1;
		$json = pressuresocketclient('WASHON',$json);
	 	logger($logger, 'Wash on<br>',1);
	 }
	 else if ($pumpsub == 'Wash OFF'){
		$json['washing']['washpumpon'] = 0;
		$json = pressuresocketclient('WASHOFF',$json);
	 	logger($logger, 'Wash off<br>',1);
	 }
	}
/*
<input type=hidden name="act" value="pumpon">
<input type=hidden name="view" value="F">
<b>Pump on time: </b><input type=text name="draintime" value="<?php echo $json['washing']['pumptime']; ?>" size=6>
<input type=submit name=pumpsub value="Drain">
<input type=submit name=pumpsub value="Wash">
*/	

	if (($act == 'drypadsettings') and (isset($_GET['washsub']))){
	  if ($_GET['washsub'] == 'Edit'){
	   $json['washing']['tdryedit'] = "1";
	  }
	  if ($_GET['washsub'] == 'Adjust TouchDry Settings'){
	  $json['washing']['tdryedit'] = "0";
	  $json['washing']['tdryxpos'] = $_GET['tdryxpos'];
	  $json['washing']['tdryxdim'] = $_GET['tdryxdim'];
	  $json['washing']['tdryypos'] = $_GET['tdryypos'];
	  $json['washing']['tdryydim'] = $_GET['tdryydim'];
	  $json['washing']['tdryzpos'] = $_GET['tdryzpos'];
	  $json['washing']['tdrylazpos'] = $_GET['tdrylazpos'];
	  $tdrypositions = array();
   	  for($y=0;$y<$json['washing']['tdryydim'];$y++){
	   $pypos = ($json['washing']['tdryypos'] + $y);
   	   for($x=0;$x<$json['washing']['tdryxdim'];$x++){
	    $newinput = array('x'=>($json['washing']['tdryxpos']+$x),'y'=>$pypos);
	    array_push($tdrypositions, $newinput);
	   }
	  }
	 $json['washing']['tdrypositions'] = ($tdrypositions);
	 }
	if ($_GET['washsub'] == 'Reset'){
	  $json['washing']['tdrycurrpos'] = 1;
	}
	}

      if (($act == 'wastesettings') and (isset($_GET['washsub']))){
	  if ($_GET['washsub'] == 'Edit'){
	   $json['washing']['wasteedit'] = "1";
	  }
	  if ($_GET['washsub'] == 'Waste Position Settings'){
	  $json['washing']['wasteedit'] = "0";
	  $json['washing']['wastex'] = $_GET['wastex'];
	  $json['washing']['wastey'] = $_GET['wastey'];
	  $json['washing']['wastez'] = $_GET['wastez'];
	  $json['washing']['wastelazpos'] = $_GET['wastelazpos'];

	  }
	}



//------Piezo control -----
	if ($act == 'piezocontrol'){
	 //if (isset($_GET['pzcaliact'])){

	//<input type=submit name=pcaliact value="Query Pressure Compensation">
	 if (isset($_GET['pcaliact'])){ 
		$json['pressure']['set'] = $_GET['setpreslev'];
		$json = pressuresocketclient('PRESSURE'.$json['pressure']['set'],$json);
	 	logger($logger, 'Bottle Liquid Set: '.$json['pressure']['set'].' Level: '.$json['pressure']['read'].'<br>',1);
	 }



	  if ((isset($_GET['report'])) and ($_GET['report'] == 'Report')){
   	   	$json = waveformsocketclient('REPORT',$json);
		echo $json['wavecontroller']['report'].'<br>';
		echo 'Actual Frequency is: '.$json['wavecontroller']['freq'].'<br>';
	 	logger($logger, 'Wave Generator: '.$json['wavecontroller']['report'].'<br>',1);
	  }
	  if (isset($_GET['setdrops'])) {
	   $json['wavecontroller']['drops'] = $_GET['setdropnumlev'];
   	   $json = waveformsocketclient('D'.$json['wavecontroller']['drops'],$json);
	  }
	  if (isset($_GET['changevolt'])) {
	   $json['wavecontroller']['volts'] = $_GET['piezovolt'];
   	   $json = waveformsocketclient('V'.$json['wavecontroller']['volt'],$json);
	  }
	  if (isset($_GET['changepulse'])) {
	   $json['wavecontroller']['pulse'] = $_GET['piezopulse'];
   	   $json = waveformsocketclient('P'.$json['wavecontroller']['pulse'],$json);
	  }
	  if (isset($_GET['changefreq'])) {
	   $json['wavecontroller']['freq'] = $_GET['piezofreq'];
   	   $json = waveformsocketclient('F'.($json['wavecontroller']['freq']/10),$json);
	  }
	  if ((isset($_GET['pzcaliact'])) and ($_GET['pzcaliact'] == 'Piezo')){
	   echo 'Dispense '.$json['wavecontroller']['drops']. ' drops<br>';
   	   $json = waveformsocketclient('FIRE',$json);
	  }
	 //<input type=submit name=pzcaliact value="Trigger On/Off">
	  if ((isset($_GET['pzcaliact'])) and ($_GET['pzcaliact'] == 'Trigger On/Off')){
	    if ($json['wavecontroller']['trigger'] == '1'){
	 	$json['wavecontroller']['trigger'] = '0';
   	   	$json = waveformsocketclient('TRIGOFF',$json);
	     } 
	    else{ 
	 	$json['wavecontroller']['trigger'] = '1';
   	   	$json = waveformsocketclient('TRIGON',$json);
	     } 
	  }
}

//------Stroboscope -----
	if ($act == 'Stroboscope'){
	if (isset($_GET['strobeditpos'])){
	  $json['strobparameters']['edit'] = 1;
	  closejson($json);
	}
	if (isset($_GET['strobpos'])){
	  $json['strobparameters']['x'] = $_GET['strobxpos'];
	  $json['strobparameters']['y'] = $_GET['strobypos'];
	  $json['strobparameters']['z'] = $_GET['strobzpos'];
	  $json['strobparameters']['edit'] = 0;
          closejson($json);
	}


	//<input type=submit name=strobconnect value="STROB CONNECT">
	  if ((isset($_GET['strobconnect'])) and ($_GET['strobconnect'] == 'STPR On/Off') and ($json['strobconnect'] == 0)){

                if ($json['wavesocketpid'] > 0) {
                  $msg = 'Problem waveform generator  socket already connected ';
                  logger($logger, $msg.': pid - '.$json['wavesocketpid'].' Type "wavesocket stop"<br>',1);
                }
                else {
                 $cmd = 'sudo php control_wavesocket.php start';
                 $json['wavesocketpid'] = sshcontrolcaller($cmd,$json['servers']['piezostrobpi'],'start');
                 sleep(1);
                 if ($json['wavesocketpid'] > 0){
                    $json['strobconnect'] = 1;
                    $msg =  'Waveform generator socket ('.$json['servers']['piezostrobpi'].') connected ';
                    logger($logger, $msg.': pid - '.$json['wavesocketpid'].'<br>',1);
                 }
                 else {
                  logger($logger, 'Problem waveform generator socket not connected<br>',1);
                 }
                }

	     if ($json['pressureconnect'] == 0){
		$msg =  'Pressure, wash, dry, headcam led and linear actuator socket ('.$json['servers']['wavepi'].') connected<br>';
		$json['pressureconnect'] = 1;
		$cmd= 'sudo python pressure.telnet.socket.py  > /dev/null &';
		ssh05caller($cmd,$json);
		sleep(2);
		logger($logger, $msg,1);
	     }
	}

	  else if ((isset($_GET['strobconnect'])) and ($_GET['strobconnect'] == 'STPR On/Off') and ($json['strobconnect']==1)){
             $msg =  'Waveform generator socket ('.$json['servers']['piezostrobpi'].') disconnected<br>';
             $cmd= 'sudo kill '.$json['wavesocketpid'];
             $json['strobconnect'] = 0;
             sshcontrolcaller($cmd,$json['servers']['piezostrobpi'],$json['wavesocketpid']);
             sleep(1);
             logger($logger, $msg.' pid - '.$json['wavesocketpid'].' is killed<br>',1);
             $json['wavesocketpid'] = 0;
	}


	  if ((isset($_GET['caliact'])) and ($_GET['caliact'] == 'Stroboscope Camera On/Off')){

	    if ($json['strobcamon'] == 1){
	     $msg =  'STROB CAMERA ('.$json['servers']['piezostrobpi'].') off<br>';
	     $json['strobcamon'] = 0;
	     $cmd = "sudo python cam.getpidandkill.py  > /dev/null & ";
 	     ssh02caller($cmd,$json);
	     logger($logger, $msg,1);
	    }
	    else {
	     $msg =  'STROB CAMERA ('.$json['servers']['piezostrobpi'].') on<br>';
	     $json['strobcamon'] = 1;
	     $cmd = 'sudo /home/richard/mjpg-streamer/mjpg-streamer/mjpg_streamer -i "/home/richard/mjpg-streamer/mjpg-streamer/input_uvc.so -n -r 640x480 -f 10" -o "/home/richard/mjpg-streamer/mjpg-streamer/output_http.so -p 8080 -w /home/richard/mjpg-streamer/mjpg-streamer/www" > /dev/null &';
	     ssh02caller($cmd,$json);
	     logger($logger, $msg,1);
	    }
	  }

	  if (isset($_GET['caliact']) and ($_GET['caliact'] == 'Snap')){
	  $timestamp =  $_SERVER['REQUEST_TIME'];
	  //http://99.117.118.141:10000/


	  //STROBOSCOPE PART NEEDS TO BE CHANGED 
	  //"strobimages":{"path":"strobimages"}
	  if (strlen($json['strobimages']['path']) < 1) {
	   $prepath = "";
	  }
	  else {
	   $prepath = $json['strobimages']['path']."/";
	  }


	  if ($json['local'] == 0){
	   $cmd = "sudo wget http://".$json['url'].":10000/?action=snapshot -O ".$prepath.$timestamp."_V".$json['wavecontroller']['volts']."_P".$json['wavecontroller']['pulse']."_LD".$json['wavecontroller']['leddelay'].".jpg";
	  }
	  else {
	   $cmd = "sudo wget -P ".$prepath." http://".$json['servers']['piezostrobpi'].":8080/?action=snapshot -O ".$prepath.$timestamp."_V".$json['wavecontroller']['volts']."_P".$json['wavecontroller']['pulse']."_LD".$json['wavecontroller']['leddelay'].".jpg";
	   //echo $cmd;
	  }
	  exec($cmd);
	  $msg = "Strobcam photo saved: ".$timestamp."_V".$json['wavecontroller']['volts']."_P".$json['wavecontroller']['pulse'].".jpg<br>";
	  logger($logger, $msg,1);

	  //echo $cmd.'<br>';
	 }
	  if (isset($_GET['strobled']) and ($_GET['strobled'] == 'STROB ON')){
	   echo 'Stroboscope is on<br>';
           closejson($json);
	   gearmanstrobon();
  	   $json = openjson();
	  }

	  if (isset($_GET['strobled']) and ($_GET['strobled'] == 'STROB OFF')){
	   echo 'Stroboscope is off<br>';
           closejson($json);
	   gearmanstroboff();
  	   $json = openjson();
          }	
	  if (isset($_GET['changedelaytime'])){
	   if ($json['strobconnect'] == 1){
	    $json['wavecontroller']['leddelay'] = $_GET['delaytime'];
	    ssh02caller('sudo python socket.client.py LEDDELAY'.$json['wavecontroller']['leddelay'],$json);
	    sleep(1);
   	    $json = waveformsocketclient('REPORT',$json);
   	    logger($logger, 'Wave Generator: SetLEDDELAY '.$json['wavecontroller']['leddelay'].' '.$json['wavecontroller']['report'].'<br>',1);
            closejson($json);
  	    $json = openjson();
	   }
	   else {
	     logger($logger, 'Wave Generator socket not connected<br>',1);
	    }
	  }
	}



//------PositionDriver -----
	if ($act == 'PositionDriver'){

	  if ((isset($_GET['caliact'])) and ($_GET['caliact'] == 'Camera On/Off')){

	    if ($json['headcamon'] == 1){
		$msg =  'HEAD CAMERA off<br>';
	        $json['headcamon'] = 0;
	        $cmd = "sudo python getpidandkill.py & > /dev/null &";
		ssh04caller($cmd);
		logger($logger, $msg,1);
	    }

	    else {
		$msg =  'HEAD CAMERA on<br>';
	        $json['headcamon'] = 1;
		$cmd = 'sudo /home/richard/mjpg-streamer/mjpg-streamer/mjpg_streamer -i "/home/richard/mjpg-streamer/mjpg-streamer/input_uvc.so -n -r 320x240 -f 10" -o "/home/richard/mjpg-streamer/mjpg-streamer/output_http.so -p 8080 -w /home/richard/mjpg-streamer/mjpg-streamer/www" > /dev/null &';
		ssh04caller($cmd);
		logger($logger, $msg,1);
	   }
	  }
	  if ((isset($_GET['caliact'])) and ($_GET['caliact'] == 'Snap')){

 	    if (strlen($json['gcodefile']['path']) < 1) {
	     $prepath = "";
	    }
	    else {
	     $prepath = $json['gcodefile']['path']."/";
	    }


	  $rampx = $json['trackxyz']['x'];
	  $rampy = $json['trackxyz']['y'];
	  $rampz = $json['trackxyz']['z'];
	  $timestamp =  $_SERVER['REQUEST_TIME'];
	  $json['positions']['file'] = $rampx."_".$rampy."_".$rampz.".jpg"; //where you save the image file
	  $json['positions']['rampx'] = $rampx;
	  $json['positions']['rampy'] = $rampy;
	  //echo 'snapped '.$json['positions']['file'].'<br>';
	  if ($json['local'] == "0"){
	   $cmd = "sudo wget http://".$json['url'].":8000/?action=snapshot -O ".$prepath.$rampx."_".$rampy."_".$rampz.".jpg";
	   //echo $cmd;
	   exec($cmd);
	   $msg = $rampx."_".$rampy."_".$rampz.".jpg taken<br>";
	   logger($logger, $msg,1);
	  }
	  else {
	   $cmd = "sudo wget http://".$json['servers']['webheadcampi'].":8080/?action=snapshot -O ".$prepath.$rampx."_".$rampy."_".$rampz.".jpg";
	   //echo $cmd;
	   exec($cmd);
	  }
	  }


//------Connect to RAMPS	
	if ((isset($_GET['ract'])) and ($_GET['ract'])){
		$ract = $_GET['ract'];	


		if ($ract == 'CONNECT')	{


			$json['ramp']['rampon'] = 1;
			sleep(1);
			$msg =  'Connecting ...Note this may take 30 seconds. <br>';
			logger($logger, $msg,1);


			$msg = 'Session  egin please make sure the fan is on. <br>';
			logger($logger, $msg,1);


			exec("sudo cp imgdata et backup.imgdataset > /dev/null &");
			sleep(5);


			$msg =  'Power relays socket ('.$json['servers']['marlin8pi'].') connected<br>';
			$json['powerrelaysocket']['on'] = 1;
			$cmd= 'sudo python powerrelay_arduinosocket.py  > /dev/null &';
			ssh01caller($cmd,$json);
			sleep(2);
			logger($logger, $msg,1);
			sleep(3);

			$json["stop"] ="0";
		  	$result = powerrelaysocketclient('poweron',$json);
			$msg =  'Turning system power on<br>';
			logger($logger, $msg,1);
		

	     		if ($json['pressureconnect'] == 0){
			  $msg =  'Pressure, wash, dry, headcam led and linear actuator socket ('.$json['servers']['wavepi'].') connected<br>';
			  $json['pressureconnect'] = 1;
			  $cmd= 'sudo python pressure.telnet.socket.py  > /dev/null &';
			  ssh05caller($cmd,$json);
			  sleep(2);
			  logger($logger, $msg,1);
	     	 	}

			$msg = 'Turning fan on..<br>';
			logger($logger, $msg,1);
                        $msg = 'Fan on<br>';
                        $json = fan('M106',$json);
                        logger($logger, $msg,1);


			$json = reportpos($json);
                        logger($logger, $json['smoothiemessage'].'<br>',1);
			$msg =  'System ready ....<br>';
			logger($logger, $msg,1);

			
		}
		if ($ract == 'DISCONNECT')	{

			$json['ramp']['rampon'] = 0;

			$msg = 'Shutting down ... <br>';
			logger($logger, $msg,1);
			sleep(2);

			$msg = 'Fan off<br>';
                        $json = fan('M107',$json);
                        logger($logger, $msg,1);

                        $json["stop"] ="1";
                        $result = relaylinearactuator('poweroff',$json);
                        $msg =  'Turning system power off<br>';
                        logger($logger, $msg,1);


			$msg =  'Power relays and linear actuator socket ('.$json['servers']['marlin8pi'].') disconnected<br>';
			$json['relaylinearactuator']['on'] = 0;
			$cmd= 'sudo python getpidandkill.py  > /dev/null &';
			ssh01caller($cmd,$json);
			exec($cmd);
			logger($logger, $msg,1);

			
			if ($json['strobcamon'] == 1){
                	 $msg =  'STROB CAMERA ('.$json['servers']['piezostrobpi'].') off<br>';
                	 $json['strobcamon'] = 0;
                	 $cmd = "sudo python cam.getpidandkill.py  > /dev/null & ";
                	 ssh02caller($cmd,$json);
                	 logger($logger, $msg,1);
			}
			if ($json['headcamon'] == 1){
                	 $msg =  'HEAD CAMERA ('.$json['servers']['webheadcampi'].') off<br>';
                	 $json['headcamon'] = 0;
                	 $cmd = "sudo python getpidandkill.py & > /dev/null &";
                	 ssh04caller($cmd,$json);
                	 logger($logger, $msg,1);
			}


			$json['pressure']['on'] = 0;
			exec("sudo python /home/pi/getpidandkill.py  > /dev/null &");
			sleep(2);
			$msg =  'Wash Dry Pumps, Pressure Level and LED controller Pi ('.$json['servers']['wavepi'].') Connected ...<br>';
			logger($logger, $msg,1);
			sleep(2);
		

			$msg = 'Session Terminated ... <br>';
			logger($logger, $msg,1);
			$json['ramp']['rampon'] = 0;

		}
		if ($ract == 'STOP')	{

                        $json["stop"] ="1";
                        $result = relaylinearactuator('poweroff',$json);
                        $msg =  'Turning system power off<br>';
                        logger($logger, $msg,1);

		}
		if ($ract == 'GO')	{

                        $json["stop"] ="0";
                        $result = relaylinearactuator('poweron',$json);
                        $msg =  'Turning system power on<br>';
                        logger($logger, $msg,1);

		}

		if ($ract == 'HOMEX'){

			if ($json["stop"] == "0"){
			  $json = homephp('G28 X0',$json);
			  $json = reportpos($json);
			  echo $json['smoothiemessage'].'<br>';
 	 	 	  logger($logger, 'Homing X axis '.$json['smoothiemessage'].'<br>',1);
		  	  echo 'X homed<br>';
			}
			else {
			 $msg = 'System halted, you need to press the "GO" button!<br>';
 	 		 logger($logger, $msg.'<br>',1);
			}
		}
		if ($ract == 'HOMEY')	{

			if ($json["stop"] == "0"){
			 $json = homephp('G28 Y0',$json);
			 $json = reportpos($json);
			 echo $json['smoothiemessage'].'<br>';
 	 		 logger($logger, 'Homing Y axis '.$json['smoothiemessage'].'<br>',1);
		 	 echo 'Y homed<br>';
			}
			else {
			 $msg = 'System halted, you need to press the "GO" button!<br>';
 	 		 logger($logger, $msg.'<br>',1);
			}
		}
		if ($ract == 'HOMEZ')	{
			if ($json["stop"] == "0"){
				$json = homephp('G28 Z0',$json);
				$json = reportpos($json);
				echo $json['smoothiemessage'].'<br>';
				logger($logger, 'Homing Z axis '.$json['smoothiemessage'].'<br>',1);
				echo 'Z homed<br>';
			}
			else {
				$msg = 'System halted, you need to press the "GO" button!<br>';
				logger($logger, $msg.'<br>',1);
			}
		}

		if ($ract == 'Report Position'){

			$json = reportpos($json);
			logger($logger, $json['smoothiemessage'].'<br>',1);
			$json = reportp1lacposition($json,$logger);

		}
		if ($ract == 'SUBMIT GCODE'){
			if ($json["stop"] == "0"){
				//here I need to compensate for hysteresis which I am seeing in the yaxis
				$gcodecmd = $_GET['gcodecmd'];	
				if (preg_match('/^G1/', $gcodecmd)){
					$json = pymove($gcodecmd,$json,$logger);
					$json = reportpos($json);
				}
				else {
					$result = socketreport($gcodecmd);
					echo $result.'<br>';
				}
			}
			else {
				//echo 'System halted, you need to press the "GO" button<br>';
				logger($logger, $report.'<br>',1);
			}
		}
	}
	}

//------Gridding Spotfinding -----
if ($act == 'Regrid'){
	//$view = $_GET['view'];
	$json['view'] = 'D';
	if ($_GET['source']){
		$source = $_GET['source'];
		if ($source == 'processbasic'){
			//echo "testing ".$view."<br>";
 			$pdir = $json['gcodefile']['path'];
			 if (strlen($pdir) > 2){
			  $pdir = $pdir.'/';
			 }
			 else {
			  $pdir = '';
			 }
			 $dir  = './'.$pdir;
			$file = $json['positions']['file'];

			$dim = 30;
			$loadcoords = 1;
			$pfile = preg_replace('/\?.*/','',$file);
			if ($_GET['subval'] == 'Find Features'){
			  $loadcoordsfindfeatures = 1;
			  $pfile = $pdir.$file;
			}
			else {
			  $loadcoordsfindfeatures = 0;
			}
			//echo 'loadcoords: '.$loadcoords.'<br>';
			$cmd =  'sudo python wellmap.py '.$json['grid']['pbx'].' '.$json['grid']['pby'].' '.$json['grid']['ex'].' '.$json['grid']['ey'].' '.$pfile.' '.$dim.' '.$json['grid']['xnum'].' '.$json['grid']['ynum'].' '.$json['grid']['spacex'].' '.$json['grid']['spacey'];
			//echo $cmd;
			echo "<br>";
			$result =  exec($cmd, $output);
			//echo $result."<br>";
			$res = preg_replace('/\[|\]/', '', $result);
			$stack = preg_split('/,/', $res);
			$cntr = ($json['grid']['xnum']) * ($json['grid']['ynum']);
			$coordsdat = array();
			$xcoords = array();
			$ycoords = array();

 			$pfilery = preg_split('/\//', $pfile);
			$json['imgprocessingtracker']['directory'] = $pfilery[1];
			$json['imgprocessingtracker']['filename'] = $pfilery[2];
		
			echo "<font size=1>";
			echo "Directory: ".$json['imgprocessingtracker']['directory'].'<br>';
			echo "Filename: ".$json['imgprocessingtracker']['filename'].'<br>';
			for ($i = 0; $i < $cntr; $i++){
				//echo $stack[$i]."<br>";
				$coordsdat[$i] = coordfunction($stack[$i]);
				$pos = poscalc($coordsdat[$i]['cx'],$coordsdat[$i]['cy'],$rampx,$rampy,$json);
				$ncx = $pos[0];	
				$ncy = $pos[1];	

				$xcoords[$i] = $ncx;
				$ycoords[$i] = $ncy;
				//imageprocessing here richard
				//echo "CX: ".$coordsdat[$i]['cx']." -- CY: ".$coordsdat[$i]['cy']." -- PX: ".$ncx." -- PY:".$ncy." -- Diam ".($coordsdat[$i]['sptdia']*20)." -- Sig/Noise: ".$coordsdat[$i]['sn'].'<br>';
	//"imgprocessingtracker":{"diameter":220,"CX":"195.5","CY":"196.5","PX":[20.26832038835],"PY":[17.127436893204],"signaltonoise":"3.56101290371"}
				$json['imageprocessing']['px'][$i] = $ncx;
				$json['imageprocessing']['py'][$i] = $ncy;

				//"\/imgsave3\/96_44.5_70.jpg";
				$json['imgprocessingtracker']['CX'] = $coordsdat[$i]['cx'];
				$json['imgprocessingtracker']['CY'] = $coordsdat[$i]['cy'];
				$json['imgprocessingtracker']['PX'][$i] = $json['imageprocessing']['px'][0];
				$json['imgprocessingtracker']['PY'][$i] = $json['imageprocessing']['py'][0]; 
				$json['imgprocessingtracker']['signaltonoise'] = $coordsdat[$i]['sn'];
				$json['imgprocessingtracker']['diameter'] = ($coordsdat[$i]['sptdia']*(1000 / $json['grid']['spacex']));
				if ($i==0){
				echo '<font color=red>Reference </font>';
				}
				echo 'PX '.round($json['imageprocessing']['px'][$i],2).' PY '.round($json['imageprocessing']['py'][$i],2);
				echo ' CX '.$coordsdat[$i]['cx'].' CY '.$coordsdat[$i]['cy'].'<br>';
				echo 'Diameter '.round(($coordsdat[$i]['sptdia']*(1000 / $json['grid']['ex'])),2);
				echo ' Signal to noise '.round($coordsdat[$i]['sn'],2).'<br>';

			}
			echo "</font>";
		}
		if ($source == 'resizegrid'){
			$loadcoords = 0;
			$homingxafterrow = $_GET['homingxafterrow'];
			if (isset($_GET['homingxafterrow'])) {
			    $json['homingxafterrow'] = 1;
			}
			else {
			    $json['homingxafterrow'] = 0;
			}
			$xnum = $_GET['xnum'];
			$ynum = $_GET['ynum'];
			$refx = $_GET['refxnum'];
			$refy = $_GET['refynum'];
			$spacex = $_GET['spacex'];
			$spacey = $_GET['spacey'];
			$pbx = $_GET['pbx'];
			$ex = $_GET['ex'];
			$pby = $_GET['pby'];
			$ey = $_GET['ey'];
			$bx = $pbx + $ex/2;
			$by = $pby + $ey/2;
			$json['grid']['bx'] = $bx;
			$json['grid']['by'] = $by;
			$json['grid']['ex'] = $ex;
			$json['grid']['ey'] = $ey;
			$json['grid']['pbx'] = $pbx;
			$json['grid']['pby'] = $pby;
			$json['grid']['xnum'] = $xnum;
			$json['grid']['ynum'] = $ynum;
			$json['grid']['refx'] = $refx;
			$json['grid']['refy'] = $refy;
			$json['grid']['spacex'] = $spacex;
			$json['grid']['spacey'] = $spacey;
			$json['camera']['offsetx'] = $_GET['offsetx'];
			$json['camera']['offsety'] = $_GET['offsety'];
			//<b>Imaging positions </b><input type=radio name=positioningmode value=imaging checked><br>
			//<b>Spotting positions </b><input type=radio name=positioningmode value=spotting>
			$json['positioningmode'] = $_GET['positioningmode'];
			$json['xyfeedrate'] = $_GET['xyfeedrate'];
			$json['zfeedrate'] = $_GET['zfeedrate'];
			$json['ztrav'] = $_GET['ztrav'];
			$json['positioningmode'] = $_GET['positioningmode'];
			$json['positioningscheme'] = $_GET['positioningscheme'];
			if (preg_match("/^G1.*/", $_GET['subval'])){
			  if ($json["stop"] == "0"){
		  	  $url = 'runner.php?mmmove='.$_GET['subval'].'&tcli=move';
		  	  echo $url.'<br>';
		  	  header('Location: '.$url);
		  	  //runner.php?mmmove=20&tcli=Right&zmmmove=10
		  	  //http://192.168.1.72/gui.mod12/runner.php?mmmove=G1X60Y20Z0F1000&tcli=move
		  	  echo $json['smoothiemessage'].'<br>';
		  	  //echo 'reset flag: '.$json['reset']['on'].'<br>';
		 	  }
		 	  else {
		  	   $msg = 'System halted, you need to press the "GO" button!<br>';
 	 	  	   logger($logger, $msg.'<br>',1);
		 	  }


			}

			if ($_GET['subval'] == 'Submit Coordinates'){
			 $json['positioningtheory']['brow'] = $_GET['brow'];
			 $json['positioningtheory']['bcol'] = $_GET['bcol'];
			 $json['positioningtheory']['erow'] = $_GET['erow'];
			 $json['positioningtheory']['ecol'] = $_GET['ecol'];
			}
			if ($_GET['subval'] == 'Edit Imageprocessing Data'){
			 $json['positionimgprocessing']['edit'] = "1";
			}
			if ($_GET['subval'] == 'Submit Imageprocessing Data'){
			 $json['positionimgprocessing']['edit'] = "0";
			 $json['positioningtheory']['brow'] = $_GET['brow'];
			 $json['positioningtheory']['bcol'] = $_GET['bcol'];
			 $json['positioningtheory']['erow'] = $_GET['erow'];
			 $json['positioningtheory']['ecol'] = $_GET['ecol'];

			 $json['positionimgprocessing']['xbrow'] = $_GET['xrefbrow'];
			 $json['positionimgprocessing']['xbcol'] = $_GET['xrefbcol'];
			 $json['positionimgprocessing']['xerow'] = $_GET['xreferow'];
			 $json['positionimgprocessing']['xecol'] = $_GET['xrefecol'];
			 $json['positionimgprocessing']['xbrowpos'] = $_GET['xbrowpos'];
			 $json['positionimgprocessing']['xbcolpos'] = $_GET['xbcolpos'];
			 $json['positionimgprocessing']['xerowpos'] = $_GET['xerowpos'];
			 $json['positionimgprocessing']['xecolpos'] = $_GET['xecolpos'];

			 $json['positionimgprocessing']['ybrow'] = $_GET['yrefbrow'];
			 $json['positionimgprocessing']['ybcol'] = $_GET['yrefbcol'];
			 $json['positionimgprocessing']['yerow'] = $_GET['yreferow'];
			 $json['positionimgprocessing']['yecol'] = $_GET['yrefecol'];
			 $json['positionimgprocessing']['ybrowpos'] = $_GET['ybrowpos'];
			 $json['positionimgprocessing']['ybcolpos'] = $_GET['ybcolpos'];
			 $json['positionimgprocessing']['yerowpos'] = $_GET['yerowpos'];
			 $json['positionimgprocessing']['yecolpos'] = $_GET['yecolpos'];


			 $json['positionimgprocessing']['xpixpermm'] = $_GET['xpixpermm'];
			 $json['positionimgprocessing']['ypixpermm'] = $_GET['ypixpermm'];
			 //$json['positionimgprocessing']['adjxpixpermm'] = $_GET['adjxpixpermm'];
			 //$json['positionimgprocessing']['adjypixpermm'] = $_GET['adjypixpermm'];
			 //$json['positionimgprocessing']['refxspacingmm'] = $_GET['refxspacingmm'];
			 //$json['positionimgprocessing']['refyspacingmm'] = $_GET['refyspacingmm'];
		
			 //"reference":{"1_1":"1","1_2":"2","1_3":"3","1_4":"4","1_5":"5","2_1":"6","2_2":"7","2_3":"8","2_4":"9","2_5":"10"}
			 $reftarx = $_GET['reftarx'];
			 $reftary = $_GET['reftary'];
			 $searchstr = $reftary."_".$reftarx;
			 if ($json['workplate']['reference'][$searchstr] > 0) {
			  $json['positionimgprocessing']['reftarx'] = $reftarx;
			  $json['positionimgprocessing']['reftary'] = $reftary;
			 }
			 else {
			  echo "<br>This target does not exist<br>";
			 }
			}

		}

	}  //passing on configuration source
}

}  //passing on configuration act







//echo '<br><font size=5>'.$file.'</font><br>';
//echo '<br><font size=5>'.$json['view'].'</font><br>';

/*
   $filen = $_GET['file']; //passing on configuration file
   $coords = $_GET['coords'];
   $act = $_GET['act'];
   $time = time();
   $rampx = $_GET['rampx'];
   $rampy = $_GET['rampy'];
   $source = $_GET['source'];
 */

//$file = "t.png";
//$file = "56.69_89.97_0.00.jpg";

$ex = $json['grid']['ex'];
$ey = $json['grid']['ey'];
$bx = $json['grid']['bx'];
$by = $json['grid']['by'];

?>

<?php  file_put_contents($imgdataset, json_encode($json)); ?>

<table cellpadding=10><tr valign=top><td><?php //include("img.processing.mod.inc.php"); ?></td><td>

</ul>

</ul>

<fieldset><legend><b>Interface Type</b></legend>
<input type="button" value="Position Driver" class="codeButtonE violet" />
<input type="button" value="Images" class="codeButtonA violet" />
<input type="button" value="Live View" class="codeButtonB violet" />
<input type="button" value="Workplate" class="codeButtonC violet" />
<input type="button" value="Gridding" class="codeButtonD violet" />
<input type="button" value="Washing" class="codeButtonF violet" />
<input type="button" value="Piezo pump" class="codeButtonG violet" />
<input type="button" value="Syringe pump" class="codeButtonH violet" />
<input type="button" value="Source plate" class="codeButtonI violet" />
<input type="button" value="Strob Images" class="codeButtonJ violet" />
<br>
<br>
</fieldset>
<br><br>



<?php include('views.php'); ?>


</td>
<td>

</td>
</tr></table>



