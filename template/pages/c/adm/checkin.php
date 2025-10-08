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

		// Carrega na página scripts (template/assets/js/) Somente pages.
		self::$params['scripts'] = [
			// pasta libs.
			'libs' => [
				'jquery/jquery.min.js',   		// Exemplo.
				'jquery/jquery.redirect.js',   		// Exemplo.
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
		// Sub menus da página.
		// self::$params['subMenus'] = ['adicionar', 'editar'];

		if (!isset($params['infoUrl']['attr'][0])) {
			// Lista de eventos.
			self::$params['eventos'] = Maanaim::listarEventos();
			self::$params['html'] = Render::obj('blocos/eventos_cards_mini_checkin.html', self::$params);
		} else {

			$idEvento = $params['infoUrl']['attr'][0];

			// Lista de eventos.
			self::$params['evento'] = Maanaim::listarEvento($idEvento);
			self::$params['inscricoes'] = Maanaim::listarInscricoes($idEvento);
			self::$params['html'] = Render::obj('blocos/checkin.html', self::$params);
		}
	}

	public function api($params)
	{
		// Finaliza a execução da função.
		self::$params['render']['content_type'] = 'application/json';
		self::$params['response'] 	= "Teste";
		self::$params['msg']		= 'Teste de api realizado com sucesso.';
		self::$params['status']   	= 200;

		$tipo = '';
		if (isset($params['infoUrl']['attr'][1])) {
			$tipo = $params['infoUrl']['attr'][1];
		}

		switch ($tipo) {
			case 'atualizar':
				$ret = 'ok';
				$msg = 'ok';
				break;
			case 'doc':

				$r = $this->editarCheckin($_POST['f-id'], ['documentacao' => $_POST['f-value']]);
				$ret = $r[0];
				$msg = $r[1];

				break;
			case 'pag':

				$r = $this->editarCheckin($_POST['f-id'], ['pago' => $_POST['f-value']]);
				$ret = $r[0];
				$msg = $r[1];

				break;
			case 'checkin':

				$r = $this->editarCheckin($_POST['f-id'], ['checkin' => $_POST['f-value']]);
				$ret = $r[0];
				$msg = $r[1];

				break;
			case 'quarto':

				$r = $this->editarCheckin($_POST['f-id'], ['quarto' => $_POST['f-value']]);
				$ret = $r[0];
				$msg = $r[1];

				break;
			case 'valorpago':

				$r = $this->editarCheckin($_POST['f-id'], ['valorPago' => $_POST['f-value']]);
				$ret = $r[0];
				$msg = $r[1];

				break;
			case 'inscricao':
				$ret = Maanaim::getInscricaoPorId($_POST['f-id']);
				$msg = 'Inscrição atualizada para ' . $_POST['f-name'];
				break;
			case 'acampante':

				// ! ANTIGO
				// // Carrega as informações do evento.
				// $infoEventoAtual = MaanaimEventos::load();
				// // Manda as informações do evento para dentro da página.
				// $this->params['page']['info'] = $infoEventoAtual;

				// $id = $_POST['f-id'];
				// $this->params['page']['inscricao'] = maanaim\BdInscricoes::selecionaPorId($id);


				// $this->params['page']['id'] = $id;

				// // Valores para o formulário de inscrição
				// $this->params['page']['eCivil']        = \controle\BdStatus::selecionaPorGrupo('users/estadoCivil');
				// $this->params['page']['cnhCategoria']  = \controle\BdStatus::selecionaPorGrupo('users/cnhCategoria');
				// $this->params['page']['pais']          = \controle\BdStatus::selecionaPorGrupo('adresses/pais');
				// $this->params['page']['uf']            = \controle\BdStatus::selecionaPorGrupo('adresses/uf');

				// $html = ControllerRender::renderObj('maanaim/acampante-info', $this->params);


				// Pega a inscrição pelo id passado por url.
				$id = $_POST['f-id'];
				self::$params['inscricao'] = Maanaim::getInscricaoPorId($id);
				// DevHelper::printr(self::$params['inscricao']);
				$options = [
					'ingressoValidade' => false,    // Ingresso dentro da validade.
				];
				self::$params['eventos'] = json_encode(Maanaim::listarEventos($options));
				self::$params['formInscricao'] = true;
				self::$params['ingressoInfo'] = Maanaim::listarIngresso(self::$params['inscricao']['idIngresso'], ['validade' => false]);
				// self::$params['evento'] = self::$params['eventos'][0];
				self::$params['evento']['ingressos'] = [];
				self::$params['evento']['ingressos'][0] = self::$params['ingressoInfo'];
				self::$params['evento']['maior_valor'] = self::$params['evento']['ingressos'][0]['valor_ingresso'];
				self::$params['inscricaoStatus'] = Maanaim::$statusInscricao;
				self::$params['urlApiInscricao'] = self::$params['base']['url'] . 'adm/inscricoes/api/';
				self::$params['infoUrl']['func'] = 'editar'; // Precisa
				$ret = Render::obj('forms/form-inscricao.html', self::$params);
				self::$params['infoUrl']['func'] = 'api'; // Precisa

				$msg = 'Acampante atualizado.';
				break;
			case 'relatorio':

				self::$params['render']['content_type'] = 'application/csv';
				$ret = "\xEF\xBB\xBF";
				$ret = Maanaim::relatorioCheckin($params['infoUrl']['attr'][2]);
				$msg = '';
				break;
			default:
				$ret = 'error';
				$msg = 'Erro';
		}


		self::$params['msg'] = $msg;
		self::$params['response'] = $ret;
	}

	private function editarCheckin($id, $dados)
	{
		if (Maanaim::editarCheckIn($id, $dados)) {
			$ret = 'ok';
			$msg = 'Checkin atualizado para ' . $_POST['f-name'];
		} else {
			$ret = 'Erro';
			$msg = 'Tente novamente.';
		}

		return [$ret, $msg];
	}
}
