<?php
	session_id(123456789);
	session_start();
?>

<!DOCTYPE html>

<html> 
	<head> 
		<meta charset="utf-8">
		<title>Coursemanager</title>  
		<link rel="stylesheet" type="text/css" href="estilo2.css">
	</head> 

	<body>
	<div class="contenedor">
		<form action="#" class="formulario" id="formulario" name="formulario" method="POST">
				<div class="contenedor-inputs">
					<p><label class="label"> Welcome to Coursemanager</label></p>
					<p><label class="label"> </label></p>
					<p><label class="label"> Nombre de usuario: <input type="texto" name="usuario"></label></p>
					<p><label class="label"> Contraseña: <input type="password" name="pass"></label></p>
					<p><label class="label"> </label></p>
					<p><input type="submit" name="login" value="Login"></p>		
					<p><label class="label"> </label></p>
					<?php
						if(isset($_POST['login'])){
							$user=$_POST["usuario"];
							$pass=$_POST["pass"];
							$logincomp=0;

							$html = file_get_contents('http://rvd1.upc.edu/proxy/pbe-telem-T2019-marc-garcia-quirantes:55555/?students'); //Convierte la información de la URL en cadena
							$htmlp = json_decode($html);

							foreach($htmlp as $item){
								if($item->uid == $pass && $item->name == $user){
									$_SESSION['usuar'] = $user;
									echo "Bienvenido\a $user";
									$logincomp=1;									
									header("Location: http://localhost/project2");
									exit();
								}
							}
							if ($logincomp==0){
								echo "Usuario o contraseña desconocidos, vuelva a intentarlo";
							}	
						}
					?>
						
					<ul class="error" id="error"></ul>
				</div>
		</form>
	</body>

</html>

