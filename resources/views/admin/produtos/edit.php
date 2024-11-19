<?php startSection('title'); ?>
Teste
<?php endSection(); ?>

<?php
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<?php startSection('content'); ?>
<div class="row">
    <div class="col-md-6">
        <h4 class="titulo-pagina mb-0">
            <span><i class="fa fa-plus fa-fw"></i> Produtos</span>
            <small>Editando produto</small>
        </h4>
    </div>
    <div class="col-md-6">
        <nav aria-label="breadcrumb" class="d-flex justify-content-end">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/admin/dashboard"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                <li class="breadcrumb-item"><a href="/admin/produtos/index">Produtos</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar</li>
            </ol>
        </nav>
    </div>
</div>
<small class="text-muted mb-2">Campo com (*) são obrigatório</small>

<form method="post" action="/admin/produtos/update/<?php echo $produto->id; ?>" class="mt-5">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">  

    <!-- Nome -->
    <div class="row mb-3">
        <label for="nome" class="col-sm-3 col-form-label text-end text-muted">Nome: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($produto->nome ?? ''); ?>">
            <?php if (!empty($errors['nome'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['nome']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Descrição -->
    <div class="row mb-3">
        <label for="descricao" class="col-sm-3 col-form-label text-end text-muted">Descrição: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <textarea class="form-control" id="descricao" name="descricao" rows="5"><?php echo htmlspecialchars($produto->descricao ?? ''); ?></textarea>
            <?php if (!empty($errors['descricao'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['descricao']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Preço -->
    <div class="row mb-3">
        <label for="preco" class="col-sm-3 col-form-label text-end text-muted">Preço: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="preco" name="preco" value="<?php echo htmlspecialchars($produto->preco ?? ''); ?>">
            <?php if (!empty($errors['preco'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['preco']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Preço Promocional -->
    <div class="row mb-3">
        <label for="preco_promocional" class="col-sm-3 col-form-label text-end text-muted">Preço Promocional: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="preco_promocional" name="preco_promocional" value="<?php echo htmlspecialchars($produto->preco_promocional ?? ''); ?>">
            <?php if (!empty($errors['preco_promocional'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['preco_promocional']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Código de Barras -->
    <div class="row mb-3">
        <label for="codigo_barras" class="col-sm-3 col-form-label text-end text-muted">Código de Barras:</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" id="codigo_barras" name="codigo_barras" value="<?php echo htmlspecialchars($produto->codigo_barras ?? ''); ?>">
            <?php if (!empty($errors['codigo_barras'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['codigo_barras']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estoque -->
    <div class="row mb-3">
        <label for="estoque" class="col-sm-3 col-form-label text-end text-muted">Estoque: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="number" class="form-control" id="estoque" name="estoque" value="<?php echo htmlspecialchars($produto->estoque ?? ''); ?>">
            <?php if (!empty($errors['estoque'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['estoque']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Imagem -->
    <div class="row mb-3">
        <label for="imagem" class="col-sm-3 col-form-label text-end text-muted">Imagem:</label>
        <div class="col-sm-7">
            <input type="file" class="form-control" id="imagem" name="imagem">
            <?php if (!empty($errors['imagem'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['imagem']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Categoria -->
    <div class="row mb-3">
        <label for="categoria_id" class="col-sm-3 col-form-label text-end text-muted">Categoria: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select class="form-select" id="categoria_id" name="categoria_id">
                <option value="">Selecione uma categoria</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?php echo $categoria->id; ?>" <?php echo (($produto->categoria_id ?? '') == $categoria->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categoria->nome); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['categoria_id'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['categoria_id']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Informações Relevantes -->
    <div class="row mb-3">
        <label for="informacoes_relevantes" class="col-sm-3 col-form-label text-end text-muted">Informações Relevantes: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <textarea class="form-control" id="informacoes_relevantes" name="informacoes_relevantes" rows="15"><?php echo htmlspecialchars($produto->informacoes_relevantes ?? ''); ?></textarea>
            <?php if (!empty($errors['informacoes_relevantes'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['informacoes_relevantes']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Data de Lançamento -->
    <div class="row mb-3">
        <label for="data_lancamento" class="col-sm-3 col-form-label text-end text-muted">Data de Lançamento: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="date" class="form-control" id="data_lancamento" name="data_lancamento" value="<?php echo htmlspecialchars($produto->data_lancamento ?? ''); ?>">
            <?php if (!empty($errors['data_lancamento'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['data_lancamento']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pontos -->
    <div class="row mb-3">
        <label for="pontos" class="col-sm-3 col-form-label text-end text-muted">Pontos: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <input type="number" class="form-control" id="pontos" name="pontos" value="<?php echo htmlspecialchars($produto->pontos ?? ''); ?>">
            <?php if (!empty($errors['pontos'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['pontos']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Promoção -->
    <div class="row mb-3">
        <label for="promocao" class="col-sm-3 col-form-label text-end text-muted">Promoção:</label>
        <div class="col-sm-7">
            <select class="form-select" id="promocao" name="promocao">
                <option value="0" <?php echo (($produto->promocao ?? '') === 0) ? 'selected' : ''; ?>>Não</option>
                <option value="1" <?php echo (($produto->promocao ?? '') === 1) ? 'selected' : ''; ?>>Sim</option>
            </select>
            <?php if (!empty($errors['promocao'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['promocao']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Destaque -->
    <div class="row mb-3">
        <label for="destaque" class="col-sm-3 col-form-label text-end text-muted">Destaque:</label>
        <div class="col-sm-7">
            <select class="form-select" id="destaque" name="destaque">
                <option value="0" <?php echo (($produto->destaque ?? '') === 0) ? 'selected' : ''; ?>>Não</option>
                <option value="1" <?php echo (($produto->destaque ?? '') === 1) ? 'selected' : ''; ?>>Sim</option>
            </select>
            <?php if (!empty($errors['destaque'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['destaque']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Status -->
    <div class="row mb-3">
        <label for="status" class="col-sm-3 col-form-label text-end text-muted">Status: <span class="requerido"></span></label>
        <div class="col-sm-7">
            <select class="form-select" id="status" name="status">
                <option value="1" <?php echo (($produto->status ?? '') === 1) ? 'selected' : ''; ?>>Ativo</option>
                <option value="0" <?php echo (($produto->status ?? '') === 0) ? 'selected' : ''; ?>>Inativo</option>
            </select>
            <?php if (!empty($errors['status'])): ?>
                <p class="error"><?php echo htmlspecialchars($errors['status']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-sm-7 offset-3 text-center">
            <button class="btn btn-primary" type="submit"><i class="fa fa-check fa-fw"></i> Editar Produto</button>
            <span class="mx-1">|</span>
            <a class="btn btn-secondary" href="/admin/produtos/index"><i class="fa fa-arrow-left fa-fw"></i> Voltar a Listagem</a>
        </div>
    </div>
</form>
<?php endSection(); ?>

<?php extend('layouts/admin'); ?>
