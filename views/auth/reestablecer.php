
<div class="contenedor reestablecer">
    
    <?php include_once __DIR__ . '../../templates/nombre-sitio.php'; ?>
    
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo password</p>
        <?php include_once __DIR__ . '../../templates/alertas.php'; ?>
        <?php if($mostrar): ?>
        <form method="POST" class="formulario">
            <div class="campo">
                <label for="password">Nuevo Password</label>
                <input 
                    type="password"
                    id="password"
                    placeholder="Tu Password"
                    name="password"
                />
            </div>
            <div class="campo">
                <label for="password2">Confirma Nuevo Password</label>
                <input 
                    type="password"
                    id="password2"
                    placeholder="Confirma Tu Password"
                    name="password2"
                />
            </div>
            <input type="submit" class="boton" value="Guardar password">
        </form>
        <?php endif; ?>
        <div class="acciones">
            <a href="/crear">Aun no tienes una cuenta? Obtener una</a>
            <a href="/olvide">Olvidaste tu password</a>
        </div>
    </div> <!-- Contenedor parcial -->
</div>