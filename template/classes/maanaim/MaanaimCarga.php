<?php

namespace template\classes\maanaim;

use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\bds\BdPermissions;
use desv\classes\bds\BdLoginsGroups;
use desv\classes\bds\BdLogins;
use desv\classes\DevHelper;
use template\classes\bds\BdEventos;
use template\classes\bds\BdIngressos;
use template\classes\bds\BdMidias;
use template\classes\Midias;

class MaanaimCarga
{

	static function criarPermissoes()
	{
		// Atualizo a senha do usuário 2 e 1
		$bdLogins = new BdLogins();
		$bdLogins->update(2, ['senha' => hash('sha256', 'samuel.kurt')]);
		$bdLogins->update(1, ['senha' => hash('sha256', 'samuel.kurt')]);

		// Deleto os menus e permissões anteriores.
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

	static function criarPermissoesOutros()
	{
		// crio os usuários
		$bdLogins = new BdLogins();

		// id 3
		$bdLogins->insert([
			// Informações do registro.
			'matricula' => '0003',

			'fullName'  => 'Felipe Conti',
			'firstName' => 'Felipe',
			'lastName'  => 'Conti',

			'userName' => 'felipe.conti',
			'email'    => 'felipe.conti@acampamaanaim.com.br',
			'telefone' => '',
			'cpf'      => '',

			'senha'          => hash('sha256', 'tito.ester'),
			// 'expirationDays' => '360',
			// 'strongPass'     => false,
			// 'dateChangePass' => '2023-05-23',

			// Observações do registro (obrigatório).
			'obs'           => 'Insert Automático.',

			// Controle padrão do registro (obrigatório).
			'idStatus'      => 1,
			'idLoginCreate' => 1,
			'dtCreate'      => date("Y-m-d H:i:s"),
			'idLoginUpdate' => 1,
			'dtUpdate'      => date("Y-m-d H:i:s"),
		]);

		// id 4
		$bdLogins->insert([
			// Informações do registro.
			'matricula' => '0004',

			'fullName'  => 'Eluan Martins',
			'firstName' => 'Eluan',
			'lastName'  => 'Martins',

			'userName' => 'eluan.martins',
			'email'    => 'eluan.martins@acampamaanaim.com.br',
			'telefone' => '',
			'cpf'      => '',

			'senha'          => hash('sha256', 'gael.davi'),
			// 'expirationDays' => '360',
			// 'strongPass'     => false,
			// 'dateChangePass' => '2023-05-23',

			// Observações do registro (obrigatório).
			'obs'           => 'Insert Automático.',

			// Controle padrão do registro (obrigatório).
			'idStatus'      => 1,
			'idLoginCreate' => 1,
			'dtCreate'      => date("Y-m-d H:i:s"),
			'idLoginUpdate' => 1,
			'dtUpdate'      => date("Y-m-d H:i:s"),
		]);

		// Crio os grupos para os usuários
		$bdLoginsGroups = new BdLoginsGroups();
		$bdLoginsGroups->insert([
			'idLogin' => 3,
			'idGroup' => 1,
		]);
		$bdLoginsGroups->insert([
			'idLogin' => 4,
			'idGroup' => 1,
		]);

		// Crio os logins e menus para os outros usuários.
		$bdLoginsGroupsMenu = new BdLoginsGroupsMenu();

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
		$bdLoginsGroupsMenu->insert(['idLogin' => 3, 'menu' => json_encode($menuUser)]); // menu do login
		// PERMISSÕES
		$bdPermissions = new BdPermissions();
		$r = $bdPermissions->addPermissionsLogin(3, 'adm/');
		$r = $bdPermissions->addPermissionsLogin(3, 'adm/eventos/');
		$r = $bdPermissions->addPermissionsLogin(3, 'adm/ingressos/');
		$r = $bdPermissions->addPermissionsLogin(3, 'adm/checkin/');
		$r = $bdPermissions->addPermissionsLogin(3, 'adm/inscricoes/');
		$r = $bdPermissions->addPermissionsLogin(3, 'adm/pessoas/');

		$bdLoginsGroupsMenu->insert(['idLogin' => 4, 'menu' => json_encode($menuUser)]); // menu do login
		// PERMISSÕES
		$bdPermissions = new BdPermissions();
		$r = $bdPermissions->addPermissionsLogin(4, 'adm/');
		$r = $bdPermissions->addPermissionsLogin(4, 'adm/eventos/');
		$r = $bdPermissions->addPermissionsLogin(4, 'adm/ingressos/');
		$r = $bdPermissions->addPermissionsLogin(4, 'adm/checkin/');
		$r = $bdPermissions->addPermissionsLogin(4, 'adm/inscricoes/');
		$r = $bdPermissions->addPermissionsLogin(4, 'adm/pessoas/');
	}

	static function realizaUpdates()
	{
		// Instancia da classe de menus
		$bdLoginsGroupsMenu = new BdLoginsGroupsMenu();
		
		// Menu baseado no login 2, adicionando a página Quartos
		$menuGroup = [
			'icon' => '',
			'title' => 'Administradores',
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
				[
					'title' => 'Quartos',
					'url_relative' => 'adm/quartos/',
					'icon' => '',
					'type' => '',
				],
			]
		];
		
		// Insere o menu para o grupo 1
		$bdLoginsGroupsMenu->insert(['idGroup' => 1, 'menu' => json_encode($menuGroup)]);
		
		// Adiciona permissões para o grupo 1 (se necessário)
		// As permissões podem ser adicionadas através de BdPermissions se houver método para grupos
	}

	static function fakeInscricao()
	{
		return [
			"idEvento" => "3",
			"idIngresso" => "4",
			"nome" => "Mateus Rocha Brust",
			"email" => "mtbrust@gmail.com",
			"telefone" => "31993265491",
			"telefoneContato" => "31993265491",
			"cpf" => "10401141640",
			"sexo" => "Masculino",
			"dtNascimento" => "2020-09-25",
			"paiNome" => "Mateus Rocha Brust",
			"maeNome" => "Mateus Rocha Brust",
			"endCEP" => "37750-000",
			"endPais" => "Brasil",
			"endEstado" => "MG",
			"endCidade" => "Machado",
			"endBairro" => "nobres",
			"endRua" => "frança",
			"endNumero" => "118",
			"endComplemento" => "ap 120",
			"RepNome" => "Mateus Rocha Brust",
			"RepEmail" => "mtbrust@gmail.com",
			"RepTelefone" => "31993265491",
			"RepCpf" => "10401141640",
			"RepSexo" => "Masculino",
			"RepDtNascimento" => "1989-09-25",
			"alergiaR" => "0",
			"alergia" => "Nenhum",
			"medicamentoR" => "0",
			"medicamento" => "Nenhum",
			"nadarR" => "1",
			"ideia" => "Nenhum",
			"conselheiro" => "Nenhum",
			"termos" => 1,
			"status" => "",
			"statusEquipe" => "",
			"obsPreAcampa" => "",
			"obsAcampa" => "",
			"obsPosAcampa" => "",
			"documentacao" => "",
			"alojamento" => "",
			"quarto" => "",
			"checkin" => "",
			"obsCheckin" => ""
		];
	}
}
