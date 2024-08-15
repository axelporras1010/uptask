<?php

namespace Model;

class Usuario extends ActiveRecord 
{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $token;
    public $confirmado;
    public $password_actual;
    public $password_nuevo;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password-actual'] ?? '';
        $this->password_nuevo = $args['password-nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    public function validarLogin()
    {
        if(!$this->email) self::$alertas['error'][] = 'El correo del usuario es obligatorio';   
        if(!$this->password) self::$alertas['error'][] = 'El password no puede ser vacio';
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) self::$alertas['error'][] = 'El email no es valido';

        return self::$alertas;
    }

    public function validarNuevaCuenta()
    {
        if(!$this->nombre) self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        if(!$this->email) self::$alertas['error'][] = 'El correo del usuario es obligatorio';

        if(!$this->password) self::$alertas['error'][] = 'El password no puede ser vacio';
        if(strlen($this->password) < 6) self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        if($this->password != $this->password2 ) self::$alertas['error'][] = 'Los passwords son diferentes';

        return self::$alertas;
    }

    
    public function comprobarPassword() : bool
    {
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() : void
    {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    //Generar un token
    public function crearToken() : void
    {
        $this->token = uniqid();
    }

    //valida un email
    public function validarEmail() : array
    {
        if(!$this->email) self::$alertas['error'][] = 'El email es obligatorio';
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) self::$alertas['error'][] = 'El email no es valido';

        return self::$alertas;
    }

    //Valida el password
    public function validarPassword() : array
    {
        if(!$this->password) self::$alertas['error'][] = 'El password no puede ser vacio';
        if(strlen($this->password) < 6) self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        if($this->password != $this->password2 ) self::$alertas['error'][] = 'Los passwords son diferentes';

        return self::$alertas;
    }

    public function validarPerfil() : array
    {
        if(!$this->nombre) self::$alertas['error'][] = 'El nombre del usuario es obligatorio';
        if(!$this->email) self::$alertas['error'][] = 'El email es obligatorio';

        return self::$alertas;
    }

    public function nuevoPassword() : array
    {
        if(!$this->password_actual) self::$alertas['error'][] = 'El password actual es obligatorio';
        if(!$this->password_nuevo) self::$alertas['error'][] = 'El password nuevo es obligatorio';
        if(strlen($this->password_nuevo) < 6) self::$alertas['error'][] = 'El password nuevo debe contener al menos 6 caracteres';

        return self::$alertas;
    }
    
}