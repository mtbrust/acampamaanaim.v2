<?php

namespace pages;

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
class index extends EndPoint
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

		// Configuração personalizada do endpoins.
		self::$params['config']      = [
			// PAGES - INFORMAÇÕES DE SEO HTML
			// *********************
			// Informações que vão ser usadas para SEO na página.
			'title'            => 'Restrito',  // Título da página exibido na aba/janela navegador.
		];

		// Opções de segurança.
		self::$params['security']    = [

			// Controller usará controller de segurança.
			'ativo'             => true,

			// Usuário só acessa logado.
			'session'           => true,

			// Permissões personalizadas da página atual. 
			// [1] Usuário tem que ter permissão, [0] Não necessita permissão.
			'permission'        => [
				"session" => 1,   // Necessário usuário com sessao nesta página.
				"get"     => 1,   // Permissão para acessar a função get desta página.
				"getFull" => 1,   // Permissão para acessar a função getFull desta página.
				"post"    => 1,   // Permissão para acessar a função post ou requisição post desta página.
				"put"     => 1,   // Permissão para acessar a função put ou requisição put desta página.
				"patch"   => 1,   // Permissão para acessar a função patch ou requisição patch desta página.
				"del"  	  => 1,   // Permissão para acessar a função delete ou requisição delete desta página.
				"api"     => 1,   // Permissão para acessar a função API desta página.
				"especific" => [
					'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
					'botao_editar' => 1,
				],
			],
		];

		// Carrega estrutura html. Somente pages.
		self::$params['structure']   = [
			// Origem
			'html'        => 'admin',   // Estrutura HTML geral.

			// Complementos
			'head'         => 'admin',   // <head> da página.
			'top'          => 'admin',   // Logo após a tag <body>.
			'header'       => 'admin',   // Após a estrutura "top".
			'nav'          => 'admin',   // Dentro do header ou personalizado.
			'content_top'  => 'admin',   // Antes do conteúdo da página.
			'content_page' => 'admin',   // Reservado para conteúdo da página. Sobrescrito depois.
			'content_end'  => 'admin',   // Depois do conteúdo da página.
			'footer'       => 'admin',   // footer da página.
			'end'          => 'admin',   // Fim da página.
		];

		// Monta estrutura de parâmetros passados na url ou metodo.
		self::$params['menus']       = [
			// Função:
			'get' => [
				'title'      => 'Listar',      // Nome exibido no menu. Somente pages.
				'permission' => [
					"session" => 1,   // Necessário usuário com sessao nesta página.
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
	 *
	 * @param  mixed $params
	 */
	public function get($params)
	{
		// Informações para montar a página.
		self::$params['html'] = Render::obj('docs/show_params.html', $params);
	}
}
