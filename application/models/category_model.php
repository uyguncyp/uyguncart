<?php

class Category_model extends CI_model
{
	public $categoryName;
	public $parentID;
	public $categoryID;

	/**
	 *	Setting category
	 *	@param string The ID of the category.
	 */
	public function set($id) {
		$this->load->database();
		$this->db->from('category')->where(array('categoryID' => $id));
		$query = $this->db->get();
		foreach ($query->result() as $row) {
			$this->categoryID = $row->categoryID;
			$this->categoryName = $row->categoryName;
			$this->parentID = $row->parentID;
		}
	}

	/**
	 *	Add category
	 *	
	 *	@param array The array include information to be updated. 
	 * 	@return boolean
	 */
	public function add($field) {
		$this->load->database();
		return $this->db->insert('category', $field);
	}

	/**
	 *	Delete category
	 *
	 *	@param array The array include information to be deleted.
	 *	@return boolean true for success
	 */
	public function delete($categoryID) {
		$this->load->database();
		return $this->db->delete('category', array('categoryID'=>$categoryID));
	}

	/**
	 *	Check if category is exist.
	 *
	 *	@param string The id of category.
	 *	@return boolean true for success
	 */
	public function category_exist ($categoryID) {
		$this->load->database();
		$this->db->from('category')->where('categoryID', $categoryID);
		if($this->db->count_all_results()>0)
			return true;
		return false;
	}

	/**
	 *	Get category path.
	 *
	 *	@param	integer	Category ID.
	 *	@param	string	used for recursion
	 *	@return	string	path of the category
	 */
	public function get_path($id, $sep = '') {
		if (empty($id)) return '';

		$this->load->database();
		$this->db->from('category')->where('categoryID', $id);

		$query = $this->db->get();
		$row = $query->result_array();
		if (empty($row)) return '';

		return $this->get_path($row[0]['parentID'], ' / ')
			. $row[0]['categoryName'] . $sep;
	}

	/**
	 *	Get list of categories as an array.
	 *
	 *	@return array
	 */
	public function fetchAll() {
		$this->load->database();
		$this->db->from('category');
		$query = $this->db->get();
		$field = array();
		foreach ($query->result() as $row) {	
		 	$field[] = array(
		 		'categoryID' => $row->categoryID,
		 		'categoryName' => $this->get_path($row->categoryID)
		 	);
		}
		return $field;
	}

}
