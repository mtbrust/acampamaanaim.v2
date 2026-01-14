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
class quartos extends EndPoint
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
			
			// IDs que tem permissão TOTAL a esta controller. Usar apenas para teste.
			'ids'            => [
			    2, // Login ID: 2.
			],
			'groups' => [
				1, // Grupo ID: 1.
			],
		];

		// Configuração personalizada do endpoins.
		self::$params['config'] = [
			'title' => 'Pessoas',  // Título da página exibido na aba/janela navegador.
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
		// self::$params['subMenus'] = ['masculino', 'feminino'];
		
		self::$params['html'] = "Dashboard";

		if (!isset($params['infoUrl']['attr'][0])) {
			self::$params['eventos'] = Maanaim::listarEventos();
			self::$params['listarInscricoes'] = true; // Exibe o botão para listar inscrições.
			self::$params['html'] = Render::obj('blocos/eventos_cards_mini_quartos.html', self::$params);
			// self::$params['html'] = 'Não temos id.';
		} else {

			$id = $params['infoUrl']['attr'][0];

			$options = [];
			$options['quartos'] = true;

			// Carrega as inscrições antes de renderizar o template
			$inscricoes = Maanaim::listarInscricoes($id, $options);
			
			// Organiza as inscrições por sexo e quarto
			$inscricoesMasculinasPorQuarto = [];
			$inscricoesMasculinasSemQuarto = [];
			$inscricoesFemininasPorQuarto = [];
			$inscricoesFemininasSemQuarto = [];
			
			foreach ($inscricoes as $inscricao) {
				$sexo = strtolower($inscricao['sexo'] ?? '');
				
				if (!empty($inscricao['quarto'])) {
					$quarto = $inscricao['quarto'];
					if ($sexo == 'masculino') {
						if (!isset($inscricoesMasculinasPorQuarto[$quarto])) {
							$inscricoesMasculinasPorQuarto[$quarto] = [];
						}
						$inscricoesMasculinasPorQuarto[$quarto][] = $inscricao;
					} elseif ($sexo == 'feminino') {
						if (!isset($inscricoesFemininasPorQuarto[$quarto])) {
							$inscricoesFemininasPorQuarto[$quarto] = [];
						}
						$inscricoesFemininasPorQuarto[$quarto][] = $inscricao;
					}
				} else {
					if ($sexo == 'masculino') {
						$inscricoesMasculinasSemQuarto[] = $inscricao;
					} elseif ($sexo == 'feminino') {
						$inscricoesFemininasSemQuarto[] = $inscricao;
					}
				}
			}
			
			self::$params['inscricoesMasculinasPorQuarto'] = $inscricoesMasculinasPorQuarto;
			self::$params['inscricoesMasculinasSemQuarto'] = $inscricoesMasculinasSemQuarto;
			self::$params['inscricoesFemininasPorQuarto'] = $inscricoesFemininasPorQuarto;
			self::$params['inscricoesFemininasSemQuarto'] = $inscricoesFemininasSemQuarto;
			self::$params['inscricoes'] = $inscricoes;

			self::$params['html'] = Render::obj('blocos/quartos_drag_drop.html', self::$params);
		}
	}

	public function adicionar($params)
	{
		// Sub menus da página.
		self::$params['subMenus'] = ['adicionar', 'editar'];

		self::$params['html'] = Render::obj('forms/form-ingressos.html', self::$params);
	}

	public function editar($params)
	{
		// Sub menus da página.
		self::$params['subMenus'] = ['adicionar', 'editar'];

		self::$params['html'] = Render::obj('forms/form-ingressos.html', self::$params);
	}

	public function api($params)
	{
		// Finaliza a execução da função.
		self::$params['render']['content_type'] = 'application/json';
		self::$params['response'] 	= [];
		self::$params['msg']		= '';
		self::$params['status']   	= 200;

		$tipo = '';
		if (isset($params['infoUrl']['attr'][1])) {
			$tipo = $params['infoUrl']['attr'][1];
		}

		switch ($tipo) {
			case 'atualizar':
				self::$params['response'] = ['status' => 'ok'];
				self::$params['msg'] = 'ok';
				break;
			case 'quarto':
				if (isset($_POST['f-id']) && isset($_POST['f-value'])) {
					$id = $_POST['f-id'];
					$quarto = $_POST['f-value'] == '' || $_POST['f-value'] == 'null' ? null : $_POST['f-value'];
					$r = $this->editarQuarto($id, $quarto);
					self::$params['response'] = ['error' => $r[0]];
					self::$params['msg'] = $r[1];
				} else {
					self::$params['response'] = ['error' => true];
					self::$params['msg'] = 'Parâmetros inválidos.';
				}
				break;
			default:
				self::$params['response'] = ['error' => true];
				self::$params['msg'] = 'Endpoint não encontrado.';
		}
	}

	private function editarQuarto($id, $value)
	{
		if (Maanaim::editarQuarto($id, $value)) {
			$ret = false; // error = false significa sucesso
			$msg = $value === null ? 'Quarto removido com sucesso.' : 'Quarto atualizado para ' . $value;
		} else {
			$ret = true; // error = true significa erro
			$msg = 'Erro ao atualizar quarto. Tente novamente.';
		}

		return [$ret, $msg];
	}
}
