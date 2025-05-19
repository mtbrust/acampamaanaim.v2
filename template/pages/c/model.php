<?php

namespace pages;

use desv\controllers\EndPoint;
use desv\controllers\Render;

/**
 * ORIENTAÇÕES DO MODELO PADRÃO
 * Modelo padrão de controller para o endpoint (páginas ou APIs).
 * Modelo contém todas as informações que são possíveis usar dentro de uma controller.
 * É possível tirar, acrescentar ou mudar parâmetros para obter resultados mais eficientes e personalizados.
 * 
 * ORIENTAÇÕES DA CONTROLLER
 * Os arquivos e classes são carregados após a função loadParams().
 * O método padrão para visualização é a get().
 * Na função get, é realizada toda a programação das informações do endpoint.
 * É possível chamar outras funções (Sub-Menus) usando parâmetros (Url e LoadParams).
 * Outras funções (Sub-Menus) são chamados de acordo com a estrutura personalizada no parâmetros "menus".
 * 
 * O nome da controller é o mesmo que o endpoint da url sem os "-".
 * Porém é possível passar pela url o endpoint "/quem-somos", pois o sistema irá tirar os "-".
 * O nome da controller vai ficar como "quemsomos".
 * 
 */
class model extends EndPoint
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
		// Opções de renderização.
		self::$params['render']      = [
			// 'cache'        => false,         // Ativa uso de cache para resultado.
			// 'cacheTime'    => (60 * 30),     // Tempo para renovar cache em segundos. (30 Minutos).
			// 'cacheParams'  => true,          // Cache por parametros (attr).
			'content_type' => 'text/html',   // * Tipo do retorno padrão do cabeçalho http.
			// 'charset'      => 'utf-8',       // * Tipo de codificação do cabeçalho http.
		];

		// Configuração personalizada do endpoins.
		self::$params['config']      = [
			// CONFIGURAÇÕES ENDPOINT
			// *********************
			// 'visita'    => true,        // Gravar registro de acesso.
			// 'bdParams'  => false,       // Parâmetros da controller vem do BD.
			// 'bdContent' => false,       // Conteúdo da página vem do BD.
			// 'versao'    => 'v1.0',      // Versão da controller atual.
			// 'feedback'  => true,        // FeedBack padrão de transações.
			// 'class'     => __CLASS__,   // Guarda classe atual


			// PAGES - INFORMAÇÕES ADICIONAIS PARA HEAD
			// *********************
			// Arquivo js ou css, o próprio código ou livre para acrescentar conteúdo na head.
			// 'head'           => '',   // Inclui antes de fechar a tag </head>
			// 'scriptHead'     => '',   // Escreve dentro de uma tag <script></script> antes da </head>.
			// 'scriptBody'     => '',   // Escreve dentro de uma tag <script></script> antes da </body>.
			// 'styleHead'      => '',   // Escreve dentro de uma tag <style></style> antes da </head>.
			// 'styleBody'      => '',   // Escreve dentro de uma tag <style></style> antes da </body>.


			// PAGES - INFORMAÇÕES DE SEO HTML
			// *********************
			// Informações que vão ser usadas para SEO na página.
			// 'title'            => 'Título',  // Título da página exibido na aba/janela navegador.
			// 'author'           => 'Mateus Brust',            // Autor do desenvolvimento da página ou responsável.
			// 'description'      => '',                        // Resumo do conteúdo do site em até 90 carecteres.
			// 'keywords'         => '',                        // palavras minúsculas separadas por "," em até 150 caracteres.
			// 'content_language' => 'pt-BR',                   // Linguagem primária da página (pt-br).
			// 'content_type'     => 'text/html',               // Tipo de codificação da página.
			// 'reply_to'         => 'contato@CETRUS.com.br',     // E-mail do responsável da página.
			// 'charset'          => 'utf-8',                   // Charset da página.
			// 'image'            => 'logo.png',                // Imagem redes sociais.
			// 'url'              => 'CETRUS',                    // Url para instagram.
			// 'site'             => 'CETRUS',                    // Site para twitter.
			// 'creator'          => 'CETRUS',                    // Perfil criador twitter.
			// 'author_article'   => 'CETRUS',                    // Autor do artigo da página atual.
			// 'generator'        => 'vscode',                  // Programa usado para gerar página.
			// 'refresh'          => false,                     // Tempo para recarregar a página.
			// 'redirect'         => false,                     // URL para redirecionar usuário após refresh.
			// 'favicon'          => 'favicon.ico',             // Imagem do favicon na página.
			// 'icon'             => 'favicon.ico',             // Imagem ícone da empresa na página.
			// 'appletouchicon'   => 'favicon.ico',             // Imagem da logo na página.
		];

		// Opções de segurança.
		self::$params['security']    = [

			// // Controller usará controller de segurança.
			// 'ativo'             => true,

			// // Usuário só acessa logado.
			// 'session'           => true,

			// // Tempo para sessão acabar nesta página.
			// 'sessionTimeOut'    => (60 * 30), // 30 min.

			// // Segurança por autorização no cabeçalho.
			// 'headers'            => [
			//     'key'   => 'Authorization',        // Tipo de autorização (Bearer Token, API Key, JWT Bearer Basic Auth, etc.).
			//     'value' => 'Bearer valor_token',   // Valor da autorização (Bearer valor_token, Basic e3t1c3VhcmlvfX06e3tzZW5oYX19, etc)
			// ],

			// // Caminho para página de login.
			// 'loginPage'         => "api/login/", // Page login dentro do modelo.

			// // Permissões personalizadas da página atual. 
			// // [1] Usuário tem que ter permissão, [0] Não necessita permissão.
			// 'permission'        => [ 
			// 	"session" => 1,   // Necessário usuário com sessao nesta página.
			// 	"get"     => 1,   // Permissão para acessar a função get desta página.
			// 	"getFull" => 1,   // Permissão para acessar a função getFull desta página.
			// 	"post"    => 1,   // Permissão para acessar a função post ou requisição post desta página.
			// 	"put"     => 1,   // Permissão para acessar a função put ou requisição put desta página.
			// 	"patch"   => 1,   // Permissão para acessar a função patch ou requisição patch desta página.
			// 	"del"  => 1,   // Permissão para acessar a função delete ou requisição delete desta página.
			// 	"api"     => 1,   // Permissão para acessar a função API desta página.
			// 	"especific" => [
			//     'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
			//     'botao_editar' => 1,
			//   ],
			// ],

			// // Transações de dados (GET - POST) apenas com token. Usar classe Tokens. Exemplo: (<input name="token" type="text" value="{{token}}" hidden>').
			// 'token'             => true, // Só aceitar com token (definido na config "BASE_AUTH['token']").

			// // FeedBack padrão de nível de acesso.
			// 'feedback'          => false,

			// // Receber transações externas. Dados de outras origens.
			// 'origin'            => [
			//     '*',                        // Permitir tudas as origens.
			//     'http://www.site.com.br/',  // URL teste.
			// ],

			// // Grupos que tem permissão TOTAL a esta controller. Usar apenas para teste.
			// 'groups'            => [
			//     // 1, // Grupo ID: 1.
			// ],

			// // IDs que tem permissão TOTAL a esta controller. Usar apenas para teste.
			// 'ids'            => [
			//     // 1, // Login ID: 1.
			// ],
		];

		// Carrega estrutura html. Somente pages.
		self::$params['structure']   = [
			// // Origem
			// 'html'        => 'default',   // Estrutura HTML geral.

			// // Complementos
			// 'head'         => 'default',   // <head> da página.
			// 'top'          => 'default',   // Logo após a tag <body>.
			// 'header'       => 'default',   // Após a estrutura "top".
			// 'nav'          => 'default',   // Dentro do header ou personalizado.
			// 'content_top'  => 'default',   // Antes do conteúdo da página.
			// 'content_page' => 'default',   // Reservado para conteúdo da página. Sobrescrito depois.
			// 'content_end'  => 'default',   // Depois do conteúdo da página.
			// 'footer'       => 'default',   // footer da página.
			// 'end'          => 'default',   // Fim da página.
		];

		// Carrega na página scripts (template/assets/js/) Somente pages.
		self::$params['scripts']     = [
			// pasta js.
			'js' => [
				// 'default-min.js',   		// Exemplo.
			],
			// pasta libs.
			'libs' => [
				// 'bootstrap/js/bootstrap.bundle.min.js',   		// Exemplo.
			],
		];

		// Carrega na página estilos (template/assets/css/) Somente pages.
		self::$params['styles']      = [
			// pasta css.
			'css' => [
				// 'default-min.css',   // Exemplo.
			],
			// pasta libs.
			'libs' => [
				// 'bootstrap/css/bootstrap.min.css',   		// Exemplo.
			],
		];

		// Carrega na página plugins (template/assets/css/) Somente pages.
		self::$params['plugins']     = [
			// 'modelo',   // Exemplo.
		];

		// Monta estrutura de parâmetros passados na url ou metodo.
		self::$params['menus']       = [
		  // Função:
		  'get' => [
			  'title'      => 'Listar',      // Nome exibido no menu. Somente pages.
			  'permission' => [
				"session" => 1,   // Necessário usuário com sessao nesta página.
				"get"     => 1,   // Permissão para acessar a função get desta página.
				"getFull" => 0,   // Permissão para acessar a função getFull desta página.
				"post"    => 0,   // Permissão para acessar a função post ou requisição post desta página.
				"put"     => 0,   // Permissão para acessar a função put ou requisição put desta página.
				"patch"   => 0,   // Permissão para acessar a função patch ou requisição patch desta página.
				"del"  => 0,   // Permissão para acessar a função delete ou requisição delete desta página.
				"api"     => 0,   // Permissão para acessar a função API desta página.
				"especific" => [
				  'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
				  'botao_editar' => 1,
				],
			  ],
			  'groups'     => [],            // Quais grupos tem acesso a esse menu.
			  'ids'        => [],            // Quais ids tem acesso a esse menu.
		  ],
		  // Função:
		  'post' => [
			  'title'      => 'Listar',      // Nome exibido no menu. Somente pages.
			  'permission' => [
				"session" => 1,   // Necessário usuário com sessao nesta página.
				"get"     => 0,   // Permissão para acessar a função get desta página.
				"getFull" => 0,   // Permissão para acessar a função getFull desta página.
				"post"    => 1,   // Permissão para acessar a função post ou requisição post desta página.
				"put"     => 0,   // Permissão para acessar a função put ou requisição put desta página.
				"patch"   => 0,   // Permissão para acessar a função patch ou requisição patch desta página.
				"del"  => 0,   // Permissão para acessar a função delete ou requisição delete desta página.
				"api"     => 0,   // Permissão para acessar a função API desta página.
				"especific" => [
				  'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
				  'botao_editar' => 1,
				],
			  ],
			  'groups'     => [],            // Quais grupos tem acesso a esse menu.
			  'ids'        => [],            // Quais ids tem acesso a esse menu.
		  ],
		  // Função:
		  'api' => [
			  'title'      => 'Listar',      // Nome exibido no menu. Somente pages.
			  'permission' => [
				"session" => 1,   // Necessário usuário com sessao nesta página.
				"get"     => 0,   // Permissão para acessar a função get desta página.
				"getFull" => 0,   // Permissão para acessar a função getFull desta página.
				"post"    => 0,   // Permissão para acessar a função post ou requisição post desta página.
				"put"     => 0,   // Permissão para acessar a função put ou requisição put desta página.
				"patch"   => 0,   // Permissão para acessar a função patch ou requisição patch desta página.
				"del"     => 0,   // Permissão para acessar a função delete ou requisição delete desta página.
				"api"     => 1,   // Permissão para acessar a função API desta página.
				"especific" => [
				  'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
				  'botao_editar' => 1,
				],
			  ],
			  'groups'     => [],            // Quais grupos tem acesso a esse menu.
			  'ids'        => [],            // Quais ids tem acesso a esse menu.
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
		self::$params['html'] = Render::obj('docs/show_params.html', $params);
	}


	/**
	 * post
	 * 
	 * Função é chamada quando recebe um post.
	 * Recebe todos os parâmetros do endpoint em $params.
	 *
	 * @param  mixed $params
	 * 
	 * @return mixed
	 */
	public function post($params)
	{
		// Exibe a lista de parâmetros que a página disponibiliza.
		self::$params['html'] = Render::obj('docs/show_params.html', $params);
	}

	/**
	 * api
	 * 
	 * Função pode ser usada como api de página.
	 * Caso tenha alguma informação específica.
	 *
	 * @param mixed $params
	 * 
	 * @return mixed
	 * 
	 */
	public function api($params)
	{
		self::$params['render']['content_type'] = 'application/json';

		// Finaliza a execução da função.
		self::$params['response'] = 'teste';
		self::$params['msg'] = 'Requisição recebida com sucesso.';
		self::$params['status']   = 200;
	}
}