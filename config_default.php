<?php

/**
 * * BASE_NAME
 * Nome do Framework
 * Não usar espaços e caracteres especiais.
 */
define('BASE_NAME', 'BASE_PHP');
define('BASE_TITLE', 'BASE PHP');


/**
 * * BASE_DOMAIN
 * Nome do Domínio
 * (localhost, www.dominio.com.br, etc.)
 */
define('BASE_DOMAIN', $_SERVER['SERVER_NAME']);


/**
 * * BASE_URL
 * URL Atual do projeto 
 * Exemplos do que pode conter:
 * (localhost, vhost, www.domínio.com.br, localhost/pasta/, www.domínio.com.br/pasta/, etc.)
 * Usado para definir variáveis de ambiente (DEV, HOMOLOG, PROD)
 */
define('BASE_URL', 'http' . ((isset($_SERVER['HTTPS'])) ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));


/**
 * * BASE_DIR
 * Diretório Atual do projeto. 
 * (Caminho completo para pasta raíz deste projeto)
 */
define('BASE_DIR', str_replace('\\', '/', realpath(dirname(__FILE__))) . '/');


/**
 * * BASE_DIR_RELATIVE
 * Diretório relativo do projeto. 
 * (Caminho relativo deste projeto, caso não seja um dómínio direto, pega o caminho para a index.)
 */
define('BASE_DIR_RELATIVE', str_replace('index.php', '', $_SERVER['SCRIPT_NAME']));


/**
 * * BASE_IP
 * Ip do usuário atual
 * (::1, 127.0.0.1, 192.168.0.1, 60.0.0.123, etc.)
 */
define('BASE_IP', $_SERVER['SERVER_ADDR']);


/**
 * * BASE_PATH_PAGES_CONTROLLERS
 * Caminho base para as páginas php.
 */
define('BASE_PATH_PAGES_CONTROLLERS', 'template/pages/c/');


/**
 * * BASE_PATH_PAGES_VIEWS
 * Caminho base para as páginas html (twig).
 */
define('BASE_PATH_PAGES_VIEWS', 'template/pages/v/');


/**
 * * BASE_PATH_API
 * Caminho base para os endpoints.
 */
define('BASE_PATH_API', 'template/api/');


/**
 * * BASE_PATH_PAGES_CONTROLLERS_DESV
 * Caminho base para as páginas php.
 */
define('BASE_PATH_PAGES_CONTROLLERS_DESV', 'desv/pages/c/');


/**
 * * BASE_PATH_PAGES_VIEWS_DESV
 * Caminho base para as páginas html (twig).
 */
define('BASE_PATH_PAGES_VIEWS_DESV', 'desv/pages/v/');


/**
 * * BASE_PATH_API_DESV
 * Caminho base para os endpoints.
 */
define('BASE_PATH_API_DESV', 'desv/api/');


/**
 * * BASE_PATH_CACHE
 * Caminho base para o cache.
 */
define('BASE_PATH_CACHE', 'cache/');


/**
 * * PARÂMETROS DEFAULT - RENDERIZAÇÃO
 * Opções de renderização.
 * 
 * * Opções com * podem ser modificadas no processamento do endpoint.
 */
define("BASE_PARAMS_RENDER", [
  'cache'        => false,                // Ativa uso de cache para resultado.
  'cacheTime'    => (60 * 60 * 24),       // Tempo para renovar cache em segundos. (1 dia).
  'cacheParams'  => true,                 // Cache por parametros (attr), cada página entra em cache dependendo dos parâmetros passados.
  // 'content_type' => 'application/json',   // * Tipo do retorno padrão do cabeçalho http.
  'content_type' => 'text/html',          // * Tipo do retorno padrão do cabeçalho http.
  'charset'      => 'utf-8',              // * Tipo de codificação do cabeçalho http.
  'showParams'   => false,                // Exibe todos os parametros no retorno da API.
]);


