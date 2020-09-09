<?php
/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 22.11.19
 * Time: 17:02
 */

namespace App\Extensions\Payment\Adapter;

use App\Extensions\Payment\PaymentAbstract;
use App\Extensions\Payment\Exception\AuthenticationException;
use App\Extensions\Payment\Exception\LockedException;
use App\Models\SettingsItem;
use Pyrobyte\Tele2Web\Exceptions\AuthBanned;
use Pyrobyte\WebPayments\Exception\AttemptsExceededException;
use Pyrobyte\WebPayments\Exception\ErrorAuthenticationWebBank;
use Pyrobyte\WebPayments\Exception\ProxyException;
use App\Extensions\Payment\Exception\NotPasswordException;

abstract class WebAbstract extends PaymentAbstract
{
    protected $client = null;
    protected $getTransactionsClass = null;
    protected $getBalanceClass = null;
    protected $transactionTranslatorClass = null;
    protected $namePayment = '';
    protected $state = null;
    protected $isCheckPassword = true;
    protected $isCheckCard = true;
    protected $card = 0;

    abstract protected function setClient($params);

    public function __construct($params, $tmpPath = null)
    {
        if ($this->isCheckPassword) {
            if (empty($params['password'])) {
                throw new NotPasswordException('Пароль не установлен');
            }
        }

        if ($this->isCheckCard) {
            if (empty($params['card'])) {
                throw new \Exception('Карта не установлена');
            }
            $this->card = $params['card'];
        }

        parent::__construct($params);

        $isAuth = $this->doAuth($params);

        $this->setClient($params);
        $this->client->setDoAuthorization($isAuth);

        if(!empty($params['proxy'])) {
            $this->client->setProxy($params['proxy']);
        }

        $this->client->init($tmpPath);

        $this->catchClientErrors(function () {
            $authed = $this->client->auth();
            if (!$authed) {
                throw new AuthenticationException('Ошибка авторизации ' . $this->namePayment);
            }
        });
    }

    protected function doAuth($params)
    {
       return true;
    }

    /**
     * Установки переменной, с помощью которой проверяется отключен ли кошелек или нет в л.к
     * @param $result
     */
    protected function setState($result)
    {
        $existsMethod = method_exists($result, 'getState');
        if ($existsMethod) {
            $this->state = $result->getState();
        }
    }

    /**
     * Получение переменной с помощью которой проверяется отключен ли кошелек или нет в лк
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @inheritdoc
     */
    public function getBalance($currency = null)
    {
        return $this->catchClientErrors(function() {
            try {
                $balanceResult = $this->client->call(new $this->getBalanceClass());
            } catch (\Exception $e) {
                throw new \Exception('Ошибка парсинга баланса через ЛК ' . $this->namePayment . ': ' . $e->getMessage());
            }

            $this->setState($balanceResult);

            return $balanceResult->getBalance();
        });
    }


    /**
     * @param $fromDate
     * @param $toDate
     * @return array
     * @throws AuthenticationException
     * @throws LockedException
     * @throws \App\Extensions\Payment\Exception\ProxyException
     */
    public function doGetTransactions($fromDate, $toDate)
    {
        return $this->catchClientErrors(function() use ($fromDate, $toDate) {
            try {
                $transactionsResult = $this->client->call(new $this->getTransactionsClass($fromDate, $toDate));
            } catch (\Exception $e) {
                throw new \Exception('Ошибка парсинга транзакций через ЛК ' . $this->namePayment . ': ' . $e->getMessage());
            }
            $binTransactions = $transactionsResult->transactions;
            $formattedTransactions = [];
            $this->setState($transactionsResult);

            foreach ($binTransactions as $transaction) {
                $translator = new $this->transactionTranslatorClass($transaction);
                $formattedTransaction = $translator->translate();
                if ($this->card) {
                    $formattedTransaction->card_number = $this->card;
                }
                $formattedTransactions[] = $formattedTransaction;
            }

            return $formattedTransactions;
        });
    }


    /**
     * Отлавливает ошибки клиента
     * @param $callback
     * @return mixed
     * @throws AuthenticationException
     * @throws LockedException
     * @throws \App\Extensions\Payment\Exception\ProxyException
     */
    private function catchClientErrors(callable $callback)
    {
        try {
            return $callback();
        } catch (\Pyrobyte\WebPayments\Exception\LockedException $e) {
            throw new LockedException($e->getMessage());
        } catch (AttemptsExceededException $e) {
            throw new AuthenticationException('Превышено количество попыток для текущего ip');
        } catch (AuthBanned $e) {
            throw new \Exception('Не возможно выполнить авторизацию, с момента предыдущей прошло меньше'
                . SettingsItem::getValueByCode(SettingsItem::CODE_TIME_BETWEEN_AUTHORIZATIONS) . 'минут');
        } catch (ProxyException $e) {
            $proxyException = new \App\Extensions\Payment\Exception\ProxyException($e->getMessage(), 0, $e);
            $proxyException->setProxy($e->getProxy());
            throw $proxyException;
        } catch (ErrorAuthenticationWebBank $e) {
            throw new \App\Extensions\Payment\Exception\ErrorAuthenticationWebBank($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Ошибка парсинга ЛК ' . $this->namePayment . ': ' . $e->getMessage());
        }
    }

}