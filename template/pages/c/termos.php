<?php

namespace pages;

use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use desv\controllers\Render;
use template\classes\AssinaturaPNG;
use template\classes\maanaim\Maanaim;
use template\classes\PDF;

/**
 * TERMOS E COMPROMISSOS
 * 
 * Página para visualizar e baixar os termos e compromissos
 * 
 */
class termos extends EndPoint
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
			'title' => 'TERMOS E COMPROMISSOS',  // Título da página exibido na aba/janela navegador.
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
			// 'modelo',   // Exemplo.
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
		// Pego a imagem da logo e mando para o pdf.
		$data = file_get_contents('./' . self::$params['config']['image']);
		$logoBase64 = base64_encode($data);
		self::$params['logo'] = 'data:image/png;base64, ' . $logoBase64;

		// Data atual para os termos
		self::$params['info'] = [
			'dataAtual' => date('d/m/Y'),
			'anoAtual' => date('Y'),
		];

		// Renderiza os termos em HTML para exibição na página usando o template unificado
		// Termo menor de 18 (em branco)
		$paramsMenor = self::$params;
		$paramsMenor['termo_em_branco'] = true;
		$paramsMenor['tipo_idade'] = 'menor';
		self::$params['htmlTermoMenor'] = Render::obj('docs/termos-e-compromissos.html', $paramsMenor);
		
		// Termo maior de 18 (em branco)
		$paramsMaior = self::$params;
		$paramsMaior['termo_em_branco'] = true;
		$paramsMaior['tipo_idade'] = 'maior';
		self::$params['htmlTermoMaior'] = Render::obj('docs/termos-e-compromissos.html', $paramsMaior);

		self::$params['html'] = ""; // conteúdo html da página.
	}

	/**
	 * api
	 * 
	 * Função para API (download de PDFs).
	 *
	 * @param  mixed $params
	 */
	public function api($params)
	{
		// Finaliza a execução da função.
		self::$params['render']['content_type'] = 'text/html';
		self::$params['response'] 	= "";
		self::$params['msg']		= "";
		self::$params['status']   	= 200;

		$acao = $params['infoUrl']['attr'][1] ?? '';

		switch ($acao) {
			case 'download-menor':
				// Pego a imagem da logo
				$data = file_get_contents('./' . self::$params['config']['image']);
				$logoBase64 = base64_encode($data);
				self::$params['logo'] = 'data:image/png;base64, ' . $logoBase64;

				// Data atual
				self::$params['info'] = [
					'dataAtual' => date('d/m/Y'),
					'anoAtual' => date('Y'),
				];

				// Parâmetros para termo em branco menor de 18
				self::$params['termo_em_branco'] = true;
				self::$params['tipo_idade'] = 'menor';

				$options = [
					'text' => 'Felipe Silva Conti',
					'font' => 'template/assets/midias/fonts/Arizonia-Regular.ttf',
					'fontSize' => 45,
					'textColor' => [33, 150, 243],
					'padding' => 0,
				];
				self::$params['assinaturaPresidente'] = AssinaturaPNG::create($options);

				$htmlTermos = Render::obj('docs/termos-e-compromissos.html', self::$params);

				self::$params['render']['content_type'] = 'application/pdf';
				$ret = PDF::arquivo($htmlTermos);

				$msg = 'ok';
				break;

			case 'download-maior':
				// Pego a imagem da logo
				$data = file_get_contents('./' . self::$params['config']['image']);
				$logoBase64 = base64_encode($data);
				self::$params['logo'] = 'data:image/png;base64, ' . $logoBase64;

				// Data atual
				self::$params['info'] = [
					'dataAtual' => date('d/m/Y'),
					'anoAtual' => date('Y'),
				];

				// Parâmetros para termo em branco maior de 18
				self::$params['termo_em_branco'] = true;
				self::$params['tipo_idade'] = 'maior';

				$options = [
					'text' => 'Felipe Silva Conti',
					'font' => 'template/assets/midias/fonts/Arizonia-Regular.ttf',
					'fontSize' => 45,
					'textColor' => [33, 150, 243],
					'padding' => 0,
				];
				self::$params['assinaturaPresidente'] = AssinaturaPNG::create($options);

				$htmlTermos = Render::obj('docs/termos-e-compromissos.html', self::$params);

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
