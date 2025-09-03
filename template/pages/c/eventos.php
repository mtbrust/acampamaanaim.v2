<?php

namespace pages;

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
class eventos extends EndPoint
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
			'title' => 'EVENTOS',  // Título da página exibido na aba/janela navegador.
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
		
		// Carrega na página scripts (template/assets/js/) Somente pages.
		self::$params['scripts'] = [
			// pasta libs.
			'libs' => [
				'jquery/jquery.min.js',   		// Exemplo.
				'jquery/jquery.redirect.js',   		// Exemplo.
			],
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
			'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/INSCRICAO/Inscricao.jpg',
			'title' => $params['config']['title'],
			'texto' => 'Momentos únicos para fortalecer sua fé, criar novas amizades e viver experiências transformadoras. Escolha sua temporada e faça parte dessa jornada!',
		];


		// Verifico se é um evento específico.
		if (isset($params['infoUrl']['attr'][0])) {
			$params['page_tipo'] = 'evento';
			$options['title'] = $params['infoUrl']['attr'][0];
			$options['texto'] = 'Evento tralala';
			self::$params['config']['title'] = 'EVENTO - ' . $params['infoUrl']['attr'][0];
			self::$params['evento'] = $this->pagina_evento($params);
			self::$params['evento']['ingressosHtml'] = Render::obj('blocos/ingressos.html', self::$params);

			// Caso não encontre o evento.
			if (!isset(self::$params['evento']['titulo_evento'])) {
				self::$params['evento']['titulo_evento'] = 'Evento não encontrado';
				self::$params['evento']['obs_evento'] = 'Evento não encontrado';
				self::$params['html'] = '<h1 class="mt-5 my-3">Evento não encontrado</h1>';
			}

			$options = [
				'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/INSCRICAO/Inscricao.jpg',
				'title' => self::$params['evento']['titulo_evento'],
				'texto' => self::$params['evento']['obs_evento'],
			];
			self::$params['config']['title'] = self::$params['evento']['titulo_evento'];
		} else {
			// Caso seja para mostrar todos os eventos.
			$params['page_tipo'] = 'eventos';
			$this->pagina_eventos($params);
		}


		self::$params['tituloPagina'] = Render::obj('blocos/titulo-pagina.html', $options);
		self::$params['htmlAssine'] = Render::obj('blocos/form-assine-discipulado.html', $params);
	}

	public function pagina_evento($params)
	{
		$idEvento = $params['infoUrl']['attr'][0];
		$evento = Maanaim::listarEvento($idEvento);
		$evento['maior_valor'] = Maanaim::maiorValorIngresso($idEvento);
		$evento['ingressos'] = Maanaim::listarIngressosEvento($idEvento);
		return $evento;
	}

	public function pagina_eventos($params)
	{
		$params['eventos'] = Maanaim::listarEventos(['ativos' => 1]);
		self::$params['htmlEventos'] = Render::obj('blocos/eventos_simples.html', $params);
		self::$params['html'] = ""; // conteúdo html da página.
	}

	public function post($params)
	{
		self::$params['formInscricao'] = true;

		$idEvento = $params['infoUrl']['attr'][0];
		$evento = Maanaim::listarEvento($idEvento);
		$options = [
			'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/INSCRICAO/Inscricao.jpg',
			'title' => $evento['titulo_evento'],
			'texto' => $evento['obs_evento'],
		];
		self::$params['tituloPagina'] = Render::obj('blocos/titulo-pagina.html', $options);
		self::$params['htmlAssine'] = Render::obj('blocos/form-assine-discipulado.html', $params);

		self::$params['ingressoInfo'] = Maanaim::listarIngresso($_POST['f-ingresso']);
		self::$params['evento'] = $evento;

		self::$params['evento']['ingressos'] = [];
		self::$params['evento']['ingressos'][0] = self::$params['ingressoInfo'];
		self::$params['evento']['maior_valor'] = self::$params['evento']['ingressos'][0]['valor_ingresso'];
		self::$params['ingressosHtml'] = Render::obj('blocos/ingressos.html', self::$params);

		// todo - Apenas para facilitar os testes.
		self::$params['inscricao'] = MaanaimCarga::fakeInscricao();

		// Formulário de inscrição.
		self::$params['urlApiInscricao'] = self::$params['base']['url'] . 'eventos/api/adicionar';
		self::$params['formInscricaoHtml'] = Render::obj('forms/form-inscricao.html', self::$params);
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

			default:
				self::$params['response'] = "Função não encontrada.";
				self::$params['status'] = 400;
				self::$params['msg'] = "Não foi encontrada a função da api.";
				break;
		}
	}
}
