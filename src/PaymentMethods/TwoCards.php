<?php namespace TourChannel\Payments\PaymentMethods;

use Illuminate\Http\Request;
use TourChannel\Payments\Enum\StatusTransactionEnum;
use TourChannel\Payments\Service\RequestConnect;
use TourChannel\Payments\Traits\Customer;
use TourChannel\Payments\Traits\ShopCart;

/**
 * Método de pagamento para dois cartões
 * Class TwoCards
 * @package TourChannel\Payments\PaymentMethods
 */
class TwoCards
{
    /** Dados do cliente e do carrinho */
    use ShopCart, Customer;

    /** PATH da URl na API */
    const _PATH = '/pay/two_cards';

    /**
     * Formatado do array que ira para API
     * @var array
     */
    protected $payload = [
        'order' => '',
        'amount' => 0,
        'cards' => [
            [
                'installments' => 0,
                'amount' => 0,
                'card' => [
                    'number' => '',
                    'holderName' => '',
                    'expirationMonth' => 0,
                    'expirationYear' => 0,
                    'cvv' => ''
                ]
            ],
            [
                'installments' => 0,
                'amount' => 0,
                'card' => [
                    'number' => '',
                    'holderName' => '',
                    'expirationMonth' => 0,
                    'expirationYear' => 0,
                    'cvv' => ''
                ]
            ]
        ],
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
     * Numero de parcelas no primerio cartão
     * @param $count
     * @return $this
     */
    public function setCardOneInstallmentsCount($count)
    {
        $this->payload['cards'][0]['installments'] = $count;

        return $this;
    }

    /**
     * Valor para cobrar no primerio cartão
     * @param $amount_card_one
     * @return $this
     */
    public function setCardOneAmount($amount_card_one)
    {
        $this->payload['cards'][0]['amount'] = $amount_card_one;

        return $this;
    }

    /**
     * Número do primerio cartão de crédito
     * @param $number_card
     * @return $this
     */
    public function setCardOneCreditCardNumber($number_card)
    {
        $this->payload['cards'][0]['card']['number'] = str_replace(" ", "", $number_card);

        return $this;
    }

    /**
     * Nome impresso no primerio cartão
     * @param $holder_name
     * @return $this
     */
    public function setCardOneHolderName($holder_name)
    {
        $this->payload['cards'][0]['card']['holderName'] = $holder_name;

        return $this;
    }

    /**
     * Mês que expira o primerio cartão
     * @param $exp_month
     * @return $this
     */
    public function setCardOneExpMonth($exp_month)
    {
        $this->payload['cards'][0]['card']['expirationMonth'] = $exp_month;

        return $this;
    }

    /**
     * Ano que expira o primerio cartão
     * @param $exp_year
     * @return $this
     */
    public function setCardOneExpYear($exp_year)
    {
        $this->payload['cards'][0]['card']['expirationYear'] = $exp_year;

        return $this;
    }

    /**
     * Código de segurança do primerio cartão
     * @param $security_code
     * @return $this
     */
    public function setCardOneSecurityCode($security_code)
    {
        $this->payload['cards'][0]['card']['cvv'] = $security_code;

        return $this;
    }

    /**
     * Numero de parcelas no segundo cartão
     * @param $count
     * @return $this
     */
    public function setCardTwoInstallmentsCount($count)
    {
        $this->payload['cards'][1]['installments'] = $count;

        return $this;
    }

    /**
     * Valor para cobrar no segundo cartão
     * @param $amount_card_one
     * @return $this
     */
    public function setCardTwoAmount($amount_card_one)
    {
        $this->payload['cards'][1]['amount'] = $amount_card_one;

        return $this;
    }

    /**
     * Número do segundo cartão de crédito
     * @param $number_card
     * @return $this
     */
    public function setCardTwoCreditCardNumber($number_card)
    {
        $this->payload['cards'][1]['card']['number'] = str_replace(" ", "", $number_card);

        return $this;
    }

    /**
     * Nome impresso no segundo cartão
     * @param $holder_name
     * @return $this
     */
    public function setCardTwoHolderName($holder_name)
    {
        $this->payload['cards'][1]['card']['holderName'] = $holder_name;

        return $this;
    }

    /**
     * Mês que expira o segundo cartão
     * @param $exp_month
     * @return $this
     */
    public function setCardTwoExpMonth($exp_month)
    {
        $this->payload['cards'][1]['card']['expirationMonth'] = $exp_month;

        return $this;
    }

    /**
     * Ano que expira o segundo cartão
     * @param $exp_year
     * @return $this
     */
    public function setCardTwoExpYear($exp_year)
    {
        $this->payload['cards'][1]['card']['expirationYear'] = $exp_year;

        return $this;
    }

    /**
     * Código de segurança do segundo cartão
     * @param $security_code
     * @return $this
     */
    public function setCardTwoSecurityCode($security_code)
    {
        $this->payload['cards'][1]['card']['cvv'] = $security_code;

        return $this;
    }

    /**
     * Efetua cobrança nos cartões de crédito
     * @return array
     */
    public function pay()
    {
        // Realiza a transação
        $response_api = $this->chargeOnCards();

        // Verifica se deu certo
        if($response_api->status == StatusTransactionEnum::PAGO) {
            return [
                'approved' => true,
                'transaction_id' => $response_api->transactionId,
                'response' => $response_api
            ];
        }

        // Verifica se algum dos dois cartoes falhou
        if($response_api->status == StatusTransactionEnum::CANCELADO) {
            return [
                'approved' => false,
                'erro' => "Pagamento não autorizado em algum dos dois cartões"
            ];
        }

        // Caso falhe a transação
        return [
            'approved' => false,
            'erro' => $response_api->message ?? "Não foi possível efetuar o pagamento!"
        ];
    }

    /**
     * Realiza a comunicação com a API de pagamentos
     * @return mixed|string
     */
    private function chargeOnCards() {

        // Connect da API de pagamentos
        $request_connect = new RequestConnect();

        // Realiza a comunicação
        return $request_connect->connect_api(self::_PATH, Request::METHOD_POST, $this->payload);
    }
}