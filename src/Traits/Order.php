<?php namespace PortalGramado\Payments\Traits;

/**
 * Trait Order
 * @package PortalGramado\Payments\Traits
 */
trait Order
{
    /**
     * Código de identificação da compra (código do pedido)
     * @param string $order_id
     * @return $this
     */
    public function setOrderId(string $order_id)
    {
        $this->payload['order']['order_id'] = $order_id;

        return $this;
    }
}