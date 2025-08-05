<?php

namespace pages;

use desv\classes\AccessControl;
use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\bds\BdPermissions;
use desv\classes\DevHelper;
use desv\controllers\EndPoint;
use template\classes\Maanaim;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class index extends EndPoint
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
			// Permissões personalizadas da página atual. 
			// [1] Usuário tem que ter permissão, [0] Não necessita permissão.
			'permission' => [
				"session"   => 1,    // Necessário usuário com sessao nesta página.
				"get"       => 1,    // Permissão para acessar a função get desta página.
				"getFull"   => 1,    // Permissão para acessar a função getFull desta página.
				"post"      => 0,    // Permissão para acessar a função post ou requisição post desta página.
				"put"       => 0,    // Permissão para acessar a função put ou requisição put desta página.
				"patch"     => 0,    // Permissão para acessar a função patch ou requisição patch desta página.
				"del"       => 0,    // Permissão para acessar a função delete ou requisição delete desta página.
				"api"       => 1,    // Permissão para acessar a função API desta página.
				"especific" => [],
			],

			// Caminho para página de login.
			'loginPage' => "login/", // Page login dentro do modelo.
		];

		// Configuração personalizada do endpoins.
		self::$params['config'] = [
			'title' => 'Administração',  // Título da página exibido na aba/janela navegador.
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
		// todo - para apoiar a criação de permissões enquanto ainda não existe painel.
		// $bdPermissions = new BdPermissions();
		// $r = $bdPermissions->addPermissionsLogin(2, 'adm/');

		// $bdLogins = new BdLogins();
		// $r = $bdLogins->selectAll();

		// DevHelper::printr($r);

		// DevHelper::echo(json_encode($r));

		// DevHelper::printr(Maanaim::ping());

		self::$params['html'] = "Conteúdo da página.";
	}

	public function api($params)
	{


		// * Apenas para testes. Aqui eu atualizo o menu do usuário logado.
		// $menuUser = [
		//     'icon' => '',
		//     'title' => 'Usuário',
		//     'type' => '',
		//     'submenu' => [
		//         [
		//             'title' => 'DashBoard',
		//             'url_relative' => 'adm/',
		//             'icon' => '',
		//             'type' => '',
		//         ],
		//         [
		//             'title' => 'Eventos',
		//             'url_relative' => 'adm/eventos/',
		//             'icon' => '',
		//             'type' => '',
		//         ],
		//         [
		//             'title' => 'Inscrições',
		//             'url_relative' => 'adm/inscricoes/',
		//             'icon' => '',
		//             'type' => '',
		//         ],
		//         [
		//             'title' => 'Check-In',
		//             'url_relative' => 'adm/checkin/',
		//             'icon' => '',
		//             'type' => '',
		//         ],
		//     ]
		// ];
		// $bdLoginsGroupsMenu = new BdLoginsGroupsMenu();
		// $r = $bdLoginsGroupsMenu->update(2, ['menu' => json_encode($menuUser)]);

		// Atualiza as permissões e menus do usuário logado.
		AccessControl::atualizar();

		// Finaliza a execução da função.
		self::$params['render']['content_type'] = 'application/json';
		self::$params['response'] 	= "Atualização";
		self::$params['msg']		= 'Permissões de usuário atualizadas com sucesso.';
		self::$params['status']   	= 200;


		
		// Caso o logado for o mateus. Para testes.
		if ($params['_session']['user']['id'] = 2) {
			// $bdPermissions = new BdPermissions();
			// $r = $bdPermissions->addPermissionsLogin(2, 'checkin/');
			$r = 1;
			self::$params['response'] = $r;
			self::$params['msg'] = 'Execução com sucesso.';
		}
	}
}
