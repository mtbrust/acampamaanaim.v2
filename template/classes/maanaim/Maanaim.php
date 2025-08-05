<?php

namespace template\classes\maanaim;

use desv\classes\DevHelper;
use desv\classes\FeedBackMessagens;
use template\classes\bds\BdEventos;
use template\classes\bds\BdIngressos;
use template\classes\bds\BdMidias;
use template\classes\Midias;

class Maanaim
{
    static function AdicionarEvento($post)
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

        $where = "id_evento = " . $idEvento . " and  dt_fim_inscricao > NOW()";
        $orderby = 'valor_inscricao DESC';

        // Obtém ingressos.
        $ingressos = $bdIngressos->select('*', $where, $orderby);

        // Caso não tenha resultados retorna o valor 0.
        if (!isset($ingressos[0])) {
            return 0;
        }

        return $ingressos[0]['valor_inscricao'];
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
