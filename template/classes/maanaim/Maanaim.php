<?php

namespace template\classes\maanaim;

use desv\classes\DevHelper;
use desv\classes\FeedBackMessagens;
use template\classes\bds\BdEventos;
use template\classes\bds\BdIngressos;
use template\classes\bds\BdInscricoes;
use template\classes\bds\BdMidias;
use template\classes\Midias;

class Maanaim
{
    static public $msg = [];
    static public $error = false;
    static public $inserir = true;

    static public $inscricao = [];

    static function AdicionarInscricao($inscricao, $options = [])
    {
        self::$inscricao = $inscricao;

        // Valores default para envio de imagens.
        $optionsDefault = [
            'admin' => false,    // Passa por todas as validações.
        ];
        $options = array_merge($optionsDefault, $options);

        // Verificar se o CPF dessa inscrição já está inscrito nesse evento.
        self::verificaInscricaoCpf(self::$inscricao['cpf']);

        // Verificar os campos obrigatórios.
        self::verificaCamposObrigatorios(self::$inscricao);

        // Limpar campos.
        self::limparCampos();

        // Validar os campos.
        self::validarCampos(self::$inscricao);

        // Verificar se ainda existe vagas. Caso não exista, a inscrição é cadastrada com status fila de espera.
        self::verificarVagas();

        // Acrescentar informações padrão.
        $options = [];
        self::informacoesPadrao($options);
        
        // Valor padrão.
        $idInscricao = 0;

        // Insere evento.
        if (self::$inserir) {
            // Inserir a inscrição no banco de dados.
            $bdInscricao = new BdInscricoes();
            $idInscricao = $bdInscricao->insert(self::$inscricao);
            
            self::$msg[] = 'Acompanhe sua inscrição no menu "MINHA INSCRIÇÃO", informando o cpf: "'. self::$inscricao['cpf'] .'" e id: "'. $idInscricao .'".';
        }

        // Retornar informações do cadastro e status da inscrição.
        return [
            'error'          => self::$error,
            'idInscricao'    => $idInscricao,
            'acompanhamento' => self::$inscricao['cpf'] . $idInscricao,
            'inscricao'      => $inscricao,
            'msg'            => self::$msg,
        ];
    }

    static public function verificaInscricaoCpf($cpf)
    {
        $bdInscricoes = new BdInscricoes();
        $fields = "id, cpf";
        $where = 'idEvento = ' . self::$inscricao['idEvento'] . ' and cpf = "' . $cpf . '"';
        $inscrito = $bdInscricoes->select($fields, $where, null, null, null, 1000);

        if (isset($inscrito[0]['cpf'])) {
            self::$error = true;
            self::$msg[] = 'Já existe uma inscrição neste evento com este cpf [' . $inscrito[0]['cpf'] . '] - ID [' . $inscrito[0]['id'] . '].';
            self::$msg[] = 'Acompanhe a sua inscrição ou entre em contato conosco.';
            self::$inserir = false;
        }
    }

