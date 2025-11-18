<?php

namespace pages;

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

				$data = file_get_contents('./template/assets/midias/font/Arizonia-Regular.ttf');
				$fontBase64 = base64_encode($data);
				self::$params['font_arizonia'] = 'data:font/ttf;base64, ' . $fontBase64;

				// DevHelper::printr(self::$params['font_arizonia']);

				// DevHelper::printr(self::$params);
				// DevHelper::printr(self::$params['inscricao']);
				// DevHelper::printr(self::$params['evento']);

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
