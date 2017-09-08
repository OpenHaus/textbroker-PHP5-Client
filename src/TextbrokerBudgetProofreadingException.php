<?php

namespace Softonic\Textbroker;

class TextbrokerBudgetProofreadingException extends TextbrokerException {

    public function __construct($message, $code = 0) {

        parent::__construct($message, $code);
    }
}
