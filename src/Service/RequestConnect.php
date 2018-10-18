<?php namespace PortalGramado\Payments\Service;

/**
 * Class RequestConnect
 * @package PortalGramado\Payments\Service
 */
class RequestConnect
{
    /**
     * Url base da API de pagamentos
     */
    const URL_BASE_API = 'https://api.getnet.com.br';

    /**
     * Url base da API de pagamentos - sandbox
     */
    const URL_BASE_API_SANDBOX = 'https://api-sandbox.getnet.com.br';

    /**
     * Curl para autenticação na API
     * @param $path
     * @param $method
     * @param $auth_string
     * @return mixed|object
     */
    public function authenticate_api($path, $method, $auth_string)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_URL => self::getUrlBase() . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POSTFIELDS => http_build_query([
                "scope" => "oob",
                "grant_type" => "client_credentials"
            ]),
            CURLOPT_HTTPHEADER => [
                "authorization: Basic $auth_string",
                "content-type: application/x-www-form-urlencoded"
            ]
        ]);

        $response = curl_exec($curl);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $error_curl = curl_error($curl);

        curl_close($curl);

        return $this->response($response, $http_status, $error_curl);
    }

    /**
     * CURL para requisições na API depois que já possui o token
     * @param $path
     * @param $method
     * @param array $data
     * @return mixed|object
     * @throws \Exception
     */
    public function connect_api($path, $method, array $data)
    {
        $token_api = Authentication::getToken();

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_URL => self::getUrlBase() . $path,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_POSTFIELDS => json_encode($data, JSON_FORCE_OBJECT),
            CURLOPT_HTTPHEADER => [
                "authorization: Bearer $token_api",
                "content-type: application/json"
            ]
        ]);

        $response = curl_exec($curl);

        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $error_curl = curl_error($curl);

        curl_close($curl);

        return $this->response($response, $http_status, $error_curl);
    }

    /**
     * Trata o retorno da API
     * @param $response
     * @param $http_status
     * @param $error_curl
     * @return mixed|object
     */
    private function response($response, $http_status, $error_curl) {

        $resp = $response;
        $response = json_decode($response) ;

        if(is_null($response)) {
            $response = json_decode(gzdecode($resp));
        };

        if($http_status != 200 && $http_status != 201) {

            $error_curl = (isset($response->message)) ? $response->message : $error_curl;

            return (object) [
                'status' => "error",
                'message' => "Falha na comunicação com a central de pagamentos: $error_curl",
                'response' => $response
            ];
        }

        return $response;
    }

    /**
     * Retorna URL BASE da API
     * @return string
     */
    private function getUrlBase()
    {
        if(env('APP_ENV') == 'local'){
            return self::URL_BASE_API_SANDBOX;
        }

        return self::URL_BASE_API;
    }
}