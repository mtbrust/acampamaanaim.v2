<?php

namespace pages;

use desv\classes\AccessControl;
use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use desv\controllers\Render;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class login extends EndPoint
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
		self::$params['config'] = [
			'title' => 'Login',  // Título da página exibido na aba/janela navegador.
		];

		// Carrega estrutura html. Somente pages.
		self::$params['structure']   = [
			// // Origem
			'html'        => 'login',   // Estrutura HTML geral.

			// // Complementos
			'head'         => 'empty',   // <head> da página.
			'top'          => 'empty',   // Logo após a tag <body>.
			'header'       => 'empty',   // Após a estrutura "top".
			'nav'          => 'empty',   // Dentro do header ou personalizado.
			'content_top'  => 'empty',   // Antes do conteúdo da página.
			'content_page' => 'empty',   // Reservado para conteúdo da página. Sobrescrito depois.
			'content_end'  => 'empty',   // Depois do conteúdo da página.
			'footer'       => 'empty',   // footer da página.
			'end'          => 'empty',   // Fim da página.
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
		// Verifica se usuário já está logado.
		if (AccessControl::logOn()) {
			self::$params['html'] = Render::obj('forms/logon.html', $params);
		} else {
			self::$params['html'] = Render::obj('forms/login.html', $params);
		}
	}

	public function post($params)
	{
		if (empty($_POST['redirect_url'])) {
			$_POST['redirect_url'] = $params['base']['url'] . 'desv/';
		}

		// Tenta realizar o login.
		$result = AccessControl::logIn($_POST['user'], $_POST['senha'], $_POST['redirect_url']);

		// Salva url de onde foi restrito.
		$params['_get']['redirect_url'] = 'Login';
		// Salva informações do login.
		$params['_get']['redirect_msg'] = $result['msg'];

		// Chama a função get novamente para criar a página. (caso login não redirecione.)
		self::get($params);
	}
}
