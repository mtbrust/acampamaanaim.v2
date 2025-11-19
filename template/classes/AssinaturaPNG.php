<?php

namespace template\classes;

class AssinaturaPNG
{
    /**
     * create
     * 
     * Cria uma imagem PNG com texto usando uma fonte específica.
     * 
     * Opções disponíveis:
     * - text: Texto principal a ser renderizado (obrigatório)
     * - text2: Texto secundário (opcional)
     * - font: Caminho para o arquivo de fonte TTF (opcional, usa fonte padrão se não especificado)
     * - fontSize: Tamanho da fonte principal (padrão: 20)
     * - fontSize2: Tamanho da fonte secundária (padrão: 16)
     * - width: Largura da imagem em pixels (padrão: calculado automaticamente)
     * - height: Altura da imagem em pixels (padrão: calculado automaticamente)
     * - backgroundColor: Cor de fundo em RGB array [r, g, b] ou 'transparent' (padrão: transparent)
     * - textColor: Cor do texto principal em RGB array [r, g, b] (padrão: [0, 0, 0] preto)
     * - textColor2: Cor do texto secundário em RGB array [r, g, b] (padrão: [0, 0, 0] preto)
     * - padding: Espaçamento interno em pixels (padrão: 20)
     * - angle: Ângulo do texto em graus (padrão: 0)
     * - outputPath: Caminho completo onde salvar a imagem (opcional, retorna base64 se não especificado)
     * - returnBase64: Se true, retorna a imagem em base64 (padrão: false se outputPath especificado)
     *
     * @param array $options
     * @return array|string Retorna array com informações ou string base64
     */
    public static function create($options)
    {
        // Valores padrão
        $defaults = [
            'text' => '',
            'text2' => '',
            'font' => null, // Usa fonte padrão do sistema se não especificado
            'fontSize' => 20,
            'fontSize2' => 16,
            'width' => null, // Será calculado automaticamente
            'height' => null, // Será calculado automaticamente
            'backgroundColor' => 'transparent', // ou array [r, g, b]
            'textColor' => [0, 0, 0], // Preto
            'textColor2' => [0, 0, 0], // Preto
            'padding' => 20,
            'angle' => 0,
            'outputPath' => null,
            'returnBase64' => false,
        ];

        $options = array_merge($defaults, $options);

        // Validação
        if (empty($options['text']) && empty($options['text2'])) {
            return [
                'error' => true,
                'msg' => 'É necessário fornecer pelo menos um texto (text ou text2).'
            ];
        }

        // Verifica se a extensão GD está disponível
        if (!extension_loaded('gd')) {
            return [
                'error' => true,
                'msg' => 'Extensão GD não está disponível no PHP.'
            ];
        }

        // Se uma fonte foi especificada, verifica se existe
        $fontPath = null;
        if (!empty($options['font'])) {
            if (file_exists($options['font'])) {
                $fontPath = $options['font'];
            } else {
                return [
                    'error' => true,
                    'msg' => 'Arquivo de fonte não encontrado: ' . $options['font']
                ];
            }
        }

        // Calcula dimensões da imagem se não foram especificadas
        $bbox1 = null;
        $bbox2 = null;
        
        if (!empty($options['text'])) {
            if ($fontPath) {
                $bbox1 = imagettfbbox($options['fontSize'], $options['angle'], $fontPath, $options['text']);
            } else {
                // Usa fonte padrão (built-in) - calcula dimensões manualmente
                // Para fontes built-in, os tamanhos são: 1, 2, 3, 4, 5
                // Limita o tamanho para valores válidos
                $fontSize1 = max(1, min(5, $options['fontSize']));
                $bbox1 = [
                    0 => 0,
                    1 => 0,
                    2 => strlen($options['text']) * imagefontwidth($fontSize1),
                    3 => imagefontheight($fontSize1),
                    4 => strlen($options['text']) * imagefontwidth($fontSize1),
                    5 => imagefontheight($fontSize1),
                    6 => 0,
                    7 => 0
                ];
            }
        }
        
        if (!empty($options['text2'])) {
            if ($fontPath) {
                $bbox2 = imagettfbbox($options['fontSize2'], $options['angle'], $fontPath, $options['text2']);
            } else {
                // Usa fonte padrão (built-in) - calcula dimensões manualmente
                $fontSize2 = max(1, min(5, $options['fontSize2']));
                $bbox2 = [
                    0 => 0,
                    1 => 0,
                    2 => strlen($options['text2']) * imagefontwidth($fontSize2),
                    3 => imagefontheight($fontSize2),
                    4 => strlen($options['text2']) * imagefontwidth($fontSize2),
                    5 => imagefontheight($fontSize2),
                    6 => 0,
                    7 => 0
                ];
            }
        }

        // Calcula largura e altura necessárias
        $textWidth = 0;
        $textHeight = 0;

        if ($bbox1) {
            $textWidth = max($textWidth, abs($bbox1[4] - $bbox1[0]));
            $textHeight += abs($bbox1[5] - $bbox1[1]);
        }
        if ($bbox2) {
            $textWidth = max($textWidth, abs($bbox2[4] - $bbox2[0]));
            $textHeight += abs($bbox2[5] - $bbox2[1]) + 10; // +10 para espaçamento entre textos
        }

        $width = $options['width'] ?? ($textWidth + ($options['padding'] * 2));
        $height = $options['height'] ?? ($textHeight + ($options['padding'] * 2));

        // Cria a imagem
        $image = imagecreatetruecolor($width, $height);

        // Define transparência se necessário
        if ($options['backgroundColor'] === 'transparent') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
            imagefill($image, 0, 0, $transparent);
        } else {
            // Define cor de fundo
            $bgColor = imagecolorallocate($image, $options['backgroundColor'][0], $options['backgroundColor'][1], $options['backgroundColor'][2]);
            imagefill($image, 0, 0, $bgColor);
        }

