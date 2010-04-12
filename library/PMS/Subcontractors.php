<?php

class PMS_Subcontractors
{
	protected $_table;
	
    public function __construct()
    {
        $this->_table = new PMS_Subcontractors_Table_Subcontractors();
    }
    
    public function add(array $params)
    {
        $f = new OSDN_Filter_Input(array(
        ), array(
            'name' => array(array('StringLength', 1, 255), 'presence' => 'required')
        ), $params);
        
        $response = new OSDN_Response();
        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }
        
        try {
            $id = $this->_table->insert($f->getData());
            $status = $id ? PMS_Status::OK : PMS_Status::FAILURE;
        } catch (Exception $e) {
            $response->addStatus(new PMS_Status(PMS_Status::DATABASE_ERROR));
            return $response;
        }

        $response->addStatus(new PMS_Status($status));
        $response->id = $id;
        return $response;
    }
    
    public function update(array $params)
    {
        $f = new OSDN_Filter_Input(array(
        ), array(
            'id'          => array('int', 'presence' => 'required'),
            'name'        => array(array('StringLength', 1, 255), 'presence' => 'required')
        ), $params);

        $response = new OSDN_Response();
        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }
        
        try {
            $updatedRows = $this->_table->updateByPk($f->getData(), $f->getEscaped('id'));
	        if ($updatedRows > 0) {
	            $status = PMS_Status::UPDATED;
	        } else if ($updatedRows === 0) {
	            $status = PMS_Status::UPDATED_NO_ONE_ROWS_UPDATED;
	        } else {
	            $status = PMS_Status::FAILURE;
	        }
        } catch (Exception $e) {
            $response->addStatus(new PMS_Status(PMS_Status::DATABASE_ERROR));
            return $response;
        }

        $response->addStatus(new PMS_Status($status));
        return $response;
    }
    
    public function delete($id)
    {
        $response = new OSDN_Response();
        $validate = new OSDN_Validate_Id();
        if (!$validate->isValid($id)) {
            return $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'id'));
        }
        $affectedRows = $this->_table->deleteByPk($id);
        $status = PMS_Status::retrieveAffectedRowStatus($affectedRows);
        $response->addStatus(new PMS_Status($status));
        return $response;
    }
    
    public function get($id)
    {
        $response = new OSDN_Response();
        $validate = new OSDN_Validate_Id();
        if (!$validate->isValid($id)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'id'));
            return $response;
        }
        $select = $this->_table->getAdapter()->select()
            ->from($this->_table->getTableName())
            ->where("id = ? ", $id);
        try {
            $response->setRow($select->query()->fetch());
            $status = PMS_Status::OK;
        } catch (Exception $e) {
            if (OSDN_DEBUG) {
                throw $e;
            }
            $status = PMS_Status::DATABASE_ERROR;
        }
        
        $response->addStatus(new PMS_Status($status));
        return $response;
    }
    
    /**
     * Retrieve orders
     *
     * @param array $params
     * The param examples<code>
     *      sort    => 'name'
     *      dir     => 'ASC'
     *      limit   => 20
     *      start   => 1
     *      ...
     *      filter[0][data][type]   string
     *      filter[0][data][value]  1
     *      filter[0][field]        alias
     * </code>
     * @param array $where      The array of where clauses<code>
     *  array(
     *      array('name = ?' => test),
     *      array('id' => 1)
     *  );</code>
     *
     * @return OSDN_Response
     * Details of contain data 
     * <code>
     *      rows array  the rows collection
     *      total int   the total count of rows
     * </code>
     */
    public function getList(array $params, array $where = array())
    {
        $response = new OSDN_Response();
        $select = $this->_table->getAdapter()->select();
        $select->from(array('s' => $this->_table->getTableName()), '*');
        $plugin = new OSDN_Db_Plugin_Select($this->_table, $select);
        $plugin->parse($params);
        $status = null;
        try {
            $response->setRowset($select->query()->fetchAll());
            $response->totalCount = $plugin->getTotalCount();
            $status = PMS_Status::OK;
        } catch (Exception $e) {
            $status = PMS_Status::DATABASE_ERROR;
            if (OSDN_DEBUG) {
                throw $e;
            }
        }
        return $response->addStatus(new PMS_Status($status));
    }

    public function attach($id, $orderId)
    {
        $response = new OSDN_Response();
        $validate = new OSDN_Validate_Id();
        if (!$validate->isValid($id)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'id'));
            return $response;
        }
        if (!$validate->isValid($orderId)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'orderId'));
            return $response;
        }
        $ordersSubcontractors = new PMS_Orders_Table_OrdersSubcontractors();
        try {
            $ordersSubcontractors->insert(array(
                'subcontractor_id'   => $id, 
                'order_id'      => $orderId, 
                'success'       => 0
            ));
            $status = PMS_Status::OK;
        } catch (Exception $e) {
            $status = PMS_Status::DATABASE_ERROR;
            if (OSDN_DEBUG) {
                throw $e;
            }
        }
        return $response->addStatus(new PMS_Status($status));
    }
    
    public function remove($id, $orderId)
    {
        $response = new OSDN_Response();
        $validate = new OSDN_Validate_Id();
        if (!$validate->isValid($id)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'id'));
            return $response;
        }
        if (!$validate->isValid($orderId)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'orderId'));
            return $response;
        }
        $ordersSubcontractors = new PMS_Orders_Table_OrdersSubcontractors();
        try {
            $ordersSubcontractors->delete(array(
               'subcontractor_id = ? ' => $id, 
               'order_id' => $orderId
            ));
            $status = PMS_Status::OK;
        } catch (Exception $e) {
            $status = PMS_Status::DATABASE_ERROR;
            if (OSDN_DEBUG) {
                throw $e;
            }
        }
        return $response->addStatus(new PMS_Status($status));
    }
    
    public function check($id, $orderId, $success, $date)
    {
        $response = new OSDN_Response();
        $validate = new OSDN_Validate_Id();
        if (!$validate->isValid($id)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'id'));
            return $response;
        }
        if (!$validate->isValid($orderId)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'orderId'));
            return $response;
        }
        if ($success !== 0 && $success !== 1) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'success'));
            return $response;
        }
        $ordersSubcontractors = new PMS_Orders_Table_OrdersSubcontractors();
        try {
            $ordersSubcontractors->update(array('success' => $success, 'date' => $date), array(
               'subcontractor_id = ? ' => $id, 'order_id' => $orderId
            ));
            $status = PMS_Status::OK;
        } catch (Exception $e) {
            $status = PMS_Status::DATABASE_ERROR;
            if (OSDN_DEBUG) {
                throw $e;
            }
        }
        return $response->addStatus(new PMS_Status($status));
    }

    public function getByOrderId($id) {
        $response = new OSDN_Response();
        $validate = new OSDN_Validate_Id();
        if (!$validate->isValid($id)) {
            $response->addStatus(new PMS_Status(PMS_Status::INPUT_PARAMS_INCORRECT, 'id'));
            return $response;
        }
        $ordersSubcontractors = new PMS_Orders_Table_OrdersSubcontractors();
        $select = $this->_table->getAdapter()->select();
        $select->from(array('s' => $this->_table->getTableName()), '*');
        $select->join(array('os' => $ordersSubcontractors->getTableName()), 'os.subcontractor_id=s.id');
        $select->where('os.order_id = ? ', $id);
        try {
            $response->setRowset($select->query()->fetchAll());
            $status = PMS_Status::OK;
        } catch (Exception $e) {
            $status = PMS_Status::DATABASE_ERROR;
            if (OSDN_DEBUG) {
                throw $e;
            }
        }
        return $response->addStatus(new PMS_Status($status));
    }
}