<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Common_Model extends CI_Model{
    function __construct() {


    }
    /*
     * get rows from the users table
     */
    function getRows($params = array(), $table){
        if(array_key_exists("selection",$params)){
             $this->db->select($params['selection']);
        } else {
             $this->db->select('*');
        }
        $this->db->from($table);

        //Joins the tables 
         if (array_key_exists('innerJoin',$params)) {
                foreach ($params['innerJoin'] as $value) {
                   $this->db->join($value['table'], $value['condition'], $value['joinType']);
                }
                
                
        }

        //Group by fetching
        if (array_key_exists('groupBy',$params)) {
                foreach ($params['groupBy'] as $value) {
                   $this->db->group_by($value);
                }
                              
        }

        //Distinct fetch
        if(array_key_exists("distinct",$params)){
             $this->db->distinct($params['distinct']);
        } 

        // Order by
        if (array_key_exists('orderBy', $params)) {
            $this->db->order_by($params['orderBy']);
        }

        
        // And like conditions.
        if(array_key_exists("like_conditions",$params)){
            foreach ($params['like_conditions'] as $key => $value) {
                $this->db->like($key, $value);

            }
        }

        // Or like conditions.
        if(array_key_exists("or_like_conditions",$params)){
            foreach ($params['or_like_conditions'] as $key => $value) {
                $this->db->or_like($key, $value);

            }
        }

        //fetch data by conditions
        if(array_key_exists("conditions",$params)){
           
            foreach ($params['conditions'] as $key => $value) {
                $this->db->where($key,$value);
            }
        }

        //fetch data by string conditions
        if(array_key_exists("string_conditions",$params)){
           
            foreach ($params['string_conditions'] as $key => $value) {
                $this->db->where($value);
            }
        }

        //fetch data by or conditions
        if(array_key_exists("or_conditions",$params)){
           
            foreach ($params['or_conditions'] as $key => $value) {
                $this->db->or_where($key,$value);
            }
        }

        //fetch data by string or conditions
        if(array_key_exists("string_or_conditions",$params)){
           
            foreach ($params['string_or_conditions'] as $key => $value) {
                $this->db->or_where($value);
            }
        }

        if(array_key_exists("id",$params)){
            $this->db->where('id',$params['id']);
            $query = $this->db->get();
            $result = $query->row_array();
        }else{
            //set start and limit
            if(array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit'],$params['start']);
            }elseif(!array_key_exists("start",$params) && array_key_exists("limit",$params)){
                $this->db->limit($params['limit']);
            }
            $query = $this->db->get();
            if(array_key_exists("returnType",$params) && $params['returnType'] == 'count'){
                $result = $query->num_rows();
            }elseif(array_key_exists("returnType",$params) && $params['returnType'] == 'single'){
                $result = ($query->num_rows() > 0)?$query->row_array():FALSE;
            }else{
                $result = ($query->num_rows() > 0)?$query->result_array():FALSE;
            }
        }
        //return fetched data
        return $result;
    }
    
    /*
     * Insert user information
     */
    public function insert($data = array(), $table) {
        //add created and modified data if not included
        if(!array_key_exists("created", $data)){
            $data['created'] = date("Y-m-d H:i:s");
        }
        if(!array_key_exists("modified", $data)){
            $data['modified'] = date("Y-m-d H:i:s");
        }
        //insert user data to users table
        $insert = $this->db->insert($table, $data);
        
        //return the status
        if($insert){
            return $this->db->insert_id();
        }else{
            return false;
        }
    }

    /*
    * Update query form here.
    */
    public function update($data = array(), $condition = array(), $table) {
        $this->db->where($condition);
        $result = $this->db->update($table, $data);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * Update one field query form here.
    */
    public function update_fields($data = array(), $condition = array(), $table) {
        $this->db->where($condition);
        foreach ($data as $key => $value) {
            $this->db->set("$key", "$value", FALSE);
        }
        
        $result = $this->db->update($table);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*
    * Delete query from here.
    */
    public function delete($condition = array(), $table) {
        $this->db->where($condition);
        $result = $this->db->delete($table);
        if ($result) {
            return true;

        } else {
            return false;
        }
    }

}