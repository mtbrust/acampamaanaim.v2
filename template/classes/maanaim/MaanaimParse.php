<?php

namespace template\classes\maanaim;

use desv\classes\DevHelper;

class MaanaimParse
{
    static function eventoPostToTable($post)
    {
        $evento = [
            "nome_evento"         => $post['f-nome_evento'],
            "titulo_evento"       => $post['f-titulo_evento'],
            "msg_espera"          => $post['f-msg_espera'],
            "nome_preletor"       => $post['f-nome_preletor'],
            "obs_preletor"        => $post['f-obs_preletor'],
            "obs_evento"          => $post['f-obs_evento'],
            "obs_atividades"      => $post['f-obs_atividades'],
            "link_album"          => $post['f-link_album'],
            "qtd_vagas_masculino" => $post['f-qtd_vagas_masculino'],
            "qtd_vagas_feminino"  => $post['f-qtd_vagas_feminino'],
            "dt_inicio_evento"    => str_replace('T', ' ', $post['f-dt_inicio_evento']),
            "dt_fim_evento"       => str_replace('T', ' ', $post['f-dt_fim_evento']),
            "idade_minima"        => $post['f-idade_minima'],
            "idStatus"            => $post['f-status'],
            // "id_midia_banner" => Null,
            // "id_midia_01" => Null,
            // "id_midia_02" => Null,
            // "id_midia_03" => Null,
        ];
        return $evento;
    }

    static function ingressoPostToTable($post) 
    {
        $ingresso = [
            "id_evento"           => $post['f-id_evento'],
            "qtd"                 => isset($post['f-qtd'])?$post['f-qtd']:'',
            "titulo"              => $post['f-titulo'],
            "dt_ini_ingresso"     => str_replace('T', ' ', $post['f-dt_ini_ingresso']),
            "dt_fim_ingresso"     => str_replace('T', ' ', $post['f-dt_fim_ingresso']),
            "valor_ingresso"      => $post['f-valor_ingresso'],
            "link_pagamento"      => $post['f-link_pagamento'],
            "dt_limit_pagamento"  => str_replace('T', ' ', $post['f-dt_limit_pagamento']),
            "chave_pix"           => $post['f-chave_pix'],
            "desc_tipo_pagamento" => $post['f-desc_tipo_pagamento'],
            "desc_ingresso"       => $post['f-desc_ingresso'],
            "desc_cuidados"       => $post['f-desc_cuidados'],
            "desc_orientacao"     => $post['f-desc_orientacao'],
            "idStatus"            => isset($post['f-status'])?$post['f-status']:1,
        ];
        return $ingresso;
    }

