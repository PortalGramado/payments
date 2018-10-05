<?php namespace PortalGramado\Payments;

use PortalGramado\Payments\PaymentMethods\CreditCard;

/**
 * Classe para os métodos de pagamento
 * Class ChannelPayments
 * @package PortalGramado\Payments
 */
class ChannelPayments
{
    /**
     * Pagamento com cartão
     * @return CreditCard
     */
    public function credit_card()
    {
        return new CreditCard();
    }
}