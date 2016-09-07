<?php

class PNR_Model extends CI_Model {
    
    public $data;

    /**
     * constructor
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Retrieve a list of records from current table
     * @example
     * array(
      'fields' => array('message.id', 'subject', 'body', 'from_user_id', 'to_user_id', 'message.created', 'message_status_id', 'firstname', 'lastname', 'email', 'message_status.name', 'message_type.name'),
      'and_filters' => array('to_user_id' => 80),
      'left_joins' => array(
      array('users.id' => 'message.to_user_id'),
      array('message_status.id' => 'message.message_status_id'),
      array('message_type.id' => 'message.message_type_id')
      ),
      'order_by'   => array('message.created' => 'DESC'),
      'assoc_keys' => true
      )
     */
    public function get_records($params = array()) {
        $fields = $params['fields'];

        $fields_map = array();
        $out_arr = array();
        $selected_fields = array();
        //$selected_fields[] = $this->table_name.'.'.$this->primary_key;
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field_idx => $field_name) {
                $selected_fields[] = $field_name . ' as f' . $field_idx;
                $fields_map['f' . $field_idx] = $field_name;
            }
            $selected_fields = join(',', $selected_fields);
        } else if ($fields == "*") {
            $selected_fields = "*";
        } else {
            $selected_fields = join(',', $selected_fields);
        }
        
        $this->db->select($selected_fields);
        $this->db->from($this->table_name);

        if (isset($params['and_filters']) && is_array($params['and_filters']) && count($params['and_filters']) > 0) {
            foreach ($params['and_filters'] as $filter_key => $filter_value) {
                /* @todo preg replace ^[0-9] */
                if ($filter_value == 'is null') {
                    $this->db->where($filter_key . ' IS NULL', null);
                } else {
                    $this->db->where($filter_key, $filter_value);
                }
            }
        }

        if (isset($params['not_in']['key']) && isset($params['not_in']['values']) && count($params['not_in']['values']) > 0) {
            $this->db->where_not_in($params['not_in']['key'], $params['not_in']['values']);
        }

        if (isset($params['or_filters']) && is_array($params['or_filters']) && count($params['or_filters']) > 0) {
            foreach ($params['or_filters'] as $filter_key => $filter_value) {
                /* @todo preg replace ^[0-9] */
                if ($filter_value == 'is null') {
                    $this->db->or_where($filter_key . ' IS NULL', null);
                } else {
                    $this->db->or_where($filter_key, $filter_value);
                }
            }
        }

        if (isset($params['like_filters']) && is_array($params['like_filters']) && count($params['like_filters']) > 0) {
            foreach ($params['like_filters'] as $filter_key => $filter_value) {
                $this->db->like($filter_key, $filter_value, 'both'); //before,after,both
            }
        }

        if (isset($params['like_before_filters']) && is_array($params['like_before_filters']) && count($params['like_before_filters']) > 0) {
            foreach ($params['like_before_filters'] as $filter_key => $filter_value) {
                $this->db->like($filter_key, $filter_value, 'before');
            }
        }

        if (isset($params['like_after_filters']) && is_array($params['like_after_filters']) && count($params['like_after_filters']) > 0) {
            foreach ($params['like_after_filters'] as $filter_key => $filter_value) {
                $this->db->like($filter_key, $filter_value, 'after');
            }
        }

        if (isset($params['left_joins']) && is_array($params['left_joins']) && count($params['left_joins']) > 0) {
            foreach ($params['left_joins'] as $joined_field => $table_field) {
                // multiple left joins
                if (is_array($table_field)) {
                    foreach ($table_field as $key => $value) {
                        list($joined_table, $joined_field_name) = explode('.', $key, 2);
                        $this->db->join($joined_table, $key . ' = ' . $value, 'left');
                    }
                } else {
                    // single left join
                    list($joined_table, $joined_field_name) = explode('.', $joined_field, 2);
                    $this->db->join($joined_table, $joined_field . ' = ' . $table_field, 'left');
                }
            }
        }

        if (isset($params['order_by']) && is_array($params['order_by']) && count($params['order_by']) > 0) {
            foreach ($params['order_by'] as $okey => $direction) {
                $this->db->order_by($okey, $direction);
            }
        }

        if (isset($params['group_by']) && is_array($params['group_by']) && count($params['group_by']) > 0) {
            $this->db->group_by($params['group_by']);
        }

        if (isset($params['limit']) && $params['limit']) {
            if (isset($params['offset']) && $params['offset']) {
                $this->db->limit($params['limit'], $params['offset']);
            } else {
                $this->db->limit($params['limit']);
            }
        }

        $selected_fields = count($fields_map);
        $return = array();

        /* @var $db CI_DB_active_record */
        $db = $this->db;
        //$this->firephp->export($db->_compile_select());
        //file_put_contents(ERROR_LOG, $db->_compile_select()."\n", FILE_APPEND);
        //var_export($db->_compile_select());

        $query = $db->get();
        foreach ($query->result() as $idx => $result_values) {
            if (isset($params['assoc_keys']) && $params['assoc_keys'] == true) {
                // select * ...
                if ($selected_fields == 0) {
                    $record = array();
                    foreach ($result_values as $key => $value) {
                        $record[$key] = $value;
                    }
                    $return[] = $record;
                } else {
                    foreach ($fields_map as $field_idx => $field_name) {
                        if (isset($params['alias']) && array_key_exists($field_name, $params['alias'])) {
                            $out_arr[$params['alias'][$field_name]] = $result_values->{$field_idx};
                        }
                        else {
                            $out_arr[$field_name] = $result_values->{$field_idx};
                        }
                    }
                    $return[] = $out_arr;
                }
            }
            else {
                $data_values = array();
                foreach ($fields_map as $field_idx => $field_name) {
                    $data_values[] = $result_values->{$field_idx};
                }
                $out_arr[$idx] = $data_values;
                $return = $out_arr;
            }
        }
        unset($db);
        unset($query);
        return $return;
    }
    
    /**
     * Get a single record, based on the id
     *
     * @param int $id - value of the id
     * @param array $fields - the fields that will be selected
     * @param array $alias - the keys used to generate the result. If blank, field names are used (assoc keys)
     * @return mixed - array | null
     */
    public function read_record_by_id($id, $fields = array(), $alias = array()) {
        $id = preg_replace("/[^0-9]/", "", $id);
        
         $params = array(
            'fields' => $fields,
            'and_filters' => array('id' => $id),
            'limit' => 1
        );
        if (is_array($alias) && sizeof($alias)) {
            $params['alias'] = $alias;
        } else {
            $params['assoc_keys'] = true;
        }
        if (count($fields) == 0)
            $params['fields'] = "*";
        $result = $this->get_records($params);
        return ($result && sizeof($result)) ? $result[0] : null;
    }

    /**
     * Get a single record, based on the a field name => value
     *
     * @param string $attribute - one of the columns in the specified table
     * @param mixed $value - value of the specified column
     * @param array $fields - the fields that will be selected
     * @param array $alias - the keys used to generate the result. If blank, field names are used (assoc keys)
     * @return mixed - array | null
     */
    public function read_record_by_attribute_value($attribute, $value, $fields = array(), $alias = array()) {
    	
    	$filters = array();
    	if (is_array($attribute)) {
            foreach($attribute as $key => $attr) {
                $filters[$attr] = $value[$key];
            }
    	} else {
            $filters = array($attribute => $value);
    	}

        $params = array(
            'fields' => $fields,
            'and_filters' => $filters,
            'limit' => 1
        );
        
        if (is_array($alias) && sizeof($alias)) {
            $params['alias'] = $alias;
        } else {
            $params['assoc_keys'] = true;
        }

        $result = $this->get_records($params);
        return ($result && sizeof($result)) ? $result[0] : null;
    }

    public function pre_add($data) {
        return $data;
    }

    public function post_add($data) {
        return $data;
    }

    public function pre_update($data) {
        return $data;
    }

    public function post_update($data) {
        return $data;
    }

    public function pre_delete($id) {
        return $id;
    }

    public function post_delete($id) {
        return $id;
    }

    /**
     * add a new record
     * @param array $data
     */
    public function add_by_array($data) {
        $data = $this->pre_add($data);
        $this->db->insert($this->table_name, $data);
        $id = $this->db->insert_id();
        $data = $this->post_add($data);
        return $id;
    }

    /**
     * edit an existing record
     * @param int $id
     * @param array $data
     */
    public function update_by_id_and_array($id, $data) {
        $id = preg_replace('/[^0-9]/', '', $id);
        if (!$id) {
            return false;
        }

        $data = $this->pre_update($data);
        $this->db->set($data);
        $this->db->where($this->primary_key, $id);
        $this->db->update($this->table_name);
        //echo $this->db->_compile_select();
        $data = $this->post_update($data);
        return true;
    }
    
    public function update_by_array_and_array($filters, $data) {
        $data = $this->pre_update($data);
        $this->db->set($data);
        $this->db->where($filters);
        $this->db->update($this->table_name);
        //echo $this->db->_compile_select();
        $data = $this->post_update($data);
        return true;
    }

    /**
     * delete a record by id
     * @param int $id
     */
    public function delete_by_id($id) {
        $id = preg_replace('/[^0-9]/', '', $id);

        $id = $this->pre_delete($id);
        if (!$id) {
            return false;
        }

        if ($this->db->get_where($this->table_name, array($this->primary_key => $id), 1)->num_rows()) {
            $this->db->where($this->primary_key, $id);
            $this->db->delete($this->table_name);
            $id = $this->post_delete($id);
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    public function delete_by_array($arr) {
        if ($this->db->get_where($this->table_name, $arr, 1)->num_rows()) {
            if (is_array($arr) && count($arr) > 0) {
                foreach ($arr as $k => $v) {
                    $this->db->where($k, $v);
                }
            }
            $this->db->delete($this->table_name);
            return true;
        } else {
            return false;
        }
    }

    public function getBlankObject() {
        $obj = new stdClass();
        if (empty($this->table_name)) {
            return $obj;
        }

        $table_definition = $this->db->query('DESCRIBE ' . $this->table_name)->result();
        foreach ($table_definition as $field) {
            $obj->{$field->Field} = $field->Default;
        }

        return $obj;
    }
}