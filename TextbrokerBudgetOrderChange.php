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
// | TextbrokerBudgetOrderChangeDAO.php                                        |
// +---------------------------------------------------------------------------+
// | Authors: Fabio Bacigalupo <info1@open-haus.de>                            |
// +---------------------------------------------------------------------------+

require_once(dirname(__FILE__) . '/Textbroker.php');

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
 *
 * @package textbroker-PHP5-Client
 * @author Fabio Bacigalupo <info1@open-haus.de>
 */
class TextbrokerBudgetOrderChange extends Textbroker {

    /**
     *
     *
     * @param string $budgetKey
     * @param string $budgetId
     */
    function __construct($budgetKey = null, $budgetId = null, $password = null, $location = 'us') {

        parent::__construct($budgetKey, $budgetId, $password, $location);
        $this->setOptions(array(
            'location'      => $this->getUri() . 'budgetOrderChangeService.php',
            'uri'           => $this->getUri(),
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