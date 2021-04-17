<?php

class PeepSoGroupCategoryAjax extends PeepSoAjaxCallback
{
	public function create(PeepSoAjaxResponse $resp) 
	{
		if (!PeepSo::is_admin()) {
			$resp->success(FALSE);
			$resp->error(__('Insufficient permissions.', 'groupso'));
			return;
		}

		$default_category_name = __('Untitled Category', 'groupso');

		$group_cat_data = array(
			'name'			=> $default_category_name,
			'description'	=> $default_category_name,
			'author_id'		=> get_current_user_id(),
			'published'		=> 'publish',
			'order'			=> $this->_input->int('order', 0),
		);

		// $errors = PeepSoGroupCategory::validate($group_data);

		// if(count($errors)){
		// 	$resp->error($errors);
		// 	return( FALSE );
		// }

		// respect line breaks
		// $description = $this->_input->value('description', '', FALSE); // SQL safe
		// $description = htmlspecialchars($description);
		// $group_data['description'] = trim(PeepSoSecurity::strip_content($description));

		$group_cat = new PeepSoGroupCategory(NULL, $group_cat_data);

		// Prepare HTML output
		ob_start();
		PeepSoTemplate::exec_template('admin','group_categories', array('category'=>$group_cat,'force_open' => 1));
		$html = ob_get_clean();

		// Set response
		$resp->set('id', $group_cat->get('id'));
		$resp->set('html', $html);
		$resp->success(TRUE);
	}

	public function delete(PeepSoAjaxResponse $resp)
	{
		if (!PeepSo::is_admin()) {
			$resp->success(FALSE);
			$resp->error(__('Insufficient permissions.', 'groupso'));
			return;
		}
		
		$category = $this->_input->int('id');
		$group_cat = new PeepSoGroupCategory($category);
		if(FALSE === $group_cat) 
		{
			return;
		}

		$group_cat->delete($category);
		$resp->success(TRUE);
	}

	public function set_prop(PeepSoAjaxResponse $resp)
	{
		if (!PeepSo::is_admin()) {
			$resp->success(FALSE);
			$resp->error(__('Insufficient permissions.', 'groupso'));
			return;
		}

		$id = $this->_input->int('id');
		$prop = $this->_input->value('prop', '', FALSE); // SQL Safe
		$value = $this->_input->value('value', '', FALSE); // SQL Safe

		$category = $this->_input->int('id');
		$group_cat = new PeepSoGroupCategory($category);
		if(FALSE === $group_cat) 
		{
			return;
		}

		$data[$prop] = $value;
		$group_cat->update($data);
		$resp->success(TRUE);
	}

	public function set_meta(PeepSoAjaxResponse $resp)
	{
		if (!PeepSo::is_admin()) {
			$resp->success(FALSE);
			$resp->error(__('Insufficient permissions.', 'groupso'));
			return;
		}

		$id = $this->_input->int('id');
		$prop = $this->_input->value('prop', '', FALSE); // SQL Safe

		$value = $this->_input->value('value', '', FALSE); // SQL safe

		if(1 == $this->_input->int('json',0)) {
			$value = htmlspecialchars_decode($value);
			$value = json_decode($value, TRUE);
		}

		$key = $this->_input->value('key', NULL, FALSE); // SQL Safe

		$meta_value = get_post_meta($id, $prop, 1);

		if( NULL !== $key) {
			if(!is_array($meta_value)) {
				$meta_value = array();
			}
			$meta_value[$key] = $value;
		} else {
			$meta_value = $value;
		}

		update_post_meta($id, $prop, $meta_value);
		$resp->success(TRUE);
	}

	public function set_order(PeepSoAjaxResponse $resp)
	{
		if (!PeepSo::is_admin()) {
			$resp->success(FALSE);
			$resp->error(__('Insufficient permissions.', 'groupso'));
			return;
		}

		// SQL safe
		if( $categories = json_decode($this->_input->value('group_category', '', FALSE)) ) {
			$i = 1;
			foreach( $categories as $category ) {
				$group_cat = new PeepSoGroupCategory($category);
				if(FALSE !== $group_cat) 
				{
					$data['order'] = $i;
					$group_cat->update($data);
					$i++;
				}
			}
		}

		$resp->success(TRUE);
	}

	public function set_admin_box_status(PeepSoAjaxResponse $resp)
	{
		if (!PeepSo::is_admin()) {
			$resp->success(FALSE);
			$resp->error(__('Insufficient permissions.', 'groupso'));
			return;
		}

		$id 	= $this->_input->int('id');
		$status = $this->_input->int('status', 0);

		$id = json_decode($id);

		foreach($id as $field_id) {
			update_user_meta(get_current_user_id(), 'peepso_admin_group_category_open_' . $field_id, $status);
		}

		$resp->success(TRUE);
	}
}

// EOF
