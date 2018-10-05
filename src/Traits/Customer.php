<?php namespace PortalGramado\Payments\Traits;

/**
 * Trait Customer
 * @package PortalGramado\Payments\Traits
 */
trait Customer
{
    /**
     * Id do cliente
     * @param string $id
     * @return $this
     */
    public function setCustomerId(string $id)
    {
        $this->payload['customer']['customer_id'] = $id;

        return $this;
    }

    /**
     * Primeiro nome do cliente
     * @param string $name
     * @return $this
     */
    public function setCustomerName(string $name)
    {
        $this->payload['customer']['name'] = $this->tirarAcentos($name);

        return $this;
    }

    /**
     * Email do cliente
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail(string $email)
    {
        $this->payload['customer']['email'] = $email;

        return $this;
    }

    /**
     * Documento do cliente
     * @param $document_number
     * @return $this
     */
    public function setCustomerDocumentNumber(string $document_number)
    {
        $this->payload['customer']['document_number'] = preg_replace("/[^0-9]/", "", $document_number);
        $this->payload['customer']['document_type'] = "CPF";

        return $this;
    }

    /**
     * Configura o telefone do cliente, contendo o DDD
     * @param $phone_number
     * @return $this
     */
    public function setCustomerPhoneNumber(string $phone_number)
    {
        $this->payload['customer']['phone_number'] = preg_replace("/[^0-9]/", "", $phone_number);

        return $this;
    }
}