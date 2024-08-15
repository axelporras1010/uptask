<?php

namespace Controllers;

use MVC\Router;
use Classes\Email;
use Model\Usuario;

class LoginController
{
    public static function login(Router $router)
    {
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();
            
            if(empty($alertas))
            {
                //Verificar que el user exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado)
                {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
                else
                {
                    if( password_verify($_POST['password'], $usuario->password) )
                    {
                        //Iniciar sesion
                        session_start();
                        unset($_SESSION['admin']);
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        header('Location: /dashboard');
                    }
                    else
                    {
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        //Render a la vista
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas
        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        header('Location: /');
    }

    public static function crear(Router $router)
    {   

        $usuario = new Usuario();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas))
            {
                $existeUsuario = Usuario::where('email', $usuario->email);
            
                if($existeUsuario)
                {
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                }
                else
                {   
                    //Hashear el password
                    $usuario->hashPassword();
                    //Eliminar password2
                    unset($usuario->password2);
                    //Generar el token
                    $usuario->crearToken();
                    //Guardar usuario en la BD

                    $resultado = $usuario->guardar();
                    
                    //Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    
                    if($resultado)
                    {
                        header('Location: /mensaje');
                    }
                }
            }
        }

        //Render a la vista
        $router->render('auth/crear', [
            'titulo' => 'Crea tu cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router)
    {
        
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas))
            {
                //Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);
                if($usuario && $usuario->confirmado)
                {
                    //Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    //Actualizar el usuario
                    $usuario->guardar();
                    //Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarRecuperacion();
                    //Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');
                }
                else
                {
                    Usuario::setAlerta('error', 'El usuario no existe o no esta confirmado');
                }
            }
        }
        $alertas = Usuario::getAlertas();
        //Render a la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvidaste tu password?',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router)
    {

        $token = s($_GET['token']);
        if(!$token) header('Location: /');
        $mostrar = true;

        //Identificar el usuario con el token
        $usuario = Usuario::where('token', $token);
        if(empty($usuario))
        {
            Usuario::setAlerta('error', 'Token no valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            //Agregar el nuevo password
            $usuario->sincronizar($_POST);
            
            //Validar password
            $alertas = $usuario->validarPassword();
            
            if(empty($alertas))
            {
                //Hashear el password
                $usuario->hashPassword();
                //Eliminar el token
                $usuario->token = null;
                //Guardar en BD
                $resultado = $usuario->guardar();
                //Redireccionar
                if($resultado) header('Location: /');
            }
        }

        $alertas = Usuario::getAlertas();
        //Render a la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router)
    {
        //Render a la vista
        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta creada exitosamente'
        ]);
    }

    public static function confirmar(Router $router)
    {
        $token = s($_GET['token']);

        if(!$token) header('Location: /');

        //Encontrar al usuario con el token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)) 
        {
            //No se encontro usuario
            Usuario::setAlerta('error', 'Token no valido');
        }
        else
        {
            //Confirmar usuario
            $usuario->confirmado = 1;
            unset($usuario->password2);
            $usuario->token = null;

            //Guardar en la base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta creada correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }

}