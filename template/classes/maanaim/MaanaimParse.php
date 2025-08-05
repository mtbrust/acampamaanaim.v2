<?php

namespace template\classes\maanaim;

use desv\classes\DevHelper;

class MaanaimParse
{
    static function EventoTableToPost($post) {}
    static function EventoPostToTable($post)
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

    static function IngressoPostToTable($post) 
    {
        $ingresso = [
            "id_evento"           => $post['f-id_evento'],
            "qtd"                 => $post['f-qtd'],
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
            "idStatus"            => $post['f-status'],
        ];
        return $ingresso;
    }


    static function ping()
    {
        return 'pong';
    }
}
