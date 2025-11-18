<?php

namespace pages;

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
class galeria extends EndPoint
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
			'title' => 'GALERIA',  // Título da página exibido na aba/janela navegador.
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
		if ($this->mostrar_album()) return;

		// Busca eventos do banco de dados (ativos e passados, pois são álbuns)
		$options = [
			'ativos' => true,   // Apenas eventos ativos
			'qtd' => 20,        // Quantidade de eventos
			'ingressoValidade' => false, // Não precisa validar ingressos para galeria
		];
		$eventosDb = Maanaim::listarEventos($options);

		// Mapeia os eventos do banco para a estrutura esperada pelo template
		$eventos['eventos'] = [];
		if ($eventosDb) {
			foreach ($eventosDb as $evento) {
				// Monta a URL do banner (seguindo o padrão usado em outros templates)
				$banner = '';
				if (!empty($evento['url_midia_banner'])) {
					$banner = $params['base']['url'] . $evento['url_midia_banner'];
				} elseif (!empty($evento['url_midia_01'])) {
					// Usa a primeira mídia como fallback
					$banner = $params['base']['url'] . $evento['url_midia_01'];
				} else {
					// Fallback para imagem padrão se não tiver nenhuma mídia
					$banner = $params['base']['dir_relative'] . 'template/assets/midias/site/HOME/evento-01.jpg';
				}

				// Usa obs_evento como descrição, ou uma descrição padrão
				$descricao = !empty($evento['obs_evento']) 
					? $evento['obs_evento'] 
					: 'Cada imagem conta uma história de fé, comunhão e renovação. Reviva os instantes especiais do Acampamento Maanaim e sinta a presença de Deus em cada detalhe';

				$eventos['eventos'][] = [
					'banner' => $banner,
					'id' => $evento['id'],
					'title' => !empty($evento['titulo_evento']) ? $evento['titulo_evento'] : 'Evento',
					'descricao' => $descricao
				];
			}
		}

		self::$params['htmlEventosAlbuns'] = Render::obj('blocos/eventos_albuns.html', $eventos);
		$options = [
			'imagemFundo' => $params['base']['dir_relative'] . 'template/assets/midias/site/GALERIA/galeria_.jpg',
			'title' => $params['config']['title'],
			'texto' => 'Cada imagem conta uma história de fé, comunhão e renovação. Reviva os instantes especiais do Acampamento Maanaim e sinta a presença de Deus em cada detalhe.',
		];
		self::$params['tituloPagina'] = Render::obj('blocos/titulo-pagina.html', $options);
		self::$params['htmlAssine'] = Render::obj('blocos/form-assine-discipulado.html', $params);
		self::$params['html'] = ""; // conteúdo html da página.
	}

	private function mostrar_album() {

		if(!isset(self::$params['infoUrl']['attr'][0])) return false;

		// todo - Pegar as fotos.

		self::$params['html'] = '<div class="container">Sem fotos para este albúm.</div>';

		return true;
	}
}
