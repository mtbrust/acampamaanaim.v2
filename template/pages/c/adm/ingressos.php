<?php

namespace pages;

use desv\classes\AccessControl;
use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use desv\controllers\Render;
use template\classes\maanaim\Maanaim;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class ingressos extends EndPoint
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
			'ativo' => 1,
			// Usuário só acessa logado.
			'session' => 1,
			// Caminho para página de login.
			'loginPage' => "login/", // Page login dentro do modelo.
		];

		// Configuração personalizada do endpoins.
		self::$params['config'] = [
			'title' => 'Ingressos',  // Título da página exibido na aba/janela navegador.
		];

		// Carrega na página plugins (template/assets/css/) Somente pages.
		self::$params['plugins']     = [
			'modelo',   // Exemplo.
		];

		// Carrega estrutura html. Somente pages.
		self::$params['structure']   = [
			// // Origem
			'html'        => 'admin',   // Estrutura HTML geral.

			// // Complementos
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
		// Sub menus da página.
		self::$params['subMenus'] = ['adicionar', 'editar'];

		self::$params['eventos'] = Maanaim::listarEventos();
		self::$params['html'] = Render::obj('blocos/eventos_ingressos_cards_mini.html', self::$params);
	}

	public function adicionar($params)
	{
		// Sub menus da página.
		self::$params['subMenus'] = ['adicionar', 'editar'];

		if (!isset($params['infoUrl']['attr'][1])) {
			self::$params['eventos'] = Maanaim::listarEventos();
			self::$params['html'] = Render::obj('blocos/eventos_ingressos_cards_mini.html', self::$params);
		} else {
			self::$params['ingresso']['id_evento'] = $params['infoUrl']['attr'][1];
			self::$params['ingresso']['idStatus'] = 1;
			self::$params['html'] = Render::obj('forms/form-ingressos.html', self::$params);
		}
	}

	public function editar($params)
	{
		// Sub menus da página.
		self::$params['subMenus'] = ['adicionar', 'editar'];

		if (!isset($params['infoUrl']['attr'][1])) {
			self::$params['eventos'] = Maanaim::listarEventos();
			self::$params['html'] = Render::obj('blocos/eventos_ingressos_cards_mini.html', self::$params);
		} else {
			$id = $params['infoUrl']['attr'][1];

			// Lista ingresso específico.
			self::$params['ingresso'] = Maanaim::listarIngresso($id, ['ativos' => 0]);

			// Caso não exista o ingresso.
			if (empty(self::$params['ingresso'])) {
				self::$params['eventos'] = Maanaim::listarEventos();
				self::$params['html'] = Render::obj('blocos/eventos_ingressos_cards_mini.html', self::$params);
			} else {
				self::$params['html'] = Render::obj('forms/form-ingressos.html', self::$params);
			}
		}
	}

	public function post($params)
	{
		// Caso não tenha passado id do ingresso, mostra página inicial e finaliza.
		if (!isset($params['infoUrl']['attr'][0])) {
			$this->get($params);
			return true;
		}

		$funcao = $params['infoUrl']['attr'][0];

		switch ($funcao) {
			case 'adicionar':
				Maanaim::adicionarIngresso($_POST);

				$this->$funcao($params);
				break;
			case 'editar':
				$id = $params['infoUrl']['attr'][1];
				$r = Maanaim::editarIngresso($id, $_POST);

				$this->$funcao($params);
				break;

			default:

				self::$params['html'] = 'teste2';
				break;
		}
	}

	public function api($params)
	{
		// Finaliza a execução da função.
		self::$params['render']['content_type'] = 'application/json';
		self::$params['response'] 	= "Teste";
		self::$params['msg']		= 'Teste de api realizado com sucesso.';
		self::$params['status']   	= 200;
	}
}
