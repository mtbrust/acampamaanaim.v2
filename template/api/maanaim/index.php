<?php

namespace api\maanaim;

use desv\controllers\EndPoint;

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
class maanaim extends EndPoint
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
      'content_type' => 'application/json',   // Tipo do retorno padrão do cabeçalho http.
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
    // Quanto conteúdo é passado por body (normalmente Json).
    $response['method'] = __FUNCTION__;
    $response[__FUNCTION__] = $params[strtolower(__FUNCTION__)];
    $response['$_GET'] = $_GET;

    // Finaliza a execução da função.
    self::$params['response'] = $response;
    self::$params['msg'] = 'Requisição recebida com sucesso.';
    self::$params['status']   = 200;
  }

  /**
   * post
   * 
   * Acessada via primeiro parâmetro ou pelo request method.
   * Recebe todos os parâmetros do endpoint em $params.
   *
   * @param  mixed $params
   */
  public function post($params)
  {
    // Quanto conteúdo é passado por body (normalmente Json).
    $response['method'] = __FUNCTION__;
    $response[__FUNCTION__] = $params[strtolower(__FUNCTION__)];
    $response['$_POST'] = $_POST;

    // Finaliza a execução da função.
    self::$params['response'] = $response;
    self::$params['msg'] = 'Requisição recebida com sucesso.';
    self::$params['status']   = 200;
  }

  /**
   * post
   * 
   * Acessada via primeiro parâmetro ou pelo request method.
   * Recebe todos os parâmetros do endpoint em $params.
   *
   * @param  mixed $params
   */
  public function put($params)
  {
    // Quanto conteúdo é passado por body (normalmente Json).
    $response['method'] = __FUNCTION__;
    $response[__FUNCTION__] = $params[strtolower(__FUNCTION__)];
    $response['$_POST'] = $_POST;

    // Finaliza a execução da função.
    self::$params['response'] = $response;
    self::$params['msg'] = 'Requisição recebida com sucesso.';
    self::$params['status']   = 200;
  }

  /**
   * foo_personalizada
   * 
   * Função é chamada quando o metodo for get e o primeiros parametro for foo_personalizada.
   * Recebe todos os parâmetros do endpoint em $params.
   *
   * @param  mixed $params
   */
  public function foo_personalizada($params)
  {
    // Quanto conteúdo é passado por body (normalmente Json).
    $response['method'] = __FUNCTION__;
    $response[__FUNCTION__] = $params[strtolower(__FUNCTION__)];
    $response['$_POST'] = $_POST;

    // Finaliza a execução da função.
    self::$params['response'] = $response;
    self::$params['status']   = 200;
    self::$params['msg']   = 'Função personalizada.';
  }
}
