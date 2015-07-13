<?php

class LMVC_DataTables {

    private $db;
    private $table;
    private $idColumn;
    private $columns = array();
    private $offset;
    private $length;
    private $totalRecords;
    private $totalFilteredRecords;
    private $joins;
    private $groupBy;
    private $columnAliases = array();
    private $filterAliases = array();
    private $defaultFilters = array();
    private $disabledSearchFields = array();
    private $formattedColumn = array();
    //private $sql;

    private $request;

    public function __construct() {
        $this->idColumn = "id";
        $this->request = LMVC_Request::getInstance();

        $this->offset = $this->getVar('iDisplayStart');
        $this->length = $this->getVar('iDisplayLength');
    }

    public function setDBAdapter($_adapter) {
        $this->db = $_adapter;
    }

    public function getDBAdapter() {
        return $this->db;
    }

    public function setTable($_table) {
        $this->table = $_table;
    }

    public function setIdColumn($_column) {
        $this->idColumn = $_column;
    }

    public function addColumnAlias($_columnName, $_aliasName) {
        $this->columnAliases[$_columnName] = $_aliasName;
    }

    public function addfilterAlias($_filter, $_aliasName) {
        $this->filterAliases[$_filter] = $_aliasName;
    }

    public function addColumn($_columnName) {
        if (is_string($_columnName)) {
            if (!in_array($_columnName, $this->columns)) {
                array_push($this->columns, $_columnName);
            }
        } elseif (is_array($_columnName)) {
            foreach ($_columnName as $col) {
                if (!in_array($col, $this->columns)) {
                    array_push($this->columns, $col);
                }
            }
        }
        return $this;
    }

    public function addFormattedColumn($colIndex, $format) {
        $this->formattedColumn[$colIndex] = $format;
    }

    public function setJoins($_joinStatement) {
        $this->joins = $_joinStatement;
    }

    public function setDefaultFilters($_defaultFilters) {
        $this->defaultFilters = $_defaultFilters;
    }

    public function setGroupBy($_groupBy) {
        $this->groupBy = $_groupBy;
    }

    public function setOffset($_offset) {
        $this->offset = $_offset;
    }

    public function setLength($_length) {
        $this->length = $_length;
    }

    public function ignoreSearchFields($_fields) {
        $this->disabledSearchFields = $_fields;
    }

    public function getVar($_varname) {
        if (!$this->request->isPost()) {
            return $this->request->getVar($_varname);
        } else {
            return $this->request->getPostVar($_varname);
        }
    }

