<?php

/**
 * Reports conroller
 * @version $Id: $
 */
class Storage_ReportController extends OSDN_Controller_Action
{
    protected $_reports;

	public function init()
	{
		$this->_reports = new PMS_Storage_Reports();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->setLayout('report');
	}

    public function permission(OSDN_Controller_Action_Helper_Acl $acl)
    {
        $acl->setResource(OSDN_Acl_Resource_Generator::getInstance()->storage);
        $acl->isAllowed(OSDN_Acl_Privilege::UPDATE, 'index');
    }

    public function indexAction()
    {
    	$response = $this->_reports->generateReport($this->_getAllParams());
    	if ($response->isSuccess()) {
	    	$this->view->data = $response->data;
	        $this->view->content = $this->view->render('report/index.phtml');
    	} else {
    		$this->_collectErrors($response);
    	}
    }
}