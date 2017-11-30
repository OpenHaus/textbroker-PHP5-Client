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
// | TextbrokerBudgetOrder.php                                                 |
// +---------------------------------------------------------------------------+
// | Authors: Fabio Bacigalupo <info1@open-haus.de>                            |
// +---------------------------------------------------------------------------+

/**
 * BudgetOrderService
 *
 * Erstellen und Abholen von Aufträgen
 *
 * @package textbroker-PHP5-Client
 * @author Fabio Bacigalupo <info1@open-haus.de>
 */
class TextbrokerBudgetOrder extends Textbroker {

    /**
     * Singleton
     *
     * @return object
     */
    public static function &singleton($budgetKey = null, $budgetId = null, $password = null, $location = self::BUDGET_LOCATION_DEFAULT) {

        static $instance;

        if (!isset($instance[$budgetKey])) {
            $class                  = __CLASS__;
            $instance[$budgetKey]   = new $class($budgetKey, $budgetId, $password, $location);
        }

        return $instance[$budgetKey];
    }

    /**
     *
     *
     * @param string $budgetKey
     * @param string $budgetId
     * @param string $password
     * @param string $location
     */
    function __construct($budgetKey = null, $budgetId = null, $password = null, $location = 'us') {

        parent::__construct($budgetKey, $budgetId, $password, $location);
        $this->setOptions(array(
            'location'      => $this->getUri() . 'budgetOrderService.php',
            'uri'           => $this->getUri(),
        ));
    }

    /**
     * Liefert einen assoziativen Array von Kategorien mit den zugehörigen Kategorien-ID's
     *
     * Rückgabe-Array:
     * "categories" - assoziativer Array von Kategorien z.B. "Astrologie"=>110, "Telekommunikation"=>133, "Finanzen"=>115
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @return array
     */
    public function getCategories() {

        $result  = $this->getClient()->getCategories();

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result['categories'];
    }

    /**
     * Generiert eine OpenOrder.
     *
     * Die Nutzung ist nur dann möglich, wenn ein Budget nicht deaktiviert worden ist.
     *
     * @param int $categoryId Kategorie-ID – kann mit Hilfe von "getCategories()" herausgefunden werden.
     * @param string $title Titel des Auftrags, den die Autoren sehen werden.
     * @param string $description Auftragsbeschreibung – Genauere Beschreibung des Auftrags (Details, die vom Autor erwartet werden).
     * @param int $minLength Minimale Wortanzahl.
     * @param int $maxLength Maximale Wortanzahl.
     * @param int $rating Einstufung – Qualitätsstufe zwischen 2 und 5 (inklusive).
     * @param int $dueDays Bearbeitungszeit [optional] – eine Zahl zwischen 1 und 10 (Tage), wird es nicht angegeben, wird 1 Tag als gewünschter Wert angenommen.
     * @return int budget_order_id – ID der Order (integer)
     * @throws TextbrokerBudgetOrderException
     */
    public function create($categoryId, $title, $description, $minLength = 350, $maxLength = 400, $rating = 4, $dueDays = 1) {

        $result = $this->getClient()->create($this->salt, $this->hash, $this->budgetKey, $categoryId, $title, $description, $minLength, $maxLength, $rating, $dueDays);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result['budget_order_id'];
    }