        // Define cores do texto
        $textColor = imagecolorallocate($image, $options['textColor'][0], $options['textColor'][1], $options['textColor'][2]);
        $textColor2 = imagecolorallocate($image, $options['textColor2'][0], $options['textColor2'][1], $options['textColor2'][2]);

        // Calcula posição do texto (centralizado)
        $y1 = $options['padding'];
        $y2 = $y1 + ($bbox1 ? abs($bbox1[5] - $bbox1[1]) : 0) + 10;

        // Renderiza o texto principal
        if (!empty($options['text'])) {
            if ($fontPath) {
                // Usa fonte TTF
                $x1 = ($width - abs($bbox1[4] - $bbox1[0])) / 2;
                imagettftext($image, $options['fontSize'], $options['angle'], $x1, $y1 + abs($bbox1[5]), $textColor, $fontPath, $options['text']);
            } else {
                // Usa fonte padrão (built-in)
                $fontSize1 = max(1, min(5, $options['fontSize']));
                $x1 = ($width - strlen($options['text']) * imagefontwidth($fontSize1)) / 2;
                imagestring($image, $fontSize1, $x1, $y1, $options['text'], $textColor);
            }
        }

        // Renderiza o texto secundário
        if (!empty($options['text2'])) {
            if ($fontPath) {
                // Usa fonte TTF
                $x2 = ($width - abs($bbox2[4] - $bbox2[0])) / 2;
                imagettftext($image, $options['fontSize2'], $options['angle'], $x2, $y2 + abs($bbox2[5]), $textColor2, $fontPath, $options['text2']);
            } else {
                // Usa fonte padrão (built-in)
                $fontSize2 = max(1, min(5, $options['fontSize2']));
                $x2 = ($width - strlen($options['text2']) * imagefontwidth($fontSize2)) / 2;
                imagestring($image, $fontSize2, $x2, $y2, $options['text2'], $textColor2);
            }
        }

        // Salva ou retorna a imagem
        if (!empty($options['outputPath'])) {
            // Cria o diretório se não existir
            $dir = dirname($options['outputPath']);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Salva a imagem
            imagepng($image, $options['outputPath']);
            imagedestroy($image);

            return [
                'error' => false,
                'msg' => 'Imagem criada com sucesso.',
                'path' => $options['outputPath']
            ];
        } else {
            // Retorna como base64
            ob_start();
            imagepng($image);
            $imageData = ob_get_contents();
            ob_end_clean();
            imagedestroy($image);

            $base64 = base64_encode($imageData);

            if ($options['returnBase64']) {
                return $base64;
            }

            return [
                'error' => false,
                'msg' => 'Imagem criada com sucesso.',
                'base64' => $base64,
                'dataUri' => 'data:image/png;base64,' . $base64
            ];
        }
    }

    /**
     * ping
     * 
     * Teste de chamada na classe.
     *
     * @return string
     */
    public static function ping() {
        return 'pong';
    }
}