/**
 * * PARÂMETROS DEFAULT - CONFIGURAÇÃO
 * Configuração personalizada do endpoins.
 */
define("BASE_PARAMS_CONFIG", [
  // CONFIGURAÇÕES ENDPOINT
  // *********************
  'visita'    => true,        // Gravar registro de acesso.
  'bdParams'  => false,       // Parâmetros da controller vem do BD.
  'bdContent' => false,       // Conteúdo da página vem do BD.
  'versao'    => 'v1.0',      // Versão da controller atual.
  'feedback'  => true,        // FeedBack padrão de transações.
  'class'     => __CLASS__,   // Guarda classe atual


  // PAGES - INFORMAÇÕES ADICIONAIS PARA HEAD
  // *********************
  // Arquivo js ou css, o próprio código ou livre para acrescentar conteúdo na head.
  'head'           => '',   // Inclui antes de fechar a tag </head>
  'scriptHead'     => '',   // Escreve dentro de uma tag <script></script> antes da </head>.
  'scriptBody'     => '',   // Escreve dentro de uma tag <script></script> antes da </body>.
  'styleHead'      => '',   // Escreve dentro de uma tag <style></style> antes da </head>.
  'styleBody'      => '',   // Escreve dentro de uma tag <style></style> antes da </body>.


  // PAGES - INFORMAÇÕES DE SEO HTML
  // *********************
  // Informações que vão ser usadas para SEO na página.
  'title'            => 'BASE PHP V4',   // Título da página exibido na aba/janela navegador.

  'author'           => 'Mateus Brust',          // Autor do desenvolvimento da página ou responsável.
  'description'      => 'BASE PHP Versão 4',     // Resumo do conteúdo do site em até 90 carecteres.
  'keywords'         => 'base, php',             // palavras minúsculas separadas por "," em até 150 caracteres.
  'content_language' => 'pt-BR',                 // Linguagem primária da página (pt-br).
  'content_type'     => 'text/html',             // Tipo de codificação da página.
  'reply_to'         => 'contato@desv.com.br',   // E-mail do responsável da página.
  'charset'          => 'utf-8',                 // Charset da página.
  'image'            => 'template/assets/midias/logo/logo.png',              // Imagem redes sociais.
  'url'              => 'desv.com.br',           // Url para instagram.
  'site'             => 'desv.com.br',           // Site para twitter.
  'creator'          => '',                      // Perfil criador twitter.
  'author_article'   => '',                      // Autor do artigo da página atual.
  'generator'        => 'vscode',                // Programa usado para gerar página.
  'refresh'          => false,                   // Tempo para recarregar a página em segundos.
  'redirect'         => false,                   // URL para redirecionar usuário após refresh.
  'favicon'          => 'template/assets/midias/logo/favicon.ico',           // Imagem do favicon na página.
  'icon'             => 'template/assets/midias/logo/favicon.ico',           // Imagem ícone da empresa na página.
  'appletouchicon'   => 'template/assets/midias/logo/favicon.ico',           // Imagem da logo na página.

  // INFORMAÇÕES ADICIONAIS PERSONALIZADAS
  // *********************
  'campo'            => 'valor',         // Personalização
]);


/**
 * * PARÂMETROS DEFAULT - SEGURANÇA
 * Opções de segurança.
 */
