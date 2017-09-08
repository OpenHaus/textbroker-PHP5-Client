<?php
namespace Softonic\Textbroker;

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
// | TextbrokerBudgetProofreading.php                                          |
// +---------------------------------------------------------------------------+
// | Authors: Fabio Bacigalupo <info1@open-haus.de>                            |
// +---------------------------------------------------------------------------+

/**
 * BudgetProofreadingService
 * Erstellung, Bearbeitung und Abnahme von Korrekturaufträgen
 *
 * Korrekturaufträge (im Folgenden BudgetProofreading genannt) können über jedes Budget erstellt werden, das aktiv und nicht im Test-Modus ist.
 * BudgetProofreadings haben eine feste Bearbeitungszeit; die Kosten berechnen sich aus der Länge des zu prüfenden Textes,
 * sodass keine nachträglichen Änderungen am Korrekturauftrag möglich oder notwendig sind.
 * Einen Test-Modus gibt es für BudgetProofreadings nicht. BudgetProofreadings besitzen die gleichen Zustände wie andere BudgetOrders auch.
 * Dieser Service ist nur für bereits angenommene Aufträge möglich.
 *
 * @package textbroker-PHP5-Client
 * @author Fabio Bacigalupo <info1@open-haus.de>
 */
class TextbrokerBudgetProofreading extends Textbroker {

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
     * @param string $budgetId
     */
    function __construct($budgetKey = null, $budgetId = null, $password = null, $location = 'us') {

        parent::__construct($budgetKey, $budgetId, $password, $location);
        $this->setOptions(array(
            'location'      => $this->getUri() . 'budgetProofreadingService.php',
            'uri'           => $this->getUri(),
        ));
    }

    /**
     * Rückgabe-Array:
     * 'proofreading_id_created' – ID des erstellten BudgetProofreadings (int) ansonsten
     * 'error' – Beschreibung des Fehlers, der die Erstellung verhindert hat (String)
     *
     * @param int $orderId - Die orderID bezeichnet in dieser Funktion die ID einer Budget-Order (OpenOrder), die korrigiert werden soll.
     * @param string $instructions – Anweisung an den Lektor (String)
     * @param string $text [optional] - der angepasste Text, wenn nicht mit dem Original gleich (String)
     * @param string $title [optional] - (String) angepasster Titel, wenn nicht mit dem Original gleich
     * @return array
     * @throws TextbrokerBudgetProofreadingException
     */
    public function create($orderId, $instructions = null, $text = null, $title = null) {

        $result = $this->getClient()->create($this->salt, $this->hash, $this->budgetKey, $orderId, $instructions, $text, $title);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result['proofreading_id_created'];
    }

