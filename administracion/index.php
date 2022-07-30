<?php
include_once '../includes/constants.php';

$accion = isset($_GET['accion'])?$_GET['accion']:"";
$publicaciones = new publicaciones();
switch ($accion) {
    
    // <editor-fold defaultstate="collapsed" desc="login">
    case "login":
        $administrador = Array();
        $login = new administrador();
        if (isset($_POST['usuario']) && isset($_POST['password'])) {
            $administrador = $login->login($_POST['usuario'], $_POST['password']);
        }
        echo $twig->render('administracion/index.html.twig', Array("administrador" => $administrador));
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="inactivar">
    case "inactivar":
        if (isset($_GET['id'])) {
            $resultado = $publicaciones->actualizar($_GET['id'], Array("inactivo" => 1));
            if ($resultado['suceed']) {
                $resultado['mensaje'] = "(".$resultado['stats']['affected_rows'].") Publicación inactivada) con éxito.";
            } else {
                $resultado['mensaje'] = "Ups! ocurrió un error al tratar de inactivar la publicación.";
            }
            
        }
        $listado = $publicaciones->obtenerPublicaciones();
        echo $twig->render('administracion/lista-publicaciones.html.twig', Array(
            "listado" => $listado['data'],"resultado"=>$resultado
        ));
        break;
    // </editor-fold>
     
    // <editor-fold defaultstate="collapsed" desc="publicaciones">
    case "publicaciones":
        $listado = $publicaciones->obtenerPublicaciones();
        echo $twig->render('administracion/lista-publicaciones.html.twig', Array(
            "listado" => $listado['data']
        ));
        break; 
    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="publicar">
    case "publicar":
        $db = new db();
        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion = $db->dame_query("select * from operaciones where id > 1 order by descripcion");
        echo $twig->render('administracion/registrar-propiedad.html.twig', Array(
            "municipios" => $municipios['data'],
            "tipo" => $tipo['data'],
            "operacion" => $operacion['data']));
        break; // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="guardar">
    case "guardar":
        $dir = "images/prv/estate/";
        $db = new db();
        $resultado = Array();
        $data = $_POST;

        if (isset($data['imagen'])) {
            $imagenes = $data['imagen'];
            unset($data['imagen']);
        }
        // editar
        if (isset($data['editar']) && $data['editar'] == 'editar') {
            unset($data['editar']);
            $id = $data['id'];
            $resultado = $publicaciones->actualizar($id, $data);
            if ($resultado['suceed']) {
                $resultado['mensaje'] = 'Publicación actualizada con éxito!';
            } else {
                $resultado['mensaje'] = 'Ups! Ocurrió un error durante el proceso. No se puedo actualizar la información';
            }
            $db->delete("inmobiliaria_img", Array("id_inmobiliaria_publicacion" => $id));
        } else {
            // guardar
            $resultado = $publicaciones->insertar($data);
            if ($resultado['suceed']) {
                $id = $resultado['insert_id'];
                $resultado['mensaje'] = 'Publicación guardada con éxito';
            } else {
                $resultado['mensaje'] = 'Ups! Ocurrió un error durante el proceso. No se puedo guardar la información';
            }
        }
        if ($resultado['suceed']) {
            if (isset($imagenes)) {
                $i = 0;
                foreach ($imagenes as $imagen) {
                    if ($i == 0) {
                        $resultado = $publicaciones->actualizar($id, Array("imagen" => $dir . $imagen));
                    } else {
                        $db->insert("inmobiliaria_img", Array("id_inmobiliaria_publicacion" => $id, "imagen" => $dir . $imagen));
                    }
                    $i++;
                }
            }
        }

        $propiedad = $publicaciones->ver($id);

        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion = $db->dame_query("select * from operaciones where id > 1 order by descripcion");


        echo $twig->render('administracion/registrar-propiedad.html.twig', Array(
            "municipios" => $municipios['data'],
            "tipo" => $tipo['data'],
            "operacion" => $operacion['data'],
            "propiedad" => $propiedad['data'],
            "accion" => "editar",
            "resultado" => $resultado));


        break; // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="editar">
    case "editar":
        $db = new db();
        
        $municipios = $db->dame_query("select * from municipios where id > 1 order by descripcion ");
        $tipo = $db->dame_query("select * from inmobiliaria_tipo where id > 1 order by descripcion");
        $operacion = $db->dame_query("select * from operaciones where id > 1 order by descripcion");

        $propiedad = $publicaciones->ver($_GET['id']);

        if ($propiedad['suceed'] && count($propiedad['data']) > 0) {
            for ($index = 0; $index < count($propiedad['data']); $index++) {
                $imagenes = $publicaciones->obtenerImagenesPorPublicacion($propiedad['data'][$index]['id']);
                $propiedad['data'][$index]['imagenes'] = $imagenes['data'];
            }
            $publicaciones->actualizar($_GET['id'], Array("visto" => $propiedad['data'][0]['visto'] + 1));
        }


        echo $twig->render('administracion/registrar-propiedad.html.twig', Array(
            "municipios" => $municipios['data'],
            "tipo" => $tipo['data'],
            "operacion" => $operacion['data'],
            "propiedad" => $propiedad['data'],
            "accion" => "editar"));


        break; // </editor-fold>

    default :
        echo $twig->render('administracion/index.html.twig');
        break;
}
        


