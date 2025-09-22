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
    static public $statusInscricao = [
        'Aguardando Pagamento',
        'Em Análise',
        'Entre em Contato',
        'Fila de Espera',
        'Confirmada',
        'Cancelada',
    ];
    static public $statusInscricaoInativa = [
        'Cancelada', // Deixar sempre na primeira posição.
    ];
    static public $msg = [];
    static public $error = false;
    static public $inserir = true;

    static public $inscricao = [];
    static public $evento = [];

    static function listarInscricoes($idEvento)
    {
        $bdInscricoes = new BdInscricoes();
        $where = 'idEvento = ' . $idEvento;
        $inscricoes = $bdInscricoes->select('*', $where);
        return $inscricoes;
    }

    static function acompanhar($cpf, $id)
    {
        $bdInscricoes = new BdInscricoes();
        $where = 'cpf = ' . $cpf . ' and id = "' . $id . '"';
        $inscrito = $bdInscricoes->select('*', $where);

        if (isset($inscrito[0])) {
            $inscrito[0]['evento'] = self::getEventoPorId($inscrito[0]['idEvento']);
            $inscrito[0]['ingresso'] = self::getIngressoPorId($inscrito[0]['idIngresso']);
            $inscrito[0]['idadeEvento'] = self::idadeNoEvento($inscrito[0]['dtNascimento'], $inscrito[0]['evento']['dt_inicio_evento']);
            return $inscrito[0];
        }

        return false;
    }

    static function getEventoPorId($id)
    {
        $bd = new Bdeventos();
        self::$evento = $bd->selectById($id);
        self::incluirMidiasEvento(self::$evento);
        return self::$evento;
    }

    static function getIngressoPorId($id)
    {
        $bd = new BdIngressos();
        return $bd->selectById($id);
    }

    static function getInscricaoPorId($id)
    {
        $bd = new BdInscricoes();
        return $bd->selectById($id);
    }

    static function editarInscricao($inscricao, $options = [])
    {
        $options['editar'] = true;
        // Prepara e trata os campos.
        self::preparaInscricao($inscricao, $options);

        // Edita evento.
        if (self::$inserir) {
            // Inserir a inscrição no banco de dados.
            $bdInscricao = new BdInscricoes();
            $idInscricao = $bdInscricao->update(self::$inscricao['id'],self::$inscricao);
            self::$msg[] = 'Inscrição editada com sucesso.';
        }

        // Retornar informações do cadastro e status da inscrição.
        return [
            'error'       => self::$error,
            'idInscricao' => isset(self::$inscricao['id']) ? self::$inscricao['id'] : 0, // 0 - não foi inserido.
            'cpf'         => self::$inscricao['cpf'],
            'inscricao'   => self::$inscricao,
            'msg'         => self::$msg,
        ];
    }

    static function adicionarInscricao($inscricao, $options = [])
    {
        // Prepara e trata os campos.
        self::preparaInscricao($inscricao, $options);

        // todo - Apenas para teste.
        // self::$inserir = false;

        // Insere evento.
        if (self::$inserir) {
            // Inserir a inscrição no banco de dados.
            $bdInscricao = new BdInscricoes();
            $idInscricao = $bdInscricao->insert(self::$inscricao);
            self::$inscricao['id'] = $idInscricao;
            self::$msg[] = 'Acompanhe sua inscrição no menu <br><b>"MINHA INSCRIÇÃO"</b><br>Logo após informe o <br>cpf: <b>"' . self::$inscricao['cpf'] . '"</b> e o <br>identificador: <b>"' . $idInscricao . '"</b>.';
        }

        // Retornar informações do cadastro e status da inscrição.
        return [
            'error'       => self::$error,
            'idInscricao' => isset(self::$inscricao['id']) ? self::$inscricao['id'] : 0, // 0 - não foi inserido.
            'cpf'         => self::$inscricao['cpf'],
            'inscricao'   => self::$inscricao,
            'msg'         => self::$msg,
        ];
    }

    static public function preparaInscricao($inscricao, $options)
    {
        // Valores default para envio de imagens.
        $optionsDefault = [
            'admin' => false,    // Passa por todas as validações.
            'editar'      => false,
        ];
        $options = array_merge($optionsDefault, $options);

        self::$inscricao = $inscricao;

        // Informações do evento.
        self::getEventoPorId(self::$inscricao['idEvento']);

        // Verificar se o CPF dessa inscrição já está inscrito nesse evento.
        self::verificaInscricaoCpf();

        // Verificar regras de idade para este evento.
        self::verificaIdade($options);

        // Verificar os campos obrigatórios.
        self::verificaCamposObrigatorios();

        // Limpar campos.
        self::limparCampos();

        // Validar os campos.
        self::validarCampos(self::$inscricao);

        // Verificar se ainda existe vagas. Caso não exista, a inscrição é cadastrada com status fila de espera.
        self::verificarVagas();

        // Acrescentar informações padrão.
        $options = [];
        self::informacoesPadrao($options);
    }

    static public function verificaInscricaoCpf()
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir || !isset(self::$inscricao['cpf'])) {
            return true;
        }

        // Caso tenha id é para editar. então não verifica.
        if (isset(self::$inscricao['id'])) {
            return true;
        }

        $bdInscricoes = new BdInscricoes();
        $fields = "id, cpf";
        $where = 'idEvento = ' . self::$inscricao['idEvento'] . ' and cpf = "' . self::$inscricao['cpf'] . '"';
        $inscrito = $bdInscricoes->select($fields, $where, null, null, null, 1000);

        if (isset($inscrito[0]['cpf'])) {

            self::$inscricao = $inscrito[0];
            self::$error = true;
            self::$msg[] = 'Já existe uma inscrição neste evento com este cpf [' . self::$inscricao['cpf'] . '] - Identificador [' . self::$inscricao['id'] . '].';
            self::$msg[] = 'Acompanhe a sua inscrição ou entre em contato conosco.';
            self::$inserir = false;
        }
    }

    static public function idadeNoEvento($dtNascimento, $dtIniEvento)
    {
        $dtNascimento = new \DateTime($dtNascimento);
        $dtEvento = new \DateTime($dtIniEvento);
        $intervalo = $dtNascimento->diff($dtEvento);
        $anos = (int)$intervalo->format('%R%y');
        return $anos;
    }

    static public function verificaIdade($options)
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir || !isset(self::$evento['dt_inicio_evento']) || $options['editar']) {
            return true;
        }

        // Valores padrão.
        self::$inscricao['menor'] = 0; // Não é menor de 18 anos.
        self::$inscricao['menorLimite'] = 0; // Não é menor que o limite de idade do evento.

        $dtNascimento = new \DateTime(self::$inscricao['dtNascimento']);
        $dtEvento = new \DateTime(self::$evento['dt_inicio_evento']);
        $intervalo = $dtNascimento->diff($dtEvento);
        $dias = (int)$intervalo->format('%R%a');

        // Menor de 18 anos.
        if ($dias <= 6575) {
            self::$inscricao['menor'] = 1;
            // self::$msg[] = "Inscrito tem menos de 18 anos. Até a data início do evento.";
            self::$inscricao['obs'] .= ' Inscrito é menor de 18 anos.';
        }

        // Menor que idade mínima do evento.
        if ($dias <= self::$evento['idade_minima'] * 365.25) {
            self::$inscricao['menorLimite'] = 1;
            // self::$msg[] = "<b>Inscrição cancelada</b>. Inscrito tem idade menor que idade mínima do evento, " . self::$evento['idade_minima'] . " anos. Até a data início do evento.";
            self::$inscricao['status'] = self::$statusInscricaoInativa[0]; // Cancelada.
            self::$inscricao['idStatus'] = 0; // Cancelada.
            self::$inscricao['obs'] .= ' Inscrição cancelada por não satisfazer idade mínima do evento [' . self::$evento['idade_minima'] . ' anos]. Cabe recurso.';
        }

        return true;
    }

    static public function verificaCamposObrigatorios()
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        // Verifica se foi enviado CPF.
        if (empty(self::$inscricao['cpf'])) {
            self::$error = true;
            self::$msg[] = 'Preencha o campo "CPF" com CPF válido.';
            self::$inserir = false;
        }

        // Evento.
        if (empty(self::$inscricao['idEvento'])) {
            self::$error = true;
            self::$msg[] = 'Selecione um "Evento".';
            self::$inserir = false;
        }

        // Ingresso.
        if (empty(self::$inscricao['idIngresso'])) {
            self::$error = true;
            self::$msg[] = 'Selecione um "Ingresso".';
            self::$inserir = false;
        }

        // Caso seja menor de 18 anos é necessário preencher os campos de responsável.
        if (self::$inscricao['menor']) {

            if (empty(self::$inscricao['RepNome'])) {
                self::$error = true;
                self::$msg[] = 'Preencha o campo "Responsável Nome" com valor válido.';
                self::$inserir = false;
            }

            if (empty(self::$inscricao['RepTelefone'])) {
                self::$error = true;
                self::$msg[] = 'Preencha o campo "Responsável Telefone" com valor válido.';
                self::$inserir = false;
            }

            if (empty(self::$inscricao['RepCpf'])) {
                self::$error = true;
                self::$msg[] = 'Preencha o campo "Responsável CPF" com valor válido.';
                self::$inserir = false;
            }

            if (empty(self::$inscricao['RepSexo'])) {
                self::$error = true;
                self::$msg[] = 'Preencha o campo "Responsável SEXO" com valor válido.';
                self::$inserir = false;
            }
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

        // [0] inativo, [1] ativo.
        if (in_array(self::$inscricao['status'], self::$statusInscricaoInativa)) {
            $options['idStatus'] = 0;
        } else {
            $options['idStatus'] = 1;
        }

        // Valores default de opções.
        $optionsDefault = [
            'idStatus' => self::$error ? 0 : 1,    // Caso ocorreu algum erro, cancela a inscrição. [0] inativo, [1] ativo.
            'obs' => implode(' ', self::$msg),
        ];
        $options = array_merge($optionsDefault, $options);

        // Limpo o campo CPF.
        self::$inscricao['idStatus'] = $options['idStatus'];
        self::$inscricao['obs'] .= $options['obs'];
    }

    static public function verificarVagas()
    {
        // Caso não seja para inserir esse registro, nem verifica.
        if (!self::$inserir) {
            return true;
        }

        $qtdM = self::$evento['qtd_vagas_masculino'];
        $qtdF = self::$evento['qtd_vagas_feminino'];

        // Quantidade de vagas.
        $bdInscricoes = new BdInscricoes();
        $fields = "sexo, count(*) as qtd";
        $where = 'idEvento = ' . self::$inscricao['idEvento'];
        $groupby = 'sexo';
        $orderby = 'sexo desc';
        $vagas = $bdInscricoes->select($fields, $where, $orderby, null, $groupby, 1000);

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
            'ativos'           => false,   // Apenas idStatus 1 [Ativo].
            'qtd'              => 10,      // Quantidade de resultados.
            'page'             => 1,       // Página.
            'id'               => 0,       // ID de evento específico.
            'ingressoValidade' => true,    // Ingresso dentro da validade.
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
        if ($eventos) {
            foreach ($eventos as $key => $evento) {

                self::incluirMidiasEvento($evento);
                $eventos[$key] = $evento;

                $optionsIngresso = [
                    'validade' => $options['ingressoValidade'],
                ];

                $eventos[$key]['ingressos'] = Maanaim::listarIngressosEvento($evento['id'], $optionsIngresso);
            }
        }

        return $eventos;
    }

    static function incluirMidiasEvento(&$evento)
    {
        $bdMidias = new BdMidias();

        $evento["url_midia_banner"] = '';
        $evento["url_midia_01"] = '';
        $evento["url_midia_02"] = '';
        $evento["url_midia_03"] = '';

        // Obtém o caminho das imagens de cada evento.
        $r = $bdMidias->selectById($evento['id_midia_banner']);
        if (isset($r['path'])) {
            $evento["url_midia_banner"] = $r['path'];
        }

        $r = $bdMidias->selectById($evento['id_midia_01']);
        if (isset($r['path'])) {
            $evento["url_midia_01"] = $r['path'];
        }

        $r = $bdMidias->selectById($evento['id_midia_02']);
        if (isset($r['path'])) {
            $evento["url_midia_02"] = $r['path'];
        }

        $r = $bdMidias->selectById($evento['id_midia_03']);
        if (isset($r['path'])) {
            $evento["url_midia_03"] = $r['path'];
        }
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
            'ativos'   => false,   // Apenas idStatus 1 [Ativo].
            'validade' => true,    // Dentro da validade do ingresso.
            'qtd'      => 10,      // Quantidade de resultados.
            'page'     => 1,       // Página.
            'id'       => 0,       // ID de evento específico.
        ];
        $options = array_merge($optionsDefault, $options);

        $where = [];

        if ($options['ativos']) {
            $where[] = "idStatus = 1";
        }

        // Instancia da tabela de Ingressos.
        $bdIngressos = new BdIngressos();

        $where[] = "id_evento = " . $idEvento;

        // Dentro da validade do ingresso.
        if ($options['validade']) {
            $where[] = "dt_fim_ingresso > NOW()";
        }

        $orderby = 'dt_ini_ingresso ASC';

        // Obtém ingressos.
        $ingressos = $bdIngressos->select('*', implode(' and ', $where), $orderby, null, null, $options['qtd'], $options['page']);

        return $ingressos;
    }

    static function listarIngresso($idIngresso, $options = [])
    {
        // Valores default para envio de imagens.
        $optionsDefault = [
            'validade'   => true,   // Validos
        ];
        $options = array_merge($optionsDefault, $options);

        // Instancia da tabela de Ingressos.
        $bdIngressos = new BdIngressos();

        $where = "id = " . $idIngresso . "";

        // Caso seja dentro da validade, busca ingressos válidos.
        if ($options['validade']) {
            $where .= " and dt_fim_ingresso > NOW()";
        }

        $orderby = 'dt_ini_ingresso ASC';

        // Obtém ingressos.
        $ingressos = $bdIngressos->select('*', $where, $orderby);

        if (isset($ingressos[0]))
            return $ingressos[0];

        return false;

    }

    static function adicionarIngresso($postIngresso)
    {

        // Pego os campos tratados do evento.
        $ingresso = MaanaimParse::ingressoPostToTable($postIngresso);

        $bdIngressos = new BdIngressos();
        $id = $bdIngressos->insert($ingresso);

        return $id;
    }

    static function editarIngresso($id, $post)
    {

        $ingresso = MaanaimParse::ingressoPostToTable($post);

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
        $evento = MaanaimParse::eventoPostToTable($post);

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
