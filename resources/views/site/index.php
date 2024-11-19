<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php startSection('content'); ?>
<div class="container my-5">
    <!-- Título e Descrição -->
    <div class="text-center mb-5">
        <h1 class="display-4">Informações das Eleições</h1>
        <p class="lead">Explore as informações completas sobre as escolas participantes, seus candidatos, e o total de eleitores por segmento.</p>
    </div>

    <!-- Informações Gerais sobre Escolas, Candidatos e Eleitores -->
    <div class="mb-5">
        <h2 class="h4">Resumo Geral</h2>
        <p class="text-center">Esta seção contém um resumo geral das escolas, candidatos e eleitores. Utilize as seções abaixo para explorar detalhes específicos.</p>
        
        <div class="row">
            <!-- Card para Total de Escolas -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total de Escolas</h5>
                        <p class="card-text display-6"><?php echo count($escolas); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Card para Total de Chapas -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total de Chapas</h5>
                        <p class="card-text display-6">
                            <?php 
                            $total_chapas = array_reduce($escolas, function($carry, $candidatos) {
                                $chapas = array_column($candidatos, 'chapa');
                                return $carry + count(array_unique($chapas));
                            }, 0);
                            echo $total_chapas;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Card para Total de Candidatos -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total de Candidatos</h5>
                        <p class="card-text display-6">
                            <?php 
                            $total_candidatos = array_reduce($escolas, function($carry, $candidatos) {
                                return $carry + count($candidatos);
                            }, 0);
                            echo $total_candidatos;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Card para Total de Eleitores -->
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Total de Eleitores</h5>
                        <p class="card-text display-6">
                            <?php 
                            $total_eleitores = array_reduce($eleitores, function($carry, $segmentos) {
                                return $carry + array_sum($segmentos);
                            }, 0);
                            echo $total_eleitores;
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php foreach ($escolas as $escola_nome => $candidatos): ?>
        <div class="row mb-5">
            <!-- Escola e Candidatos -->
            <div class="col-md-6">
                <h3 class="h5">Escola: <?php echo $escola_nome; ?></h3>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Chapa</th>
                            <th>Cargo</th>
                            <th>Candidato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Agrupa candidatos por chapa
                        $chapas = [];
                        foreach ($candidatos as $candidato) {
                            $chapas[$candidato['chapa']][] = $candidato;
                        }

                        // Itera sobre as chapas
                        foreach ($chapas as $chapa => $candidatosDaChapa): 
                            // Ordena por cargo, assumindo "Diretor" deve vir antes de "Diretor Adjunto"
                            usort($candidatosDaChapa, function($a, $b) {
                                return strcmp($a['cargo'], $b['cargo']);
                            });
                            ?>
                            <tr>
                                <td rowspan="<?php echo count($candidatosDaChapa); ?>"><?php echo $chapa; ?></td>
                                <?php foreach ($candidatosDaChapa as $index => $candidato): ?>
                                    <?php if ($index > 0): ?>
                                        <tr>
                                    <?php endif; ?>
                                    <td><?php echo $candidato['cargo']; ?></td>
                                    <td><?php echo $candidato['candidato_nome']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Eleitores por Segmento -->
            <div class="col-md-6">
                <h4 class="h5">Total de Eleitores por Segmento</h4>
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Segmento</th>
                            <th>Total de Eleitores</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($eleitores[$escola_nome])): ?>
                            <?php foreach ($eleitores[$escola_nome] as $segmento => $total): ?>
                                <tr>
                                    <td><?php echo ucfirst($segmento); ?></td>
                                    <td><?php echo $total; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2">Sem eleitores registrados</td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td><strong>Total Geral</strong></td>
                            <td><strong><?php echo $totaisPorEscola[$escola_nome] ?? 0; ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endSection(); ?>

<?php extend('layouts/site'); ?>
