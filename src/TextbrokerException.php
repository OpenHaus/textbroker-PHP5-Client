<?php

namespace Softonic\Textbroker;

/**
 * Custom exception
 *
 */
class TextbrokerException extends \Exception {

    public function __construct($message, $code = 0) {

        parent::__construct($message, $code);
    }
}
