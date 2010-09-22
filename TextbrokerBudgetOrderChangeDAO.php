<?php
require_once(dirname(__FILE__) . '/TextbrokerDAO.php');

/**
 * BudgetOrderChangeService
 *
 * Verändern von Aufträgen
 *
 * Über https://api.textbroker.de/Budget/budgetOrderChangeService.php werden Vorgaben der Aufträge (BudgetOrders) verändert.
 * Dazu zählen: das Ändern der minimalen und maximalen Wortanzahl sowie der maximalen Bearbeitungszeit.
 * Besonderheit: Wirksam ist dieser Service nur bei Aufträgen, die nicht fertiggestellt
 * bzw. gelöscht sind (also READY, ACCEPTED, DELIVERED, DELETED, REJECTION_GRANTED und ORDER_REFUSED). Erlaubt ist das Ändern
 * der Vorgaben auch während der Bearbeitung durch den Autor oder nach dem Anfordern einer verbesserten Version, jedoch nur zu Gunsten des Autors.
 */
class TextbrokerBudgetOrderChangeDAO extends TextbrokerDAO {

    /**
     *
     *
     * @param string $budgetKey
     * @param string $budgetId
     */
    function __construct($budgetKey = null, $budgetId = null) {

        parent::__construct($budgetKey, $budgetId);
        $this->setOptions(array(
            'location'      => 'https://api.textbroker.de/Budget/budgetOrderChangeService.php',
            'uri'           => self::BUDGET_URI,
        ));
    }

    /**
     * Verändert die zulässige Bearbeitungsdauer
     *
     * Rückgabe-Array:
     * „order_id_changed“ - im Erfolgsfall die ID des geänderten Auftrags
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @param int $workTime -  Dauer in Tagen. >=1 und <=10
     * @return array
     */
    public function changeWorkTime($budgetOrderId, $workTime) {

        return $this->getClient()->changeWorkTime($this->salt, $this->hash, $this->budgetKey, $budgetOrderId, $workTime);
    }

    /**
     * Verändert die erwartete Länge des Textes: minimal- und maximal-Wert
     *
     * Rückgabe-Array:
     * „order_id_changed“ - im Erfolgsfall die ID des geänderten Auftrags
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @param int $minLength – die kleinste zulässige Länge des Textes
     * @param int $maxLength – die größte erwartete Länge des Textes
     * @return array
     */
    public function changeWordsCount($budgetOrderId, $minLength, $maxLength) {

        return $this->getClient()->changeWordsCount($this->salt, $this->hash, $this->budgetKey, $budgetOrderId, $minLength, $maxLength);
    }
}
?>