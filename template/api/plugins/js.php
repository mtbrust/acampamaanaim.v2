<?php

namespace api;

use desv\classes\ManagerFile;
use desv\controllers\EndPoint;
use desv\controllers\Render;

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
class js extends EndPoint
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
    self::$params['render'] = [
      'content_type' => 'application/javascript',   // Tipo do retorno padrão do cabeçalho http.
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
    if(!isset($params['infoUrl']['attr'][0])) {
      self::$params['response'] = '';
      self::$params['msg'] = 'Módulo de plugin sem parâmetro de plugin.';
      self::$params['status']   = 200;
      return true;
    }

    // Obtenho o caminho do arquivo do plugin.
    $pathFile = BASE_DIR . '/template/plugins/' . $params['infoUrl']['attr'][0] . '/' . $params['infoUrl']['attr'][0] . '.js';

    // Carrego o arquivo js do plugin.
    $plugin = ManagerFile::read($pathFile);

    // Trabalho os parâmetros dentro do plugin. Com cache.
    // $response = Render::doc($plugin, $params, 5, 'plugin-' . $params['infoUrl']['attr'][0]);
    // Sem cache
    $response = Render::doc($plugin, $params);

    // Finaliza a execução da função.
    self::$params['response'] = $response;
    self::$params['msg'] = 'Requisição recebida com sucesso.';
    self::$params['status']   = 200;
  }
}
