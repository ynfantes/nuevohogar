<?php
include_once '../includes/constants.php';


//echo $twig->render('mantenimiento.html.twig');
//die();


$propietario = new propietario();

$result = array();
$password = '';
$apto = '';

if (isset($_POST['cedula']) && isset($_POST['password'])) {

//if (isset($_POST['submit'])) {
    $apto = $_POST['cedula'];
    $password = $_POST['password'];
    
    $result = $propietario->login($apto,$password, 0);
    
//    if ($result['suceed']=='true') {
//        
//        if ($_SESSION['status'] == 'logueado') {
//            header("location:" . URL_SISTEMA );
//            die();
//        }
//    } else {
        echo json_encode($result);
//    }
} else {
    if (isset($_POST['cedula'])) {
        
        $result = $propietario->recuperarContraSena($_POST['cedula']);
        echo json_encode($result);
    }
}

//echo $twig->render('index.html.twig', array("mensaje" => $result,"apto"=>$apto,"password"=>$password));