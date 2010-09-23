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

    const BUDGET_URI    = 'https://api.textbroker.de/Budget/';
    const BUDGET_ID     = 0; # Set this or pass in constructor
    const BUDGET_KEY    = ''; # Set this or pass in constructor
    const PASSWORD      = ''; # Set this or pass in constructor

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
    function __construct($budgetKey = null, $budgetId = null, $password = null) {

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

        $this->salt = rand(0, 10000);
        $this->hash = md5($this->salt . $password);
        $this->login();
    }

    /**
     * Singleton
     *
     * @return object
     */
    public static function &singleton($budgetKey = null, $budgetId = null, $password = null) {

        static $instance;

        if (!isset($instance)) {
            $class      = get_called_class();
            $instance   = new $class($budgetKey, $budgetId, $password);
        }

        return $instance;
    }

    /**
     * Login to textbroker service
     *
     * @throws Exception
     */
    private function login() {

        $this->setOptions(array(
            'location'      => 'https://api.textbroker.de/Budget/loginService.php',
            'uri'           => self::BUDGET_URI,
        ));

        if (!$this->getClient()->doLogin($this->salt, $this->hash, $this->budgetKey)) {
            throw new Exception('Could not login');
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
}
?>