define("BASE_PARAMS_SECURITY", [

  // Controller usará segurança.
  'ativo'             => false,

  // Acessar apenas logado.
  'session'           => false,

  // Tempo para sessão acabar em segundos.
  'sessionTimeOut'    => (60 * 60 * 24), // 24 horas.

  // Segurança por autorização no cabeçalho.
  'headers'            => [
    'key'   => '',   // Tipo de autorização (Bearer Token, API Key, JWT Bearer Basic Auth, etc.).
    'value' => '',   // Valor da autorização (Bearer valor_token, Basic e3t1c3VhcmlvfX06e3tzZW5oYX19, etc)
  ],

  // Caminho para página de login.
  'loginPage'         => 'admin/login/',

  // Caminho para página restrita.
  'restrictPage'      => 'admin/', // Page admin dentro do modelo.

  // // Permissões personalizadas da página atual. 
  // // [1] Usuário tem que ter permissão, [0] Não necessita permissão.
  'permission'        => [
    "session" => 0,   // Necessário usuário com sessao nesta página.
    "get"     => 0,   // Permissão para acessar a função get desta página.
    "getFull" => 0,   // Permissão para acessar a função getFull desta página.
    "post"    => 0,   // Permissão para acessar a função post ou requisição post desta página.
    "put"     => 0,   // Permissão para acessar a função put ou requisição put desta página.
    "patch"   => 0,   // Permissão para acessar a função patch ou requisição patch desta página.
    "del"     => 0,   // Permissão para acessar a função delete ou requisição delete desta página.
    "api"     => 0,   // Permissão para acessar a função API desta página.
    "especific" => [
      'botao_excluir' => 1, // Permissão personalizada da página. Exemplo: só aparece o botão excluir para quem tem essa permissão específica da página.
      'botao_editar' => 1,
    ],
  ],

  // Transações de dados (GET - POST) apenas com token. Usar classe Tokens. Exemplo: (<input name="token" type="text" value="{{token}}" hidden>').
  'token'             => false, // Só aceitar com token (definido na config "BASE_AUTH['token']").

  // FeedBack padrão de nível de acesso.
  'feedback'          => true, // true - mostra feedback de ações.

  // Receber transações externas. Dados de outras origens.
  'origin'            => [
    // 'site.com.br',  // URL teste.
  ],

  // Grupos que tem permissão TOTAL a esta controller. Usar apenas para teste.
  'groups'            => [
    // 1, // Grupo ID: 1.
  ],

  // IDs que tem permissão TOTAL a esta controller. Usar apenas para teste.
  'ids'            => [
    // 1, // Login ID: 1.
  ],
]);


/**
 * * PARÂMETROS DEFAULT - INFORMAÇÕES
 * Informações extras.
 */
define("BASE_PARAMS_INFO", [

  // INFORMAÇÕES GERAIS
  // *********************
  'empresa'         => 'DESV',
  'slogan'          => 'DESV',
  'nomeFantasia'    => 'DESV - DESENVOLVIMENTO',
  'razaoSocial'     => 'DESV',
  'cnpj'            => '123456789',
  'ie'              => '123456789',
  'endereco'        => 'São Paulo/SP',
  'email'           => 'contato@desv.com.br',
  'emailSuporte'    => 'contato@desv.com.br',
  'telefoneSuporte' => '',
  'telefone'        => '',
  'whatsapp'        => '',
  'since'           => '2019',                     // 10/04/2019 - DESV
  'dataAtual'       => date('d/m/Y H:i:s'),
  'anoAtual'        => date('Y'),
  'logo'            => 'logo.png',

  // INFORMAÇÕES ADICIONAIS PERSONALIZADAS
  // *********************
  'campo' => 'valor', // Adicional.
]);

/**
 * * PARÂMETROS DEFAULT - MENUS
 * Monta estrutura de parâmetros passados na url.
 */
