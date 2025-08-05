<?php

namespace template\classes;

use desv\classes\DevHelper;

class Midias
{
    private static $result = [];
    private static $options = [];

    static function deleteFile($pathFile):array
    {
        self::$result['error'] = 0;
        self::$result['msg'] = 'Arquivo deletado com sucesso.';

        if (file_exists($pathFile)) {
            unlink($pathFile);
            self::$result['error'] = 0;
            self::$result['msg'] = 'Arquivo deletado com sucesso.';
        } else {
            self::$result['error'] = 0;
            self::$result['msg'] = 'Arquivo não existe.';
        }

        return self::$result;
    }

    /**
     * saveFile
     * 
     * As opçoes são:
     * path             => Caminho completo para salvar o arquivo.
     * file             => Arquivo enviado via Post pelo $_FILE['file'].
     * fileName         => Caso queira decidir o nome.
     * checkImage       => Caso queira checar se é uma imagem.
     * limitSize        => Caso queira limitar o tamanho (bytes) do arquivo a ser recebido.
     * formatValid      => Caso queira limitar as extensões do arquivo.
     *
     * @param  mixed $options
     * @return array
     */
    static function saveFile($options):array
    {
        self::$options = [];
        // Valores default para envio de imagens.
        $optionsDefault = [
            'path'        => '/upload//',       // caminho padrão.
            'file'        => [],                // File post.
            'fileName'    => '',                // Especifica nome do arquivo.
            'checkImage'  => false,             // Apenas imagem.
            'limitSize'   => 1024 * 1024 * 10,   // 1mb
            'formatValid' => '',                // 'jpg,png' extensões separadas por ','.
        ];
        self::$options = array_merge($optionsDefault, $options);
        self::$result['error'] = 0;
        self::$result['msg'] = '';

        // Obtém informações do arquivo.
        self::arquivoInfo();
        
        // Verifica se arquivo do upload exite
        self::checkFileUploadExiste();

        if (self::$result['error']) {
            return self::$result;
        }
        
        // Verifica se arquivo destino já exite e ajusta nome.
        self::checkFileExiste();

        // Verifica se é uma imagem.
        self::checkImage();

        // Verifica o limite de tamanho do arquivo.
        self::checkFileSize();

        // Verifica se é uma extensão válida.
        self::checkFileFormats();

        // Verifica se a pasta existe.
        self::checkFolderExists();

        // Verifica se consegiu salvar a imagem.
        self::checkUploadFile();

        // Retorna o resultado do processamento da imagem.
        return self::$result;
    }
    
    /**
     * arquivoInfo
     * 
     * Obtém informações iniciais do arquivo.
     * Definido nas opções ['file'], ['fileName'], ['path'];
     *
     * @return void
     */
    static function arquivoInfo()
    {
        self::$result['type'] = strtolower(pathinfo(self::$options['file']['name'], PATHINFO_EXTENSION));
        self::$result['fileNameOrigin'] = self::$options['file']['name'];
        self::$result['fileSize'] = self::$options['file']['size'];
        // Monto o nome final do arquivo.
        if (self::$options['fileName'] != '') {
            // Monto o nome de acordo com o parâmetro passado.
            self::$result['fileName'] = self::sanitizeString(self::$options['fileName']);
        } else {
            // Pego o nome do proprio arquivo.
            self::$result['fileName'] = str_replace('.' . self::$result['type'], '', self::$options['file']['name']);
        }
        self::$result['fileFullName'] = self::$result['fileName'] . '.' . self::$result['type'];
        self::$result['target_file'] = self::$options['path'] . self::$result['fileName'] . '.' . self::$result['type'];
    }
    
    /**
     * checkImage
     * 
     * Verifica se o arquivo recebido é uma imagem.
     * Definido nas opções ['file'].
     *
     * @return void
     */
    static function checkImage()
    {
        if (!self::$options['checkImage']) {
            return;
        }
        
        $check = getimagesize(self::$options['file']['tmp_name']);
        self::$result['mime'] = $check['mime'];
        if ($check == false) {
            self::$result['msg'] .= 'Não é uma imagem válida. ';
            self::$result['error'] = 1;
        }
    }
    
    /**
     * checkFileUploadExiste
     * 
     * Verifica se o arquivo enviado existe.
     *
     * @return void
     */
    static function checkFileUploadExiste()
    {
        // Verifico se foi enviado arquivo.
        if (isset(self::$options['file']['error']) && self::$options['file']['error'] > 0 ) {
            self::$result['msg'] .= 'Erro no arquivo enviado.';
            self::$result['error'] = 1;
        }
    }
    
