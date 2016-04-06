<?php
/*
 * seriesposter.php
 * 
 * Copyright 2016 diego <diego@mitica>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

echo "||||||  ||||||  ||||||  ||||  ||||||  ||||||\n";
echo "||      ||   |  ||  ||   ||   ||   |  ||\n";
echo "||||||  ||||    ||||||   ||   ||||    ||||||\n";
echo "    ||  ||   |  || ||    ||   ||   |      ||\n";
echo "||||||  ||||||  ||  ||  ||||  ||||||  ||||||\n";
echo "||||||  ||||||  |||||| |||||| ||||||  ||||||\n";
echo "||  ||  ||  ||  ||       ||   ||   |  ||  ||\n";
echo "||||||  ||  ||  ||||||   ||   ||||    ||||||\n";
echo "||      ||  ||      ||   ||   ||   |  || ||\n";
echo "||      ||||||  ||||||   ||   ||||||  ||  ||v2.1\n";

echo "Ingresar direcotrio raiz donde se encuentran las SERIES: ";
$raiz = trim(fgets(STDIN));
$ultima = substr($raiz, -1);
if ( $ultima == '/') {
	$dir = $raiz;
}else{
	$dir = $raiz.'/';
}
## $dir = 'C:/xampp/htdocs/www/seriestv/tvseries/';

function nombreTemporada($nombre,$ser){
	$t = strtolower($nombre);
	$patronBorrar = array("season","temp","temporada"," ","_",$ser);
	$nombreT = str_replace($patronBorrar,"",$t);
	return $nombreT;
}

function download($direccion,$descargado){

	$config['useragent'] = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, $config['useragent']);
	curl_setopt($ch, CURLOPT_URL, $direccion); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_NOPROGRESS, true);

	$json = curl_exec($ch);
	if ($errno = curl_errno($ch)) {
		echo json_encode(array('error'=>"volver a intentarlo \n"));
		exit;
	}
	curl_close($ch);
	$obj = json_decode($json, true);
	if ($obj['resultCount']==0) {
		echo "No se encuentra cover\n";
	}else{
	foreach ($obj as $objeto) {
		$imagen = $objeto[0]['artworkUrl100'];
	}
	$imagengrande = str_replace("100x100bb", "300x300", $imagen);
		$img = $descargado."/cover.jpg";
//la descarga se da aca
	//echo "serie: ".$imagengrande."\n";
	file_put_contents($img, file_get_contents($imagengrande));

}
	

}


function descargaCover($series,$temp,$paths){
	$cambiosNombre = array('/','-','.','_',' ');
	$buscado = str_replace($cambiosNombre, '+', $series);
	if (empty($temp)) {
		$url = 'https://itunes.apple.com/search?term='.$buscado.'&entity=tvSeason';
		if (file_exists($paths.$series.'/cover.jpg')) {
			echo 'Existe cover de: '.$series."\n";
		}else{
			
			echo 'Descargando '.$series."\n";
			$desc= $paths.$series;
			download($url,$desc);

		}
	}else{
		$nombreT = nombretemporada($temp,$series);
		$url = 'https://itunes.apple.com/search?term='.$buscado.'+season+'.(int)$nombreT.'&entity=tvSeason';
		if (file_exists($paths.$series.'/'.$temp.'/cover.jpg')) {
			echo "Existe cover de Temporada ".$nombreT." de ".$series."\n";
		}else{

			echo "Descargando Temporada ".$nombreT." de ".$series."\n";
			$desc= $paths.$series."/".$temp;
			download($url,$desc);
		}
	}
}


 
function scandirectorio($path){ //Escanea los directorios $path= al directorio raiz de las series
	$estructuraSeries = array_values(array_diff(scandir($path),array('..','.','cover.jpg'))); // genera array de dir con nombre serie
	foreach ($estructuraSeries as $serie) { //recorre el directorio base de series
		$temporada=0;	
		if (is_dir($path.$serie)) { // si es un directorio y no un archivo ejecuto busqueda
																					
			$directorio=array_values(array_diff(scandir($path.$serie), array('..','.','cover.jpg'))); // genera array de dir con nombre 																								temporada
			//echo "Serie: ".$serie."\n";
			descargaCover($serie,$temporada,$path);
			foreach ($directorio as $temporada) { // recorre el directorio para sus temporadas
				if (is_dir($path.$serie.'/'.$temporada)) { // si es un dir y no un archivo ejecuto busqueda
					//echo "\t Temporada: ".$temporada."\n";
					descargaCover($serie,$temporada,$path);
				}
				
			}
		}
		
	}
}

scandirectorio($dir);

?>
