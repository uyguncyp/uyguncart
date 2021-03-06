<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Category extends Admin_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->model('Category_model');
	}

	public function index()
	{
		$this->load->helper('url');
		redirect('/admin/category/view', 'location', 301);
	}

	public function view()
	{
		$data = array(
			'title' => 'Categories',
			'breadcrumb' => array(
				'admin/home' => 'Dashboard',
				'admin/category' => 'Category',
				'last' => 'View'
			),
			'menu_active' => 'catalog',
			'mainview' => 'category',
			'js' => array('view.js'),
		);

		$this->load_view($data);
	}

	public function add()
	{
		$this->load->library('form_validation');
		$this->load->helper('form');

		$data = array(
			'title' => 'Add Category',
			'breadcrumb' => array(
				'admin/home' => 'Dashboard',
				'admin/category' => 'Category',
				'last' => 'Add'
			),
			'menu_active' => 'catalog',
			'mainview' => 'category_add',
			'categories' => $this->Category_model->fetchAll(true),
		);
		$rules = array(
			array(
				'field' => 'parentID',
				'label' => 'Parent Category',
				'rules' => 'exists_null[category.category_id]'
			),
			array(
				'field' => 'categoryName',
				'label' => 'Category',
				'rules' => 'required|max_length[45]|is_unique[category.name]'
			)
		);
		$this->form_validation->set_rules($rules);

		if ($this->form_validation->run() == true) {
			$parentID = $this->input->post('parentID');
			$field = array('name'=> $this->input->post('categoryName'));
			if (strlen($parentID) > 0) {
				$field['parent_id'] = $this->input->post('parentID');
			}

			if ($this->Category_model->insert($field)) {
				$data["alert_message"] = "The Category is added succesfuly.";
				$data["alert_class"] = "alert-success";
				$data["categories"] = $this->Category_model->fetchAll(true);
			} else {
				$data["alert_message"] = "Something went wrong. Please try again.";
				$data["alert_class"] = "alert-error";
			}
		}

		$this->load_view($data);
	}

	public function edit($id)
	{
		$this->load->library('form_validation');
		$this->load->helper('form');

		$this->Category_model->set($id);

		$cat_name_changed = $this->Category_model->name != $this->input->post('categoryName');

		if ($cat_name_changed || $this->Category_model->parent_id != $this->input->post('parentID')) {
			$this->form_validation->set_rules(
				'parentID',
				'Parent Category',
				'exists_null[category.category_id]|not_sub_category['. $id .']'
			);
			if ($cat_name_changed) {
				$this->form_validation->set_rules(
					'categoryName',
					'Category',
					'required|max_length[45]|is_unique[category.name]'
				);
			}
		}

		$data = array(
			'title' => 'Edit Category',
			'breadcrumb' => array(
				'admin/home' => 'Dashboard',
				'admin/category' => 'Category',
				'last' => 'Edit'
			),
			'menu_active' => 'catalog',
			'mainview' => 'category_edit',
			'categories' => $this->Category_model->fetchAll(true, $id),
		);

		if ($this->form_validation->run() == TRUE) {
			$parentID = $this->input->post('parentID');
			if (empty($parentID)) {
				$parentID = null;
			}

			$field = array(
				'parent_id' => $parentID,
				'name' => $this->input->post('categoryName')
			);

			if ($this->Category_model->update($field, $id)) {
				$data["alert_message"] = "The Category was updated successfully.";
				$data["alert_class"] = "alert-success";
				$data["categories"] = $this->Category_model->fetchAll(true, $id);
			} else {
				$data["alert_message"] = "Something went wrong. Please try again.";
				$data["alert_class"] = "alert-error";
			}
		}

		$data['parentID'] = $this->Category_model->parent_id;
		$data['categoryName'] = $this->Category_model->name;

		$this->load_view($data);
	}

	public function delete()
	{
		$list = $this->input->post('list');

		foreach ($list as $value) {
			$this->Category_model->delete($value);
		}
	}

	public function ajax()
	{
		$search = $this->input->post('search');
		$page = $this->input->post('page');

		$params = array(
			'search_term' => $search,
			'order_by' => 'name',
			'page' => $page,
		);

		$categories = $this->Category_model->fetch($params);

		foreach ($categories as &$category) {
			$category['name'] = $this->Category_model->get_path($category['category_id']);
		}

		$array = array(
			$categories,
			array(
				$this->Category_model->pagecount,
				$this->Category_model->page,
				$this->Category_model->entries
			)
		);

		$this->output_json($array);
	}
}
