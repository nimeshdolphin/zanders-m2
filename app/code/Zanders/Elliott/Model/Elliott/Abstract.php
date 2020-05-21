<?php
class Zanders_Elliott_Model_Elliott_Abstract extends Zend_Soap_Client
{
	protected $_elliotServer = '66.186.97.204';

	protected $_production = true;

	protected $_oldfile = false;

	protected $_elliotService = '/ElliottQuery.asmx?wsdl';

	protected $_EWS_User = "Supervisor";
	protected $_EWS_passwd = "Zanders801";

	public function __construct(){
		parent::__construct('http://'.$this->_elliotServer.'/Elliott85WS'.$this->_elliotService);
	}

	public function setElliottServer($server){
		if($this->_production){
			$this->setWsdl('http://'.$server.'/Elliott85WS'.$this->_elliotService);
		}else{
			$this->setWsdl('http://'.$server.'/Elliott85WS96'.$this->_elliotService);
		}
		return $this;
	}

	public function setProduction($value){
		if($value){
			$this->_production = true;
		}else{
			$this->_production = false;
		}
		if($this->_production){
			$this->setWsdl('http://'.$this->_elliotServer.'/Elliott85WS'.$this->_elliotService);
		}else{
			$this->setWsdl('http://'.$this->_elliotServer.'/Elliott85WS96'.$this->_elliotService);
		}
	}

	public function setOldFile($value){
		if($value){
			$this->_oldfile = $value;
		}else{
			$this->_oldfile = false;
		}
		if($this->_oldfile!==false){
		    if(is_bool($this->_oldfile) || $this->_oldfile=='03'){
			     $this->setWsdl('http://'.$this->_elliotServer.'/ElliottWS03'.$this->_elliotService);
		    }else if($this->_oldfile=='04'){
		        $this->setWsdl('http://'.$this->_elliotServer.'/ElliottWS04'.$this->_elliotService);
		    }
		}else{
			$this->setWsdl('http://'.$this->_elliotServer.'/Elliott85WS'.$this->_elliotService);
		}
	}
}
