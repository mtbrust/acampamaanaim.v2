<?php

namespace template\classes\maanaim;

use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\bds\BdPermissions;
use desv\classes\DevHelper;
use template\classes\bds\BdEventos;
use template\classes\bds\BdIngressos;
use template\classes\bds\BdMidias;
use template\classes\Midias;

class MaanaimCarga
{

    static function criarPermissoes()
    {
        $bdLoginsGroupsMenu = new BdLoginsGroupsMenu();

        $table = $bdLoginsGroupsMenu->fullTableName();
        $sql = "TRUNCATE TABLE $table";
        $bdLoginsGroupsMenu->executeQuery($sql);

		// Cadastro os menus para o usuário 2
		$menuUser = [
			'icon' => '',
			'title' => 'Usuário',
			'type' => '',
			'submenu' => [
				[
					'title' => 'DashBoard',
					'url_relative' => 'adm/',
					'icon' => '',
					'type' => '',
				],
				[
					'title' => 'Eventos',
					'url_relative' => 'adm/eventos/',
					'icon' => '',
					'type' => '',
				],
				[
					'title' => 'Ingressos',
					'url_relative' => 'adm/ingressos/',
					'icon' => '',
					'type' => '',
				],
				[
					'title' => 'Inscrições',
					'url_relative' => 'adm/inscricoes/',
					'icon' => '',
					'type' => '',
				],
				[
					'title' => 'Pessoas',
					'url_relative' => 'adm/pessoas/',
					'icon' => '',
					'type' => '',
				],
				[
					'title' => 'Check-In',
					'url_relative' => 'adm/checkin/',
					'icon' => '',
					'type' => '',
				],
			]
		];
		$bdLoginsGroupsMenu->insert(['idLogin' => 2, 'menu' => json_encode($menuUser)]); // menu do login
        
		// PERMISSÕES
		$bdPermissions = new BdPermissions();
		$r = $bdPermissions->addPermissionsLogin(2, 'adm/');
		$r = $bdPermissions->addPermissionsLogin(2, 'adm/eventos/');
		$r = $bdPermissions->addPermissionsLogin(2, 'adm/ingressos/');
		$r = $bdPermissions->addPermissionsLogin(2, 'adm/checkin/');
		$r = $bdPermissions->addPermissionsLogin(2, 'adm/inscricoes/');
		$r = $bdPermissions->addPermissionsLogin(2, 'adm/pessoas/');
    }
}
