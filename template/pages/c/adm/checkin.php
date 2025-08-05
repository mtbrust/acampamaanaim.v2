<?php

namespace pages;

use desv\classes\AccessControl;
use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use template\classes\Maanaim;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class checkin extends EndPoint
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
			'title' => 'Check-In',  // Título da página exibido na aba/janela navegador.
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
		// todo - Desenvolver a função que tras informações dessa página.
		// DevHelper::printr(Maanaim::ping());
		
		self::$params['html'] = "Conteúdo da página.";
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
