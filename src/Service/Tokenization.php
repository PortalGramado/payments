<?php namespace PortalGramado\Payments\Service;

use Illuminate\Http\Request;

/**
 * Informações para tokenização do cartão de crédito
 * Class Tokenization
 * @package PortalGramado\Payments\Service
 */
class Tokenization
{
    /**
     * Path da URL de requisição
     */
    const _PATH = '/v1/tokens/card';

    /**
     * Recupera o token para o cartão de crédito informado
     * @param string $number_card
     * @return mixed
     * @throws \Exception
     */
    static public function getNumberToken(string $number_card)
    {
       $token = self::getNewNumberToken($number_card);

       return $token->number_token ?? '';
    }


    /**
     * Comunicação com a API para gerar novo Number Token do Cartão de Crédito
     * @param string $number_card
     * @return mixed|string
     * @throws \Exception
     */
    private static function getNewNumberToken(string $number_card)
    {
        // Connect da API de pagamentos
        $request_connect = new RequestConnect();

        $data = [
            'card_number' => $number_card
        ];

        // Realiza a comunicação
        return $request_connect->connect_api(self::_PATH, Request::METHOD_POST, $data);
    }
}