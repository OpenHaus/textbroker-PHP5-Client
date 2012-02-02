<?php
// +---------------------------------------------------------------------------+
// | Copyright (c) 2012, Fabio Bacigalupo                                      |
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
// | textbroker-PHP5-Client 1.0                                                |
// +---------------------------------------------------------------------------+
// | TextbrokerBudgetCheck.php                                                 |
// +---------------------------------------------------------------------------+
// | Authors: Fabio Bacigalupo <info1@open-haus.de>                            |
// +---------------------------------------------------------------------------+

require_once(dirname(__FILE__) . '/Textbroker.php');

/**
 * BudgetCheckService
 *
 * Budgetinformationen abfragen
 *
 * @package textbroker-PHP5-Client
 * @author Fabio Bacigalupo <info1@open-haus.de>
 */
class TextbrokerBudgetCheck extends Textbroker {

    /**
     * Singleton
     *
     * @return object
     */
    public static function &singleton($budgetKey = null, $budgetId = null, $password = null, $location = self::BUDGET_LOCATION_DEFAULT) {

        static $instance;

        if (!isset($instance)) {
            $class      = __CLASS__;
            $instance   = new $class($budgetKey, $budgetId, $password, $location);
        }

        return $instance;
    }

    /**
     *
     *
     * @param string $budgetKey
     * @param int $budgetId
     * @param string $password
     */
    function __construct($budgetKey = null, $budgetId = null, $password = null, $location = 'us') {

        parent::__construct($budgetKey, $budgetId, $password, $location);
        $this->setOptions(array(
            'location'      => $this->getUri() . 'budgetCheckService.php',
            'uri'           => $this->getUri(),
        ));
    }

    /**
     * Die aktuelle Belastung des Budgets als Array-Element "usage"
     * in Form einer Zahl mit 2 Stellen hinter dem Komma.
     * Betrifft nur die bereits abgerechneten BudgetOrders.
     * (Kein "sandbox"-Element im Testmodus)
     *
     * @throws SoapFault
     * @return array
     */
    public function getUsage() {

        return $this->getClient()->getUsage($this->salt, $this->hash, $this->budgetKey);
    }

    /**
     * Gibt Auskunft, ob das Budget im Testmodus betrieben wird
     *
     * @throws SoapFault
     * @return array (Array-Element "sandbox" = 1)
     */
    public function isInSandbox() {

        return $this->getClient()->isInSandbox($this->salt, $this->hash, $this->budgetKey);
    }

    /**
     * Gibt den im Kunden-Interface eingegebenen Namen zurück
     *
     * @throws SoapFault
     * @return string
     */
    public function getName() {

        $result = $this->getClient()->getName($this->salt, $this->hash, $this->budgetKey);

        return $result['name'];
    }

    /**
     * getActualPeriodData – Gibt genauere Auskunft über die Laufzeit
     * und die Nutzungswerte des Budgets. (Kein "sandbox"-Element im Testmodus.)
     *
     * Zurückgegeben wird ein Array mit folgenden Elementen:
     * start – Startzeit als Unix-Zeitstempel (integer)
     * end – Ende der BudgetPeriod als Unix-Zeitstempel (integer)
     * left – Verfügbarer Betrag im Budget (float)
     * locked – Der maximale Betrag, der von diesem Budget abgebucht wird, wenn alle
     * Aufträge fertig sind (float)
     * max – Der definierte Maximalbetrag für dieses Budget (float)
     * name – Der im Kunden-Interface eingegebene Name (nur bei getName)
     *
     * Ist ein Budget ohne Limits, wird start und stop gleich 0 sein, 'left' ist kleiner
     * oder gleich 0 und 'max' gleich 0
     *
     * @throws SoapFault
     * @return array
     */
    public function getActualPeriodData() {

        return $this->getClient()->getActualPeriodData($this->salt, $this->hash, $this->budgetKey);
    }
}
?>