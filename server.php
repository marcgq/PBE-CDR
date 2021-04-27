<?php
$servername = "localhost";
$username = "marc";
$password = "pbemarcgq";
$dbname = "pbe";

$conn = new \mysqli($servername, $username, $password, $dbname);

if($conn->connect_error) {
die("connection failed ". $conn->connect-error);
}

//echo $_SERVER['QUERY_STRING']."<br>";
//echo date('Y-m-d  D')."<br>";
$full_query = explode("?", $_SERVER['QUERY_STRING']);
$sql= "SELECT * FROM " .$full_query[0];
$res =array();
if(!empty($full_query[1])){
	
	parse_str($full_query[1], $res);
	//echo var_dump($res) ."<br>";
	if(array_key_exists('Subject', $res)){
		$sql .= " WHERE Subject ='".$res['Subject']."'";
	}
	if(array_key_exists('Mark', $res)){
		switch (gettype($res['Mark'])) {
			case 'string':
				$sql .= " WHERE Mark ='".$res['Mark']."'";
				break;
			case 'array':
				if(array_key_exists('gt', $res['Mark'])) {
					$sql .= " WHERE Mark >'".$res['Mark']['gt']."'";
				} elseif (array_key_exists('gte', $res['Mark'])) {
					$sql .= " WHERE Mark >='".$res['Mark']['gte']."'";
				} elseif (array_key_exists('lt', $res['Mark'])) {
					$sql .= " WHERE Mark <'".$res['Mark']['lt']."'";
				} elseif (array_key_exists('lte', $res['Mark'])) {
					$sql .= " WHERE Mark <='".$res['Mark']['lte']."'";
				}
				break;
		}
	}
	if(array_key_exists('Date', $res)){
		switch (gettype($res['Date'])) {
			case 'string':
				$sql .= " WHERE Date ='";
				$value=$res['Date'];
				break;
			case 'array':
				if(array_key_exists('gt', $res['Date'])) {
					$sql .= " WHERE Date >'";
					$value=$res['Date']['gt'];
				} elseif (array_key_exists('gte', $res['Date'])) {
					$sql .= " WHERE Date >='";
					$value=$res['Date']['gte'];
				} elseif (array_key_exists('lt', $res['Date'])) {
					$sql .= " WHERE Date <'";
					$value=$res['Date']['lt'];
				} elseif (array_key_exists('lte', $res['Date'])) {
					$sql .= " WHERE Date <='";
					$value=$res['Date']['lte'];
				}
				break;

		}
		if ($value=='now'){
			$sql .= date('Y-m-d')."'";
		} else {
			$sql .= $value."'";
		}
	}
	if(array_key_exists('Hour', $res)){
		switch (gettype($res['Hour'])) {
			case 'string':
				$sql .= " WHERE Hour ='";
				$value=$res['Hour'];
				break;
			case 'array':
				if(array_key_exists('gt', $res['Hour'])) {
					$sql .= " WHERE Hour >'";
					$value=$res['Hour']['gt'];
				} elseif (array_key_exists('gte', $res['Hour'])) {
					$sql .= " WHERE Hour >='";
					$value=$res['Hour']['gte'];
				} elseif (array_key_exists('lt', $res['Hour'])) {
					$sql .= " WHERE Hour <'";
					$value=$res['Hour']['lt'];
				} elseif (array_key_exists('lte', $res['Hour'])) {
					$sql .= " WHERE Hour <='";
					$value=$res['Hour']['lte'];
				}
				break;

		}
		if ($value=='now'){
				$sql .= date('H:m:s')."'";
			}	else{
				$sql .= $value."'";
			}
	}
	if(array_key_exists('Day', $res)){
		if ($res['Day']=='now'){
			$sql .= " WHERE Day ='".date('D')."'";
		}	else{
			$sql .= " WHERE Day ='".$res['Day']."'";

		}	
	}	
	
	if(array_key_exists('limit', $res)){
		$sql .= " LIMIT ".$res['limit'];
	}
} 
if ($full_query[0]=='timetables' && !array_key_exists('Day', $res)) {
	$sql .= " ORDER BY Day ASC";
} elseif ($full_query[0]=='tasks'&&!array_key_exists('Date', $res)) {
	$sql .= " ORDER BY Date ASC";
} elseif ($full_query[0]=='marks'&&!array_key_exists('Subject', $res)) {
	$sql .= " ORDER BY Subject ASC";
} elseif ($full_query[0]=='students'&&array_key_exists('uid', $res)) {
	$sql .= " WHERE uid='".$res['uid']."'";
}

$result = $conn->query($sql);
//echo $sql;
$rows = array();
while($r = $result->fetch_object()){
	array_push($rows, $r);
}
$json_result= json_encode($rows, 128);
echo $json_result;
//print("<pre>".$json_result."</pre>");

$conn->close();

?>