    static function inscricaoPostToTable($post)
    {        
        $inscricao = [
            "idEvento"        => (isset($post['f-idEvento']))?$post['f-idEvento']:'',
            "idIngresso"      => (isset($post['f-idIngresso']))?$post['f-idIngresso']:'',
            
            // Informações"
            "nome"            => (isset($post['f-nome']))?$post['f-nome']:'',
            "email"           => (isset($post['f-email']))?$post['f-email']:'',
            "telefone"        => (isset($post['f-telefone']))?$post['f-telefone']:'',
            "telefoneContato" => (isset($post['f-telefoneContato']))?$post['f-telefoneContato']:'',
            "cpf"             => (isset($post['f-cpf']))?$post['f-cpf']:'',
            "sexo"            => (isset($post['f-sexo']))?$post['f-sexo']:'',
            "dtNascimento"    => (isset($post['f-dtNascimento']))?$post['f-dtNascimento']:'',
            // "idMidiaFoto"     => ($post['f-idMidiaFoto'])?$post['f-idMidiaFoto']:'',
            "menor"           => (isset($post['f-menor']))?$post['f-menor']:0,   // Menor de idade (menor de 18 anos até o evento).
            "menorLimite"     => (isset($post['f-menorLimite']))?$post['f-menorLimite']:0,   // Menor que limite de idade do evento.

            // Informações"
            "paiNome"         => (isset($post['f-paiNome']))?$post['f-paiNome']:'',
            // "paiCpf"          => ($post['f-paiCpf'])?$post['f-paiCpf']:'',
            // "paiDtNascimento" => ($post['f-paiDtNascimento'])?$post['f-paiDtNascimento']:'',
            "maeNome"         => (isset($post['f-maeNome']))?$post['f-maeNome']:'',
            // "maeCpf"          => ($post['f-maeCpf'])?$post['f-maeCpf']:'',
            // "maeDtNascimento" => ($post['f-maeDtNascimento'])?$post['f-maeDtNascimento']:'',

            // Endereço"
            "endCEP"          => (isset($post['f-endCEP']))?$post['f-endCEP']:'',
            "endPais"         => (isset($post['f-endPais']))?$post['f-endPais']:'',
            "endEstado"       => (isset($post['f-endEstado']))?$post['f-endEstado']:'',
            "endCidade"       => (isset($post['f-endCidade']))?$post['f-endCidade']:'',
            "endBairro"       => (isset($post['f-endBairro']))?$post['f-endBairro']:'',
            "endRua"          => (isset($post['f-endRua']))?$post['f-endRua']:'',
            "endNumero"       => (isset($post['f-endNumero']))?$post['f-endNumero']:'',
            "endComplemento"  => (isset($post['f-endComplemento']))?$post['f-endComplemento']:'',

            // informações"
            "RepNome"         => (isset($post['f-RepNome']))?$post['f-RepNome']:'',
            "RepEmail"        => (isset($post['f-RepEmail']))?$post['f-RepEmail']:'',
            "RepTelefone"     => (isset($post['f-RepTelefone']))?$post['f-RepTelefone']:'',
            "RepCpf"          => (isset($post['f-RepCpf']))?$post['f-RepCpf']:'',
            "RepSexo"         => (isset($post['f-RepSexo']))?$post['f-RepSexo']:'',
            "RepDtNascimento" => (isset($post['f-RepDtNascimento']))?$post['f-RepDtNascimento']:'',

            // Informações"
            "alergiaR"        => (isset($post['f-alergiaR']))?$post['f-alergiaR']:'',
            "alergia"         => (isset($post['f-alergia']))?$post['f-alergia']:'',
            "medicamentoR"    => (isset($post['f-medicamentoR']))?$post['f-medicamentoR']:'',
            "medicamento"     => (isset($post['f-medicamento']))?$post['f-medicamento']:'',
            "nadarR"          => (isset($post['f-nadarR']))?$post['f-nadarR']:'',
            "nadarA"          => (isset($post['f-nadarA']))?$post['f-nadarA']:'',
            "nadarP"          => (isset($post['f-nadarP']))?$post['f-nadarP']:'',

            // Informações"
            "ideia"           => (isset($post['f-ideia']))?$post['f-ideia']:'',
            "conselheiro"     => (isset($post['f-conselheiro']))?$post['f-conselheiro']:'',

            // Informações"
            "termos"          => 1,
            "status"          => (isset($post['f-status']))?$post['f-status']:'Aguardando Pagamento',
            "statusEquipe"    => (isset($post['f-statusEquipe']))?$post['f-statusEquipe']:'',
            "obs"    => (isset($post['f-obs']))?$post['f-obs']:'',
            "obsPreAcampa"    => (isset($post['f-obsPreAcampa']))?$post['f-obsPreAcampa']:'',
            "obsAcampa"       => (isset($post['f-obsAcampa']))?$post['f-obsAcampa']:'',
            "obsPosAcampa"    => (isset($post['f-obsPosAcampa']))?$post['f-obsPosAcampa']:'',

            // Checkin"
            "documentacao"    => (isset($post['f-documentacao']))?$post['f-documentacao']:'',
            "alojamento"      => (isset($post['f-alojamento']))?$post['f-alojamento']:'',
            "quarto"          => (isset($post['f-quarto']))?$post['f-quarto']:'',
            "checkin"         => (isset($post['f-checkin']))?$post['f-checkin']:'',
            "obsCheckin"      => (isset($post['f-obsCheckin']))?$post['f-obsCheckin']:'',
        ];

        if (isset($post['f-id']))
        {
            $inscricao['id'] = $post['f-id'];
        }

        return $inscricao;
    }

    static function ping()
    {
        return 'pong';
    }
}
