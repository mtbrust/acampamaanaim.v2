<?php

namespace pages;

use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use desv\controllers\Render;
use template\classes\maanaim\Maanaim;
use template\classes\PDF;
use template\classes\AssinaturaPNG;
use template\classes\bds\BdInscricoes;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class minha_inscricao extends EndPoint
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
			'title' => 'MINHA INSCRIÇÃO',  // Título da página exibido na aba/janela navegador.
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
			'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/SOBRE/BANNER-TOPO.jpg',
			'title' => $params['config']['title'],
			'texto' => 'Acompanhe a sua inscrição. Saiba como anda o status da sua inscrição e se necessita entrar em contato.',
		];
		self::$params['tituloPagina'] = Render::obj('blocos/titulo-pagina.html', $options);
		self::$params['htmlAssine'] = Render::obj('blocos/form-assine-discipulado.html', $params);
		self::$params['html'] = ""; // conteúdo html da página.
	}

	public function post($params)
	{
		self::$params['inscricao'] = false;

		// Verifica se tem o id e CPF.
		if (isset($_POST['f-idInscricao']) && isset($_POST['f-cpf']) && $_POST['f-idInscricao'] && $_POST['f-cpf']) {
			self::$params['inscricao'] = Maanaim::acompanhar($_POST['f-cpf'], $_POST['f-idInscricao']);
		}

		// DevHelper::printr(self::$params['inscricao']);

		if (!self::$params['inscricao']) {
			$this->get($params);
		}

		$options = [
			'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/SOBRE/BANNER-TOPO.jpg',
			'title' => $params['config']['title'],
			'texto' => 'Acompanhe a sua inscrição. Saiba como anda o status da sua inscrição e se necessita entrar em contato.',
		];
		self::$params['tituloPagina'] = Render::obj('blocos/titulo-pagina.html', $options);
		self::$params['htmlAssine'] = Render::obj('blocos/form-assine-discipulado.html', $params);

		self::$params['blocoAcompanheHtml'] = Render::obj('blocos/acompanhe.html', self::$params); // conteúdo html da página.

	}

	public function api($params)
	{
		// Finaliza a execução da função.
		self::$params['render']['content_type'] = 'application/json';
		self::$params['response'] 	= "";
		self::$params['msg']		= "";
		self::$params['status']   	= 200;

		$tipo = '';
		if (isset($params['infoUrl']['attr'][1])) {
			$tipo = $params['infoUrl']['attr'][1];
		}

		switch ($tipo) {
			case 'teste':
				$ret = 'Teste ok.';
				$msg = 'Teste realizado com sucesso.';
				break;
			case 'cancelar':
				// Valida se foi enviado ID e CPF
				if (!isset($_POST['id']) || !isset($_POST['cpf'])) {
					$ret = ['error' => true, 'msg' => 'ID e CPF são obrigatórios.'];
					$msg = 'Erro: ID e CPF são obrigatórios.';
					self::$params['status'] = 400;
					break;
				}

				$idInscricao = $_POST['id'];
				$cpf = $_POST['cpf'];

				// Verifica se a inscrição existe e pertence ao CPF informado
				$inscricao = Maanaim::acompanhar($cpf, $idInscricao);

				if (!$inscricao) {
					$ret = ['error' => true, 'msg' => 'Inscrição não encontrada ou CPF não confere.'];
					$msg = 'Erro: Inscrição não encontrada ou CPF não confere.';
					self::$params['status'] = 404;
					break;
				}

				// Verifica se o status permite cancelamento (apenas Fila de Espera)
				if ($inscricao['status'] != 'Fila de Espera') {
					$ret = ['error' => true, 'msg' => 'Apenas inscrições em "Fila de Espera" podem ser canceladas pelo próprio inscrito.'];
					$msg = 'Erro: Apenas inscrições em "Fila de Espera" podem ser canceladas pelo próprio inscrito.';
					self::$params['status'] = 400;
					break;
				}

				// Atualiza o status para Cancelada
				$bdInscricoes = new \template\classes\bds\BdInscricoes();
				$updateData = [
					'status' => 'Cancelada',
					'idStatus' => 0,
					'obs' => ($inscricao['obs'] ? $inscricao['obs'] . ' ' : '') . 'Cancelada pelo próprio inscrito em ' . date('d/m/Y H:i:s') . '.'
				];

				$resultado = $bdInscricoes->update($idInscricao, $updateData);

				if ($resultado) {
					$ret = ['error' => false, 'msg' => 'Inscrição cancelada com sucesso.', 'id' => $idInscricao];
					$msg = 'Inscrição cancelada com sucesso.';
				} else {
					$ret = ['error' => true, 'msg' => 'Erro ao cancelar a inscrição. Tente novamente.'];
					$msg = 'Erro ao cancelar a inscrição.';
					self::$params['status'] = 500;
				}
				break;
			case 'pagbank':
				// Webhook do PagBank para notificações de pagamento
				
				// Lê o body da requisição (JSON)
				$json = file_get_contents('php://input');
				$webhookData = json_decode($json, true);

				// Log para debug (opcional - remover em produção ou usar sistema de logs)
				// file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . ' - ' . $json . PHP_EOL, FILE_APPEND);

				if (!$webhookData) {
					$ret = ['error' => true, 'msg' => 'Dados inválidos no webhook.'];
					$msg = 'Erro: Dados inválidos.';
					self::$params['status'] = 400;
					break;
				}

				// Verifica se tem a referência (ID da inscrição)
				$idInscricao = null;
				if (isset($webhookData['reference_id'])) {
					$idInscricao = $webhookData['reference_id'];
				} elseif (isset($webhookData['reference'])) {
					$idInscricao = $webhookData['reference'];
				} elseif (isset($webhookData['order']) && isset($webhookData['order']['reference_id'])) {
					$idInscricao = $webhookData['order']['reference_id'];
				}

				if (!$idInscricao) {
					$ret = ['error' => true, 'msg' => 'Referência (ID da inscrição) não encontrada no webhook.'];
					$msg = 'Erro: Referência não encontrada.';
					self::$params['status'] = 400;
					break;
				}

				// Busca a inscrição
				$inscricao = Maanaim::getInscricaoPorId($idInscricao);

				if (!$inscricao) {
					$ret = ['error' => true, 'msg' => 'Inscrição não encontrada.'];
					$msg = 'Erro: Inscrição não encontrada.';
					self::$params['status'] = 404;
					break;
				}

				// Verifica o status do pagamento
				$statusPagamento = null;
				if (isset($webhookData['status'])) {
					$statusPagamento = $webhookData['status'];
				} elseif (isset($webhookData['order']) && isset($webhookData['order']['status'])) {
					$statusPagamento = $webhookData['order']['status'];
				} elseif (isset($webhookData['charges']) && is_array($webhookData['charges']) && isset($webhookData['charges'][0]['status'])) {
					$statusPagamento = $webhookData['charges'][0]['status'];
				}

				// Status possíveis do PagBank: PAID, IN_ANALYSIS, DECLINED, CANCELED, etc.
				// Verifica se o pagamento foi aprovado
				$pagamentoAprovado = false;
				if ($statusPagamento) {
					$statusPagamentoUpper = strtoupper($statusPagamento);
					// Status que indicam pagamento aprovado
					if (in_array($statusPagamentoUpper, ['PAID', 'APPROVED', 'CONFIRMED', 'COMPLETED'])) {
						$pagamentoAprovado = true;
					}
				}

				// Se o pagamento foi aprovado e a inscrição ainda não está confirmada
				if ($pagamentoAprovado && $inscricao['status'] != 'Confirmada') {
					$bdInscricoes = new BdInscricoes();
					$updateData = [
						'status' => 'Confirmada',
						'idStatus' => 1,
						'obs' => ($inscricao['obs'] ? $inscricao['obs'] . ' ' : '') . 'Pagamento confirmado via PagBank em ' . date('d/m/Y H:i:s') . '.'
					];

					$resultado = $bdInscricoes->update($idInscricao, $updateData);

					if ($resultado) {
						$ret = [
							'error' => false,
							'msg' => 'Status da inscrição atualizado para Confirmada.',
							'inscricao_id' => $idInscricao,
							'status_anterior' => $inscricao['status'],
							'status_novo' => 'Confirmada',
							'status_pagamento' => $statusPagamento
						];
						$msg = 'Webhook processado com sucesso. Inscrição confirmada.';
					} else {
						$ret = [
							'error' => true,
							'msg' => 'Erro ao atualizar status da inscrição.',
							'inscricao_id' => $idInscricao
						];
						$msg = 'Erro ao atualizar status.';
						self::$params['status'] = 500;
					}
				} else {
					// Pagamento não aprovado ou já estava confirmada
					$ret = [
						'error' => false,
						'msg' => 'Webhook recebido, mas status não foi alterado.',
						'inscricao_id' => $idInscricao,
						'status_atual' => $inscricao['status'],
						'status_pagamento' => $statusPagamento,
						'razao' => $inscricao['status'] == 'Confirmada' ? 'Inscrição já estava confirmada.' : 'Pagamento não foi aprovado.'
					];
					$msg = 'Webhook processado.';
				}

				// Retorna 200 OK para o PagBank (importante para não receber múltiplas tentativas)
				self::$params['status'] = 200;
				break;
			case 'termosecompromisso':

				$idInscricao = 0;
				if (isset($params['infoUrl']['attr'][2])) {
					$idInscricao = $params['infoUrl']['attr'][2];
				}

				$inscricao = Maanaim::getInscricaoPorId($idInscricao);

				$ret = $inscricao;
				self::$params['inscricao'] = $inscricao;
				self::$params['evento'] = Maanaim::listarEvento($inscricao['idEvento']);
				self::$params['evento']['ingressos'][0] = Maanaim::listarIngresso($inscricao['idIngresso'], ['validade' => false]);


				// Pego a imagem da logo e mando para o pdf.
				$data = file_get_contents('./' .self::$params['config']['image']);
				$logoBase64 = base64_encode($data);
				self::$params['logo'] = 'data:image/png;base64, ' . $logoBase64;

				$data = file_get_contents('./template/assets/midias/fonts/Arizonia-Regular.ttf');
				$fontBase64 = base64_encode($data);
				self::$params['font_arizonia'] = 'data:font/ttf;base64, ' . $fontBase64;

				// DevHelper::printr(self::$params['font_arizonia']);

				// DevHelper::printr(self::$params);
				// DevHelper::printr(self::$params['inscricao']);
				// DevHelper::printr(self::$params['evento']);

				// Se for menor de idade, usa o nome do representante
				$nomeAssinatura = $inscricao['nome'];
				if (!empty($inscricao['menor']) && $inscricao['menor'] == 1 && !empty($inscricao['RepNome'])) {
					$nomeAssinatura = $inscricao['RepNome'];
				}

				$options = [
					'text' => $nomeAssinatura,
					'font' => 'template/assets/midias/fonts/Arizonia-Regular.ttf',
					'fontSize' => 45,
					'textColor' => [33, 150, 243],
					'padding' => 0,
				];
				self::$params['assinaturaRepresentante'] = AssinaturaPNG::create($options);

				$options = [
					'text' => 'Felipe Silva Conti',
					'font' => 'template/assets/midias/fonts/Arizonia-Regular.ttf',
					'fontSize' => 45,
					'textColor' => [33, 150, 243],
					'padding' => 0,
				];
				self::$params['assinaturaPresidente'] = AssinaturaPNG::create($options);


				$htmlTermos = Render::obj('docs/termos-e-compromissos.html', self::$params);
				// self::$params['render']['content_type'] = 'text/html';
				// DevHelper::printr($htmlTermos);
				// DevHelper::printr(self::$params['base']['dir'] . self::$params['config']['image']);
				// DevHelper::printr(self::$params['base']['dir']);
				// $arquivos = scandir(self::$params['base']['dir'] . 'template/assets/midias/logo/');
				// echo $_SERVER['DOCUMENT_ROOT'];
				// $arquivos = scandir('../../desv/acampamaanaim.v2/template/assets/midias/logo/');
				// $arquivos = scandir('../../');
				// DevHelper::printr($arquivos);

				self::$params['render']['content_type'] = 'application/pdf';
				$ret = PDF::arquivo($htmlTermos);

				$msg = 'ok';
				break;
			default:
				$ret = 'error';
				$msg = 'Erro';
		}


		self::$params['msg'] = $msg;
		self::$params['response'] = $ret;
	}
}
