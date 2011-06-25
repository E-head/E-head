<?php

class PMS_Staff_Reports
{
    public function generateStaff(array $params)
    {
        $response = new OSDN_Response();

        $f = new OSDN_Filter_Input(array(
            '*' => 'StringTrim'
        ), array(
            'start'  => array('Date', 'allowEmpty' => false, 'presence' => 'required'),
            'end'    => array('Date', 'allowEmpty' => false, 'presence' => 'required')
        ), $params);

        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $rowsMerged     = array();
        $rowStructure   = array(
            'hours_total'   => 0,
            'summ_total'    => 0,
            'pays_total'    => 0,
            'rate'          => 0,
            'period'        => 0,
            'name'          => '',
            'function'      => ''
        );

        $tableHr = new PMS_Staff_Hr_Table();
        $tablePayments = new PMS_Staff_Payments_Table();
        $tableStaff = new PMS_Staff_Table();

        // Get total summ of working hours by person for given period
        $select = $tableHr->getAdapter()->select()
        ->from(array('s' => $tableStaff->getTableName()),
            array(
                'id', 'name', 'function',
                'rate' => 's.pay_rate',
                'period' => 's.pay_period',
                'hours_total'   => new Zend_Db_Expr('SUM(h.value)'),
                'summ_total'    => new Zend_Db_Expr('IF(s.pay_period = "month",
                    s.pay_rate,SUM(h.value*s.pay_rate))')
            )
        )
        ->joinLeft(array('h' => $tableHr->getTableName()),
            'h.staff_id=s.id', array()
        )
        ->group('s.id')
        ->where('archive = 0')
        ->orWhere('(h.date >= ?', $f->start)
        ->where('h.date <= ?)', $f->end)
        ;

        try {
            $rows = $select->query()->fetchAll();
        } catch (Exception $e) {
            if (OSDN_DEBUG) {
                throw $e;
            }
            return $response->addStatus(new PMS_Status(PMS_Status::DATABASE_ERROR));
        }

        // Parse result rows into one merged array
        foreach ($rows as $row) {
            $rowsMerged[$row['id']] = $rowStructure;
            $rowsMerged[$row['id']]['name'] = $row['name'];
            $rowsMerged[$row['id']]['function'] = $row['function'];
            $rowsMerged[$row['id']]['rate'] = $row['rate'];
            $rowsMerged[$row['id']]['period'] = $row['period'];
            $rowsMerged[$row['id']]['hours_total'] = intval($row['hours_total']);
            $rowsMerged[$row['id']]['summ_total'] = intval($row['summ_total']);
        }

        // Get total summ of payments by person for given period
        $select = $tableHr->getAdapter()->select()
        ->from(array('s' => $tableStaff->getTableName()),
            array(
                'id', 'name', 'function',
                'rate' => 's.pay_rate',
                'period' => 's.pay_period',
                'pays_total'   => new Zend_Db_Expr('SUM(p.value)')
            )
        )
        ->joinLeft(array('p' => $tablePayments->getTableName()),
            'p.staff_id=s.id', array()
        )
        ->group('s.id')
        ->where('archive = 0')
        ->where('p.date >= ?', $f->start)
        ->where('p.date <= ?', $f->end)
        ;

        try {
            $rows = $select->query()->fetchAll();
        } catch (Exception $e) {
            if (OSDN_DEBUG) {
                throw $e;
            }
            return $response->addStatus(new PMS_Status(PMS_Status::DATABASE_ERROR));
        }

        // Parse result rows into one merged array
        foreach ($rows as $row) {
            if (isset($rowsMerged[$row['id']])) {
                $rowsMerged[$row['id']]['pays_total'] = $row['pays_total'];
            } else {
                $rowsMerged[$row['id']] = $rowStructure;
                $rowsMerged[$row['id']]['name'] = $row['name'];
                $rowsMerged[$row['id']]['function'] = $row['function'];
                $rowsMerged[$row['id']]['rate'] = $row['rate'];
                $rowsMerged[$row['id']]['period'] = $row['period'];
                $rowsMerged[$row['id']]['pays_total'] = $row['pays_total'];
            }
        }

        $response->data = array(
            'rows'  => array_values($rowsMerged),
            'start' => $f->start,
            'end'   => $f->end
        );
        return $response->addStatus(new PMS_Status(PMS_Status::OK));
    }

