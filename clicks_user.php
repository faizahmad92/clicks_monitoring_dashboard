<HEAD>

<link rel="stylesheet" href="style.css">
<meta http-equiv="refresh" content="1">

</HEAD>

<?php
require_once ('config.ini');

date_default_timezone_set("Asia/Kuala_Lumpur");

session_start();

if(isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
    echo $message;
}

try{

  // Connection to clicks online banking database
  $ssh = new SSH2($CliksHostOptions);
  $ssh->connect();
  
  if($ssh->authenticate()){
   	
    //running a shell command on remote server
    $sqlOutput = $ssh->shell(array('bash','./Count.sh.bck'));
    if(!$sqlOutput ){
      echo $ssh->getLastError();
    }
    else{
	//trimming the output and separate different part of the output
      $rccpdb2 = explode("RCCPDB2",$sqlOutput);
      $rccpdb2 = end($rccpdb2);
	  $SQLresult = explode("-----------",$rccpdb2);
	  
	 //split each element of $SQLresult into separate parts based on whitespac.
	  $arr1 = preg_split($whitespace_patern, trim($SQLresult[0]));
	  $arr2 = preg_split($whitespace_patern, trim($SQLresult[1]));
	  $arr3 = preg_split($whitespace_patern, trim($SQLresult[2]));
	  $arr4 = preg_split($whitespace_patern, trim($SQLresult[3]));
	  
	 //values extracted and assigned to variables
	  $datetime = $arr1[0]." ".$arr1[1];
	  $webCount = $arr2[0];
	  $mobileCount = $arr3[0];
	  $totalCount = $webCount + $mobileCount;
	  $FPX = $arr4[0];
	  
	  //trimming date/time value.
	  $time_string = trim($SQLresult[0]);
	  $pattern = '/(\d{2}:\d{2}:\d{2})/';
	  preg_match($pattern, $time_string, $matches);
	  $TIME = $matches[0];
	   


$mysqli = new mysqli($db_host, $db_usr, $db_pwd, $db_name);
// check mysql database connection
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}

//inserting historical data into local mysql database
$sql = "INSERT INTO clicks_user(web_count, mobile_count ,fpx_count, total_count, state_time, date_created) VALUES (?, ?, ?, ?, ?, NOW())";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiiss", $webCount, $mobileCount, $FPX, $totalCount, $TIME);

if ($stmt->execute()) {
    $file_id = $mysqli->insert_id;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();

    }
  }

  // ALWAYS DISCONNECT
  $ssh->disconnect();

}
catch(SSH2FailedToConnectException $e){
  print_r($e->getMessage());
}
catch(SSH2FailedToAuthenticate $e){
  print_r($e->getMessage());
}

