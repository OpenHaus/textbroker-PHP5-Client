<?php
require_once(dirname(__FILE__) . '/TextbrokerDAO.php');

/**
 * BudgetCheckService
 *
 * Budgetinformationen abfragen
 */
class TextbrokerBudgetCheckDAO extends TextbrokerDAO {

    function __construct($budgetKey = null, $budgetId = null) {

        parent::__construct($budgetKey, $budgetId);
        $this->setOptions(array(
            'location'      => 'https://api.textbroker.de/Budget/budgetCheckService.php',
            'uri'           => self::BUDGET_URI,
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

        return $this->getClient()->getName($this->salt, $this->hash, $this->budgetKey);
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