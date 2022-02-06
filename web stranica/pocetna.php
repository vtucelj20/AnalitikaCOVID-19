<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <link rel="stylesheet" href="css/leaflet.css">
        <link rel="stylesheet" href="css/qgis2web.css"><link rel="stylesheet" href="css/fontawesome-all.min.css">
        <link rel="stylesheet" href="css/leaflet-measure.css">
        <style>
        html, body, #map {
            width: 100%;
            height: 100%;
            padding: 0;
        }
        li {list-style: none;}
        table {
  			border-collapse: collapse;
  			width: 97%;
		}
		th, td {
  			text-align: center;
  			padding: 8px;
		}
		tr:nth-child(even){
			background-color: #f2f2f2
		}
		th {
  			background-color: #04AA6D;
  			color: white;
		}
        input[type=submit] {
  			background-color: #555555;
  			border: none;
  			color: white;
  			padding: 10px;
  			text-align: center;
  			text-decoration: none;
  			display: inline-block;
  			font-size: 16px;
  			margin: 4px 2px;
  			cursor: pointer;
  			border-radius: 12px;
		}
		.naslov {
  			background-color: lightblue;    
  			text-align: center;
  			width: 100%;
  			padding: 5px;
  			margin-bottom: 6px;
		}
		.forma {
			border-radius: 5px;
			background-color: #f2f2f2;
  			padding: 20px;
		}
		p {
			margin-left: 5px;
			margin-top: 5px;
		}
		form {
			margin-left: 5px;
		}
		ul {
  			list-style-type: none;
  			margin: 0;
  			padding: 0;
  			overflow: hidden;
  			background-color: #333;
		}
		li {
  			float: left;
		}
		li a {
  			display: block;
  			color: white;
  			text-align: center;
  			padding: 14px 20px;
  			text-decoration: none;
		}		
		li a:hover:not(.active) {
  			background-color: #111;
		}
		.active {
  			background-color: #04AA6D;
		}
        </style>
        <title>COVID-19 u Hrvatskoj</title>
    </head>
    <body>
    
    	<div class="naslov">
    	<h1 style="text-align:center">Sustav za analitiku COVID-19 u Hrvatskoj</h1>
    	</div>
    	
    	<ul>
  			<li><a class="active" href="pocetna.php">Početna - pregled po datumima za županiju</a></li>
  			<li><a href="drugipregled.php">Pregled svih županija na određeni datum</a></li>
  			<li><a href="index.html">Pogledaj na karti</a></li>
		</ul>
    	
    	<div class="forma">
    	<p><b>Izaberite županiju i vrijeme za koje želite pregledati podatke</b><i style="font-size:14px">(najmanji dostupan datum je 01.01.2021., a najviši 27.01.2022.)</i></p>
		<form name="ispis" action="pocetna.php" method="POST" >
		<label for="zupanija">Županija:</label>
		<?php
		$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=projektcovid user=valentina password=lozinka");
		if(!$dbconn) {
		    die('Error connecting to database: ' . pg_last_error());
		}
		$select = pg_query("select zupanija_id, naziv_zupanije from zupanije");
		echo "<select name='zupanija' style='width: 300px; margin-right: 15px'>";
    	while ($row = pg_fetch_row($select)) {
                  unset($id, $name);
                  $id = $row[0];
                  $name = $row[1]; 
                  echo '<option value="'.$id.'">'.$name.'</option>';
			}
    	echo "</select>";
		?>
		<label for="datumOd">Datum od:</label>
		<input type="date" value="2022-01-25" min="2021-11-01" max="2022-01-26" name="datum_od" style="margin-right: 15px"/>
		<label for="datumDo">Datum do:</label>
		<input type="date" value="2022-01-27" min="2021-11-02" max="2022-01-27" name="datum_do" style="margin-right: 15px"/>
		<input type="submit" value="Pregledaj podatke!" />
		</form>
		</div>
		
		<?php
		$dbconn = pg_connect("host=127.0.0.1 port=5432 dbname=projektcovid user=valentina password=lozinka");
		if(!$dbconn) {
		    die('Error connecting to database: ' . pg_last_error());
		}

		if(isset($_REQUEST["zupanija"]) && isset($_REQUEST["datum_od"]) && isset($_REQUEST["datum_do"])) {
 		  $zupanija = $_POST["zupanija"];
  		  $datumOd = $_POST["datum_od"];
  		  $datumDo = $_POST["datum_do"];
  		  $select = pg_query("select z.naziv_zupanije, di.za_datum, pd.broj_ukupno_zarazenih, pd.broj_ukupno_umrlih, pd.broj_novozarazenih, pd.broj_trenutno_aktivnih from zupanije z, podaci_detaljno pd, dnevni_izvjestaji di where z.zupanija_id = pd.zupanija_id and pd.izvjestaj_id = di.izvjestaj_id and z.zupanija_id = '$zupanija' and di.za_datum between '$datumOd' and '$datumDo'");
 		   if(!$select) {
 		       echo ("Error while inserting data: " . pg_last_error());
  		  } else {
  		      echo "<table border='1' style='margin-top: 20px'>\n<tr><th>Županija</th><th>Datum</th><th>Ukupno zaraženi</th><th>Ukupno umrli</th><th>Novozaraženih</th><th>Trenutno aktivnih</th></tr>\n";

		while($row = pg_fetch_row($select)) {
 		 echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td><td>" . $row[5] . "</td></tr>\n";
		}
		echo "</table>\n";
  		  }
		}
		pg_close($dbconn);
		?>
		
    </body>
</html>

