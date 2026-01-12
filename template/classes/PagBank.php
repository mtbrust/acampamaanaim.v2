<?php

namespace template\classes;

class PagBank
{
    /**
     * URL base da API do PagBank
     */
    private static $apiUrl = 'https://api.pagbank.com.br';
    private static $apiUrlSandbox = 'https://sandbox.api.pagbank.com.br';

    /**
     * Criar link de pagamento
     * 
     * Cria um link de pagamento no PagBank para uma inscrição.
     * 
     * @param array $options Opções do pagamento:
     *   - token: Token de acesso da API (obrigatório)
     *   - valor: Valor do pagamento em reais (obrigatório)
     *   - descricao: Descrição do pagamento (obrigatório)
     *   - referencia: Referência única (ID da inscrição) (obrigatório)
     *   - nome_cliente: Nome do cliente (opcional)
     *   - email_cliente: Email do cliente (opcional)
     *   - telefone_cliente: Telefone do cliente (opcional)
     *   - cpf_cliente: CPF do cliente (opcional)
     *   - data_expiracao: Data de expiração do link (opcional, formato: Y-m-d\TH:i:s)
     *   - sandbox: true para usar ambiente sandbox, false para produção (padrão: false)
     *   - webhook_url: URL para receber notificações de status do pagamento (opcional)
     * 
     * @return array Retorna array com:
     *   - success: true/false
     *   - link_pagamento: URL do link de pagamento (se sucesso)
     *   - id_pagamento: ID do pagamento no PagBank (se sucesso)
     *   - msg: Mensagem de retorno
     *   - error: Detalhes do erro (se houver)
     */
    public static function criarLinkPagamento($options)
    {
        // Valores padrão
        $defaults = [
            'token' => '',
            'valor' => 0,
            'descricao' => '',
            'referencia' => '',
            'nome_cliente' => '',
            'email_cliente' => '',
            'telefone_cliente' => '',
            'cpf_cliente' => '',
            'data_expiracao' => null,
            'sandbox' => false,
            'webhook_url' => null,
        ];

        $options = array_merge($defaults, $options);

        // Validações
        if (empty($options['token'])) {
            return [
                'success' => false,
                'msg' => 'Token de acesso é obrigatório.',
                'error' => 'TOKEN_REQUIRED'
            ];
        }

        if (empty($options['valor']) || $options['valor'] <= 0) {
            return [
                'success' => false,
                'msg' => 'Valor do pagamento deve ser maior que zero.',
                'error' => 'INVALID_VALUE'
            ];
        }

        if (empty($options['descricao'])) {
            return [
                'success' => false,
                'msg' => 'Descrição do pagamento é obrigatória.',
                'error' => 'DESCRIPTION_REQUIRED'
            ];
        }

        if (empty($options['referencia'])) {
            return [
                'success' => false,
                'msg' => 'Referência (ID da inscrição) é obrigatória.',
                'error' => 'REFERENCE_REQUIRED'
            ];
        }

        // Define URL base (sandbox ou produção)
        $baseUrl = $options['sandbox'] ? self::$apiUrlSandbox : self::$apiUrl;

        // Prepara dados do pagamento
        // Nota: A estrutura pode variar conforme a versão da API do PagBank
        // Ajuste conforme a documentação oficial mais recente
        $dadosPagamento = [
            'amount' => [
                'value' => (int)($options['valor'] * 100), // Valor em centavos
                'currency' => 'BRL'
            ],
            'reference_id' => $options['referencia'],
            'description' => $options['descricao'],
        ];

        // Adiciona informações do cliente se fornecidas
        if (!empty($options['nome_cliente']) || !empty($options['email_cliente']) || !empty($options['cpf_cliente'])) {
            $dadosPagamento['customer'] = [];
            
            if (!empty($options['nome_cliente'])) {
                $dadosPagamento['customer']['name'] = $options['nome_cliente'];
            }
            
            if (!empty($options['email_cliente'])) {
                $dadosPagamento['customer']['email'] = $options['email_cliente'];
            }
            
            if (!empty($options['telefone_cliente'])) {
                $dadosPagamento['customer']['phone'] = preg_replace('/[^0-9]/', '', $options['telefone_cliente']);
            }
            
            if (!empty($options['cpf_cliente'])) {
                $cpf = preg_replace('/[^0-9]/', '', $options['cpf_cliente']);
                $dadosPagamento['customer']['tax_id'] = $cpf;
            }
        }

        // Adiciona data de expiração se fornecida
        if (!empty($options['data_expiracao'])) {
            $dadosPagamento['expires_at'] = $options['data_expiracao'];
        }

        // Adiciona webhook se fornecido
        if (!empty($options['webhook_url'])) {
            $dadosPagamento['notification_urls'] = [$options['webhook_url']];
        }

        // Configura método de pagamento (link de pagamento)
        $dadosPagamento['payment_method'] = [
            'type' => 'CREDIT_CARD',
            'installments' => 1
        ];

        // Realiza a requisição
        $url = $baseUrl . '/orders';
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $options['token'],
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dadosPagamento));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $resposta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Verifica erros de cURL
        if ($curlError) {
            return [
                'success' => false,
                'msg' => 'Erro ao conectar com a API do PagBank: ' . $curlError,
                'error' => 'CURL_ERROR'
            ];
        }

        // Decodifica resposta
        $respostaDecodificada = json_decode($resposta, true);

        // Verifica se houve erro na resposta
        if ($httpCode >= 400) {
            $mensagemErro = 'Erro na API do PagBank';
            if (isset($respostaDecodificada['error_message'])) {
                $mensagemErro = $respostaDecodificada['error_message'];
            } elseif (isset($respostaDecodificada['message'])) {
                $mensagemErro = $respostaDecodificada['message'];
            }

            return [
                'success' => false,
                'msg' => $mensagemErro,
                'error' => isset($respostaDecodificada['error']) ? $respostaDecodificada['error'] : 'API_ERROR',
                'http_code' => $httpCode,
                'response' => $respostaDecodificada
            ];
        }

        // Verifica se o link foi criado com sucesso
        if (isset($respostaDecodificada['links']) && is_array($respostaDecodificada['links'])) {
            // Procura pelo link de pagamento
            $linkPagamento = null;
            foreach ($respostaDecodificada['links'] as $link) {
                if (isset($link['rel']) && $link['rel'] === 'PAY') {
                    $linkPagamento = $link['href'];
                    break;
                }
            }

            if ($linkPagamento) {
                return [
                    'success' => true,
                    'link_pagamento' => $linkPagamento,
                    'id_pagamento' => isset($respostaDecodificada['id']) ? $respostaDecodificada['id'] : null,
                    'msg' => 'Link de pagamento criado com sucesso.',
                    'response' => $respostaDecodificada
                ];
            }
        }

        // Se chegou aqui, pode ser que a estrutura da resposta seja diferente
        // Tenta encontrar o link de outra forma
        if (isset($respostaDecodificada['checkout_url']) || isset($respostaDecodificada['payment_url'])) {
            $linkPagamento = $respostaDecodificada['checkout_url'] ?? $respostaDecodificada['payment_url'];
            
            return [
                'success' => true,
                'link_pagamento' => $linkPagamento,
                'id_pagamento' => isset($respostaDecodificada['id']) ? $respostaDecodificada['id'] : null,
                'msg' => 'Link de pagamento criado com sucesso.',
                'response' => $respostaDecodificada
            ];
        }

        // Se não encontrou o link, retorna erro
        return [
            'success' => false,
            'msg' => 'Link de pagamento não foi retornado pela API.',
            'error' => 'LINK_NOT_FOUND',
            'response' => $respostaDecodificada
        ];
    }

    /**
     * Consultar status do pagamento
     * 
     * Consulta o status de um pagamento no PagBank.
     * 
     * @param array $options Opções:
     *   - token: Token de acesso da API (obrigatório)
     *   - id_pagamento: ID do pagamento no PagBank (obrigatório)
     *   - sandbox: true para usar ambiente sandbox (padrão: false)
     * 
     * @return array Retorna array com status do pagamento
     */
    public static function consultarStatusPagamento($options)
    {
        $defaults = [
            'token' => '',
            'id_pagamento' => '',
            'sandbox' => false,
        ];

        $options = array_merge($defaults, $options);

        if (empty($options['token'])) {
            return [
                'success' => false,
                'msg' => 'Token de acesso é obrigatório.',
                'error' => 'TOKEN_REQUIRED'
            ];
        }

        if (empty($options['id_pagamento'])) {
            return [
                'success' => false,
                'msg' => 'ID do pagamento é obrigatório.',
                'error' => 'PAYMENT_ID_REQUIRED'
            ];
        }

        $baseUrl = $options['sandbox'] ? self::$apiUrlSandbox : self::$apiUrl;
        $url = $baseUrl . '/orders/' . $options['id_pagamento'];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $options['token'],
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $resposta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'msg' => 'Erro ao conectar com a API do PagBank: ' . $curlError,
                'error' => 'CURL_ERROR'
            ];
        }

        $respostaDecodificada = json_decode($resposta, true);

        if ($httpCode >= 400) {
            return [
                'success' => false,
                'msg' => 'Erro ao consultar status do pagamento.',
                'error' => 'API_ERROR',
                'http_code' => $httpCode,
                'response' => $respostaDecodificada
            ];
        }

        return [
            'success' => true,
            'status' => $respostaDecodificada['status'] ?? null,
            'msg' => 'Status consultado com sucesso.',
            'response' => $respostaDecodificada
        ];
    }

    /**
     * ping
     * 
     * Teste de chamada na classe.
     *
     * @return string
     */
    public static function ping() {
        return 'pong';
    }
}