    /**
     * checkFileExiste
     * 
     * Verifica se o arquivo já existe no repositório definido.
     * Caso exista é acrescentado o sufixo "_1" onde vai incrementando.
     *
     * @return void
     */
    static function checkFileExiste()
    {
        // Verifica se arquivo destino existe e ajusta o nome.
        if (file_exists(self::$result['target_file'])) {
            $i = 1;
            while (file_exists(self::$options['path'] . self::$result['fileName'] . '_' . $i . '.' . self::$result['type'])) {
                $i++;
            }
            self::$result['target_file'] = self::$options['path'] . self::$result['fileName'] . '_' . $i . '.' . self::$result['type'];
            self::$result['fileFullName'] = self::$result['fileName'] . '_' . $i . '.' . self::$result['type'];
            self::$result['fileName'] = self::$result['fileName'] . '_' . $i ;
        }
    }
    
    /**
     * checkFileSize
     * 
     * Verifica se o tamanho do arquivo é permitido.
     * Definido nas options ['limitSize'].
     *
     * @return void
     */
    static function checkFileSize()
    {
        if (self::$options['file']['size'] > self::$options['limitSize']) {
            self::$result['msg'] .= 'Tamanho do arquivo superior a ' . self::$options['limitSize'] . ' bites. ';
            self::$result['error'] = 1;
        }
    }
    
    /**
     * checkFileFormats
     * 
     * Verifica se o formato do arquivo é permitido.
     * Definido nas options ['formatValid'].
     *
     * @return void
     */
    static function checkFileFormats()
    {
        if (self::$options['formatValid'] != '' && !in_array(self::$result['type'], explode(',', self::$options['formatValid']))) {
            self::$result['msg'] .= 'Formato do arquivo inválido. Formatos válidos: ' . self::$options['formatValid'] . '. ';
            self::$result['error'] = 1;
        }
    }
    
    /**
     * checkFolderExists
     * 
     * Verifica se a pasta já existe, caso contrário cria.
     *
     * @return void
     */
    static function checkFolderExists()
    {
        $pastas = explode('/', self::$options['path']);

        // Guarda início do diretório.
        $temp_pasta = '';

        // Percorre cada pasta para criar a pasta solicitada se não existir.
        foreach ($pastas as $pasta) {
            // Caso tenha valor
            if ($pasta) {
                $temp_pasta .= $pasta . '/';
                // Verifica se diretório existe e já cria.
                if (!file_exists($temp_pasta)) {
                    mkdir($temp_pasta, 0755, true);
                }
            }
        }
    }
    
    /**
     * checkUploadFile
     * 
     * Verifica se foi possível salvar a imagem em local definido.
     * Definido nas options ['path'].
     * 
     *
     * @return void
     */
    static function checkUploadFile()
    {
        if (move_uploaded_file(self::$options['file']['tmp_name'], self::$result['target_file'])) {
            self::$result['msg'] .= "O arquivo " . htmlspecialchars(basename(self::$options['file']['name'])) . " foi recebido com sucesso.";
        } else {
            self::$result['msg'] .= 'Erro ao receber o arquivo, tente novamente. ';
            self::$result['error'] = 1;
        }
    }
    
    /**
     * sanitizeString
     * 
     * Limpa a string.
     * Usado para criar o nome do arquivo.
     *
     * @param  mixed $str
     * @return string
     */
    static function sanitizeString($str)
    {
        $str = preg_replace('/[áàãâä]/ui', 'a', $str);
        $str = preg_replace('/[éèêë]/ui', 'e', $str);
        $str = preg_replace('/[íìîï]/ui', 'i', $str);
        $str = preg_replace('/[óòõôö]/ui', 'o', $str);
        $str = preg_replace('/[úùûü]/ui', 'u', $str);
        $str = preg_replace('/[ç]/ui', 'c', $str);
        $str = preg_replace('/[^a-z0-9]/i', '_', $str);
        $str = preg_replace('/_+/', '_', $str); // ideia do Bacco :)
        return $str;
    }
    
    /**
     * ping
     * 
     * Teste de chamada na classe.
     *
     * @return string
     */
    static function ping()
    {
        return 'pong';
    }
}
