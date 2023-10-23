<main class="container">
    <div class="card">
        <div class="card-header">
            <h3 class="text-center">Verificar Cuenta</h3>
        </div>
        <div class="card-body">
            <form action="" method="post" id="usuarioDocumentacion">
                <div class="mb-3">
                    <label for="documentacion" class="form-label">documentacion</label>
                    <input type="file" class="form-control" name="documentacion" id="documentacion" aria-describedby="usuarioHelp">
                    <div id="emailHelp" class="form-text">Por favor ingrese una foto de su documento.</div>
                </div>
                <div class="col-md-12 d-none">
                    <label for="textID" class="form-label">ID</label>
                    <input type="text" class="form-control" value="<?= $parameters['intereses'] ?? '' ?>" name="textID" id="textID" placeholder="ID">
                </div>
                <button type="submit" class="btn btn-success">Enviar Solicitud</button>
                <a id="" class="btn btn-primary" href="<?=URL_PATH.'/Usuario/home/';?>" role="button">Cancelar</a>
            </form>
        </div>
    </div>
</main>

<script src="<?= URL_PATH?>/Assets/js/usuarioDocumentacion.js"></script>