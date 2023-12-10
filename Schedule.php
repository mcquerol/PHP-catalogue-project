<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
 <title>Calendar</title>
 <meta name="description" content="Calendar" />
 <meta name="keywords" content="type, keywords, here" />
 <meta name="author" content="Your Name" />
 <meta http-equiv="content-type" content="text/html;charset=UTF-8" /> 
 <link rel="stylesheet" type="text/css" href="calendar.css" />
</head>

<script type="text/javascript">
function startTime() {
    var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();
    m = checkTime(m);
    s = checkTime(s);
    document.getElementById('txt').innerHTML =
    h + ":" + m + ":" + s;
    var t = setTimeout(startTime, 500);
}
function checkTime(i) {
    if (i < 10) {i = "0" + i};  // add zero in front of numbers < 10
    return i;
}
</script>

<body onload="startTime()">


<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "calendar";

echo "<div align=\"right\" id=\"txt\"></div>"; // --- this is where the time will be displayed
// ---- define variables --------------------------------------------
$time = time();
$day = 0;
$month = date('m',$time);
$year = date('Y',$time);

// ---- Get the values passed through the REQUEST --------------------
//var_dump($_REQUEST); --- for debugging
if (isset($_REQUEST["year"])) {
	$year = $_REQUEST["year"];
	$month = $_REQUEST["month"];	
}
if (isset($_REQUEST["p"])) {
	$p = $_REQUEST["p"];
}
//---- Main Prog -----------------------------------------------------
fNextMonth();
fCalendar();
//---- Functions -----------------------------------------------------
function fNextMonth(){
	global $year, $month;

	$pMonth = $month-1;
	$pYear = $year;

	$nMonth = $month+1;
	$nYear = $year;
	
	if ($pMonth < 1) {
		$pYear--;
		$pMonth=12;
	}
	if ($nMonth > 12) {
		$nYear++;
		$nMonth=1;
	}
	
	echo "<tr>\n<td>&nbsp;</td>";
	echo "<td style=\"width:150px;\">\n<a href=\"Schedule.php?month=" .$pMonth ."&year=" .$pYear ."\">" 
		.date("M Y", strtotime($pYear ."-" .$pMonth ."-01")) ."</a></td>";
	echo "<td style=\"text-align:center;font-weight: bold;\">" .date("F Y", strtotime($year ."-" .$month ."-01"))  ."</td>\n";
	echo "<td style=\"width:150px;\">\n<a href=\"Schedule.php?month=" .$nMonth ."&year=" .$nYear ."\">" 
		.date("M Y", strtotime($nYear ."-" .$nMonth ."-01")) ."</a></td>";
	echo "</tr>\n";
	echo "</table>\n";	
}
//---------------------------------------------------------------------------
function fCalendar(){
    global $day, $month, $year, $time,$servername, $username, $password, $dbname; 
	// ---- This is the first day of the week
	$weekd = date("w", strtotime($year ."-" .$month ."-1"));
	if ($weekd==0){$weekd=7;}
	echo "<table style=\"align=\"center;\" border=\"1px\">\n";
		echo "<tr>\n";
		echo "<th>Monday</th>";
		echo "<th>Tuesday</th>";
		echo "<th>Wednesday</th>";
		echo "<th>Thursday</th>";
		echo "<th>Friday</th>";
		echo "<th>Saturday</th>";
		echo "<th>Sunday</th>";
        echo "</tr>\n";		
		
		$style1="width:100px;height:75px;border:1px solid grey;background-color:#DDDDDD;";
		$style2="width:100px;height:75px;border:1px solid black;"; 
		$style3="width:100px;height:75px;border:1px ;background-color:#FF0000;"; 


	$conn = mysqli_connect($servername, $username, $password, $dbname);
	if (!$conn) {
		die ("Connection failed: " . mysqli_connect_error());
	}

	//--- Insert new record in table ----------	
	
	$sql = "SELECT DAY(eventDate),MONTH(eventDate),YEAR(eventDate), Title, Detail FROM eventcalendar";
	   
	if(!mysqli_query($conn, $sql)){
		echo "ERROR: Failed to execute $sql. " . mysqli_error($conn);
	}


	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0 ) {
		while($row = mysqli_fetch_assoc($result)){
			//TODO What is supposed to go here?
		}
	}	

		for ($r=1;$r<7;$r++){
            echo "<tr>\n";
            for ($c=1;$c<8;$c++){
				if ($r==1 and $c<$weekd){   //if we are on the first week and BEFORE the weekday of the first day of the month...
					echo "<td style=\"".$style1."\">";
				}else{				//otherwise...
					$day++;
					$intDate = strtotime($year ."-" .$month ."-" .$day);
					if ((date("m",$intDate)==$month) and (date("Y",$intDate)==$year)) {
						$curDate = date("d", $intDate);
						//$day=$curDate; TOFIX
						if($curDate == date('d',$time) && $month == date('m', $time) && $year == date('Y', $time)) {
							echo "<td style=\"".$style3."\"><a href=\"Schedule.php?day=" .$curDate ."&month=" .$month ."&year=" .$year."&v=true\">".$curDate."</a>";

						//$result = mysqli_query($conn, $sql);
						//	if (mysqli_num_rows($result) > 0 ) {
						//		while($row = mysqli_fetch_assoc($result)){
						//			echo $row['eventDate'];
						//			echo $row['Title'];
							//	}
							//}
						//}elseif(DAY($row['eventDate']) == date('d', $time) && MONTH($row['eventDate']) == date('m', $time) && YEAR($row['eventDate']) == date('Y', $time)){
						//	echo "<td style=\"".$style1."\">".$row['Title']."";
						}else{
							echo"<td style=\"".$style2."\" onMouseOver=\"this.style.background='#666666'\" onMouseOut=\"this.style.background='white'\"><a href=\"Schedule.php?day=" .$curDate ."&month=" .$month ."&year=" .$year."&v=true\" onClick=\"window.open('eventform.php?day=" .$curDate ."&month=" .$month ."&year=" .$year."&f=true', '', 'toolbar=yes,scrollbars=yes,resizable=no,width=400,height=200');\">".$curDate."</a>";
						}
					}
				}
				echo "</td>\n";
            }
            echo "</tr>\n";
			if ((date("m",$intDate+86400)!=$month) or (date("Y",$intDate)!=$year)){$r=7;}
        }
    echo "</table>\n";
}
?>

</body>
</html>
