<?php namespace TourChannel\Payments\PaymentMethods;

use Illuminate\Http\Request;
use TourChannel\Payments\Enum\StatusTransactionEnum;
use TourChannel\Payments\Service\RequestConnect;
use TourChannel\Payments\Traits\Customer;
use TourChannel\Payments\Traits\ShopCart;

/**
 * Método de pagamento via Boleto
 * Class Ticket
 * @package TourChannel\Payments\PaymentMethods
 */
class Ticket
{
    /** Dados do cliente e do carrinho */
    use ShopCart, Customer;

    /** PATH da URl na API */
    const _PATH = '/pay/ticket';

    /** Quantidade de dias para vencimento do boleto */
    const daysDue = 2;

    /**
     * Formatado do array que ira para API
     * @var array
     */
    protected $payload = [
        'order' => '',
        'amount' => 0,
    ];

    /**
     * Numero do pedido
     * @param $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->payload['order'] = $order;

        return $this;
    }

    /**
     * Valor da compra em centavos
     * @param $valor
     * @return $this
     */
    public function setAmount($valor)
    {
        $this->payload['amount'] = $valor;

        return $this;
    }

    /**
     * Gera o boleto com a central de pagamentos
     * @return array
     */
    public function pay()
    {
        // Realiza a transação
        $response_api = $this->generateTicket();

        // Verifica se deu certo
        if($response_api->status == StatusTransactionEnum::PENDENTE) {
            return [
                'approved' => true,
                'transaction_id' => $response_api->transactionId,
                'barcode' => $response_api->barcode,
                'boleto_url' => $response_api->url,
                'response' => $response_api
            ];
        }

        // Caso falhe a transação
        return [
            'approved' => false,
            'erro' => $response_api->message ?? "Não foi possível gerar o boleto, tente novamente!"
        ];
    }

    /**
     * Comunica com a API para gerar o boleto
     * @return mixed|string
     */
    private function generateTicket() {

        // Connect da API de pagamentos
        $request_connect = new RequestConnect();

        // Realiza a comunicação
        return $request_connect->connect_api(self::_PATH, Request::METHOD_POST, $this->payload);
    }
}