    public function generateStaff_NEW(array $params)
    {
        $response = new OSDN_Response();

        $f = new OSDN_Filter_Input(array(
            '*' => 'StringTrim'
        ), array(
            'start'  => array('Date', 'allowEmpty' => false, 'presence' => 'required'),
            'end'    => array('Date', 'allowEmpty' => false, 'presence' => 'required')
        ), $params);

        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $tableStaff = new PMS_Staff_Table();
        $tableHr = new PMS_Staff_Hr_Table();
        $tablePayments = new PMS_Staff_Payments_Table();
        $response->data = array();

        $persons = $tableStaff->fetchAll();
        if (!$persons->count()) {
            return $response->addStatus(new PMS_Status(PMS_Status::OK));
        }

        $tableVacations = new PMS_Staff_Vacations_Table();

        $persons = $persons->toArray();
        foreach ($persons as $person) {

            // Get total summ of working hours by person for given period
            $select = $tableHr->getAdapter()->select()
            ->from(array('s' => $tableStaff->getTableName()),
                array(
                    'id', 'name', 'function',
                    'rate' => 's.pay_rate',
                    'period' => 's.pay_period',
                    'hours_total'   => new Zend_Db_Expr('SUM(h.value)'),
                    'summ_total'    => new Zend_Db_Expr('IF(s.pay_period = "month",
                        s.pay_rate,SUM(h.value*s.pay_rate))')
                )
            )
            ->joinLeft(array('h' => $tableHr->getTableName()),
                'h.staff_id=s.id', array()
            )
            ->group('s.id')
            ->where('archive = 0')
            ->where('h.date >= ?', $f->start)
            ->where('h.date <= ?', $f->end)
            ;
        }
    }

    public function generateVacations($params)
    {
        $response = new OSDN_Response();

        $f = new OSDN_Filter_Input(array(
            '*' => 'StringTrim'
        ), array(
            'start'  => array('Date', 'allowEmpty' => false, 'presence' => 'required'),
            'end'    => array('Date', 'allowEmpty' => false, 'presence' => 'required')
        ), $params);

        $response->addInputStatus($f);
        if ($response->hasNotSuccess()) {
            return $response;
        }

        $tableStaff = new PMS_Staff_Table();
        $result = array();

        $debug = array();

        $persons = $tableStaff->fetchAllColumns(null, null, array('id', 'name', 'function'));

        if ($persons->count()) {

            $tableVacations = new PMS_Staff_Vacations_Table();
            $persons = $persons->toArray();

            foreach ($persons as $person) {

                $select = $tableVacations->getAdapter()->select()
                ->from(array('v' => $tableVacations->getTableName()), array('from', 'to'))
                ->where('v.staff_id = ?', $person['id'])
                ->where('(v.from >= ?', $f->start)
                ->where('v.from <= ?', $f->end)
                ->orWhere('v.to >= ?', $f->start)
                ->where('v.to <= ?)', $f->end)
                ;

                $debug[] = $select->assemble();

                try {
                    $rows = $select->query()->fetchAll();
                } catch (Exception $e) {
                    if (OSDN_DEBUG) {
                        throw $e;
                    }
                    return $response->addStatus(new PMS_Status(PMS_Status::DATABASE_ERROR));
                }

                if (count($rows) > 0) {

                    $person['periods'] = array();

                    foreach ($rows as $row) {
                        $person['periods'][] = array(
                            'from'  => new Zend_Date($row['from']),
                            'to'    => new Zend_Date($row['to'])
                        );
                    }

                    $result[$person['id']] = $person;
                }
            }
        }

        $response->data = array(
            'debug' => $debug,
            'rows'  => $result,
            'days'  => array(),
            'start' => $f->start,
            'end'   => $f->end
        );
        return $response->addStatus(new PMS_Status(PMS_Status::OK));
    }
}