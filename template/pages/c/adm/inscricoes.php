<?php

namespace pages;

use desv\classes\AccessControl;
use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use desv\controllers\Render;
use template\classes\maanaim\Maanaim;
use template\classes\maanaim\MaanaimCarga;
use template\classes\maanaim\MaanaimParse;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class inscricoes extends EndPoint
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
			'title' => 'Inscrições',  // Título da página exibido na aba/janela navegador.
		];

		// Carrega na página plugins (template/assets/css/) Somente pages.
		self::$params['plugins']     = [
			'modelo',   // Exemplo.
		];

		// Carrega na página scripts (template/assets/js/) Somente pages.
		self::$params['scripts']     = [
			// pasta libs.
			'libs' => [
				'jquery/jquery.min.js',   		// Exemplo.
				'jquery/jquery.redirect.js',   		// Exemplo.
			],
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
		$this->subMenu();

		self::$params['html'] = "Dashboard";

		// Formulário de inscrição.
		self::$params['formInscricaoHtml'] = '';
	}

	public function adicionar($params)
	{
		// Sub menus da página.
		$this->subMenu();

		$options = [
			'ingressoValidade' => false,    // Ingresso dentro da validade.
		];

		self::$params['eventos'] = json_encode(Maanaim::listarEventos($options));
		// DevHelper::printEncodedJson(self::$params['eventos']);

		// todo - inscrição fake. para ajudar no preenchimento e teste.
		if (isset($_GET['fake']) && $_GET['fake']) {
			self::$params['inscricao'] = MaanaimCarga::fakeInscricao();
			self::$params['formInscricao'] = true;
			self::$params['ingressoInfo'] = Maanaim::listarIngresso(self::$params['inscricao']['idIngresso'], ['validade' => false]);
			self::$params['evento']['ingressos'] = [];
			self::$params['evento']['ingressos'][0] = self::$params['ingressoInfo'];
			self::$params['evento']['maior_valor'] = self::$params['evento']['ingressos'][0]['valor_ingresso'];
			// DevHelper::printr(self::$params['inscricao']);
		}

		self::$params['inscricaoStatus'] = Maanaim::$statusInscricao;

		self::$params['urlApiInscricao'] = self::$params['base']['url'] . 'adm/inscricoes/api/adicionar/';
		self::$params['html'] = Render::obj('forms/form-inscricao.html', self::$params);
	}

	public function editar($params)
	{
		// Sub menus da página.
		$this->subMenu();

		// DevHelper::printr(self::$params);

		// Verifica se foi passado um id de inscrito.
		if (!isset($params['infoUrl']['attr'][1])) {
			self::$params['html'] = 'Vá em listar, para encontrar a inscrição que queira editar, ou preencha o id da inscrição que queira editar.';
			$this->listar($params);
		} else {

			// self::$params['inscricao'] = MaanaimCarga::fakeInscricao();
			// DevHelper::printr(self::$params['inscricao']);

			// Pega a inscrição pelo id passado por url.
			$id = $params['infoUrl']['attr'][1];
			self::$params['inscricao'] = Maanaim::getInscricaoPorId($id);
			// DevHelper::printr(self::$params['inscricao']);
			$options = [
				'ingressoValidade' => false,    // Ingresso dentro da validade.
			];
			self::$params['eventos'] = json_encode(Maanaim::listarEventos($options));
			self::$params['formInscricao'] = true;
			self::$params['ingressoInfo'] = Maanaim::listarIngresso(self::$params['inscricao']['idIngresso'], ['validade' => false]);
			self::$params['evento']['ingressos'] = [];
			self::$params['evento']['ingressos'][0] = self::$params['ingressoInfo'];
			self::$params['evento']['maior_valor'] = self::$params['evento']['ingressos'][0]['valor_ingresso'];


			// DevHelper::printr(self::$params['inscricao']);

			self::$params['inscricaoStatus'] = Maanaim::$statusInscricao;
			self::$params['urlApiInscricao'] = self::$params['base']['url'] . 'adm/inscricoes/api/';
			self::$params['html'] = Render::obj('forms/form-inscricao.html', self::$params);
		}
	}

	public function listar($params)
	{
		// Sub menus da página.
		$this->subMenu();

		if (!isset($params['infoUrl']['attr'][1])) {
			self::$params['eventos'] = Maanaim::listarEventos();
			self::$params['listarInscricoes'] = true; // Exibe o botão para listar inscrições.
			self::$params['html'] = Render::obj('blocos/eventos_cards_mini.html', self::$params);
			// self::$params['html'] = 'Não temos id.';
		} else {
			$id = $params['infoUrl']['attr'][1];

			$options = [];
			if (isset($_GET['status'])) {
				$options['status'] = $_GET['status'];
			}

			self::$params['inscricoes'] = Maanaim::listarInscricoes($id, $options);
			// DevHelper::printr(self::$params['inscricoes']);

			// Verifica se tem inscrições para este evento.
			if (empty(self::$params['inscricoes'])) {
				self::$params['html'] = 'Nenhuma inscrição para este evento.';
			} else {
				self::$params['html'] = Render::obj('blocos/inscricoes_cards_mini.html', self::$params);
			}
		}

		// self::$params['html'] = Render::obj('forms/form-inscricao.html', self::$params);
	}

	public function api($params)
	{
		// Valores padrão
		self::$params['response'] = "Padrão.";
		self::$params['render']['content_type'] = 'application/json';
		self::$params['status'] = 200;
		self::$params['msg'] = "Sucesso. Sem processamento.";

		switch ($params['infoUrl']['attr'][1]) {
			case 'teste':
				break;
			case 'adicionar':

				// Pego os campos tratados do evento.
				$inscricao = MaanaimParse::inscricaoPostToTable($_POST);

				// Inscrição adicionada.
				self::$params['response'] = Maanaim::adicionarInscricao($inscricao);

				// Verifico se teve algum erro.
				if (isset(self::$params['response']['error']) && self::$params['response']['error']) {
					self::$params['status'] = 200;
				}

				// Monto a mensagem de retorno.
				self::$params['msg'] = implode('<br>', self::$params['response']['msg']);

				break;
			case 'editar':

				// Pego os campos tratados do evento.
				$inscricao = MaanaimParse::InscricaoPostToTable($_POST);

				// Inscrição adicionada.
				self::$params['response'] = Maanaim::editarInscricao($inscricao);

				// Verifico se teve algum erro.
				if (isset(self::$params['response']['error']) && self::$params['response']['error']) {
					self::$params['status'] = 200;
				}

				// Monto a mensagem de retorno.
				self::$params['msg'] = implode('<br>', self::$params['response']['msg']);

				break;

			default:
				self::$params['response'] = "Função não encontrada.";
				self::$params['status'] = 400;
				self::$params['msg'] = "Não foi encontrada a função da api.";
				break;
		}
	}

	public function subMenu()
	{
		// Sub menus da página.
		self::$params['subMenus'] = ['adicionar', 'editar', 'listar'];
	}
}
