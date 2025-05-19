
/**
 * Função Ajax com CallBack.
 * Função tem o objetivo de enviar dados POST para uma URL e realizar uma função CALLBACK personalizada com o responseorno.
 * 
 * @param string url_api
 * @param array dados
 * @param function callback
 * @param string type
 * 
 * @return void
 * 
 */
function ajaxDados(url_api, dados, callback, type = 'POST') {

    // Realiza chamada AJAX.
    $.ajax({
        type: type,
        url: url_api,
        // async: false,
        myCallback: callback,
        data: dados,
        processData: false,
        contentType: false,
        beforeSend: function (response) {
            // Preparação antes do envio. 
        },
        success: function (response) {
            // Chama callback.
            this.myCallback(response);

            // Mostra o alerta na tela.
            alerts2("Sucesso", "success", response.msg);
        },
        error: function (response) {
            // Caso não tenha mensagem da api.
            if (!response || !response.responseText || !JSON.parse(response.responseText).msg) {
                // Mensagem padrão
                response = { response: '', msg: 'Erro na chamada AJAX.' };
            } else {
                // Mensagem do servidor.
                response = { response: '', msg: JSON.parse(response.responseText).msg, status: response.status };
            }
            this.myCallback(response);

            // Mostra o alerta na tela.
            alerts2("Erro " + response.status, "error", response.msg);
        },
        complete: function (response) {
            // Ao finalizar toda a execução.
        },
    }).done(function (response) {
        // Para concluir o processo.
    });
}


function alerts2(title = 'Notificação', icon = 'success', html = 'ND') {
    Swal.fire({
        title: title,
        icon: icon,
        html: html
    });
}

/**
 * Alerta pequeno
 *
 * @param string title
 * @param string icon
 * 
 * @return void
 * 
 */
function alertMin(title = 'Sucesso', icon = 'success') {
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 1000,
        timerProgressBar: true,
        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });
      Toast.fire({
        icon: icon,
        title: title
      });
}

/**
 * Função geral para exibir notificação.
 * 
 * @param {*} options 
 */
function notificaBrowser(options) {

    // Verifica se o browser suporta notificações
    if (!("Notification" in window)) {
        console.log("Este browser não suporta notificações de Desktop")
    }

    // Se tiver permissão, exibe a notificação.
    else if (Notification.permission === "granted") {
        var notification = new Notification(options.title, {
            icon: options.icon,
            body: options.body
        });
    }

    // Se não tiver permissão, pergunta se usuário quer permitir notificação.
    else if (Notification.permission !== 'denied') {
        Notification.requestPermission(function (permission) {

            // Se usuário aceitar, envia a notificação.
            if (permission === "granted") {
                var notification = new Notification(options.title, {
                    icon: options.icon,
                    body: options.body
                });
            }
        });
    }
}
// Solicita permissão para notificação.
Notification.requestPermission();

/**
 * Função para copiar valor para área de transferência.
 * @param {*} value 
 */
// 
function cpTransferencia(value) {
    
    // Se input não está criado, cria.
    if (!$("input#cpTransferencia").length) {
        $("body").append('<input type="text" id="cpTransferencia" value="" class="m-0 p-0" style="height: 0px;border: 0;position: absolute;"/>');
    }

    // Atribui o valor a ser copiado ao input.
    $("#cpTransferencia").val(value);
    $("#cpTransferencia").attr('value', value);

    // Exibe formulário.
    $("#cpTransferencia").show();

    // Seleciona o texto.
    $("#cpTransferencia").select();

    // Realiza o comando copiar.
    document.execCommand('copy');
}

/**
 * Copia o conteúdo (texto) do elemento
 *
 * @param Element e
 * 
 * @return void
 * 
 */
function cpTransferenciaText(e) {
    cpTransferencia($(e).text());
    alertMin("Copiado para Área de Transferência");
}

/**
 * Função que registra ação na tabela de registros.
 * 
 * @param {*} cod 
 * @param {*} obs 
 * @param {*} url 
 * @param {*} token // {{token.registro}}
 */
function btnRegistro(cod, obs, url, token) {

    // Prepara valores
    data = {
        cod: cod,
        obs: obs,
        token: token,
    };

    $.ajax({
        // type: 'GET',
        url: url,
        // data: data,
        processData: false,
        contentType: false,
        success: function (data) {
            // Envio com sucesso.
            console.log(data);
            // dados = data;
        },
        error: function (data) {
            // Erro ao realizar a chamada.
            console.log('Não registrou a ação: ' + data.cod);
        }
    });
}














/**
 * ******************************************************************************************************
 * TESTES
 */



/**
 * Exemplo de uso da ajaxDados(url, dados, callback);
 * Neste exemplo o script faz uma consulta na api da página enviando os dados criados e escreve o responseorno no console.log.
 */
function teste_ajaxDados() {
    // Console
    console.log('Início do teste ajaxDados');

    // Preparação dos dados.
    dados = new FormData(/* ID formulário */);
    dados.append('f-formToken', '{{tokens.pageApi}}');  // Token para uso de API da própria página.
    dados.append('f-formToken', '{{tokens.pageFunc}}'); // Token para uso envio de dados para própria página.
    dados.append('campo', 'valor'); // Exemplo de inclusão de valores.

    // Chamada AJAX
    ajaxDados('https://v3.local/api/00-modelo/teste/test', dados, function (response) {
        // Para testes
        console.log(response);

        // Verifica se teve responseorno ok.
        if (response.response) {

            // code...

            // Notificação.
            Swal.fire({
                icon: "success",
                title: "Sucesso.",
                text: response.msg,
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        } else {

            // code...

            // Notificação.
            Swal.fire({
                icon: 'error',
                title: 'Erro.',
                text: response.msg,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
            });
        }
    })

    // Console
    console.log('Fim do teste ajaxDados');
}

/**
 * Teste da função de notificação.
 */
function teste_notificaBrowser() {
    // Console
    console.log('Início do teste notificaBrowser');

    // Prepara os dados.
    options = {
        title: 'título da notificação',
        icon: 'https://v3.local/favicon.ico',
        body: 'Texto da notificação.',
    }

    // Chama a notificação.
    notificaBrowser(options);

    // Console
    console.log('Fim do teste notificaBrowser');
}