# Portal Gramado - Payments GetNet

Pacote de integração com API de pagamento da GETNET

## Instalação da biblioteca

Instale a biblioteca: `composer require portalgramado/payments`

## Configuração das chaves

Adicione em seu `.env` o trecho abaixo e altere com os seus dados de acesso

```text
# Credenciais da Getnet
SELLER_ID=
CLIENT_ID=
CLIENT_SECRET=
```

## Changelog

Lista de mudanças, melhorias e correções de bugs.

### *v1.0.6 - (20 Outubro 2018)*

- Ajustado metodo para parcelamento, quando for 1 parcela envia como FULL

### *v1.0.5 - (18 Outubro 2018)*

- Adicionado a casa `response` a todos os métodos de pagamento

### *v1.0.3 e v1.0.4 - (18 Outubro 2018)*

- Arrumado o expires_in no cache do token de autenticação da Getnet
- Fixes class Carbon

### *v1.0.2 - (15 Outubro 2018)*

- Correção da URL de produção da GetNet

### *v1.0.1 - (05 Outubro 2018)*

- Correção do método setAmount

### *v1.0.0 - (05 Outubro 2018)*

- Criação e configuração da bilbioteca