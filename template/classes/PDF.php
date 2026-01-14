<?php

namespace template\classes;

use Dompdf\Dompdf;

// require ('m/assets/admin/plugins/dompdf/autoload.inc.php');
/**
 * Classe para criação de pdf padrão e personalizado.
 */
class PDF
{
    private static $domPDF;


	/**
	 * Função que cria o pdf.
	 *
	 * @param string $cabecalho
	 * @param string $corpo
	 * @param string $rodape
	 * @return void
	 */
	public static function criaPDF($cabecalho, $corpo)
	{
        //Instância a classe
        $domPDF = new Dompdf();

        //Cria o documento com HTML
        $domPDF ->loadHtml('<h1>'.$cabecalho.'</h1><div class="corpo">'.$corpo.'</div><footer>Teste</footer>');

        //Renderiza o HTML
        $domPDF -> render();

        //Exibir a página
        $domPDF -> stream('relatorio_colaborador.pdf',
        array(
            "Attachment" => false, //False desativa o download direto true habilita
        )
        );

	}
    
    public static function htmlToPdf($html)
    {
        /**
         * DOM PDF
         */
        // Instancia a classe DOMPDF
        self::$domPDF = new Dompdf();
        // Carrega um HTML dentro do DOM.
        self::$domPDF->loadHtml($html);
        // Define tamanho do papel.
        self::$domPDF->setPaper('A4', 'portrait');
        // Exibe número de páginas.
        self::$domPDF->set_option('isPhpEnabled', true); 
        self::$domPDF->set_option('isRemoteEnabled', true);
        // Renderiza o Html para PDF.
        self::$domPDF->render();

    }

    public static function browser($html, $fileName)
    {
        self::htmlToPdf($html);
        
        // Para browser
        self::$domPDF->stream($fileName);

        // Retorna o conteúdo do Arquivo.
        $pdf_gen = self::$domPDF->output();

        // Retorna o PDF.
        return $pdf_gen;
    }

    public static function base64($html)
    {
        self::htmlToPdf($html);

        // Retorna o conteúdo do Arquivo.
        $pdf_gen = self::$domPDF->output();

        // Transforma o Arquivo em base64.
        $pdfBase64 = base64_encode($pdf_gen);

        return $pdfBase64;
    }

    public static function arquivo($html)
    {
        self::htmlToPdf($html);

        // Retorna o conteúdo binário do PDF diretamente
        $pdf_gen = self::$domPDF->output();

        return $pdf_gen;
    }

	/**
	 * Função que cria um rodapé do padrão TI para os relatórios.
	 * @return void
	 */
	public static function rodapePDF(){
	    echo 'Teste';
	}
}
