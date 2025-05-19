<?php

namespace api;

use desv\controllers\EndPoint;
use desv\classes\Session;

/**
 * ORIENTAÇÕES DO MODELO PADRÃO
 * Modelo padrão de controller para o endpoint (páginas ou APIs).
 * Modelo contém todas as informações que são possíveis usar dentro de uma controller.
 * É possível tirar, acrescentar ou mudar parâmetros para obter resultados mais eficientes e personalizados.
 * 
 * ORIENTAÇÕES DA CONTROLLER
 * Os arquivos e classes são carregados após a função loadParams().
 * O método padrão para visualização é a get().
 * Na função get, é realizada toda a programação das informações do endpoint.
 * É possível chamar outras funções (Sub-Menus) usando parâmetros (Url e LoadParams).
 * Outras funções (Sub-Menus) são chamados de acordo com a estrutura personalizada no parâmetros "menus".
 * 
 * O nome da controller é o mesmo que o endpoint da url sem os "-".
 * Porém é possível passar pela url o endpoint "/quem-somos", pois o sistema irá tirar os "-".
 * O nome da controller vai ficar como "quemsomos".
 * 
 */
class end extends EndPoint
{

  /**
   * * *******************************************************************************************
   * PERSONALIZAÇÃO DO ENDPOINT
   * * *******************************************************************************************
   */


  /**
   * loadParams
   * Carrega os parâmetros de personalização do endpoint.
   * Valores Default vem da config.
   * 
   * * Opções com * podem ser modificadas no processamento.
   *
   * @return void
   */
  public function loadParams()
  {
    // Opções de renderização.
    self::$params['render']      = [
      // 'cache'        => false,                // Ativa uso de cache para resultado.
      // 'cacheTime'    => (60 * 30),            // Tempo para renovar cache em segundos. (30 Minutos).
      // 'cacheParams'  => true,                 // Cache por parametros (attr).
      'content_type' => 'application/json',   // Tipo do retorno padrão do cabeçalho http.
      // 'charset'      => 'utf-8',              // Tipo de codificação do cabeçalho http.
      // 'showParams'   => false,           // Exibe todos os parametros.
    ];

    // Configuração personalizada do endpoins.
    self::$params['config']      = [
      // CONFIGURAÇÕES ENDPOINT
      // *********************
      // 'visita'    => true,        // Gravar registro de acesso.
      // 'bdParams'  => false,       // Parâmetros da controller vem do BD.
      // 'bdContent' => false,       // Conteúdo da página vem do BD.
      // 'versao'    => 'v1.0',      // Versão da controller atual.
      // 'feedback'  => true,        // FeedBack padrão de transações.
      // 'class'     => __CLASS__,   // Guarda classe atual
    ];

    // Opções de segurança.
    self::$params['security']    = [

      // // Controller usará controller de segurança.
      // 'ativo'             => true,

      // // Usuário só acessa logado.
      // 'session'           => true,

      // // Tempo para sessão acabar nesta página.
      // 'sessionTimeOut'    => (60 * 30), // 30 min.

      // // Segurança por autorização no cabeçalho.
      // 'headers'            => [
      //     'key'   => 'Authorization',        // Tipo de autorização (Bearer Token, API Key, JWT Bearer Basic Auth, etc.).
      //     'value' => 'Bearer valor_token',   // Valor da autorização (Bearer valor_token, Basic e3t1c3VhcmlvfX06e3tzZW5oYX19, etc)
      // ],

      // // Caminho para página de login.
      // 'loginPage'         => "api/login/", // Page login dentro do modelo.

      // // Permissões personalizadas da página atual. 
      // // [1] Usuário tem que ter permissão, [0] Não necessita permissão.
      // 'permission'        => [ 
      // 	"session" => 1,   // Necessário usuário com sessao nesta página.
      // 	"get"     => 1,   // Permissão para acessar a função get desta página.
      // 	"getFull" => 1,   // Permissão para acessar a função getFull desta página.
      // 	"post"    => 1,   // Permissão para acessar a função post ou requisição post desta página.
      // 	"put"     => 1,   // Permissão para acessar a função put ou requisição put desta página.
      // 	"patch"   => 1,   // Permissão para acessar a função patch ou requisição patch desta página.
      // 	"del"  => 1,   // Permissão para acessar a função delete ou requisição delete desta página.
      // 	"api"     => 1,   // Permissão para acessar a função API desta página.
      // 	"especific" => [
      //     'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
      //     'botao_editar' => 1,
      //   ],
      // ],

      // // Transações de dados (GET - POST) apenas com token. Usar classe Tokens. Exemplo: (<input name="token" type="text" value="{{token}}" hidden>').
      // 'token'             => true, // Só aceitar com token (definido na config "BASE_AUTH['token']").

      // // FeedBack padrão de nível de acesso.
      // 'feedback'          => false,

      // // Receber transações externas. Dados de outras origens.
      // 'origin'            => [
      //     '*',                        // Permitir tudas as origens.
      //     'http://www.site.com.br/',  // URL teste.
      // ],

      // // Grupos que tem permissão TOTAL a esta controller. Usar apenas para teste.
      // 'groups'            => [
      //     // 1, // Grupo ID: 1.
      // ],

      // // IDs que tem permissão TOTAL a esta controller. Usar apenas para teste.
      // 'ids'            => [
      //     // 1, // Login ID: 1.
      // ],
    ];

    // Carrega controllers de bancos de dados.
    self::$params['bds']         = [
      // 'BdTeste',
    ];

    // Carrega classes de apoio.
    self::$params['classes']     = [
      // 'Midia',
    ];

    // Carrega controllers para reutilizar funções.
    self::$params['controllers'] = [
      // // Controllers de API
      // 'api' => [
      //     'pasta/controller', // Sintax.
      //     'modellimpo',
      // ],

      // // Controllers de Páginas
      // 'pages' => [
      //     'pasta/controller', // Sintax.
      //     'modulo/controller',
      // ],
    ];

    // Monta estrutura de parâmetros passados na url ou metodo.
    self::$params['menus']       = [
      // Função:
      'get' => [
        'title'      => 'Listar',      // Nome exibido no menu. Somente pages.
        'permission' => [
          "session" => 0,   // Necessário usuário com sessao nesta página.
          "get"     => 1,   // Permissão para acessar a função get desta página.
          "getFull" => 0,   // Permissão para acessar a função getFull desta página.
          "post"    => 0,   // Permissão para acessar a função post ou requisição post desta página.
          "put"     => 0,   // Permissão para acessar a função put ou requisição put desta página.
          "patch"   => 0,   // Permissão para acessar a função patch ou requisição patch desta página.
          "del"  => 0,   // Permissão para acessar a função delete ou requisição delete desta página.
          "api"     => 0,   // Permissão para acessar a função API desta página.
          "especific" => [
            'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
            'botao_editar' => 1,
          ],
        ],
        'groups'     => [],            // Quais grupos tem acesso a esse menu.
        'ids'        => [],            // Quais ids tem acesso a esse menu.
      ],
    ];
  }


  /**
   * get
   * 
   * Função principal.
   * Recebe todos os parâmetros do endpoint em $params.
   * Retorna todas as informações em return.
   *
   * @param  mixed $params
   */
  public function get($params)
  {
    Session::destroy();
    // Finaliza a execução da função.
    self::$params['response'] = 'destroy';
    self::$params['msg'] = 'Session destruída com sucesso.';
    self::$params['status']   = 200;
  }
}
