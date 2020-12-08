<?php

namespace App\Pix;

use \App\Pix\ValidationCodePix;

class Payload {
    /**
     * Pix Payload IDs
     * @var string
     */
    const ID_PAYLOAD_FORMAT_INDICATOR = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION = '26';
    const ID_MERCHANT_ACCOUNT_INFORMATION_GUI = '00';
    const ID_MERCHANT_ACCOUNT_INFORMATION_KEY = '01';
    const ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION = '02';
    const ID_MERCHANT_CATEGORY_CODE = '52';
    const ID_TRANSACTION_CURRENCY = '53';
    const ID_TRANSACTION_AMOUNT = '54';
    const ID_COUNTRY_CODE = '58';
    const ID_MERCHANT_NAME = '59';
    const ID_MERCHANT_CITY = '60';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE = '62';
    const ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID = '05';

    /**
     * Key pix
     * @var string
     */
    private $pixKey;

    /**
     * Payment description
     * @var string
     */
    private $description;

    /**
     * Name of the account holder
     * @var string
     */
    private $merchantName;

    /**
     * Account holder city
     * @var string
     */
    private $merchantCity;

    /**
     * pix transaction id
     * @var string
     */
    private $txid;

    /**
     * transaction amount
     * @var string
     */
    private $amount;


    public function setPixKey(string $pixKey): Payload
    {
        $this->pixKey = $pixKey;

        return $this;
    }

    public function setDescription(string $description): Payload
    {
        $this->description = $description;

        return $this;
    }

    public function setMerchantName(string $merchantName): Payload
    {
        $this->merchantName = $merchantName;

        return $this;
    }

    public function setMerchantCity(string $merchantCity): Payload
    {
        $this->merchantCity = $merchantCity;

        return $this;
    }

    public function setTxid(string $txid): Payload
    {
        $this->txid = $txid;

        return $this;
    }

    public function setAmount(string $amount): Payload
    {
        $this->amount = (string) number_format($amount, 2, '.', '');

        return $this;
    }

    /**
     * @return string
     */
    private function getMerchantAccountInformation()
    {
        $gui = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_GUI, 'br.gov.bcb.pix');
        $key = $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_KEY, $this->pixKey);
        $description = mb_strlen($this->description)  ? $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION_DESCRIPTION, $this->description) : '';

        return $this->getValue(self::ID_MERCHANT_ACCOUNT_INFORMATION, $gui.$key.$description);
    }

    /**
     * @param string $id
     * @param string $value
     * @return string $id.$size.$value
     */
    private function getValue($id, $value)
    {
        $size = str_pad(mb_strlen($value), 2, '0', STR_PAD_LEFT);
        return $id.$size.$value;
    }

    /**
     * @return string
     */
    private function getAdditionalDatFieldTemplate()
    {
        $txid = $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE_TXID, $this->txid);

        return $this->getValue(self::ID_ADDITIONAL_DATA_FIELD_TEMPLATE, $txid);
    }

    public function getPayload()
    {
        $payload = $this->getValue(self::ID_PAYLOAD_FORMAT_INDICATOR, '01').
                    $this->getMerchantAccountInformation().
                    $this->getValue(self::ID_MERCHANT_CATEGORY_CODE, '0000'). //fixed code
                    $this->getValue(self::ID_TRANSACTION_CURRENCY, '986'). // fixed code
                    $this->getValue(self::ID_TRANSACTION_AMOUNT, $this->amount).
                    $this->getValue(self::ID_COUNTRY_CODE, 'BR'). //fixed code
                    $this->getValue(self::ID_MERCHANT_NAME, $this->merchantName).
                    $this->getValue(self::ID_MERCHANT_CITY, $this->merchantCity).
                    $this->getAdditionalDatFieldTemplate();

        return $payload.ValidationCodePix::getCRC16($payload);
    }
}
