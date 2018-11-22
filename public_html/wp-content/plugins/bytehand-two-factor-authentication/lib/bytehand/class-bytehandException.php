<?php
/**
 * bytehand PHP API
 *
 * @package     bytehand
 * @copyright   bytehand Ltd 2012
 * @license     ISC
 * @link        http://www.bytehand.com
 */ 

/*
 * bytehandException
 *
 * The bytehand wrapper class will throw these if a general error
 * occurs with your request, for example, an invalid API key.
 *
 * @package     bytehand
 * @subpackage  Exception
 * @since       1.0
 */
class bytehandException extends Exception {

    public function __construct( $message, $code = 0 ) {
        // make sure everything is assigned properly
        parent::__construct( $message, $code );
    }
}