$conn = new mysqli($db_host, $db_usr, $db_pwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//selecting historical data up to 5 row to show on dashboard.
$sql_hist1 = "SELECT * FROM `clicks_user` ORDER BY ID DESC limit 1";
$result1 = $conn->query($sql_hist1);

if ($result1->num_rows > 0) {
    // output data of each row
    while($row1 = $result1->fetch_assoc()) {
        
		$web1 = $row1["web_count"] ;
		$fpx1= $row1["fpx_count"] ;
		$mobile1 = $row1["mobile_count"] ;
		$state_time1 = $row1["state_time"] ;
		
    }
} else {
    echo "0 results";
}


$sql_hist2 = "SELECT * FROM `clicks_user` ORDER BY ID DESC limit 2";
$result2 = $conn->query($sql_hist2);

if ($result2->num_rows > 0) {
    // output data of each row
    while($row2 = $result2->fetch_assoc()) {
        
		$web2 = $row2["web_count"] ;
		$fpx2= $row2["fpx_count"] ;
		$mobile2 = $row2["mobile_count"] ;
		$state_time2 = $row2["state_time"] ;
		
    }
} else {
    echo "0 results";
}

$sql_hist3 = "SELECT * FROM `clicks_user` ORDER BY ID DESC limit 3";
$result3 = $conn->query($sql_hist3);

if ($result3->num_rows > 0) {
    // output data of each row
    while($row3 = $result3->fetch_assoc()) {
        
		$web3 = $row3["web_count"] ;
		$fpx3= $row3["fpx_count"] ;
		$mobile3 = $row3["mobile_count"] ;
		$state_time3 = $row3["state_time"] ;
		
    }
} else {
    echo "0 results";
}

$sql_hist4 = "SELECT * FROM `clicks_user` ORDER BY ID DESC limit 4";
$result4 = $conn->query($sql_hist4);

if ($result4->num_rows > 0) {
    // output data of each row
    while($row4 = $result4->fetch_assoc()) {
        
		$web4 = $row4["web_count"] ;
		$fpx4= $row4["fpx_count"] ;
		$mobile4 = $row4["mobile_count"] ;
		$state_time4 = $row4["state_time"] ;
		
    }
} else {
    echo "0 results";
}

$sql_hist5 = "SELECT * FROM `clicks_user` ORDER BY ID DESC limit 5";
$result5 = $conn->query($sql_hist5);

if ($result5->num_rows > 0) {
    // output data of each row
    while($row5 = $result5->fetch_assoc()) {
        
		$web5 = $row5["web_count"] ;
		$fpx5= $row5["fpx_count"] ;
		$mobile5 = $row5["mobile_count"] ;
		$state_time5 = $row5["state_time"] ;
		
    }
} else {
    echo "0 results";
}

$conn->close();

// threshold value setting

$t_red = "30000";
$t_ember = "25000";
$m_red = "25000";
$m_ember = "20000";
$f_red = "2000";
$f_ember = "1500";

?>

<BODY bgcolor="#000000">


<br><div class="border">


<div id="mydiv" style="float:left;width:50%;height:35%;">
    <h1 style="font-size:80%;text-align:right;font-weight:bold;font-style:italic;margin-bottom:1px;">CLICKS</h1>
    <h2 style="font-size:40%;text-align:right;margin-top:1px;">Concurrent User</h2>
</div>

<div id="mydiv1" style="float:right;width:50%;height:35%;">
    <h2 style="font-size:110%;text-align:center;font-weight:bold;margin-top:25px;">
        <?php
        echo date("d/m/Y H:i:sa");
        ?><br>
    </h2>
</div>



<?php if ($webCount > $t_red) { ?>
<div style="width: 70px; float:left; height:100; background:#ba000d;border-radius: 11px; margin:12px">
<?php } else if ($webCount > $t_ember){ ?>
<div style="width: 70px; float:left; height:100; background:#ff7d00;border-radius: 11px; margin:12px">
<?php } else { ?>
<div style="width: 70px; float:left; height:100; background:#009624;border-radius: 11px; margin:12px">
<?php } ?>
<h3><b>TOTAL <?php echo $webCount;?></b></h3>
</div>

<?php if ($mobileCount > $m_red) { ?>
<div style="width: 70px; float:left; height:100; background:#ba000d;border-radius: 11px; margin:12px">
<?php } else if ($mobileCount > $m_ember){ ?>
<div style="width: 70px; float:left; height:100; background:#ff7d00;border-radius: 11px; margin:12px">
<?php } else { ?>
<div style="width: 70px; float:left; height:100; background:#009624;border-radius: 11px; margin:12px">
<?php } ?>
<h3><b>MTR <?php echo $mobileCount;?></b></h3>
</div>

<?php if ($FPX > $f_red) { ?>
<div style="width: 70px; float:left; height:100; background:#ba000d;border-radius: 11px; margin:12px">
<?php } else if ($FPX > $f_ember){ ?>
<div style="width: 70px; float:left; height:100; background:#ff7d00;border-radius: 11px; margin:12px">
<?php } else { ?>
<div style="width: 70px; float:left; height:100; background:#009624;border-radius: 11px; margin:12px">
<?php } ?>
<h3><b>FPX <br><?php echo $FPX;?></b></h3>
</div>

  </div>

											
<br> <br>

<section ID="sectt">
    <p class="a">
										&nbsp;	<B>TIME</B>  &nbsp;&nbsp;&nbsp; &nbsp;
											<B>TOTAL
											USER </B>  &nbsp;&nbsp;&nbsp;
											<B>MTR</B>&nbsp;&nbsp;&nbsp;
											<B>FPX </B></p><br>
</section>



<section ID="sect1">
    <div class="rt-container">
          <div class="col-rt-12">
              <div class="Scriptcontent">
				<div id="content">
                        <div class="vertical-flip-container flip-container floatL" ontouchstart="this.classList.toggle('hover');">
                                <div class="flipper">
								<?php if ($webCount > $t_red || $mobileCount > $m_red || $FPX > $f_red ) { ?>
										<div class="front_red">
										<?php } else if ($webCount > $t_ember|| $mobileCount > $m_ember || $FPX > $f_ember){ ?>
										<div class="front_ember">
										<?php } else { ?>
										<div class="front">
										<?php } ?>
											<?php echo $TIME ; ?> &nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $webCount;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobileCount;?>&nbsp;&nbsp;&nbsp;
											<?php echo $FPX;?>
										</div>
										<?php if ($webCount > $t_red || $mobileCount > $m_red || $FPX > $f_red ) { ?>
                                        <div class="back_red">
										<?php } else if ($webCount > $t_ember|| $mobileCount > $m_ember || $FPX > $f_ember){ ?>
										<div class="back_ember">
										<?php } else { ?>
										<div class="back">
										<?php } ?>
											<?php echo $TIME ; ?> &nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $webCount;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobileCount;?>&nbsp;&nbsp;&nbsp;
											<?php echo $FPX;?>
										</div>
                                        <div class="clear"></div>
                                </div>
                        </div>
                </div>
			</div>
		</div>
    </div>
</section>
<BR><BR>

<section ID="sect2">
    <div class="rt-container">
          <div class="col-rt-12">
              <div class="Scriptcontent">
				<div id="content">
                        <div class="vertical-flip-container1 flip-container floatL " ontouchstart="this.classList.toggle('hover');">
                                <div class="flipper1">
                                        <?php if ($web2 > $t_red || $mobile2 > $m_red || $fpx2 > $f_red ) { ?>
										<div class="front_red">
										<?php } else if ($web2 > $t_ember|| $mobile2 > $m_ember || $fpx2 > $f_ember){ ?>
										<div class="front_ember">
										<?php } else { ?>
										<div class="front">
										<?php } ?>
											<?php echo $state_time2 ; ?> &nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $web2;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile2;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx2;?>
										</div>
                                        <?php if ($web2 > $t_red || $mobile2 > $m_red || $fpx2 > $f_red ) { ?>
                                        <div class="back_red">
										<?php } else if ($web2 > $t_ember|| $mobile2 > $m_ember || $fpx2 > $f_ember){ ?>
										<div class="back_ember">
										<?php } else { ?>
										<div class="back">
										<?php } ?>
											<?php echo $state_time2 ; ?> &nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $web2;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile2;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx2;?>
										</div>
                                        <div class="clear"></div>
                                </div>
                        </div>
                </div>
			</div>
		</div>
    </div>
</section>
<BR><BR>

<section ID="Sect3">
    <div class="rt-container">
          <div class="col-rt-12">
              <div class="Scriptcontent">
				<div id="content">
                        <div class="vertical-flip-container2 flip-container floatL" ontouchstart="this.classList.toggle('hover');">
                                <div class="flipper2">
                                        <?php if ($web3 > $t_red || $mobile3 > $m_red || $fpx3 > $f_red ) { ?>
										<div class="front_red">
										<?php } else if ($web3 > $t_ember|| $mobile3 > $m_ember || $fpx3 > $f_ember){ ?>
										<div class="front_ember">
										<?php } else { ?>
										<div class="front">
										<?php } ?>
											<?php echo $state_time3 ; ?> &nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $web3;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile3;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx3;?>
										</div>
                                        <?php if ($web3 > $t_red || $mobile3 > $m_red || $fpx3 > $f_red ) { ?>
                                        <div class="back_red">
										<?php } else if ($web3 > $t_ember|| $mobile3 > $m_ember || $fpx3 > $f_ember){ ?>
										<div class="back_ember">
										<?php } else { ?>
										<div class="back">
										<?php } ?>
											<?php echo $state_time3 ; ?> &nbsp;&nbsp;&nbsp;&nbsp; 
											<?php echo $web3;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile3;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx3;?>
										</div>
                                        <div class="clear"></div>
                                </div>
                        </div>
                </div>
			</div>
		</div>
    </div>
</section>
<BR><BR>

<section ID="sect4">
    <div class="rt-container">
          <div class="col-rt-12">
              <div class="Scriptcontent">
				<div id="content">
                        <div class="vertical-flip-container3 flip-container floatL" ontouchstart="this.classList.toggle('hover');">
                                <div class="flipper3">
                                        <?php if ($web4 > $t_red || $mobile4 > $m_red || $fpx4 > $f_red ) { ?>
										<div class="front_red">
										<?php } else if ($web4 > $t_ember|| $mobile4 > $m_ember || $fpx4 > $f_ember){ ?>
										<div class="front_ember">
										<?php } else { ?>
										<div class="front">
										<?php } ?>
											<?php echo $state_time4 ; ?> &nbsp;&nbsp;&nbsp;&nbsp; 
											<?php echo $web4;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile4;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx4;?>
										</div>
                                        <?php if ($web4 > $t_red || $mobile4 > $m_red || $fpx4 > $f_red ) { ?>
                                        <div class="back_red">
										<?php } else if ($web4 > $t_ember|| $mobile4 > $m_ember || $fpx4 > $f_ember){ ?>
										<div class="back_ember">
										<?php } else { ?>
										<div class="back">
										<?php } ?>
											<?php echo $state_time4 ; ?> &nbsp;&nbsp;&nbsp;&nbsp; 
											<?php echo $web4;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile4;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx4;?>
										</div>
                                        <div class="clear"></div>
                                </div>
                        </div>
                </div>
			</div>
		</div>
    </div>
</section>
 
<BR><BR>

<section>
    <div class="rt-container">
          <div class="col-rt-12">
              <div class="Scriptcontent">
				<div id="content">
                        <div class="vertical-flip-container4 flip-container floatL" ontouchstart="this.classList.toggle('hover');">
                                <div class="flipper4">
                                       <?php if ($web5 > $t_red || $mobile5 > $m_red || $fpx5 > $f_red ) { ?>
										<div class="front_red">
										<?php } else if ($web5 > $t_ember|| $mobile5 > $m_ember || $fpx5 > $f_ember){ ?>
										<div class="front_ember">
										<?php } else { ?>
										<div class="front">
										<?php } ?>
											<?php echo $state_time5 ; ?> &nbsp;&nbsp;&nbsp;&nbsp; 
											<?php echo $web5;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile5;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx5;?>
										</div>
                                        <?php if ($web5 > $t_red || $mobile5 > $m_red || $fpx5 > $f_red ) { ?>
                                        <div class="back_red">
										<?php } else if ($web5 > $t_ember|| $mobile5 > $m_ember || $fpx5 > $f_ember){ ?>
										<div class="back_ember">
										<?php } else { ?>
										<div class="back">
										<?php } ?>
											<?php echo $state_time5 ; ?> &nbsp;&nbsp;&nbsp;&nbsp; 
											<?php echo $web5;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
											<?php echo $mobile5;?>&nbsp;&nbsp;&nbsp;
											<?php echo $fpx5;?>
										</div>
                                        <div class="clear"></div>
                                </div>
                        </div>
                </div>
			</div>
		</div>
    </div>
</section>
</BODY>