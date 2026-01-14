<?php

namespace pages;

use DateTime;
use template\classes\bds\BdDocumentos;
use template\classes\bds\BdDocumentosLog;
use template\classes\bds\BdEventos;
use desv\classes\bds\BdLogins;
use desv\classes\bds\BdLoginsGroupsMenu;
use desv\classes\bds\BdPermissions;
use desv\classes\DevHelper;
use desv\classes\FeedBackMessagens;
use desv\controllers\EndPoint;
use Respect\Validation\Rules\Length;
use template\classes\bds\BdEventosLog;
use template\classes\bds\BdIngressos;
use template\classes\bds\BdInscricoes;
use template\classes\bds\BdInscricoesLog;
use template\classes\bds\BdMidias;
use template\classes\bds\BdPessoas;
use template\classes\bds\BdPessoasLog;
use template\classes\maanaim\Maanaim;
use template\classes\maanaim\MaanaimCarga;
use template\classes\maanaim\MaanaimParse;

/**
 * INDEX LOGIN
 * 
 * Login para o painel administrativo da DESV
 * 
 */
class montar extends EndPoint
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
		// Configuração personalizada do endpoins.
		self::$params['config'] = [
			'title' => 'Montar',  // Título da página exibido na aba/janela navegador.
		];

		// Opções de segurança.
		self::$params['security']    = [

			// Controller usará controller de segurança.
			'ativo'             => true,

			// Usuário só acessa logado.
			'session'           => true,
			
			// Caminho para página de login.
			'loginPage' => "login/", // Page login dentro do modelo.

			// IDs que tem permissão TOTAL a esta controller. Usar apenas para teste.
			'ids'            => [
			    2, // Login ID: 2.
			],
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

		// Verifica se a extensão GD está habilitada.
		if (extension_loaded('gd')) {
			self::$params['GD'] = "GD está habilitada!";
			// print_r(gd_info());
		} else {
			self::$params['GD'] = "GD NÃO está habilitada!";
		}

		self::$params['html'] = "";

		
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
				self::$params['msg'] = 'Teste de api realizado com sucesso.';
				break;
			case 'teste_bd':
				// Verifico se existe conexão. Se a base está correta. Se existe tabela.
				$this->teste_bd();
				break;
			case 'criar_tabelas':
				$this->criar_tabelas();
				break;
			case 'deletar_tabelas':
				$this->deletar_tabelas();
				break;
			case 'cria_permissoes':
				$this->cria_permissoes();
				break;
			case 'cria_permissoes_outros':
				$this->cria_permissoes_outros();
				break;
			case 'realiza_updates':
				$this->realiza_updates();
				break;
			case 'teste_maanaim':
				self::$params['msg'] = 'Teste Classe Maanaim';
				break;
			case 'arquivos':
				self::$params['response'] = DevHelper::listarArquivosPasta('template/classes/bds/');
				self::$params['msg'] = 'Teste arquivos';
				break;
			case 'criar_eventos':

				// Crio as datas para testes.
				$dtIniEvento = (new DateTime('+10 day'))->format('Y-m-d\TH:i:s');
				$dtFimEvento = (new DateTime('+15day'))->format('Y-m-d\TH:i:s');

				$id = [];

				// Crio eventos para teste (sem imagens).
				for ($i = 1; $i < 4; $i++) {
					// Simulo o post do formulário de eventos.
					$post = json_decode('{
					"f-nome_evento": "evento_teste_0' . $i . '",
					"f-titulo_evento": "Evento Teste 0' . $i . '",
					"f-msg_espera": "Aguarde a liberação das inscrições.",
					"f-nome_preletor": "Hudson Zanoni",
					"f-obs_preletor": "Hudson Zanoni, também conhecido como palhaço Adalberto Pé de Chinelo, mora em Maringá – Pr, é casado com Flaviana e são pais de 4 filhos, Kalel, Melina, Lilie e Lavi. Formado em Marketing, ator e palhaço profissional, membro da IPVO (2ª Igreja Presbiteriana do Brasil em Maringá), estudou na JOCUM Almirante Tamandaré (ETED) em 1997 e hoje é missionário itinerante em tempo integral desde 2007. Fundador e Diretor da Cia de Teatro Expressão de Amor e da Associação Terapia da Alegria, projeto de capelania que utiliza a linguagem do “palhaço” em hospitais, asilos e escolas. Nos últimos 15 anos tem viajado por todo o Brasil e mundo afora para dar suporte artístico e treinamento a diversas agências missionárias em mais de 40 países..",
					"f-obs_evento": "Este evento terá participação especial.",
					"f-obs_atividades": "Prepare-se para uma trilha, campeonato de futsal e jantar de gala.",
					"f-link_album": "teste",
					"f-qtd_vagas_masculino": "10",
					"f-qtd_vagas_feminino": "10",
					"f-dt_inicio_evento": "' . $dtIniEvento . '",
					"f-dt_fim_evento": "' . $dtFimEvento . '",
					"f-idade_minima": "13",
					"f-status": "1"
					}', true);
					$id[] = Maanaim::AdicionarEvento($post);
				}

				// Aqui eu acrescento as 4 primeiras imagens em todos os eventos.
				$bdEventos = new BdEventos();
				$bdEventos->update(null, [
					"id_midia_banner" => 1,
					"id_midia_01" => 2,
					"id_midia_02" => 3,
					"id_midia_03" => 4
				], '');


				self::$params['response'] = $id;
				self::$params['msg'] = 'Eventos criados com sucesso.';
				break;
			case 'criar_ingressos':

				// Obtenho todos os eventos.
				$eventos = Maanaim::listarEventos();

				$id = [];

				// Cria ingressos para cada evento.
				foreach ($eventos as $key => $evento) {

					for ($i = 1; $i < 5; $i++) {

						// Crio as datas para testes.
						$dtIniIngresso = (new DateTime('+' . $i . ' day'))->format('Y-m-d\T01:00:00');
						$dtFimIngresso = (new DateTime('+' . $i . ' day'))->format('Y-m-d\T23:00:00');

						$postIngresso = json_decode('{
						"f-id_evento": "' . $evento['id'] . '",
						"f-titulo": "lOTE 0' . $i . '",
						"f-dt_ini_ingresso": "' . $dtIniIngresso . '",
						"f-dt_fim_ingresso": "' . $dtFimIngresso . '",
						"f-valor_ingresso": "' . (string)($i * 100) . '.90",
						"f-link_pagamento": "http://www.google.com/",
						"f-dt_limit_pagamento": "' . $dtFimIngresso . '",
						"f-chave_pix": "123456789",
						"f-desc_tipo_pagamento": "Pagamento via PIX.",
						"f-desc_ingresso": "Este é um ingresso promocional.",
						"f-desc_cuidados": "Confira os links e os destinatários para não cair em golpes.",
						"f-desc_orientacao": "Este ingresso tem uma validade. Após o vencimento deste ingresso, sua inscrição será cancelada. Este ingresso tem um valor, é necessário realizar o pagamento integral deste valor, para validar sua inscrição. Após realizar o pagamento, é necessário enviar o comprovante para o whatsapp (35 9 8863-9702)."
						}', true);

						$id[] = Maanaim::adicionarIngresso($postIngresso);
					}
				}


				self::$params['msg'] = 'Ingressos Adicionados';
				break;
			case 'modelo':

				self::$params['msg'] = 'Modelo';
				break;
			case 'criar_inscricoes_1':

				$row = 1;
				$cabecalho = [];
				$inscricao = [];
				if (($handle = fopen(BASE_DIR . "template/assets/midias/csv/maanaim_inscricoes.csv", "r")) !== FALSE) {

					// Carrega linha por linha.
					while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
						$num = count($data);
						// echo "<p> $num campos na linha $row: <br /></p>\n";
						$row++;
						$inscricao = [];
						for ($c = 0; $c < $num; $c++) {
							// echo $data[$c] . "<br />\n";
							if ($row == 2) {
								$cabecalho[] = $data[$c];
							} else {
								$inscricao[$cabecalho[$c]] = $data[$c];
							}
						}

						// Tento cadastrar os primeiros 200 registros
						if ($row == 200){
							break;
						}

						// Insere no banco de dados.
						if ($row > 8){
							$inscricaov2 = MaanaimParse::inscricaoV1($inscricao);
							echo $inscricaov2['cpf'];
							echo '
							';
							$r = Maanaim::adicionarInscricao($inscricaov2);
							var_dump($r['msg'][0]);
							echo '


							';
							if ($r['error']) {
								// echo $r['msg'][0];
							}
						}
					}
					fclose($handle);
				}

				// DevHelper::printr($cabecalho);
				self::$params['response'] = $row;
				self::$params['msg'] = 'Modelo';
				break;

			default:
				# code...
				break;
		}
	}

	private function cria_permissoes()
	{
		self::$params['msg'] = __FUNCTION__;
		MaanaimCarga::criarPermissoes();
		self::$params['msg'] = "Carga realizada com sucesso.";
	}

	private function cria_permissoes_outros()
	{
		self::$params['msg'] = __FUNCTION__;
		MaanaimCarga::criarPermissoesOutros();
		self::$params['msg'] = "Criação dos usuários e permissões realizada com sucesso.";
	}

	private function realiza_updates()
	{
		self::$params['msg'] = __FUNCTION__;
		MaanaimCarga::realizaUpdates();
		self::$params['msg'] = "Updates realizadas com sucesso.";
	}

	private function deletar_tabelas()
	{
		// Deleto as tabelas do sistema e do maanaim.
		self::$params['msg'] = __FUNCTION__;

		$this->actionBd('dropTable', $this->maanaimTables());
	}

	private function criar_tabelas()
	{
		// Crio as tabelas do sistema e do maanaim.
		self::$params['msg'] = __FUNCTION__;

		$this->actionBd('createTable', $this->maanaimTables());

		// $bdDocumentos = new BdDocumentos();
		// $bdDocumentos->createTable();
		// $bdDocumentos->dropTable();
		// $bdDocumentosLog = new BdDocumentosLog();
		// $bdDocumentosLog->createTable();
		// $bdEventos = new BdEventos();
		// $bdEventos->createTable();
		// $bdEventosLog = new BdEventosLog();
		// $bdEventosLog->createTable();
		// $bdIngressos = new BdIngressos();
		// $bdIngressos->createTable();
		// $bdInscricoes = new BdInscricoes();
		// $bdInscricoes->createTable();
		// $bdInscricoesLog = new BdInscricoesLog();
		// $bdInscricoesLog->createTable();
		// $bdMidias = new BdMidias();
		// $bdMidias->createTable();
		// $bdPessoas = new BdPessoas();
		// $bdPessoas->createTable();
		// $bdPessoasLog = new BdPessoasLog();
		// $bdPessoasLog->createTable();
	}

	/**
	 * teste_bd
	 * 
	 * Verifico se existe conexão. Se a base está correta. Se existe tabela.
	 *
	 * @return void
	 */
	private function teste_bd()
	{
		// Verifico se existe conexão. Se a base está correta. Se existe tabela.
		self::$params['msg'] = 'Erro ao conectar na base de dados.';
		try {
			$bdLogins = new BdLogins();
			$tables = $bdLogins->getTables();
		} catch (\Throwable $th) {
			$tables = array();
		}
		$r = unserialize(FeedBackMessagens::get());
		if (isset($r[0]['msg'])) {
			self::$params['msg'] = $r[0]['msg'];
			self::$params['status'] = 500;
		} else {
			self::$params['response'] = $tables;
			if (sizeof($tables)) {
				self::$params['msg'] = 'Conexão com banco de dados realizado com sucesso. Quantidade de tabelas: ' . sizeof($tables);
			} else {
				self::$params['msg'] = 'Conexão com banco de dados realizado com sucesso. É necessário criar as tabelas.';
			}
		}
	}

	private function actionBd($action, $tables)
	{
		foreach ($tables as $key => $bd) {
			$bd = str_replace(['.php', '/'], ['', '\\'], $bd);
			$table = new $bd();
			$table->$action();
		}
	}

	private function maanaimTables()
	{
		return DevHelper::listarArquivosPasta('template/classes/bds/');
	}
}