    /**
     * Liefert einen Array mit den wichtigsten Informationen
     *
     * Rückgabe-Array:
     * word_count - die übermittelte Anzahl der Wörter
     * cost_per_word - die Kosten pro Wort in der jeweiligen Einstufung
     * cost_total - die Gesamtkosten für den Auftrag
     * currency - die jeweilige Währung
     *
     * @param string $text Erwartet wird: der Text der zum Korrektorat gesendet werden soll
     * @return array
     * @throws TextbrokerBudgetProofreadingException
     */
    public function getCosts($text) {

        $result = $this->getClient()->getCosts($this->salt, $this->hash, $this->budgetKey, $text);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     * Verhält sich prinzipiell wie bei BudgetOrder auch. Der Unterschied besteht darin, dass der Text im Array-Element 'text' (aber nicht der Titel) die Unterschiede
     * zwischen der ursprünglichen und der korrigierten Fassung aufzeigt. Es ist als HTML formatiert und sollte dem Benutzer angezeigt werden.
     *
     * @param int $orderId
     * @return array
     * @throws TextbrokerBudgetProofreadingException
     */
    public function preview($orderId) {

        $result = $this->getClient()->preview($this->salt, $this->hash, $this->budgetKey, $text);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     * Akzeptieren von fertiggestellten Proofreadings
     *
     * Bewertung – ein Integer zwischen 0 und 4, um zu akzeptieren.
     * 0 = keine Bewertung abgeben, aber den Artikel annehmen.
     * 1 = super
     * 2 = gut
     * 3 = geht so
     * 4 = nicht so besonders
     *
     * Rückgabe-Array:
     * result – "OK" wenn problemlos abgelaufen (String "OK")
     * sandbox [optional] – wenn die Order im Textmodus betrieben wird = 1, sonst 0 (Integer)
     * title [optional, falls OK] - der vom Autor vergebene Titel (String)
     * text [optional, falls OK] - der Text (String)
     * author – Autoren-Nickname (String)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @param int $proofreadingId Proofreading-ID
     * @param int $rating Bewertung – ein Integer zwischen 0 und 4, um zu akzeptieren.
     * @param string $comment - Nachricht an den Autor – Ein Text mit Begründung der abgegebenen Bewertung (optional)
     * @return array
     */
    public function accept($proofreadingId, $rating = 0, $comment = null) {

        $result = $this->getClient()->accept($this->salt, $this->hash, $this->budgetKey, $proofreadingId, $rating, $comment);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     * Den Bearbeitungsstatus des Auftrages abfragen
     *
     * Rückgabe-Array:
     * budget_order_status – Kurze Beschreibung des Bearbeitungsstandes (String)
     * budget_order_status_id – Zahl, die den Status beschreibt (vgl. BudgetOrder-Eigenschaften) (integer)
     * budget_order_id – ID der BudgetOrder, die abgefragt wurde (integer)
     * budget_order_created – Erstellungszeitpunkt des Auftrages als Unix Zeitstempel (integer)
     * sandbox [optional] – wenn die Order im Textmodus betrieben wird = 1 (integer)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @param int $proofreadingId Proofreading-ID
     * @return array
     */
    public function getStatus($proofreadingId) {

        $result = $this->getClient()->getStatus($this->salt, $this->hash, $this->budgetKey, $proofreadingId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     * Auflisten von Aufträgen, die den angegebenen Zustand haben
     *
     * Rückgabe-Array:
     * ein Array aller Proofreading-IDs, die den Status erreicht haben. (Kann leer sein)
     *
     * @param int $status – numerischer Wert, der den abzufragenden Status identifiziert wie ACCEPTED = 5
     * @return array
     * @throws TextbrokerBudgetProofreadingException
     */
    public function getOrdersByStatus($status) {

        $result = $this->getClient()->getOrdersByStatus($this->salt, $this->hash, $this->budgetKey, $status);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     * Löschen von eingestellten Aufträgen vor ihrer Bearbeitung durch Autoren
     *
     * @param int $proofreadingId Proofreading-ID – die ID, die abgefragt werden soll.
     * @return int proofreading_id_deleted – ID der gelöschten BudgetOrder (Integer)
     * @throws TextbrokerBudgetProofreadingException
     */
    public function delete($proofreadingId) {

        $result = $this->getClient()->delete($this->salt, $this->hash, $this->budgetKey, $budgetOrderId);;

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result['proofreading_id_deleted'];
    }

    /**
     *
     *
     * @param int $proofreadingId Proofreading-ID – die ID, die abgefragt werden soll.
     * @return array
     * @throws TextbrokerBudgetProofreadingException
     */
    public function pickUp($proofreadingId) {

        $result = $this->getClient()->pickUp($this->salt, $this->hash, $this->budgetKey, $proofreadingId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     *
     *
     * @param int $proofreadingId Proofreading-ID – die ID, die abgefragt werden soll.
     * @param string $comment
     * @return array
     * @throws TextbrokerBudgetProofreadingException
     */
    public function revise($proofreadingId, $comment) {

        $result = $this->getClient()->revise($this->salt, $this->hash, $this->budgetKey, $proofreadingId, $comment);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result;
    }

    /**
     *
     *
     * @param int $proofreadingId
     * @return int
     */
    public function reject($proofreadingId) {

        $result = $this->getClient()->reject($this->salt, $this->hash, $this->budgetKey, $proofreadingId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetProofreadingException($result['error']);
        }

        return $result['proofreading_id_rejected'];
    }
}
