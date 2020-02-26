<?php  
/**
 * Bithumb File_exception
 *
 *
 * @package      bithumbApp 
 * @subpackage   libraries/core
 * @author       bithumb.developers <contact@bithumb.com>
 * @version      v.0.1
 * @copyright    Copyright (c) 2017.04.14 
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class File_exception extends Exception
{
	public $message	= NULL;
	public $error	= NULL;

	public function __construct($error=0, $message='')
	{
 		$this->error		= $error;
		$this->message	= $message;
	}


	public function __toString()
	{
		return json_encode(array('error'=>$this->error, 'message'=>$this->message));
	}
}



/** End of file File_exception.php */
/** Location: /application/libraries/core/File_exception.php */
