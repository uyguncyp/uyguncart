<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product extends Admin_Controller
{
	public function __construct() {
		parent::__construct();

		$this->load->model('Product_model');
	}

	public function index()
	{
		$this->load->helper('url');
		redirect('/admin/product/view', 'location', 301);
	}

	public function view()
	{
		$data = array(
			'title' => 'Products',
			'breadcrumb' => array(
				'admin/home' => 'Dashboard',
				'admin/product' => 'Product',
				'last' => 'View'
			),
			'menu_active' => 'catalog',
			'mainview' => 'product',
			'js' => array('view.js'),
		);

		$this->load_view($data);
	}

	public function add()
	{
		$this->load_model(array('Category', 'Manufacturer'));
		$this->load->library('form_validation');
		$this->load->helper('form');

		$data = array(
			'title' => 'Add Product',
			'breadcrumb' => array(
				'admin/home' => 'Dashboard',
				'admin/product' => 'Product',
				'last' => 'Add'
			),
			'menu_active' => 'catalog',
			'mainview' => 'product_add',
			'categories' => $this->Category_model->fetchAll(true),
			'manufacturer' => $this->Manufacturer_model->fetchAll(true),
		);
		if ($this->input->post('categoryID') != NULL) {
			$this->form_validation->set_rules(
				'categoryID',
				'Category',
				'required|exists[category.category_id]'
			);
		}

		if ($this->input->post('manufacturer_id') != NULL) {
			$this->form_validation->set_rules(
				'manufacturer_id',
				'Manufacturer',
				'required|exists[manufacturer.manufacturer_id]'
			);
		}

		$this->form_validation->set_rules(
			'name',
			'Product',
			'required|max_length[75]|is_unique[product.name]'
		);

		if ($this->form_validation->run() == true) {
			$field = array(
				'name'=> $this->input->post('name'),
				'category_id'=> $this->input->post('category_id'),
				'manufacturer_id'=> $this->input->post('manufacturer_id')
			);
			if ($productID = $this->Product_model->insert($field)) {
				redirect('/admin/product/edit/' . $productID);
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
		$this->load_model(array('Category', 'Manufacturer'));
		$this->load->helper('form');

		$data = array(
			'title' => 'Edit Product',
			'breadcrumb' => array(
				'admin/home' => 'Dashboard',
				'admin/product' => 'Product',
				'last' => 'Edit'
			),
			'menu_active' => 'catalog',
			'mainview' => 'product_edit',
			'js' => array(
				'tinymce/tinymce.min.js',
				'product_edit.js',
				'jquery.form.min.js'
			),
			'status' => array(0 => 'Disabled', 1 => 'Enabled'),
			'categories' => $this->Category_model->fetchAll(true),
			'manufacturers' => $this->Manufacturer_model->fetchAll(true),
		);

		if ($this->input->server('REQUEST_METHOD') === 'POST') {
			$fields = array(
				array(
					'field' => 'name',
					'label' => 'Product Name',
					'rules' => 'required|is_unique[product.name]',
				),
				array(
					'field' => 'category_id',
					'label' => 'Category',
					'rules' => 'exists_null[category.category_id]',
				),
				array(
					'field' => 'manufacturer_id',
					'label' => 'Manufacturer',
					'rules' => 'exists_null[manufacturer.manufacturer_id]',
				),
				array(
					'field' => 'price',
					'label' => 'Product Price',
					'rules' => 'numeric',
				),
				array(
					'field' => 'status',
					'label' => 'Product Status',
					'rules' => 'status',
				),
				array(
					'field' => 'description',
					'label' => 'Product Description',
					'rules' => 'required',
				),
				array(
					'field' => 'default_image',
					'label' => 'Default Image',
					'rules' => ''
				)
			);

			$this->Product_model->set($id);
			$update = array();
			foreach ($fields as $field) {
				if ($this->input->post($field['field']) != $this->Product_model->{$field['field']}) {
					$this->form_validation->set_rules(array($field));
					$update[$field['field']] = $this->input->post($field['field']);

					if ($field['field'] == 'default_image' && !$update[$field['field']]) {
						$update[$field['field']] = null;
					}
				}
			}

			if (empty($update)) {
				$data['alert_message'] = 'Enter data you want to update.';
				$data['alert_class'] = 'alert-error';
			}

			if ($this->form_validation->run() == true) {
				if ($this->Product_model->update($update, $id)) {
					$data['alert_message'] = 'Your data has been updated successfully.';
					$data['alert_class'] = 'alert-success';
				} else {
					$data['alert_message'] = 'Something went wrong.';
					$data['alert_class'] = 'alert-error';
				}
			}
		}

		$data['product'] = $this->Product_model->set($id);
		$data['productImages'] = $this->Product_model->get_images();
		$this->load_view($data);
	}

	public function get_images($id)
	{
		$this->Product_model->set($id);

		$images = $this->Product_model->get_images();
		$this->output_json($images);
	}

	public function upload_image($id = null)
	{
		if (!isset($id)) {
			$id = $this->input->post('productID');
			$this->Product_model->set($id);

			$res = $this->Product_model->upload_image('image');
			$output = array('success' => true);
			if ($res !== true) {
				$output['success'] = false;
				$output['errors'] = $res;
			}

			$this->output_json($output);
			return;
		}

		$this->load->helper('form');
		$data['productID'] = $id;

		$this->load->view('/admin/product_upload_image', $data);
	}

	public function delete_image($id)
	{
		$this->Product_model->delete_image($id);
	}

	public function delete()
	{
		$list = $this->input->post('list');

		foreach ($list as $id) {
			$this->Product_model->delete($id);
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

		$products = $this->Product_model->fetch($params);

		$array = array(
			$products,
			array(
				$this->Product_model->pagecount,
				$this->Product_model->page,
				$this->Product_model->entries
			)
		);

		$this->output_json($array);
	}
}
