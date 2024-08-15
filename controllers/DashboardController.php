<?php

namespace Controllers;

use MVC\Router;
use Model\Usuario;
use Model\Proyecto;

class DashboardController
{
    public static function index(Router $router)
    {
        session_start();
        isAuth();

        $id = $_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId', $id);

        $router->render('dashboard/index', [
            'titulo' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $proyecto = new Proyecto($_POST);
            
            //Validacion
            $alertas = $proyecto->validarProyecto();
            
            if(empty($alertas))
            {
                //Generar una url unica
                $proyecto->url = md5(uniqid());

                //Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                //Guardar el  proyecto
                $proyecto->guardar();

                //Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url );
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'titulo' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function perfil(Router $router)
    {
        session_start();
        isAuth();
        $alertas = [];

        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarPerfil();

            if(empty($alertas))
            {
                //Verificar que el email no existe
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario && $existeUsuario->id != $usuario->id)
                {
                    //Mensaje de error
                    Usuario::setAlerta('error', 'El email ya ha sido registrado');
                    $alertas = Usuario::getAlertas();
                }
                else
                {
                    //Guardar el registro
                    $usuario->guardar();

                    Usuario::setAlerta('exito', 'Guardado correctamente');
                    $alertas = Usuario::getAlertas();
    
                    //Asignar el nombre nuevo a la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }
                
            }
        }

        $router->render('dashboard/perfil', [
            'titulo' => 'Perfil',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router)
    {
        session_start();
        isAuth();

        $token = $_GET['id'];
        if(!$token) header('Location: /dashboard');
        //Revisar que la persona que visita el proyecto es quien lo creo
        $proyecto = Proyecto::where('url', $token);
        if($proyecto->propietarioId != $_SESSION['id']) header('Location: /dashboard');

        $router->render('dashboard/proyecto',  [
            'titulo' => $proyecto->proyecto
        ]);
    }

    public static function cambiar_password(Router $router)
    {
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario = Usuario::find($_SESSION['id']);

            //Sincronizar con los datos del usuario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevoPassword();

            if(empty($alertas))
            {
                $resultado = $usuario->comprobarPassword();

                if($resultado)
                {
                    //Asignar el nuevo password
                    $usuario->password = $usuario->password_nuevo;

                    //Eliminar propiedades no necesarias
                    unset($usuario->password_actual);
                    unset($usuario->password_nuevo);

                    //Hashear el nuevo password
                    $usuario->hashPassword();

                    //Actualizar en la BD
                    $resultado = $usuario->guardar();

                    if($resultado)
                    {
                        Usuario::setAlerta('exito', 'Password nuevo guardado correctamente');
                    }
                }
                else
                {
                    Usuario::setAlerta('error', 'Password actual incorrecto');
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        $router->render('dashboard/cambiar-password', [
            'titulo' => 'Cambiar password',
            'alertas' => $alertas
        ]);
    }
}