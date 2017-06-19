# Your RESTFUL API
<?php
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'),true);



$GLOBALS['dbconn'] = pg_connect("host= $LOCATION dbname= $DBNAME user='$UTORID' password='$DBPASSWORD'") or die('Could not connect: ' . pg_last_error());
//$GLOBALS['dbconn'] = pg_connect("host= mcsdb.utm.utoronto.ca dbname= abbass13_309 user='abbass13' password='90444'") or die('Could not connect: ' . pg_last_error());
	
$unameE = $pwdE = $nameE = $emailE = "";



if($method == 'POST'){

	$username = strtolower($input["username"]);
	$password = md5($input["password"]);

	//-----------Display Top Ten Highscores---------------------//
	$mode = $input["type"];
		if($mode =="hs"){

		$query5  = "select username, userscore from highscores order by userscore DESC fetch first 10 rows only;";
		$result = pg_query($GLOBALS['dbconn'], $query5);
		$result4 = pg_fetch_all($result);
			
		echo json_encode($result4);
		header($_SERVER["SERVER_PROTOCOL"]." 200");
	}

	//---------------Update Number of Game Played-----------------//
	

	if($mode =="updateNumG"){
		$userNumG = strtolower($input["userNumG"]);
		$query4  = "SELECT numgamesplayed FROM users WHERE username=$1;";
		$result4 = pg_prepare($GLOBALS['dbconn'], "validateUser", $query4);
		$result4 = pg_execute($GLOBALS['dbconn'], "validateUser", array($userNumG));
		$result4 = pg_fetch_array($result4);
		$numPlayed = $result4[0];
		$numPlayed++;

		$query5  = "UPDATE users SET numgamesplayed=$1 where username=$2;";
		$result = pg_prepare($GLOBALS['dbconn'], "test", $query5);
		$result = pg_execute($GLOBALS['dbconn'], "test", array($numPlayed, $userNumG));
		
		echo json_encode($numPlayed);
		header($_SERVER["SERVER_PROTOCOL"]." 200");
	}


	//----------------Login Validation-------------------------//
	if(!empty($username) && !empty($password)){
	#checking if username already exists in the database
	
		
		$query  = "SELECT * FROM users where username =$1 and password =$2;";
		$result = pg_prepare($GLOBALS['dbconn'], "validateUser", $query);
		$result = pg_execute($GLOBALS['dbconn'], "validateUser", array($username, $password));
		$result = pg_fetch_array($result);
		
	
		if (!empty($result)) {
		
		
			#updating Last Login Date
			$query2  = "UPDATE users SET lastlogin=current_timestamp where username=$1;";
			$result2 = pg_prepare($GLOBALS['dbconn'], "updateLoginDate", $query2);
			$result2 = pg_execute($GLOBALS['dbconn'], "updateLoginDate", array($username));
	  		
	
			//echo json_encode("logged in");
			//echo json_encode($username);
			header($_SERVER["SERVER_PROTOCOL"]." 200");
					
		
		} else {
			//echo json_encode("invalid login");
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");  
			
		}
	}
	
	//-----------------POST METHOD FOR 'PROFILE UPDATE'-----------------------//

	$username2 = strtolower($input["username2"]);
	$password2 = md5($input["password2"]);
	$name2 = $input["name2"];
	$email2 = $input["email2"];

	if(!empty($username2) && !empty($password2) && !empty($name2) && !empty($email2)){
	#checking if username already exists in the database

		$query3  = "UPDATE users SET password = $1, email = $2, name = $3 WHERE username = $4";
		$result3 = pg_prepare($GLOBALS['dbconn'], "validateUser", $query3);
		$result3 = pg_execute($GLOBALS['dbconn'], "validateUser", array($password2, $email2, $name2, $username2));
		
	
		if (!empty($result3)) {

			header($_SERVER["SERVER_PROTOCOL"]." 200");
		
		} else {
			header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		}
	}
	//-------------------Getting Top 3 Score-------------------------//
	
	$scoreUser=$input["usernameG"];

	if(!empty($scoreUser)){
		
		$query4  = "select userscore from highscores where username=$1 order by userscore DESC fetch first 3 rows only;";
		$result4 = pg_prepare($GLOBALS['dbconn'], "validateUser", $query4);
		$result4 = pg_execute($GLOBALS['dbconn'], "validateUser", array($scoreUser));
		$result4 = pg_fetch_all($result4);
		
		echo json_encode($result4);
		header($_SERVER["SERVER_PROTOCOL"]." 200");
	}
	

}else if($method == 'PUT'){
	
	$username = strtolower($input["username"]);
	$password = md5($input["password"]);
	$email = $input["email"];
	$name = $input["name"];

	$timed = $input["timed"];
	$scored = $input["scored"];
	$userG = $input["usernameG"];

	
	

	#Checking if Username already exists
	$query  = "SELECT * FROM users where username =$1;";
	$result = pg_prepare($GLOBALS['dbconn'], "checkUser", $query);
	$result = pg_execute($GLOBALS['dbconn'], "checkUser", array($username));
        $result = pg_fetch_array($result);

	



        if (empty($result)) {
		
        	$query2="INSERT INTO users(username, password, email, name, numgamesplayed) VALUES ($1,$2, $3, $4, $5);";
		$result2 = pg_prepare($GLOBALS['dbconn'], "my_query", $query2);			
		$result2 = pg_execute($GLOBALS['dbconn'], "my_query", array($username, $password, $email, $name, 0));
		if($result2){
			//echo json_encode($username);
			header($_SERVER["SERVER_PROTOCOL"]." 200");
		} else {
			echo json_encode("Unsuccessful Registration");
		}
        } else {

	    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        }	
	
	if (!empty($timed) && !empty($scored) && !empty($userG)) {
		
        	$query3="INSERT INTO highscores(username, userscore, gameduration) VALUES ($1,$2, $3);";
		$result3 = pg_prepare($GLOBALS['dbconn'], "my_query", $query3);			
		$result3 = pg_execute($GLOBALS['dbconn'], "my_query", array($userG, $scored, $timed));
		header($_SERVER["SERVER_PROTOCOL"]." 200");
	}
	

}else if($method == 'GET'){

	//$table = $_GET['p'];
	//$username = $_GET['n'];

	if(!isset( $_GET['table'] ) ){
		 echo '<p>Invalid Table Name (Lowercase Letters Only)</p>';
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
		
	}else {
		//echo '<p>'.$_GET['table'].'</p>';
		header($_SERVER["SERVER_PROTOCOL"]." 200");
		//$table = $_GET['p'];
	}
	if(!isset( $_GET['n'] ) ){ 
		echo '<p>Invalid Username (Lowercase Letters Only)</p>';
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	}else {

		if($_GET['table'] == "users" && $_GET['score'] != "highscores"){
			$username = $_GET['n'];
		
			//$username = "fajo";
			//$table = 'users';

			$query  = "SELECT name, email, numgamesplayed, lastlogin FROM users where username =$1;";
			$result = pg_prepare($GLOBALS['dbconn'], "checkUser", $query);
			$result = pg_execute($GLOBALS['dbconn'], "checkUser", array($username));	
			$result = pg_fetch_row($result);
			$data = array('name' => $result[0], 'email' => $result[1], 'numGamesPlayed' => $result[2], 'Last Login' => $result[3]);
		
			echo json_encode($data);
			header($_SERVER["SERVER_PROTOCOL"]." 200");	
		}
		if($_GET['score'] == "highscores"){
			//$table = $_GET['table'];
			$username = $_GET['n'];
		
			//$username = "fajo";
			//$table = 'users';

			$query4  = "select userscore from highscores where username=$1 order by userscore DESC fetch first 3 rows only;";
			$result4 = pg_prepare($GLOBALS['dbconn'], "validateUser", $query4);
			$result4 = pg_execute($GLOBALS['dbconn'], "validateUser", array($username));
			$result4 = pg_fetch_all($result4);
			$data = array($result4[0], $result4[1], $result4[2]);
		
			echo json_encode($data);
			header($_SERVER["SERVER_PROTOCOL"]." 200");	
		}	
		

	}
	

	
	

}

?>
