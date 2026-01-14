<?php

namespace pages;

use desv\classes\AccessControl;
use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use desv\controllers\Render;
use template\classes\maanaim\Maanaim;
use template\classes\PDF;

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
			case 'pdf':
				// Gera PDF da relação de inscritos por quarto
				// Na URL adm/quartos/api/pdf/{idEvento}, attr[0] é 'pdf' e attr[1] é o idEvento
				$idEvento = $params['infoUrl']['attr'][2] ?? $params['infoUrl']['attr'][2] ?? null;
				if ($idEvento) {
					self::$params['render']['content_type'] = 'application/pdf';
					$ret = $this->gerarPdfQuartos($idEvento);
					self::$params['response'] = $ret;
					self::$params['msg'] = 'PDF gerado com sucesso.';
				} else {
					self::$params['response'] = ['error' => true];
					self::$params['msg'] = 'ID do evento não informado.';
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

	private function gerarPdfQuartos($idEvento)
	{
		// Carrega o evento usando listarEvento que já funciona
		$evento = Maanaim::listarEvento($idEvento);
		// Garante que $evento é um array válido
		if (!is_array($evento) || empty($evento)) {
			$evento = ['titulo_evento' => 'Evento não encontrado', 'nome_evento' => 'Evento não encontrado'];
		}
		self::$params['evento'] = $evento;

		// Carrega as inscrições com dados completos para PDF
		$options = [];
		$options['quartos'] = true;
		$options['pdf'] = true;
		$inscricoes = Maanaim::listarInscricoes($idEvento, $options);
		
		// Garante que $inscricoes é um array
		if (!is_array($inscricoes)) {
			$inscricoes = [];
		}
		
		// Organiza as inscrições por sexo e quarto
		$inscricoesMasculinasPorQuarto = [];
		$inscricoesMasculinasSemQuarto = [];
		$inscricoesFemininasPorQuarto = [];
		$inscricoesFemininasSemQuarto = [];
		
		foreach ($inscricoes as $inscricao) {
			$sexo = strtolower($inscricao['sexo'] ?? '');
			
			// Calcula a idade (idade no evento se disponível, senão idade atual)
			if (!empty($inscricao['dtNascimento'])) {
				if (!empty($evento['dt_inicio_evento'])) {
					$inscricao['idade'] = Maanaim::idadeNoEvento($inscricao['dtNascimento'], $evento['dt_inicio_evento']);
				} else {
					// Calcula idade atual
					$dtNascimento = new \DateTime($inscricao['dtNascimento']);
					$hoje = new \DateTime();
					$intervalo = $dtNascimento->diff($hoje);
					$inscricao['idade'] = (int)$intervalo->format('%y');
				}
			} else {
				$inscricao['idade'] = '-';
			}
			
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

		// Pego a imagem da logo e mando para o pdf
		$logoPath = self::$params['config']['favicon'] ?? self::$params['config']['image'] ?? 'template/assets/midias/logo/maanaim-logo.png';
		if (file_exists('./' . $logoPath)) {
			$data = file_get_contents('./' . $logoPath);
			$logoBase64 = base64_encode($data);
			self::$params['logo'] = 'data:image/png;base64, ' . $logoBase64;
		} else {
			self::$params['logo'] = '';
		}

		// Data atual
		self::$params['info']['dataAtual'] = date('d');
		self::$params['info']['anoAtual'] = date('Y');

		// Renderiza o template do PDF
		$htmlPdf = Render::obj('docs/relacao-inscritos-quartos.html', self::$params);
		
		// Gera e retorna o PDF usando o método arquivo (corrigido para retornar binário)
		$ret = PDF::arquivo($htmlPdf);
		
		return $ret;
	}
}