define("BASE_PARAMS_MENUS", [
  // Função:
  'get' => [
    'title'      => 'Listar',      // Nome exibido no menu. Somente pages.
    'permission' => [
      "session" => 0,   // Necessário usuário com sessao nesta página.
      "get"     => 0,   // Permissão para acessar a função get desta página.
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
  'test' => [
    'title'      => 'Teste',       // Nome exibido no menu. Somente pages.
    'permission' => [
      "session" => 0,   // Necessário usuário com sessao nesta página.
      "get"     => 0,   // Permissão para acessar a função get desta página.
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
]);


/**
 * * PARÂMETROS DEFAULT - STRUCTURE
 * Carrega estrutura html. Somente pages.
 */
define("BASE_PARAMS_STRUCTURE", [
  // Origem
  'html'        => 'default',   // Estrutura HTML geral.

  // Complementos (opcionais da estrutura)
  'head'         => 'default',   // <head> da página.
  'top'          => 'default',   // Logo após a tag <body>.
  'header'       => 'default',   // Após a estrutura "top".
  'nav'          => 'default',   // Dentro do header ou personalizado.
  'content_top'  => 'default',   // Antes do conteúdo da página.
  'content_page' => 'default',   // Reservado para conteúdo da página. Sobrescrito depois.
  'content_end'  => 'default',   // Depois do conteúdo da página.
  'footer'       => 'default',   // footer da página.
  'end'          => 'default',   // Fim da página.
]);


/**
 * * PARÂMETROS DEFAULT - SCRIPTS
 * Carrega os scripts para todos os endpoints de pages.
 * Local js: "template/assets/js/".
 * Local libs: "template/assets/libs/".
 */
define("BASE_PARAMS_SCRIPTS", [
  'js' => [
    'bootstrap.bundle.min.js', // 5.3.3
    'default.min.js', // Default.
  ],
  'libs' => [
    'jquery/jquery.min.js',  // JQuery.
    'sweetalert2/sweetalert2.min.js',  // sweetalert2.
  ],
]);


/**
 * * PARÂMETROS DEFAULT - STYLES
 * Carrega os estilos para todos os endpoints de pages.
 * Local css: "template/assets/css/"
 * Local libs: "template/assets/libs/".
 */
define("BASE_PARAMS_STYLES", [
  'css' => [
    'bootstrap.min.css',  // bootstrap 5.3.3
    'admin/bootstrap-icons.min.css', // 5.3.3
    'default.min.css', // Default.
    'simple.css', // demostração.
  ],
  'libs' => [
    'sweetalert2/sweetalert2.min.css',  // sweetalert2
    'fontawesome/css/all.min.css',  // fontawesome
  ],
]);


/**
 * * PARÂMETROS DEFAULT - PLUGINS
 * Carrega os plugins para todos os endpoints de pages.
 * Local: "template/plugins/"
 */
define("BASE_PARAMS_PLUGINS", [
  // 'modelo',   // Exemplo.
]);


/**
 * * BANCO DE DADOS
 * Conexão com os bancos de dados de acordo com ambiente.
 */
define("BASE_BDS", [
  0 => [
    'TITLE'     => 'BD Principal DEV',
    'ACTIVE'    => true,
    'DBMANAGER' => 'mysql',
    'HOST'      => 'localhost',
    'PORT'      => '3306',
    'USERNAME'  => 'root',
    'PASSWORD'  => '',
    'DATABASE'  => 'desv_base_php',
    'CHARSET'   => 'utf8',
    'PREFIX'    => 'base_php_',
  ],
  1 => [
    'TITLE'     => 'BD Secundario DEV',
    'ACTIVE'    => true,
    'DBMANAGER' => 'mysql',
    'HOST'      => 'localhost',
    'PORT'      => '3306',
    'USERNAME'  => 'root',
    'PASSWORD'  => '',
    'DATABASE'  => 'base_php',
    'CHARSET'   => 'utf8',
    'PREFIX'    => 'v4',
  ],
]);


/**
 * * AUTORIZAÇÕES
 * Autorizações de APIs, TOKENS, Terceiros, Logins, etc de acordo com ambiente.
 */
define("BASE_AUTH", [
  // Exemplo
  'CAMPO' => 'valor', // Campo personalizado.
]);


/**
 * * CONFIGURAÇÕES
 * Configurações personalizadas de acordo com ambiente.
 */
define("BASE_CONFIG", [
  'SHOW_ERRORS' => 1,                     // [0] Não exibe erros php. [1] Exibe erros php na tela.
  'TIMEZONE'    => 'America/Sao_Paulo',   // Seta o horário local para o horário de brasília.
  'CAMPO'       => 'valor',               // Campo personalizado.
]);
