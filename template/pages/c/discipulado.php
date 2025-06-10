<?php

namespace pages;

use desv\controllers\EndPoint;
use desv\controllers\Render;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class discipulado extends EndPoint
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
		// Opções de segurança.
		self::$params['security']    = [
			// Controller usará controller de segurança.
			'ativo' => 0,
			// Usuário só acessa logado.
			'session' => 0,
			// Permissões personalizadas da página atual. 
			// [1] Usuário tem que ter permissão, [0] Não necessita permissão.
			'permission' => [
				"session"   => 0,    // Necessário usuário com sessao nesta página.
				"get"       => 0,    // Permissão para acessar a função get desta página.
				"getFull"   => 0,    // Permissão para acessar a função getFull desta página.
				"post"      => 0,    // Permissão para acessar a função post ou requisição post desta página.
				"put"       => 0,    // Permissão para acessar a função put ou requisição put desta página.
				"patch"     => 0,    // Permissão para acessar a função patch ou requisição patch desta página.
				"del"       => 0,    // Permissão para acessar a função delete ou requisição delete desta página.
				"api"       => 0,    // Permissão para acessar a função API desta página.
				"especific" => [],
			],

			// Caminho para página de login.
			'loginPage' => "login/", // Page login dentro do modelo.
		];

		// Configuração personalizada do endpoins.
		self::$params['config'] = [
			'title' => 'DISCIPULADO',  // Título da página exibido na aba/janela navegador.
		];

		// Carrega estrutura html. Somente pages.
		self::$params['structure']   = [
			// // Origem
			'html'        => 'maanaim',   // Estrutura HTML geral.

			// // Complementos
			'head'         => 'maanaim',   // <head> da página.
			'top'          => 'maanaim',   // Logo após a tag <body>.
			'header'       => 'maanaim',   // Após a estrutura "top".
			'nav'          => 'maanaim',   // Dentro do header ou personalizado.
			'content_top'  => 'maanaim',   // Antes do conteúdo da página.
			'content_page' => 'maanaim',   // Reservado para conteúdo da página. Sobrescrito depois.
			'content_end'  => 'maanaim',   // Depois do conteúdo da página.
			'footer'       => 'maanaim',   // footer da página.
			'end'          => 'maanaim',   // Fim da página.
		];

		// Carrega na página plugins (template/assets/css/) Somente pages.
		self::$params['plugins']     = [
			'modelo',   // Exemplo.
		];
	}


	/**
	 * get
	 * 
	 * Função principal.
	 * Recebe todos os parâmetros do endpoint em $params.
	 *
	 * @param  mixed $params
	 */
	public function get($params)
	{
		$options = [
			'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/DISCIPULADO/discipulado.jpg',
			'title' => $params['config']['title'],
			'texto' => 'O discipulado não termina quando o acampamento acaba. É o começo de uma jornada profunda com Cristo.',
		];
		self::$params['tituloPagina'] = Render::obj('blocos/titulo-pagina.html', $options);
		self::$params['htmlAssine'] = Render::obj('blocos/form-assine-discipulado.html', $params);
		self::$params['html'] = ""; // conteúdo html da página.
	}
}
