<?php
	session_id(123456789);
	session_start();
?>

<!DOCTYPE html>
<html> 
	<head> 
		<meta charset="utf-8">
		<title>Coursemanager</title>  
		<link rel="stylesheet" type="text/css" href="estilo.css">
	</head> 

	<body>
	<div class="contenedor">
		<form action="#" class="formulario" id="formulario" name="formulario" method="POST">
				<div class="contenedor-inputs">
					<?php
						echo "<p><label>Welcome: $_SESSION[usuar] </label></p>";
					?>
					<p><label class="label"> </label></p>
					<p><label class="label"> Introduzca query: <input type="texto" name="query"></label></p>
					<p><label class="label"> </label></p>
					<p><input type="submit" name="search" value="Search"> <input type="submit" name="logout" value="Logout"></p>		
					<p><label class="label"> </label></p>
					<?php
					$full_query="vacio";
						if(isset($_POST['search'])){
							$full_query=$_POST["query"];
							$html = @file_get_contents("http://rvd1.upc.edu/proxy/pbe-telem-T2019-marc-garcia-quirantes:55555/?" . $full_query); //Convierte la información de la URL en cadena				
							if($html != FALSE){
								$htmlp = json_decode($html);
								$separador = explode("?", $full_query);
								if($separador[0] == "students"){
									echo "<table>";
									echo "<tr><b><center> Students </center></b></tr>";
									echo "  <td><b> Nombre </b></td>";
									echo "  <td><b> UID </b></td>";
									echo "</tr>";
									foreach($htmlp as $item){																	
										echo "<tr>";
										echo "<td> " . $item->name . "    " ."</td>";
										echo "<td> " . $item->uid. "</td>";
										echo "</tr>";								
									}
									echo "</table>";
								}
								else if($separador[0] == "timetables"){
									echo "<table>";
									echo "<tr><b><center> Timetables </center></b></tr>";
									echo "  <td><b> Day  </b></td>";
									echo "  <td><b> Hour </b></td>";
									echo "  <td><b> Subject </b></td>";
									echo "  <td><b> Room </b></td>";
									echo "</tr>";
									foreach($htmlp as $item){																	
										echo "<tr>";
										echo "<td> " . $item->Day . "</td>";
										echo "<td> " . $item->Hour . "</td>";
										echo "<td> " . $item->Subject . "</td>";
										echo "<td> " . $item->Room . "</td>";
										echo "</tr>";								
									}
									echo "</table>";
								}
								else if($separador[0] == "tasks"){
									echo "<table>";
									echo "<tr><b><center> Tasks </center></b></tr>";
									echo "  <td><b> Date  </b></td>";
									echo "  <td><b> Subject </b></td>";
									echo "  <td><b> Name </b></td>";
									echo "</tr>";
									foreach($htmlp as $item){																	
										echo "<tr>";
										echo "<td> " . $item->Date . "</td>";
										echo "<td> " . $item->Subject . "</td>";
										echo "<td> " . $item->Name . "</td>";
										echo "</tr>";								
									}
									echo "</table>";
								}
								else if($separador[0] == "marks"){
									echo "<table>";
									echo "<tr><b><center> Marks </center></b></tr>";
									echo "  <td><b> Subject  </b></td>";
									echo "  <td><b> Name </b></td>";
									echo "  <td><b> Mark </b></td>";
									echo "</tr>";
									foreach($htmlp as $item){																	
										echo "<tr>";
										echo "<td> " . $item->Subject . "</td>";
										echo "<td> " . $item->Name . "</td>";
										echo "<td> " . $item->Mark . "</td>";
										echo "</tr>";								
									}
									echo "</table>";
								}							
							}
							else{
								echo "<p>La orden solicitada es incorrecta<p>";
							}	
						}
						if(isset($_POST['logout'])){
							header("Location: http://localhost/project");
						}
					?>
						
					<ul class="error" id="error"></ul>
				</div>
		</form>
	</body>

</html>

