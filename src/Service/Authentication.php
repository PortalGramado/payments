<?php namespace PortalGramado\Payments\Service;

use Cache;
use Illuminate\Http\Request;

/**
 * Informações para autenticação
 * Class Authentication
 * @package PortalGramado\Payments\Service
 */
class Authentication
{
    /**
     * Client ID de acesso na API
     * @var
     */
    static protected $client_id;

    /**
     * Client secret de acesso API
     * @var
     */
    static protected $client_secret;

    /**
     * Nome da chave onde fica o token da API em cache até expirar
     */
    const TOKEN_CACHE = 'token_portalgramado_payment';

    /**
     * Path da URL de requisição
     */
    const _PATH = '/auth/oauth/v2/token';

    /**
     * Recupera o token de autenticação no Cache
     * Caso não tenha gera um novo token e coloca no cache
     * @return mixed
     * @throws \Exception
     */
    static public function getToken()
    {
        // Verifica os dados de acesso
        self::verifyCredentials();

        // Recupera o token no cache
        $token_cache = Cache::get(self::TOKEN_CACHE, null);

        // Caso não tenha o token gera um novo token
        return $token_cache ?? self::getNewToken();
    }

    /**
     * Verifica as credenciais colocadas no env
     * @throws \Exception
     */
    static public function verifyCredentials()
    {
        $client_id = env('CLIENT_ID');
        $client_secret = env('CLIENT_SECRET');

        // Verifica se a configuração existe no ENV
        if($client_id == "" || $client_secret == "") {
            throw new \Exception('CLIENT_ID ou CLIENT_SECRET para acesso à API não está configurado no .ENV');
        } else {
            // Configura o CLIENT_ID
            self::$client_id = $client_id;
            // Configura o CLIENT_SECRET
            self::$client_secret = $client_secret;
        }
    }

    /**
     * Gera um novo token e coloca no cache
     * @return mixed
     */
    static private function getNewToken()
    {
        // Token devolvido pela API
        $token_access = self::authenticate();

        // Expires token
        $expires = Carbon::now()->addSeconds($token_access->expires_in - 1200);

        // Coloca o token em cache, retirando 20 minutos do tempo de expiração
        Cache::put(self::TOKEN_CACHE, $token_access->access_token, $expires);

        return $token_access->access_token;
    }

    /**
     * Comunicação com a API para gerar novo Token
     * @return mixed|string
     */
    private static function authenticate() {

        // Connect da API de pagamentos
        $request_connect = new RequestConnect();

        // Gerando o AuthString em base64
        $auth_string = base64_encode(self::$client_id . ':' . self::$client_secret);

        // Realiza a comunicação
        return $request_connect->authenticate_api(self::_PATH, Request::METHOD_POST, $auth_string);
    }
}