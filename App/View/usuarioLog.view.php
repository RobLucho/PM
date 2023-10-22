<div class="container mt-5">
    <br><br>
    <div class="row">
        <div class="col-4"></div>
        <div class="col-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-center">Iniciar Sesion</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post" id="usuarioLogin">
                        <div class="mb-3">
                            <label for="usuario" class="form-label">Usuario</label>
                            <input type="text" class="form-control" name="usuario" id="usuario" aria-describedby="usuarioHelp">
                        </div>
                        <div class="mb-3">
                            <label for="contrasena" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="contrasena" id="contrasena" aria-describedby="contrasenaHelp">
                        </div>
                        <button type="submit" class="btn btn-success">Ingresar</button>
                    </form>
                </div>        
            </div>
        </div>
    </div>
</div>

<script src="<?= URL_PATH?>/Assets/js/usuarioLog.js"></script>           