    /**
     * Den Bearbeitungsstatus des Auftrags abfragen
     *
     * Rückgabe-Array:
     * budget_order_status – Kurze Beschreibung des Bearbeitungsstandes (String)
     * budget_order_status_id – Zahl, die den Status beschreibt (vgl. BudgetOrder-Eigenschaften) (integer)
     * budget_order_id – ID der BudgetOrder, die abgefragt wurde (integer)
     * budget_order_created – Erstellungszeitpunkt des Auftrages als Unix Zeitstempel (integer)
     * sandbox [optional] – wenn die Order im Textmodus betrieben wird = 1 (integer)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @return array
     */
    public function getStatus($budgetOrderId) {

        $result = $this->getClient()->getStatus($this->salt, $this->hash, $this->budgetKey, $budgetOrderId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Auflisten von Aufträgen, die den angegebenen Zustand haben
     *
     * Rückgabe-Array:
     * ein Array aller Budget-Order-IDs, die den Status erreicht haben. (Kann leer sein)
     *
     * @param int $status – numerischer Wert, der den abzufragenden Status identifiziert wie ACCEPTED = 5
     * @return array
     * @throws TextbrokerBudgetOrderException
     */
    public function getOrdersByStatus($status) {

        $result = $this->getClient()->getOrdersByStatus($this->salt, $this->hash, $this->budgetKey, $status);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Das Abholen der fertigen und akzeptierten Aufträge.
     *
     * Besonderheit: Um einen Auftrag abholen zu können, muss er über API oder im Textbroker-Kundenbereich zunächst akzeptiert werden (BudgetOrder-Status =ACCPETED).
     * Bei Sandbox-Orders muss stattdessen die Funktion "accept" angewendet werden.
     * Automatisiertes Akzeptieren der Orders als Antwort auf unser "Callback" (mit staus=READY) ist grundsätzlich zulässig, wird aber nicht empfohlen.
     * Besonderheit beim Sandbox-Modus: Als Text wird ein "Lorem ipsum..."-Text geliefert, der die erwartete Maximalzahl an Wörtern enthält.
     *
     * Rückgabe-Array:
     * your_title – Der vom Kunden vergebene Titel des Auftrags (String)
     * your_description – Die vom Kunden vergebene Auftragsbeschreibung (String)
     * your_note – Die vom Kunden vergebene Notiz (String)
     * min_words – Die vom Kunden erwartete Mindestanzahl an Wörtern (Integer)
     * max_words - Die vom Kunden erwartete maximale Anzahl an Wörtern (Integer)
     * count_words – Die Anzahl der Wörter im Text (Integer)
     * title – Der vom Autor vergebene Titel (String)
     * text – Der Textinhalt (String)
     * already_delivered – Ist gleich 0, wenn es zum ersten mal abgeholt wird, gleich 1, wenn pickUp schon einmal für diese Order aufgerufen wurde. Es ist ein Hinweis, dass dieser Text möglicherweise schon vom Kunden benutzt wird. (Integer)
     * sandbox [optional] – wenn die Order im Textmodus betrieben wird = 1 (Integer)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     * budget_order_status [optional bei Fehlern] – Statusbeschreibung (String)
     * budget_order_status_id [optional bei Fehlern] – Status-ID (Integer)
     * budget_order_id [optional bei Fehlern] – BudgetOrder-ID, die angefragt wurde (Integer)
     * budget_order_created [optional bei Fehlern] – Erstellungszeit als Unix-Zeitstempel (Integer)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @return array
     */
    public function pickUp($budgetOrderId) {

        $result = $this->getClient()->pickUp($this->salt, $this->hash, $this->budgetKey, $budgetOrderId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Löschen von eingestellten Aufträgen vor ihrer Bearbeitung durch Autoren
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @return int order_id_deleted – ID der gelöschten BudgetOrder (Integer)
     */
    public function delete($budgetOrderId) {

        $result = $this->getClient()->delete($this->salt, $this->hash, $this->budgetKey, $budgetOrderId);;

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result['order_id_deleted'];
    }

    /**
     * Vorschau des zu akzeptierenden Textes
     *
     * Rückgabe-Array:
     * your_title – der vom Kunden vergebene Titel des Auftrags (String)
     * count_words – Anzahl geschriebener Wörter (Integer)
     * author – Autoren-Nickname (String)
     * title – der vom Autor vergebene Titel (String)
     * text – der Text (String)
     * classification – die erwartete Qualitätsstufe (Integer)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     * budget_order_status [optional bei Fehlern] – Statusbeschreibung (String)
     * budget_order_status_id [optional bei Fehlern] – Status-ID (Integer)
     * budget_order_id [optional bei Fehlern] – BudgetOrder-ID, die angefragt wurde (Integer)
     * budget_order_created [optional bei Fehlern] – Erstellungszeit als Unix-Zeitstempel (Integer)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @return array
     */
    public function preview($budgetOrderId) {

        $result = $this->getClient()->preview($this->salt, $this->hash, $this->budgetKey, $budgetOrderId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Automatischer Abgleich des bearbeiteten Texts mit Datenbank zum Ausschluss von Plagiaten mit CopyScape
     *
     * Rückgabe-Array:
     * answer ['response_code'] - int - 0 = not available, 1 = no match has been found, 2 = matches have been found
     * answer ['response_text'] - string - not available, no match has been found, matches have been found
     * answer ['checked'] - Zeitstempel - Zzeitpunkt der Prüfung
     * answer ['results_count'] - int - Anzahl der Ergebnisse
     * answer ['copyscape_results'] - empty Array oder assoziatives Array
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @param mixed $sandboxResponseCode (Optional - Nur in Sandbox-Mode verfügbar) int 0-2 : Es können alle 3 Fallbeispiele simuliert werden.
     * @return array
     */
    public function getCopyscapeResults($budgetOrderId, $sandboxResponseCode = null) {

        $result = $this->getClient()->getCopyscapeResults($this->salt, $this->hash, $this->budgetKey, $budgetOrderId, $sandboxResponseCode);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Akzeptieren von fertiggestellten Orders
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
     * budget_order_status [optional bei Fehlern] – Statusbeschreibung (String)
     * budget_order_status_id [optional bei Fehlern] – Status-ID (Integer)
     * budget_order_id [optional bei Fehlern] – BudgetOrder-ID, die angefragt wurde (Integer)
     * budget_order_created [optional bei Fehlern] – Erstellungszeit als Unix-Zeitstempel (Integer)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @param int $rating Bewertung – ein Integer zwischen 0 und 4, um zu akzeptieren.
     * @param string $comment - Nachricht an den Autor – Ein Text mit Begründung der abgegebenen Bewertung (optional)
     * @return array
     */
    public function accept($budgetOrderId, $rating = 0, $comment = null) {

        $result = $this->getClient()->accept($this->salt, $this->hash, $this->budgetKey, $budgetOrderId, $rating, $comment);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Anfordern einer Überarbeitung
     *
     * Rückgabe-Array:
     * result – "OK" wenn problemlos abgelaufen (String "OK")
     * order_id_revised – ID der BudgetOrder, die zur Überarbeitung an den Autor geschickt wurde
     * sandbox [optional] – wenn die Order im Textmodus betrieben wird = 1, sonst 0 (Integer)
     * error [optional bei Fehlern] – die Fehlerbeschreibung (String)
     * budget_order_status [optional bei Fehlern] – Statusbeschreibung (String)
     * budget_order_status_id [optional bei Fehlern] – Status-ID (Integer)
     * budget_order_id [optional bei Fehlern] – BudgetOrder-ID, die angefragt wurde (Integer)
     * budget_order_created [optional bei Fehlern] – Erstellungszeit als Unix-Zeitstempel (Integer)
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @param string $comment - Nachricht an den Autor – Nachricht an den Autor – Sie darf nicht leer sein, wenn ein Auftrag abgelehnt wird, damit der Autor weiß, was geändert werden soll. Sollte deshalb mehr als 50 Zeichen beinhalten.
     * @return array
     */
    public function revise($budgetOrderId, $comment) {

        $result = $this->getClient()->revise($this->salt, $this->hash, $this->budgetKey, $budgetOrderId, $comment);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Ablehnen von fertiggestellten Orders
     *
     * Ablehnungen sind nur über diese Funktion möglich. Nur möglich, wenn Auftrag bereits einmal zur Überarbeitung an den Autor geschickt wurde.
     *
     * @param int $budgetOrderId BudgetOrder-ID – die ID der Order, die abgefragt werden soll. Diese wurde beim Aufruf von "create" im Element "budget_order_id" zurückgegeben.
     * @return int order_id_rejected – ID der abgelehnten BudgetOrder (Integer)
     */
    public function reject($budgetOrderId) {

        $result = $this->getClient()->reject($this->salt, $this->hash, $this->budgetKey, $budgetOrderId);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result['order_id_rejected'];
    }

    /**
     *
     * Rückgabe-Array:
     * $answer['classification'] - Übermittelte Einstufung
     * $answer['word_count'] - Übermittelte Anzahl der Wörter
     * $answer['cost_per_word'] - Kosten pro Wort in der jeweiligen Einstufung
     * $answer['cost_order'] - Kosten für die Order
     * $answer['cost_order_fee'] - Bearbeitungsdgebuer
     * $answer['cost_total'] - Gesamtkosten für den Auftrag
     * $answer['currency'] - Jeweilige Währung
     *
     * @param int $wordCount
     * @param int $classification
     * @return array
     */
    public function getCosts($wordCount, $classification) {

        $result = $this->getClient()->getCosts($this->salt, $this->hash, $this->budgetKey, $wordCount, $classification);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Auswahl von zuvor eingestellten Expertenteams
     *
     * Bevor eine Abfrage möglich ist, muss im Textbroker Kundenaccount mindestens ein Team angelegt sein.
     *
     * Zurückgegeben wird ein Zweidimensionales-Array:
     * team_id - die Team ID (Integer)
     * category_id - die Kategorie ID (Integer)
     * team_name - der Name des Teams (String)
     * team_description - die Beschreibung zu dem Team (String)
     * team_price_per_word - der angegebene Preis pro Wort (Float)
     * team_order_fee - die Textbroker Bearbeitungsgebühren
     * team_bonus_per_article [optional] = Bonus pro Artikel (Float)
     * team_public – die Angabe, ob das Team öffentlich ist oder nicht: 1 = öffentlich; 0 = nicht öffentlich, Einladung erforderlich
     * team_status – der Teamstatus: 1 = aktiv; 0 = inaktiv (Integer)
     * time_created – Erstellungsdatum des Teams: Unix Zeitstempel (Integer)
     *
     * @return array
     */
    public function getTeams() {

        $result = $this->getClient()->getTeams($this->salt, $this->hash, $this->budgetKey);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Abfrage der Kosten für eine TeamOrder
     *
     * Bevor eine Abfrage möglich ist, muss im Textbroker Kundenaccount mindestens ein Team angelegt sein.
     *
     * Rückgabe:
     * classification = 'TeamOrder' (String)
     * team_id - die Team ID (Integer)
     * word_count - die Anzahl der übermittelten Worte (Integer)
     * cost_per_word - die Kosten für den Auftrag ohne Bearbeitungsgebühr (Float)
     * cost_order - die Kosten pro Wort (Float)
     * cost_order_fee - die TB Bearbeitungsgebühr (Float)
     * cost_total - die Gesamtkosten für den Auftrag inkl. Bearbeitungsgebühr (Float)
     * currency - die Währung (String)
     *
     * @param int $teamId die Team ID (Integer)
     * @param int $wordCount
     * @return array
     */
    public function getCostsTeamOrder($teamId, $wordCount) {

        $result = $this->getClient()->getCostsTeamOrder($this->salt, $this->hash, $this->budgetKey, $teamId, $wordCount);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result;
    }

    /**
     * Erstellt eine TeamOrder
     *
     * Rückgabe:
     * budget_order_id - (Integer) die Order ID der TeamOrder, wenn erfolgreich ansonsten
     * error - ein Fehler mit Fehlermeldung (String)
     *
     * @param int $teamId - die Team ID (Integer)
     * @param string $orderTitle - Titel des Auftrags, den die Autoren sehen werden.
     * @param string $orderDescription - Genauere Beschreibung des Auftrags
     * @param int $wordsMin - Minimale Wortanzahl
     * @param int $wordsMax - Maximale Wortanzahl
     * @param int $workingTime -  eine Zahl zwischen 1 und 10 (Tagen)
     * @param string $note [optional] - wenn Sie eine Notiz für sich selbst hinterlassen wollen, die Sie beim pickUp angezeigt bekommen, müssen Sie als weitere zwei Parameter zunächst eine 0 und dann die Notiz übergeben.
     * @return array
     */
    public function createTeamOrder($teamId, $orderTitle, $orderDescription, $wordsMin, $wordsMax, $workingTime, $note = null) {

        $result = $this->getClient()->createTeamOrder($this->salt, $this->hash, $this->budgetKey, $teamId, $orderTitle, $orderDescription, $wordsMin, $wordsMax, $workingTime, $note);

        if (isset($result['error']) && !empty($result['error'])) {
        	throw new TextbrokerBudgetOrderException($result['error']);
        }

        return $result['budget_order_id'];
    }
}
