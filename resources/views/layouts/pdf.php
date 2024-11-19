<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Documento PDF' ?></title>
    <style>
        /* Defina estilos básicos aqui que serão aplicados ao conteúdo do PDF */
        body { font-family: helvetica, sans-serif; }
        header { text-align: center; margin-bottom: 20px; }
        footer { position: fixed; bottom: 0; text-align: center; }
        .page-number:after { content: "Página " counter(page); }
    </style>
</head>
<body>
    <main>
        <?= $content ?>
    </main>
</body>
</html>
