<?php
use Picqer\Barcode\BarcodeGeneratorHTML;

$generator = new BarcodeGeneratorHTML();
?>

<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="container">
    <h1>Cédulas de Votação</h1>

    <div class="row">
        <?php foreach ($cedulas as $cedula): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body mb-0">
                        <h5 class="card-title">Escola: <?php echo htmlspecialchars($cedula['escola_nome'], ENT_QUOTES, 'UTF-8'); ?></h5>

                        <form>
                            <?php foreach ($chapas as $chapaNome => $candidatosDaChapa): ?>
                                <div class="mb-3">
                                    <table class="table mb-0">
                                        <tr>
                                            <td width="40%">
                                                <div class="form-check">
                                                    <input class="form-check-input chapa-checkbox" type="checkbox" id="chapa-<?php echo htmlspecialchars($chapaNome, ENT_QUOTES, 'UTF-8'); ?>" />
                                                    <label class="form-check-label h5" for="chapa-<?php echo htmlspecialchars($chapaNome, ENT_QUOTES, 'UTF-8'); ?>">
                                                        <?php echo htmlspecialchars($chapaNome, ENT_QUOTES, 'UTF-8'); ?>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled mb-0">
                                                    <?php foreach ($candidatosDaChapa as $candidato): ?>
                                                        <li class="mb-1 d-flex align-items-center">
                                                            <img src="<?php __DIR__ ?>/assets/images/user-perfil.png" alt="Foto do Candidato" class="candidate-photo me-2" style="width: 40px; height: 40px">
                                                            <div>
                                                                <h6 class="mb-0"><?php echo htmlspecialchars($candidato['candidato_nome'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                                                <small class="text-muted"><?php echo htmlspecialchars($candidato['cargo'], ENT_QUOTES, 'UTF-8'); ?></small>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        </form>

                        <div class="text-center">
                            <p>Código de Segurança: <?php echo htmlspecialchars($cedula['codigo_seguranca'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="mx-auto">
                                <?php echo $generator->getBarcode($cedula['codigo_seguranca'], $generator::TYPE_CODE_128); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endSection(); ?>

<?php extend('layouts/site'); ?>
