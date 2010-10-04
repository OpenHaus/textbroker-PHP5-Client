<?php
// +---------------------------------------------------------------------------+
// | Copyright (c) 2010, Fabio Bacigalupo                                      |
// | All rights reserved.                                                      |
// |                                                                           |
// | Redistribution and use in source and binary forms, with or without        |
// | modification, are permitted provided that the following conditions        |
// | are met:                                                                  |
// |                                                                           |
// | o Redistributions of source code must retain the above copyright          |
// |   notice, this list of conditions and the following disclaimer.           |
// | o Redistributions in binary form must reproduce the above copyright       |
// |   notice, this list of conditions and the following disclaimer in the     |
// |   documentation and/or other materials provided with the distribution.    |
// | o The names of the authors may not be used to endorse or promote          |
// |   products derived from this software without specific prior written      |
// |   permission.                                                             |
// |                                                                           |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS       |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT         |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR     |
// | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT      |
// | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,     |
// | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT          |
// | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,     |
// | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY     |
// | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT       |
// | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE     |
// | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | textbroker-PHP5-Client 0.1                                                |
// +---------------------------------------------------------------------------+
// | TextbrokerDAO.php                                                         |
// +---------------------------------------------------------------------------+
// | Authors: Fabio Bacigalupo <info1@open-haus.de>                            |
// +---------------------------------------------------------------------------+

/**
 * Wrapper for Textbroker API
 *
 * Description for classes and methods (parameter, return values) is taken from "Textbroker API-Dokumentation".
 *
 * Usage:
 * <code>
 * require_once 'TextbrokerBudgetOrderDAO.php';
 * $budgetOrder = TextbrokerBudgetOrderDAO::singleton();
 * $aCategories = $budgetOrder->getCategories();
 * $aStatus     = $budgetOrder->getStatus($budgetOrderId);
 * </code>
 *
 * @package textbroker-PHP5-Client
 * @author Fabio Bacigalupo <info1@open-haus.de>
 * @since PHP 5.3
 */
class Textbroker {

    const BUDGET_URI_GERMANY            = 'https://api.textbroker.de/Budget/';
    const BUDGET_URI_USA                = 'https://api.textbroker.com/Budget/';
    const BUDGET_LOCATION_DEFAULT       = 'us'; # or 'de' ATM
    const BUDGET_ID                     = 0; # Set this or pass in constructor
    const BUDGET_KEY                    = ''; # Set this or pass in constructor
    const PASSWORD                      = ''; # Set this or pass in constructor

    /**
     * You have placed a BudgetOrder which has not been saved correctly.
     * Please contact support.
     */
    const TB_STATUS_INTERNAL_ERROR      = 0;

    /**
     * Your BudgetOrder has been placed and saved correctly.
     */
    const TB_STATUS_PLACED              = 1;

    /**
     * Your BudgetOrder has been processed by Textbroker and is visible to authors.
     */
    const TB_STATUS_TB_ACCEPTED         = 2;

    /**
     * Your order is being written. With this status, a BudgetOrder can no longer be deleted.
     */
    const TB_STATUS_INWORK              = 3;

    /**
     * Text from the author has been completed and has passed through CopyScape.
     * Your BudgetOrder is waiting to be reviewed. Ownership rights have not yet been transferred to the client.
     * Notification of this status will be delivered to your CallbackURL.
     * An OrderID that is identified as BudgetID with Status 4 is ready to be reviewed.
     */
    const TB_STATUS_READY               = 4;

    /**
     * Text has been accepted by the client. All ownership rights have been transferred;
     * the final version of the text can be picked up.
     *
     * The callbackURL is used to set this status. An OrderID that is identified as
     * BudgetID with Status 5 is ready to be picked up.
     */
    const TB_STATUS_ACCEPTED            = 5;

    /**
     * Text has been delivered (particularly important as a control function,
     * so that you don’t pick up and use the same article twice)
     */
    const TB_STATUS_DELIVERED           = 6;

    /**
     * Client has deleted the BudgetOrder
     * (this is possible as long as the order is not being written by an author).
     */
    const TB_STATUS_DELETED             = 7;

    /**
     * Textbroker has approved the rejection.
     * From here you must decide whether you want to place the order again or delete it.
     */
    const TB_STATUS_REJECTION_GRANTED   = 8;

    /**
     * The order could not be released to the authors due to errors in the order description.
     * In this case, you will need to revise your description before continuing.
     */
    const TB_STATUS_ORDER_REFUSED       = 9;

    /**
     * The BudgetOrder is waiting for actions from the client, the author, or Textbroker
     *
     * (Example: when a rejection is awaiting approval from Textbroker).
     */
    const TB_STATUS_WAITING             = 10;

    private $location;
    private $aOptions;

    protected $budgetId;
    protected $budgetKey;
    protected $salt;
    protected $hash;

    /**
     * If you use multiple budgets you want to pass settings in constructor
     * rather than statically defining it above as constant
     *
     * @param string $budgetKey Budget key as shown in "Budget Login information" in textbroker API backend
     * @param int $budgetId Budget ID as shown in "Budget Login information" in textbroker API backend
     * @param string $password Password as defined in textbroker API backend
     */
    function __construct($budgetKey = null, $budgetId = null, $password = null, $location = self::BUDGET_LOCATION_DEFAULT) {

        if (!is_null($budgetKey)) {
            $this->budgetKey    = $budgetKey;
        } else {
            $this->budgetKey    = self::BUDGET_KEY;
        }

        if (!is_null($budgetId)) {
            $this->budgetId     = $budgetId;
        } else {
            $this->budgetId     = self::BUDGET_ID;
        }

        if (is_null($password)) {
            $password           = self::PASSWORD;
        }

        $this->location = $location;
        $this->salt     = rand(0, 10000);
        $this->hash     = md5($this->salt . $password);
        $this->login();
    }

    /**
     * Singleton
     *
     * @return object
     */
    public static function &singleton($budgetKey = null, $budgetId = null, $password = null, $location = self::BUDGET_LOCATION_DEFAULT) {

        static $instance;

        if (!isset($instance)) {
            $class      = get_called_class();
            $instance   = new $class($budgetKey, $budgetId, $password, $location);
        }

        return $instance;
    }

    /**
     * Login to textbroker service
     *
     * @throws Exception
     */
    private function login() {

        $this->setOptions(
            array(
                'location'      => $this->getUri() . 'loginService.php',
                'uri'           => $this->getUri(),
            )
        );

        if (!$this->getClient()->doLogin($this->salt, $this->hash, $this->budgetKey)) {
            throw new TextbrokerException('Could not login');
        }
    }

    /**
     * Open a new connection
     *
     * @param array $aOptions
     * @return object SoapClient
     */
    protected function getClient() {

        return new SoapClient(null, $this->aOptions);
    }

    /**
     * Set options which are passed to SoapClient
     *
     * @param array $aOptions
     */
    protected function setOptions(array $aOptions) {

        $this->aOptions = $aOptions;
    }

    /**
     * Get the correct uri depending on location
     *
     * @return string
     */
    protected function getUri() {

        if ($this->location == 'de') {
        	return self::BUDGET_URI_GERMANY;
        } else {
        	return self::BUDGET_URI_USA;
        }
    }
}

/**
 *
 *
 */
class TextbrokerException extends Exception {

    function __construct() {

        parent::__construct();
    }
}
?>