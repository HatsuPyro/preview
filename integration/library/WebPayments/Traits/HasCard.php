<?php

namespace Pyrobyte\WebPayments\Traits;

trait HasCard
{
    protected $card;

    /**
     * Получает номер карты для получения баланса
     */
    public function getCard()
    {
        return $this->card;
    }

    /**
     * Задает номер карты
     *
     * @param $card
     * @return $this
     */
    public function setCard($card)
    {
        $this->card = $card;
        return $this;
    }
}