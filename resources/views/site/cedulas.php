<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="cedula">
    <h3>Cédula de Votação - <?php echo $escola_nome; ?></h3>
    <p>Código de Segurança: <?php echo $codigo_seguranca; ?></p>
    <form>
        <ul>
            <?php foreach ($chapas as $chapa => $candidatosDaChapa): ?>
                <li>
                    <strong>Chapa <?php echo $chapa; ?></strong>
                    <ul>
                        <?php foreach ($candidatosDaChapa as $candidato): ?>
                            <li>
                                <input type="checkbox" name="voto" value="<?php echo $candidato['id']; ?>" />
                                <?php echo $candidato['cargo'] . ': ' . $candidato['candidato_nome']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </form>
</div>
<?php endSection(); ?>

<?php extend('layouts/site'); ?>
