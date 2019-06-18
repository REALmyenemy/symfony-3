<?php


	namespace App\Model;
	
	class Model
	{
		protected $conexion;

		public function __construct($dbname,$dbuser,$dbpass,$dbhost)
		{
		$mvc_bd_conexion = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

		if (!$mvc_bd_conexion) {
			die('No ha sido posible realizar la conexión con la base de datos: ' . mysqli_error());
		}
		

		mysqli_set_charset($mvc_bd_conexion,'utf8');

		$this->conexion = $mvc_bd_conexion;
		}



		public function bd_conexion()
		{

		}

		public function dameAlimentos()
		{
			$sql = "select * from alimentos order by energia desc";

			$result = mysqli_query($this->conexion,$sql);

			$alimentos = array();
			while ($row = mysqli_fetch_assoc($result))
			{
				$alimentos[] = $row;
			}

			return $alimentos;
		}

		public function buscarAlimentosPorNombre($nombre)
		{
			$nombre = htmlspecialchars($nombre);

			$sql = "select * from alimentos where nombre like '" . $nombre . "' order by energia desc";

			$result = mysqli_query($this->conexion,$sql);

			$alimentos = array();
			while ($row = mysqli_fetch_assoc($result))
			{
				$alimentos[] = $row;
			}

			return $alimentos;
		}

		public function buscarAlimentosPorEnergia($energia)
		{
			$energia = htmlspecialchars($energia);

			$sql = "select * from alimentos where energia=". $energia . " order by energia desc";

			$result = mysqli_query( $this->conexion,$sql);

			$alimentos = array();
			while ($row = mysqli_fetch_assoc($result))
			{
				$alimentos[] = $row;
			}

			return $alimentos;
		}
		
		public function buscarAlimentosCombinada($energia,$nombre)
		{
			$energia = htmlspecialchars($energia);
					$nombre = htmlspecialchars($nombre);
			$sql = "select * from alimentos where energia=". $energia . " and nombre like '" . $nombre . "' order by nombre desc";

			$result = mysqli_query($this->conexion,$sql );

			$alimentos = array();
			while ($row = mysqli_fetch_assoc($result))
			{
				$alimentos[] = $row;
			}

			return $alimentos;
		}

	public function buscarUsuario($nombre,$pass)
		{
			$nombre = htmlspecialchars($nombre);
		$pass = htmlspecialchars($pass);
			$sql = "select * from usuarios where nombre='". $nombre . "' and pass='" . $pass . "'";

			$result = mysqli_query($this->conexion,$sql );

			$usuarios = array();
			while ($row = mysqli_fetch_assoc($result))
			{
				$usuarios[] = $row;
			}

			return $usuarios;
		}

		public function dameAlimento($id)
		{
			$id = htmlspecialchars($id);

			$sql = "select * from alimentos where id=".$id;

			$result = mysqli_query( $this->conexion,$sql);

			$alimentos = array();
			$row = mysqli_fetch_assoc($result);

			return $row;

		}

		public function pdfTest($container)
		{
			// $pdf = $container->get("white_october.tcpdf")->create();

			// $pdf->AddPage();
			// $response = new Response(
			// 	$pdf->Output("archivo.pdf",'I'),
			// 	Response::HTTP_OK,	array('content-type' => 'application/pdf')
			// );
			$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


			return $pdf;
		}


		public function insertarAlimento($n, $e, $p, $hc, $f, $g, $c)
		{
				$n = htmlspecialchars($n);
				$e = htmlspecialchars($e);
				$p = htmlspecialchars($p);
				$hc = htmlspecialchars($hc);
				$f = htmlspecialchars($f);
				$g = htmlspecialchars($g);
				$c = htmlspecialchars($c);

				$sql = "insert into alimentos
				(nombre,		energia,	 proteina, hidratocarbono, fibra, grasatotal, calificacion) values
				('" .$n . "'," .$e . "," .  $p . "," . $hc . "," .     $f . "," . $g . ",'" . "$c"."')";
				

				$result = mysqli_query($this->conexion,$sql);

				return $result;
			
		}

		public function editarAlimento($n, $e, $p, $hc, $f, $g, $c,$i)
		{
			$n = htmlspecialchars($n);
			$e = htmlspecialchars($e);
			$p = htmlspecialchars($p);
			$hc = htmlspecialchars($hc);
			$f = htmlspecialchars($f);
			$g = htmlspecialchars($g);
			$c = htmlspecialchars($c);

			$sql = "update alimentos set
			nombre='".$n."',
			energia=".$e.",
			proteina=".$p.",
			hidratocarbono=".$hc.",
			fibra=".$f.",
			grasatotal=".$g.",
			calificacion='".$c."'
			where id=".$i;

			$result = mysqli_query($this->conexion,$sql);

			return $result;
		
		}
	
		public function borrarAlimento($id)
		{
			$result = mysqli_query($this->conexion, "delete from alimentos where id=".$id);
		}

		public function validarDatos($n, $e, $p, $hc, $f, $g, $c)
		{
			return (is_string($n) & is_numeric($e) & is_numeric($p) & is_numeric($hc) & is_numeric($f) & is_numeric($g) & isCertainLetter($c));
		}
		
		public function isCertainLetter($c) //No quiere coger la función
		{
			return $c=="A"||$c=="B"||$c=="C"||$c=="D"||$c=="";
		}

	}
?>