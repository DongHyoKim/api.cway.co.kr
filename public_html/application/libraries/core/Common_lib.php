<?php
/**
 * Bithumb Common library
 *
 * Bithumb 공통사용 메소드 라이브러리 
 *
 * @package      bithumbApp 
 * @subpackage   libraries/core
 * @author       bithumb.developers <contact@bithumb.com>
 * @version      v.0.1
 * @copyright    Copyright (c) 2017.04.14 
 */
defined('BASEPATH') OR exit('No direct script access allowed');
class Common_lib {
	
	/** @var CI object */
	private $CI;
	
	/** @var MASTER DB object */
	private $masterDb;

	/** @var SLAVE DB object */
	private $slaveDb;

	/** @var sCurrency string */
	private $sCurrency = null;
	
    /** 
	 * __construct 
	 * @param Object $CI
	 * @param String $sCurrency(coinType) , default = ETH
	 */
	public function __construct() 
	{
		$this->CI =& get_instance();
		
	}

    /**
     * loadMasterDb
	 *
     * @access public
     * @description load master db
	 */
	public function loadMasterDb()
	{	
		if (! isset($this->CI->masterDb) OR ! is_object($this->CI->masterDb))
		{
			$this->CI->masterDb = $this->CI->load->database('master', TRUE);
			$this->CI->masterDb->conn_id->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

    /**
     * loadSlaveDb
	 *
     * @access public
     * @description load slave db
	 */
	public function loadSlaveDb()
	{
		if (! isset($this->CI->slaveDb) OR ! is_object($this->CI->slaveDb))
		{
			$this->CI->slaveDb = $this->CI->load->database('slave', TRUE);
			$this->CI->masterDb->conn_id->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}

    /**
     * pdoExceptionHandler
	 *
     * @access public
	 * @param Object $ex
     * @description pdo exception handler
	 */
	public function pdoExceptionHandler($ex)
    {
        $traces = $ex->getTrace();
        log_message('error', $ex);
        if (isset($traces[3])) 
		{
            log_message('error', serialize($traces[3])."\r\n");
        }
    }
	
	/**
     * isDbError
	 *
     * @access public
	 * @param Object $db
	 * @return Boolean $bIsDbError
     * @description models db error 2.x , 3,x common
	 */
	public function isDbError(&$db)
    {
        $bIsDbError = false;
        if(substr(CI_VERSION, 0, 1) === '3')
        {
            $errors = $db->error();
            if ($errors['code'] !== '00000') 
			{
                $bIsDbError = true;
            }
        }
        else
        {
            if ($db->_error_number() !== '00000') 
			{
                $bIsDbError = true;
            }
        }

        return $bIsDbError;
    }

	/**
     * bindParams
	 *
     * @access public
     * @param Object $db
	 * @param String $sQuery
	 * @param Array $params
	 * @return String $sQuery
	 * @description models query bind , escape
	 */
	public function bindParams(&$db, $sQuery, $params)
	{
		if (! isset($params)) 
		{
			return $sQuery;
		}
		if (! is_array($params)) 
		{
			$params = array($params);
		}
		foreach ($params as $k=>$v) 
		{
			$sQuery = str_replace($k, $db->escape($v), $sQuery);
		}

		return $sQuery;
	}

	public function getFoundRows()
	{
		$results = array (
			'error'		=> '0000',
			'message'	=> '',
			'data'		=> null
		);
		$this->CI->load->model('core/common_model');
		$iCount = $this->CI->common_model->getFoundRows();
		if ($iCount !== false) 
		{
			$results['data'] = $iCount;
		}
		else
		{
			$results['error'] = '3000';
		}
		return $results;		
	}

	/**
     * getNowDatetime
	 *
     * @access public
	 * @param Int $digit
	 * @return Array $rgResponse
     * @description 날짜 반환
	 */
	public function getNowDatetime($digit=6, $sDbName='masterDb')
	{
        $results = array (
            'error'		=> '0000',
            'message'	=> '',
            'data'		=> null
        );
        $this->CI->load->model('core/common_model');
        $sDateTime = $this->CI->common_model->getNowDatetime($digit,$sDbName);
        if ($sDateTime !== false) 
        {
            $results['data'] = $sDateTime;
        }
        else
        {
            $results['error'] = '6001';
        }
        return $results;	
	}

	/**
   * sendEmail
   *
   * @access public
   * @param Array $param
   * @return Boolean
   * @description 메일 발송
   */
	public function sendEmail($param) 
	{
        /**
		* 기존 common_service/sendEmail2
		*/
		try
		{
			$strContent = "";

			if(isset($param["memName"]) == FALSE) {
				if(strlen($param["memId"]) > 0) {
					$this->CI->load->model('core/common_model');
					$param["memName"] = $this->CI->common_model->getMemIdName($param);
				}
			}

			$strTemplateFile = $param["template"];
			$strContent = $this->CI->load->view($strTemplateFile, $param, true);
			$this->CI->load->library('email');
			$this->CI->email->set_newline("\r\n");
			$this->CI->email->from('contact@bithumb.com', 'BITHUMB');
			$this->CI->email->to($param["to"]);
			$this->CI->email->subject($param["subject"]);
			$this->CI->email->message($strContent);
			$bResult = $this->CI->email->send();

			return $bResult;
		} catch (PDOException $e) {
			log_message('error', $e);
			log_message('error', serialize($e->getTrace()[3])."\r\n");
			return FALSE;
		}
	}
}

/** End of file Common_lib.php */
/** Location: /application/libraries/core/Common_lib.php */
