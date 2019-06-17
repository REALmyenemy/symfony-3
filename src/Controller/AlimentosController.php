<?php
// src/Controller/AlimentosController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Model\Model;
use App\Config\Config;
use Fpdf\Fpdf;

session_start();

class AlimentosController extends AbstractController
{


	public function inicio()
	{
		$params = array(
			'mensaje' => 'Bienvenido al curso de Symfony2',
			'fecha' => date('d-m-y'),
		);

		return $this->render('alimentos/inicio.html.twig',$params);
	}

	public function listar()
	{
		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario, Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		$params = array(
			'alimentos' => $m->dameAlimentos(),
		);
		if (isset($_SESSION["usu"]))
			return $this->render('alimentos/listar.html.twig', $params);
		else
			return $this->render('alimentos/listarB.html.twig', $params);
	}

	public function insertar()
	{
		$params = array(
		'nombre' => '',
		'energia' => '',
		'proteina' => '',
		'hc' => '',
		'fibra' => '',
		'grasa' => '',
		'calificacion'=>''
		);

		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			// comprobar campos formulario
			if ($m->insertarAlimento($_POST['nombre'], $_POST['energia'],$_POST['proteina'], $_POST['hc'], $_POST['fibra'], $_POST['grasa'], $_POST['calificacion']))
			{
				$params['mensaje'] = 'Alimento insertado correctamente';
			}
			else
			{
				$params = array(
				'nombre' => $_POST['nombre'],
				'energia' => $_POST['energia'],
				'proteina' => $_POST['proteina'],
				'hc' => $_POST['hc'],
				'fibra' => $_POST['fibra'],
				'grasa' => $_POST['grasa'],
				'calificacion' => $_POST['calificacion'],
				);
				$params['mensaje'] = 'No se ha podido insertar el alimento. Revisa el formulario';
			}
		}

		return $this->render('alimentos/formInsertar.html.twig', $params);

	}

	public function editar()
	{
		if (isset($_SESSION["usu"])){
		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$id = $_GET['id'];

		$alimento = $m->dameAlimento($id);

		$params = $alimento;
		return $this->render('alimentos/formEditar.html.twig', $params);
		}
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {

			// comprobar campos formulario
			if ($m->editarAlimento($_POST['nombre'], $_POST['energia'],$_POST['proteina'],	$_POST['hidratocarbono'],$_POST['fibra'],$_POST['grasatotal'],$_POST['calificacion'],$_POST['id']))
			{
				return $this->redirectToRoute('app_alimentos_listar');
			}
			else
			{
				$params = array(
				'nombre' => $_POST['nombre'],
				'energia' => $_POST['energia'],
				'proteina' => $_POST['proteina'],
				'hidratocarbono' => $_POST['hidratocarbono'],
				'fibra' => $_POST['fibra'],
				'grasatotal' => $_POST['grasatotal'],
				'calificacion' => $_POST['calificacion'],
				'id' => $_POST['id'],
				);
				$params['mensaje'] = 'No se ha podido insertar el alimento. Revisa el formulario';
			}
		}

		}

	}

	public function buscarPorNombre()
	{
		$params = array(
			'nombre' => '',
			'resultado' => array(),
		);

		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$params['nombre'] = $_POST['nombre'];
			$params['resultado'] = $m->buscarAlimentosPorNombre($_POST['nombre']);
		}

		return	$this->render('alimentos/buscarPorNombre.html.twig',$params);
	}

	public function buscarPorEnergia()
	{
		$params = array(
			'energia' => '',
			'resultado' => array(),
		);

		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$params['energia'] = $_POST['energia'];
			$params['resultado'] = $m->buscarAlimentosPorEnergia($_POST['energia']);
		}

		return	$this->render('alimentos/buscarPorEnergia.html.twig',$params);

	}

	public function busquedaCombinada()
	{
		$params = array(
			'nombre' => '',
			'energia' => '',
			'resultado' => array()
		);

		$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$params['nombre'] = $_POST['nombre'];
			$params['energia'] = $_POST['energia'];
			$params['resultado'] = $m->buscarAlimentosCombinada($_POST['energia'],$_POST['nombre']);
		}

		return	$this->render('alimentos/busquedaCombinada.html.twig',$params);

	}

	public function borrar()
	{
		if (!isset($_GET['id'])||!isset($_SESSION["usu"]))
		{
			throw new Exception('Página no encontrada');
		}
		else
		{
			$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);
			$m->borrarAlimento($_GET['id']);
			return $this->redirectToRoute('app_alimentos_listar');
		}
	}

	public function ver()
	{
		if (!isset($_GET['id'])) {
			throw new Exception('Página no encontrada');
		}
		else
			
			$id = $_GET['id'];

			$m = new Model(Config::$mvc_bd_nombre, Config::$mvc_bd_usuario,	Config::$mvc_bd_clave, Config::$mvc_bd_hostname);

			$alimento = $m->dameAlimento($id);

			$params = $alimento;
			

			if (isset($_GET['ext']))
			{
				switch(strtolower($_GET['ext']))
				{
					case 'xml':
						$response=$this->render('alimentos/verAlimento.xml.twig', $params);
						$response->headers->set('Content-Type', 'text/xml');

						break;
					case 'pdf':
						$pdf=$m->pdfTest($this->render('alimentos/verAlimento.html.twig', $params));
						
						break;
					default:
						throw new Exception('Página no encontrada');
				}
			}
			else
			{
				$response=$this->render('alimentos/verAlimento.html.twig', $params);
			}

			return $response;
	}

	public function doLogin()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			if (isset($_POST['usuario'])&&isset($_POST['pass']))
				if ($_POST['usuario']=="admin"&&$_POST['pass']=="admin")
				{
					$_SESSION["usu"]="admin";
					$session = $this->get('session');
					$session->set('usu', 'admin');
					return $this->inicio();
				}
				else
					$response=$this->render('alimentos/conectar.html.twig', array('mensaje' => 'Usuario/contraseña equivocado/s'));
			else
				$response=$this->render('alimentos/conectar.html.twig', array('mensaje' => 'Introduce usuario y contraseña'));
		}
		else
			$response=$this->render('alimentos/conectar.html.twig', array('mensaje' => ''));
		return $response;
	}

}