    public function getData() {

        $aColumns = $this->columns;

        /* foreach($aColumns as $key=>$val)
          {
          if(in_array($val,array_keys($this->columnAliases)))
          {
          $aColumns[$key] = $val ." AS ". $this->columnAliases[$val];
          }
          }
         */
        //print_r($aColumns);

        $sTable = $this->table;


        /** Paging */
        $sLimit = "";
        if (isset($this->offset) && $this->length != -1) {
            $sLimit = "LIMIT " . $this->offset . ", " . $this->length;
        }

        //ordering
        $sOrder = "";

        if ($this->getVar('iSortCol_0') != "") {
            $sOrder = "ORDER BY  ";
            $iSortingCols = $this->getVar('iSortingCols');
            $iSortingCols = (empty($iSortingCols)) ? 0 : $iSortingCols;

            for ($i = 0; $i < $iSortingCols; $i++) {
                if ($this->getVar('bSortable_' . $this->getVar('iSortCol_' . $i)) == "true") {

                    $orderField = $aColumns[$this->getVar('iSortCol_' . $i)];
                    if (array_key_exists($orderField, $this->columnAliases)) {
                        //	$orderField = $this->columnAliases[$orderField];
                    }

                    $sOrder .= $orderField . " " . $this->getVar('sSortDir_' . $i) . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        //filtering		
        $sWhere = "";
        if ($this->getVar('sSearch') != "") {
            $sWhere = "WHERE (";
            for ($i = 0; $i < count($aColumns); $i++) {
                $fitlerField = $aColumns[$i];

                if (in_array($fitlerField, $this->disabledSearchFields)) {
                    continue;
                }

                if (array_key_exists($fitlerField, $this->filterAliases)) {
                    $fitlerField = $this->filterAliases[$fitlerField];
                }
                $sWhere .= $fitlerField . " LIKE '%" . $this->getVar('sSearch') . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        /* Individual column filtering */

        for ($i = 0; $i < count($aColumns); $i++) {
            if ($this->getVar('bSearchable_' . $i) == "true" && $this->getVar('sSearch_' . $i) != '') {
                if ($sWhere == "") {
                    $sWhere = "WHERE ";
                } else {
                    $sWhere .= " AND ";
                }
                $fitlerField = $aColumns[$i];
                if (array_key_exists($fitlerField, $this->filterAliases)) {
                    $fitlerField = $this->filterAliases[$fitlerField];
                }
                $sWhere .= $fitlerField . " LIKE '%" . $this->getVar('sSearch_' . $i) . "%' ";
            }
        }

        //default filters

        $sDefaultFilter = "";
        foreach ($this->defaultFilters as $key => $val) {

            if ($sWhere == "") {
                $sWhere = "WHERE ";
            } else {
                $sWhere .= " AND ";
            }

            if ($key == "combination_filter") {
                $sWhere .= $val;
            } elseif (is_array($val)) {
                $sWhere .= $key . " " . $val['condition'] . " ";
            } else {
                $sWhere .= $key . "=" . $val . " ";
            }


            //default filter
            if ($sDefaultFilter == "") {
                $sDefaultFilter = "WHERE ";
            } else {
                $sDefaultFilter .= " AND ";
            }


            if ($key == "combination_filter") {
                $sDefaultFilter .= $val;
            } elseif (is_array($val)) {
                $sDefaultFilter .= $key . " " . $val['condition'] . " ";
            } else {
                $sDefaultFilter .= $key . "=" . $val . " ";
            }
        }



        $sJoin = "";
        if ($this->joins) {
            $sJoin = $this->joins;
        }


        $sGroupBy = "";

        if ($this->groupBy) {
            $sGroupBy = $this->groupBy;
        }


        /** SQL queries. Get data to display */
        $db = $this->getDBAdapter();

        $fields = array();
        foreach ($aColumns as $key => $col) {
            if (isset($this->formattedColumn[$key])) {
                array_push($fields, $this->formattedColumn[$key]);
            } else {
                array_push($fields, $col);
            }
        }

        $sQuery = "SELECT SQL_CALC_FOUND_ROWS " . str_replace(" , ", " ", implode(", ", $fields)) . " FROM $sTable $sJoin $sWhere $sGroupBy $sOrder $sLimit";
        //echo $sQuery;
        //die();				
        $result = $db->fetchALL($sQuery,Zend_DB::FETCH_ASSOC);

        $sQuery = "SELECT FOUND_ROWS()";
        $this->totalFilteredRecords = $db->fetchOne($sQuery);

        /* Total data set length */

        if ($sGroupBy != "") {
            $sQuery = "SELECT COUNT(" . $this->idColumn . ")	FROM   $sTable $sJoin $sDefaultFilter $sGroupBy";
            $result2 = $db->getCol($sQuery);

            if (is_array($result2))
                $this->totalRecords = count($result2);
            else
                $this->totalRecords = 0;
        }
        else {
            $sQuery = "SELECT COUNT(" . $this->idColumn . ")	FROM   $sTable $sJoin $sDefaultFilter";
            $this->totalRecords = $db->fetchOne($sQuery);
        }




        echo $this->encodeJSON($result, $this->totalRecords, $this->totalFilteredRecords);
    }

    public function encodeJSON($data, $totalRecords, $totalFilteredRecords) {
        $aColumns = $this->columns;
        $output = array(
            "sEcho" => $this->getVar('sEcho'),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalFilteredRecords,
            "aaData" => array()
        );
        //print_r($this->columns);
        //print_r($data);
        foreach ($data as $aRow) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                if ($aColumns[$i] == "version") {
                    /* Special output formatting for 'version' column */
                    $row[] = ($aRow[$aColumns[$i]] == "0") ? '-' : $aRow[$aColumns[$i]];
                } else if ($aColumns[$i] != ' ') {
                    /* General output */
                    if (array_key_exists($aColumns[$i], $aRow)) {
                        $row[] = $aRow[$aColumns[$i]];
                    } else {
                        if (array_key_exists($aColumns[$i], $this->columnAliases)) {
                            $row[] = $aRow[$this->columnAliases[$aColumns[$i]]];
                        } else {
                            $row[] = 'No Field';
                        }
                    }
                }
            }
            $output['aaData'][] = $row;
        }
        return json_encode($output);
    }

}

?>