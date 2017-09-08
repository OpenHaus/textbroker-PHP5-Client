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
// | TextbrokerBudgetOrderChange.php                                           |
// +---------------------------------------------------------------------------+
// | Authors: Fabio Bacigalupo <info1@open-haus.de>                            |
// +---------------------------------------------------------------------------+

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

    /**
     * Setzt die gewünschten Schlüsselworte mit oder ohne minimal- und maximal-Wert für deren Häufigkeit im Text
     *
     * Rückgabe-Array:
     * order_id_changed - im Erfolgsfall die ID des geänderten Auftrags
     * warning [optional bei unzulässigen Zeichen in Keywords] – Eine Beschreibung, welche Keywords verändert worden sind und wie sie bei uns abgespeichert werden. Beispiel: ››This keyword(s) “FF/Z5, Oh!, 66€, $_[key]” was changed to “FF Z5, Oh, 66, key”‹‹ (String)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @param int $orderId
     * @param string $keywords – (UTF-8-kodierte) Liste der zu verwendeten Begriffe, separiert mit Komma oder Semikolon (Leerzeichen beim Separator sind erlaubt). Es gelten die gleichen Bedingungen wie im Web-Interface: Zulässig sind nur Buchstaben (aus gleichen Bedingungen wie im Web-Interface: Zulässig sind nur Buchstaben (aus UTF-8-Bereich) und Zahlen, Leerzeichen, „&“, „„“ (Apostroph), „-“ (Minus), „%“ und „.“ (Punkt). Sollten nicht zulässige Zeichen verwendet worden sein, werden diese durch Leerzeichen ersetzt und eine Warnung wird zurückgegeben. (String)
     * @param int $min – [optional, wenn eine Keyworddichte erwartet wird] die kleinste geforderte Häufigkeit jedes der Keywords im Text (Natürliche Zahl).
     * @param int $max – [optional, wenn eine Keyworddichte erwartet wird] die höchste geforderte Häufigkeit jedes der Keywords im Text (Natürliche Zahl).
     * @return array
     */
    public function setSEO($orderId, $keys, $min = null, $max = null, $useInflections = 0, $useStopwords = 0) {

        $result = $this->getClient()->setSEO($this->salt, $this->hash, $this->budgetKey, $orderId, $keys, $min, $max, $useInflections, $useStopwords);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderChangeException($result['error']);
        }

        return $result;
    }
}
