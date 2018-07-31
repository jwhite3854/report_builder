<?php

if ( ! defined( 'ABSPATH' ) ) {
    die;
}

class JWEB_Report_Builder
{
	public $id;
	public $title;
	public $list_type;
	public $list_scope;
	public $list_joins;
	public $list_fields;
	public $list_sort_order;
	public $list_settings;
	public $access_level;
	public $active_inactive;
	public $created_at;
	public $updated_at;
	public $created_by;
	public $updated_by;
	
	public function __construct($id = null)
	{	
		if ($id) {
			$this->id = $id;
			$entity = JWEB_Base::load('jweb_list_builder', $id);
			foreach ($entity as $key => $value) {
				$this->$key = $value;
			}
		}
		return $this;
	}
	
	public function __toString()
    {
        return $this->title;
    }
    
}

class JWEB_Report_Builder_Query extends JWEB_Base {
	
	public static function select($filters = array())
	{
		$entities = parent::gen_select('jweb_list_builder', $filters);
		$results = array();
		foreach ($entities as $entity) {
			$address = new AEX_List_Builder;
			foreach ($entity as $key => $value) {
				$address->$key = $value;
			}
			$results[] = $address; 
		}
		return $results;
	}
	
	public static function getByType( $list_type )
	{
		$entities = parent::gen_select('jweb_list_builder', array( 'list_type' => $list_type ) );
		$results = array();
		foreach ($entities as $entity) {
			$results[$entity->id] = $entity->title; 
		}
		return $results;
	}
	
	public static function getFieldOptions() {
		global $wpdb;
		$options = array();
		$exclude = array('id', 'created_at', 'updated_at', 'created_by', 'updated_by');

		$columns = $wpdb->get_col( "DESC aen_aex_program", 0 );
		$options['aen_aex_program']['key_title'] = 'Program Line Up Data';
		foreach( $columns as $column ) {
			if ( !in_array( $column, $exclude ) ) {
				$options['aen_aex_program'][$column] = ucwords(str_replace('_',' ',$column));
			}
		}
		$options['aen_aex_program']['term_id'] = 'Term';
		$options['aen_aex_program']['institution_id'] = 'Institution';
		$options['aen_aex_program']['is_new'] = 'New Program?';
		$options['aen_aex_program']['is_budget_req'] = 'Budget Required?';
		$options['aen_aex_program']['is_visa_req'] = 'Visa Required?';
		$options['aen_aex_program']['is_midterm_break'] = 'Has Mid-Term Break?';
		$options['aen_aex_program']['is_isic'] = 'ISIC Required?';
		$options['aen_aex_program']['addl_info'] = 'Additional Program Information';
		$options['aen_aex_program']['id'] = 'Enrolled Number';
		$options['aen_aex_program']['is_active'] = 'Active?';
		



		$columns = $wpdb->get_col( "DESC aen_aex_program_crosscheck", 0 );
		$options['aen_aex_program_crosscheck']['key_title'] = 'Cross Checks';
		foreach( $columns as $column ) {
			if ( !in_array( $column, $exclude ) ) {
				$options['aen_aex_program_crosscheck'][$column] = ucwords(str_replace('_',' ',$column));
			}
		}
		unset($options['aen_aex_program_crosscheck']['util_exch_rate']);
		
				
		return $options;
	}	


	public static function insert ($varset = array())
	{
		return parent::gen_insert('jweb_list_builder', $varset);
	}
	
	public static function update($varset = array(), $id)
	{
		return parent::gen_update('jweb_list_builder', $varset, $id);
	}
	
	public static function delete($id)
	{
		return parent::gen_delete('jweb_list_builder', $id);
	}
}