    static public function verificaCamposObrigatorios($inscricao)
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        // Verifica se foi enviado CPF.
        if (empty($inscricao['cpf'])) {
            self::$error = false;
            self::$msg[] = 'Preencha o campo "CPF".';
        }
    }

    static public function limparCampos()
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        // Limpo o campo CPF.
        self::$inscricao['cpf'] = preg_replace('/[^0-9]/is', '', self::$inscricao['cpf']);
    }

    static public function informacoesPadrao($options = [])
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        // Valores default para envio de imagens.
        $optionsDefault = [
            'idStatus' => self::$error ? 0 : 1,    // Caso ocorreu algum erro, cancela a inscrição.
            'obs' => implode(' ', self::$msg),
        ];
        $options = array_merge($optionsDefault, $options);


        // Limpo o campo CPF.
        self::$inscricao['idStatus'] = $options['idStatus'];
        self::$inscricao['obs'] = $options['obs'];
    }

    static public function verificarVagas()
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        // Informações do evento.
        $bdEventos = new BdEventos();
        $evento = $bdEventos->selectById(self::$inscricao['idEvento']);

        $qtdM = $evento['qtd_vagas_masculino'];
        $qtdF = $evento['qtd_vagas_feminino'];

        // Quantidade de vagas.
        $bdInscricoes = new BdInscricoes();
        $fields = "sexo, count(*) as qtd";
        $where = 'idEvento = ' . self::$inscricao['idEvento'];
        $groupby = 'sexo';
        $orderby = 'sexo desc';
        $vagas = $bdInscricoes->select($fields, $where, $orderby, null, $groupby, 1000);

        // DevHelper::printr($bdInscricoes::$sql);
        // DevHelper::printr($vagas);

        // Calculo a quantidade de vagas restantes.
        if (isset($vagas[0]['sexo']) && $vagas[0]['sexo'] == 'Masculino') {
            $qtdM -= $vagas[0]['qtd'];
        }
        if (isset($vagas[1]['sexo']) && $vagas[1]['sexo'] == 'Feminino') {
            $qtdF -= $vagas[1]['qtd'];
        }
        if (isset($vagas[0]['sexo']) && $vagas[0]['sexo'] == 'Feminino') {
            $qtdF -= $vagas[0]['qtd'];
        }
        if (isset($vagas[1]['sexo']) && $vagas[1]['sexo'] == 'Masculino') {
            $qtdM -= $vagas[1]['qtd'];
        }

        // Verifica se foi enviado CPF.
        if ($qtdM <= 0) {
            self::$error = true;
            self::$msg[] = 'Não há mais vagas masculinas.';
        }

        if ($qtdF <= 0) {
            self::$error = true;
            self::$msg[] = 'Não há mais vagas femininas.';
        }

        $vagas = [
            'f' => $qtdF,
            'm' => $qtdM,
        ];

        // DevHelper::printr($vagas);

        return $vagas;
    }

    static public function validarCampos($inscricao)
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        // Verifica se foi enviado CPF.
        if (!self::validaCPF($inscricao['cpf'])) {
            self::$error = true;
            self::$msg[] = 'CPF não atende ao padrão de conformidade.';
        }
    }

    static function validaCPF($cpf)
    {
        // Extrai somente os números
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;
    }


    static public function AdicionarEvento($post)
    {

        $evento = self::processaCamposEvento($post);

        // Instancia da tabela de eventos.
        $bdEvento = new BdEventos();
        // Insere evento.
        $idEvento = $bdEvento->insert($evento);

        // Retorna o ID do evento cadastrado.
        return $idEvento;
    }

    static function listarEventos($options = [])
    {
        // Valores default para envio de imagens.
        $optionsDefault = [
            'ativos' => false,    // Apenas idStatus 1 [Ativo].
            'qtd'    => 10,   // Quantidade de resultados.
            'page'   => 1,    // Página.
            'id'     => 0,    // ID de evento específico.
        ];
        $options = array_merge($optionsDefault, $options);

        $where = [];

        if ($options['ativos']) {
            $where[] = "idStatus = 1";
        }

        if ($options['id']) {
            $where[] = "id = " . $options['id'];
        }

        // Instancia da tabela de eventos.
        $bdEvento = new BdEventos();
        // Seleciona os eventos.
        $eventos = $bdEvento->select('*', implode(' and ', $where), 'idStatus DESC, id DESC', null, null, $options['qtd'], $options['page']);

        $bdMidias = new BdMidias();
        foreach ($eventos as $key => $evento) {

            $eventos[$key]["url_midia_banner"] = '';
            $eventos[$key]["url_midia_01"] = '';
            $eventos[$key]["url_midia_02"] = '';
            $eventos[$key]["url_midia_03"] = '';

            // Obtém o caminho das imagens de cada evento.
            $r = $bdMidias->selectById($evento['id_midia_banner']);
            if (isset($r['path'])) {
                $eventos[$key]["url_midia_banner"] = $r['path'];
            }

            $r = $bdMidias->selectById($evento['id_midia_01']);
            if (isset($r['path'])) {
                $eventos[$key]["url_midia_01"] = $r['path'];
            }

            $r = $bdMidias->selectById($evento['id_midia_02']);
            if (isset($r['path'])) {
                $eventos[$key]["url_midia_02"] = $r['path'];
            }

            $r = $bdMidias->selectById($evento['id_midia_03']);
            if (isset($r['path'])) {
                $eventos[$key]["url_midia_03"] = $r['path'];
            }

            $eventos[$key]['ingressos'] = Maanaim::listarIngressosEvento($evento['id']);
        }

        return $eventos;
    }

    static function listarEvento($id)
    {
        $options = [
            'id' => $id,
        ];
        $lista = self::listarEventos($options);

        if (isset($lista[0])) {
            return $lista[0];
        }

        return [];
    }

    static function editarEvento($id, $post)
    {
        $evento = self::processaCamposEvento($post);

        // Instancia da tabela de eventos.
        $bdEvento = new BdEventos();
        // Atualiza evento.
        $idEvento = $bdEvento->update($id, $evento);

        // Retorna o ID do evento cadastrado.
        return $id;
    }

    static function maiorValorIngresso($idEvento)
    {
        // Instancia da tabela de Ingressos.
        $bdIngressos = new BdIngressos();

        $where = "id_evento = " . $idEvento . " and  dt_fim_ingresso > NOW()";
        $orderby = 'valor_ingresso DESC';

        // Obtém ingressos.
        $ingressos = $bdIngressos->select('*', $where, $orderby);

        // Caso não tenha resultados retorna o valor 0.
        if (!isset($ingressos[0])) {
            return 0;
        }

        return $ingressos[0]['valor_ingresso'];
    }

    static function listarIngressosEvento($idEvento, $options = [])
    {
        // Valores default para envio de imagens.
        $optionsDefault = [
            'ativos' => false,    // Apenas idStatus 1 [Ativo].
            'qtd'    => 10,   // Quantidade de resultados.
            'page'   => 1,    // Página.
            'id'     => 0,    // ID de evento específico.
        ];
        $options = array_merge($optionsDefault, $options);

        $where = [];

        if ($options['ativos']) {
            $where[] = "idStatus = 1";
        }

        // Instancia da tabela de Ingressos.
        $bdIngressos = new BdIngressos();

        $where[] = "id_evento = " . $idEvento . " and dt_fim_ingresso > NOW()";
        $orderby = 'dt_ini_ingresso ASC';

        // Obtém ingressos.
        $ingressos = $bdIngressos->select('*', implode(' and ', $where), $orderby, null, null, $options['qtd'], $options['page']);

        return $ingressos;
    }

    static function listarIngresso($idIngresso)
    {
        // Instancia da tabela de Ingressos.
        $bdIngressos = new BdIngressos();

        $where = "id = " . $idIngresso . " and dt_fim_ingresso > NOW()";
        $orderby = 'dt_ini_ingresso ASC';

        // Obtém ingressos.
        $ingressos = $bdIngressos->select('*', $where, $orderby);

        return $ingressos[0];
    }

    static function adicionarIngresso($postIngresso)
    {

        // Pego os campos tratados do evento.
        $ingresso = MaanaimParse::IngressoPostToTable($postIngresso);

        $bdIngressos = new BdIngressos();
        $id = $bdIngressos->insert($ingresso);

        return $id;
    }

    static function editarIngresso($id, $post)
    {

        $ingresso = MaanaimParse::IngressoPostToTable($post);

        // Instancia da tabela de eventos.
        $bdIngressos = new BdIngressos();
        // Atualiza ingresso.
        $r = $bdIngressos->update($id, $ingresso);

        if (!$r) {
            return false;
        }

        // Retorna o ID do evento cadastrado.
        return $id;
    }

    static function ping()
    {
        return 'pong';
    }

    static private function processaCamposEvento($post)
    {
        // Pego os campos tratados do evento.
        $evento = MaanaimParse::EventoPostToTable($post);

        // Guarda os ids dos arquivos recebidos.
        $idsMidia = [];
        // Instancia do banco de dados de midias.
        $bdMidias = new BdMidias();

        // Percorre todos os arquivos enviados (4 fotos).
        foreach ($_FILES as $key => $value) {
            $path = 'template/assets/midias/eventos/';
            $options = [
                'path'       => BASE_DIR . $path,
                'file'       => $_FILES[$key],
                'fileName'   => $evento['nome_evento'] . ' banner',
                'checkImage' => true,
            ];
            $resultMidia = Midias::saveFile($options);

            if (!$resultMidia['error']) {
                $midia = [
                    'name' => $resultMidia['fileName'],
                    'fullName' => $resultMidia['fileFullName'],
                    'mime' => $resultMidia['mime'],
                    'type' => $resultMidia['type'],
                    'size' => $resultMidia['fileSize'],
                    'path' => $path . $resultMidia['fileFullName'],
                    'obs' => $evento['nome_evento'],
                ];
                $idsMidia[] = $bdMidias->insert($midia);
                continue;
            }
            // Caso não foi enviado imagem, colocamos vazio.
            $idsMidia[] = false;
        }

        if (empty($idsMidia)) {
            $idsMidia = [
                0 => false,
                1 => false,
                2 => false,
                3 => false,
            ];
        }

        // Ajustamos a posição de cada imagem enviada.
        if ($idsMidia[0]) {
            $evento["id_midia_banner"] = $idsMidia[0];
        }
        if ($idsMidia[1]) {
            $evento["id_midia_01"] = $idsMidia[1];
        }
        if ($idsMidia[2]) {
            $evento["id_midia_02"] = $idsMidia[2];
        }
        if ($idsMidia[3]) {
            $evento["id_midia_03"] = $idsMidia[3];
        }

        return $evento;
    }
}
