<html>
<head><title>CS143 Project 1C by Jonathan Nguy</title></head>
<link href="style.css" rel="stylesheet" type="text/css" />

<body>

<div id="contents">
<div id="left">

<?php
// get the input
if($_GET["id"]){
	$input = $_GET["id"];

	// establish connection
	$db = mysql_connect("localhost", "cs143", "");
	if(!$db) {
		$errmsg = mysql_error($db);
		print "Connection failed: $errmsg <br />";
		exit(1);
	}
	mysql_select_db("CS143", $db);

	// look at movie
	$mov = "SELECT title, rating, year, company 
		FROM Movie M
		WHERE id = '$input'";

	// look for director
	$dir = "SELECT first, last, dob, id
		FROM Director D, MovieDirector MD
		WHERE MD.mid = '$input' AND MD.did = D.id";

	// look for actors
	$act = "SELECT first, last, dob, id, role
		FROM Actor A, MovieActor MA
		WHERE MA.mid = '$input' AND MA.aid = A.id ORDER BY first, last";

	// genre
	$gen = "SELECT genre FROM MovieGenre WHERE mid = '$input'";

	// review
	$rev = "SELECT name, time, mid, rating, comment
		FROM Review WHERE mid = '$input'";

	// ratings
	$rat = "SELECT AVG(rating) FROM Review WHERE mid = '$input' 
		GROUP BY mid";

	$movie = mysql_query($mov, $db);
	$director = mysql_query($dir, $db);
	$actor = mysql_query($act, $db);
	$genre = mysql_query($gen, $db);
	$review = mysql_query($rev, $db);
	$rating = mysql_query($rat, $db);

	if ($movie && $director && $genre){
		$m = mysql_fetch_row($movie);
		
		echo "<h2>Movie info on " .$m[0]. "</h2>";
		echo "<p>";
		echo "Title: <b>$m[0]</b><br/>";
		echo "Production Company: <b>$m[3]</b><br/>";
		echo "MPAA Rating: <b>$m[1]</b><br/>";
		echo "Director: <b> ";
		if (mysql_num_rows($director) > 0) {
			$x=0;
			while ($d = mysql_fetch_row($director)){	
				echo "<a href='./directors.php?id=$d[3]'>$d[0] $d[1]</a> ";
				if($x<mysql_num_rows($director)-1){
					echo "| "; // add a pipe unless it's the last one
					$x++;
				}					
			}
		} else { 
			// this is extremely ugly, I should've just used HTML
			echo "<form action=\"./add.php?type=5\" method=\"GET\">";
			echo "<input type=\"hidden\" name=\"type\" value =\"5\"/>";
			echo "<input type=\"hidden\" name=\"mid\" value =\"$input\"/>";
			echo "<input type=\"submit\" value=\"Add director!\"/>";
			echo "</form>";	
		}
		echo "</b><p>";
		echo "Genre: <b>";
		$x=0;
		while ($g = mysql_fetch_row($genre)){
			echo "$g[0] ";
			if($x<mysql_num_rows($genre)-1){
				echo "| "; // add a pipe unless it's the last one
				$x++;
			}
		}
		echo "</b><br/>";
		echo "Average rating: <b> ";
		if ($rating && mysql_num_rows($rating) > 0) {
			$avgr = mysql_fetch_row($rating);
			if ($avgr[0] > 0)	
				echo "$avgr[0] stars.</b>";
			else echo "No ratings yet! </b>";
		} else { echo "No ratings yet! </b>";}	
		echo "<br/><br/>";
	}
	else {
		echo "There's something wrong with this movie!";
	}

	echo "<b>Cast: </b><br>";
	if ($actor && mysql_num_rows($actor) > 0){
		while ($a = mysql_fetch_row($actor)){
			// for each element in that row
			echo "<a href = './actors.php?id=$a[3]'>";
			for($x=0; $x<2; $x++){
				echo " $a[$x]";
			}
			echo "</a> ";
			echo "as \"" .$a[4]. "\"<br/>";

			//echo "DOB: ";
			//echo "" .$a[2]. " <br/>";
		}			
	} else { 
		echo "There's no cast for this movie! <br/>"; 
		echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		//echo "Add one <a href=./add.php?type=5&mid=$input>here</a>";
	} echo "</p>";
	// see if they want to add more actors
	echo "<form action=\"./add.php\" method=\"GET\">";
	echo "<input type=\"hidden\" name=\"type\" value =\"5\"/>";
	echo "<input type=\"hidden\" name=\"mid\" value =\"$input\"/>";
	echo "<input type=\"submit\" value=\"Add an actor!\"/>";
	echo "</form>";	

	echo "<h2> Reviews: </h2>";
	if ($review && mysql_num_rows($review)>0){
		while($r = mysql_fetch_row($review)){
			echo "<p> <b>$r[0]</b> rated this movie <b>$r[3]</b> stars";
			echo " on " .$r[1]. ". <br/>";
			echo "<b>Comment:</b> $r[4]</p>";	
		}
	} else { echo "<p> No reviews currently! </p>" ; }
?>
<form action="./comment.php?id=" method="GET">
<p>
<input type="hidden" name="id" value ="<?php echo $input; ?>"/>
<input type="submit" value="Add Review!" /></p>
</form>
<?php

	// close database
	mysql_close($db);
}
?>

<?php
// show results of actors that start with the letter
if($_GET["title"]){
	$ttl = $_GET["title"];

	// establish connection
	$db = mysql_connect("localhost", "cs143", "");
	if(!$db) {
		$errmsg = mysql_error($db);
		print "Connection failed: $errmsg <br />";
		exit(1);
	}
	mysql_select_db("CS143", $db);


	$query = "SELECT title, id FROM Movie
		WHERE title LIKE '$ttl%' ORDER BY title";

	echo "<h2>Movies starting with letter <u>".$ttl. "</u></h2>";
	$result = mysql_query($query, $db);

	echo "<p>";
	if ($result && mysql_num_rows($result) > 0){
		echo "<h3>Results: </h3><p>";
		
		// bordered box to display results (so it doesn't get ugly)
		echo "<div style=\"border:1px solid #8D6932;width:500px;height:500px;overflow:auto;overflow-y:scroll;overflow-x:hidden;text-align:left\" ><p>";
		
		while($row = mysql_fetch_row($result)){
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "<a href = './movies.php?id=$row[1]'>";
			echo "" .$row[0]. " ";
			echo "</a><br/>";
		}
		echo "</p></div>";
	} else { echo "<b><p>No movies found. </p></b>";} // not practical to ever reach here 
	

	// close databse
	mysql_close($db);
}

?>




<?php
// PHP to display letters at the bottom
echo "<h3>View Movies (by Title): </h3>";
$some = array(A, B, C, D, E, F, G, H, I, J, K, L, M, N, O, P, Q, R, S, T, U, V, W, X, Y, Z);

foreach ($some as $s){
	echo " <a href=./movies.php?title=$s>$s</a> ";
	if($letter != Z){
		echo "|";
	}
}
?>



<p><p><p>
</body>
</html>
