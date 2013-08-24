<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model
{
	public $pagecount;
	public $page = 1;
	public $entries;
	public $limit = 10;

	protected $table;
	protected $primary_key;

	protected $search_term = '';
	protected $order_by;
	protected $search_field;
	protected $join = array();
	protected $sort = 'asc';


	public function initialize($table, $primary_key)
	{
		$this->table = $table;
		$this->primary_key = $primary_key;
	}

	public function set($id)
	{
		$this->load->database();
		$row = $this->db->from($this->table)
			->where($this->primary_key, $id)
			->get()->row();

		foreach ($row as $col => $field) {
			$this->$col = $field;
		}

		return $row;
	}

	public function insert($fields)
	{
		$this->load->database();
		$this->db->insert($this->table, $fields);

		return $this->db->insert_id();
	}

	public function update($fields, $id)
	{
		$this->load->database();
		$data = array($this->primary_key => $id);

		return $this->db->update($this->table, $fields, $data);
	}

	public function delete($id)
	{
		$this->load->database();
		$data = array($this->primary_key => $id);

		return $this->db->delete($this->table, $data);
	}

	public function fetch(array $params = array())
	{
		$this->load->database();

		foreach ($params as $key => $value) {
			$this->$key = $value;
		}

		// Count all results matching the search for pagination
		$this->entries = $this->db->from($this->table)
			->like($this->search_field, $this->search_term)
			->count_all_results();
		$this->pagecount = ceil($this->entries / $this->limit);

		// Return empty array if no result found
		if ($this->entries == 0) {
			return array();
		}

		// Check if the requested page exists
		if ($this->page > $this->pagecount) {
			$this->page = $this->pagecount;
		} else if ($this->page < 1) {
			$this->page = 1;
		}
		// Calculate query offset
		$start = ($this->page - 1) * $this->limit;

		// Do the search
		$this->db->from($this->table)
			->like($this->search_field, $this->search_term);

		// Make sure join parameter is array of arrays
		if (isset($this->join[0]) && !is_array($this->join[0])) {
			$this->join = array($this->join);
		}

		// Do the joins
		foreach ($this->join as $join) {
			call_user_func_array(array($this->db, 'join'), $join);
		}

		// Do the order by and limit
		$this->db->order_by($this->order_by, $this->sort)
			->limit($this->limit, $start);

		// Get the result
		$result = $this->db->get();

		$rows = array();
		foreach ($result->result_array() as $row) {
			$rows[] = $row;
		}

		return $rows;
	}
}