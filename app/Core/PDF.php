<?php

namespace App\Core;

use TCPDF;

class PDF extends TCPDF
{
    // Construtor
    public function __construct()
    {
        parent::__construct();

        // Configurações padrão
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('Nome do Autor');
        $this->SetTitle('Título Padrão');
        $this->SetSubject('Assunto Padrão');
        $this->SetKeywords('TCPDF, PDF, exemplo');

        // Defina as margens
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);
    }

    // Método para configurar o cabeçalho
    public function Header() {
        // Defina a fonte para o cabeçalho
        $this->SetFont('helvetica', 'B', 12);
        
        // Desenhe uma célula com o título do cabeçalho
        $this->Cell(0, 10, 'Relatório de Vendas', 0, 1, 'C');
    }

    // Método para configurar o rodapé
    public function Footer() {
        // Posicione 15 mm do final da página
        $this->SetY(-15);

        // Linha superior do rodapé
        $this->Line(10, $this->GetY(), 200, $this->GetY());

        // Defina a fonte para o rodapé
        $this->SetFont('helvetica', 'I', 8);

        // Adicionar logotipo (se necessário, ajuste o caminho e tamanho)
        $imageFile = K_PATH_IMAGES . 'logo.png'; // Certifique-se de ter uma imagem válida em K_PATH_IMAGES
        $this->Image($imageFile, 10, $this->GetY() + 3, 15, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Texto do rodapé
        $this->SetX(25); // Espaço após a imagem
        $this->Cell(0, 10, 'Texto do rodapé - Informação adicional', 0, 0, 'L');

        // Número da página
        $this->SetX(-30); // Move o cursor para a esquerda
        $pagenumtxt = 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages();
        $this->Cell(0, 10, $pagenumtxt, 0, 0, 'R');
    }
}
