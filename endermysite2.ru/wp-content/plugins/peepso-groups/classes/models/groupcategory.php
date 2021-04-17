<?php

class PeepSoGroupCategory
{
	protected static $_instance = NULL;

	// post data
	public $id;
	public $author_id;

	public $published;

	public $name;
	public $description;

	public $order;

	// post meta
	public $groups_count;

	// property
	private $post_data_map;
	private $meta_data_map;

	const POST_TYPE 		= 'peepso-group-cat';

	private $_table;

	/**
	 * PeepSoGroupCategory constructor
	 * @param $id
	 */
	public function __construct($id = NULL, $data = NULL)
	{
		// post data mapping
		$this->post_data_map = array(
			'id' 			=> 'ID',
			'author_id'		=> 'post_author',
			'name'			=> 'post_title',
			'description'	=> 'post_content',
			'published'		=> 'post_status',
			'order'			=> 'menu_order',
		);

		// post meta mapping
		$this->meta_data_map = array(
			'groups_count' 				=> 0,
		);

		// constructor is also able to handle group creation
		if( NULL == $id && is_array($data) ) {

			$this->id = $id = $this->create();

			if(isset($data['meta'])) {
				$meta = $data['meta'];

				if(is_array($meta)) {
					foreach($meta as $key=>$val) {
						add_post_meta($this->id, 'peepso_group_cat_' . $key, $val, TRUE);
					}
				}

				unset($data['meta']);
			}
			// Default the "order" to group_cat_id, ensures the new group category will be on the bottom
			$data['order'] = $this->id;
			// force open new category
			update_user_meta(get_current_user_id(), 'peepso_admin_group_category_open_' . $this->id, 1);
			$this->update($data);

			do_action('peepso_action_group_category_create', $this);
		}

		if(-1 == $id) {
			$this->id = -1;
			$this->name = __('Uncategorized', 'groupso');
			return;
		}

		// grabbing group by numeric id
		$args = array(
			'include'	=> array($id),
			'post_type' => self::POST_TYPE,
		);

		$args['post_status'] = 'any';

		$posts = get_posts($args);

		// category not found
		if(!count($posts)) {
			return FALSE;
		}

		// category found
		$post = $posts[0];

		// map wp_posts data to class properties
		foreach($this->post_data_map as $property => $post_key) {
			$this->$property = $post->$post_key;
		}

		// map postmeta data to class properties
		foreach($this->meta_data_map as $property=>$default) {
			$this->$property = get_post_meta($this->id, 'peepso_group_cat_'.$property, TRUE);
			// get_post_meta WILL RETURN AN EMPTY STRING if the key is not found
			if('' === $this->$property) {
				add_post_meta($this->id, 'peepso_group_cat_' . $property, $default, TRUE);
				$this->$property = $default;
			}
		}

		// Published flag 0/1
		$this->published = ('publish' == $this->published) ? TRUE : FALSE;
	}

	public function get($prop)
	{
		if(property_exists($this, $prop)) {
			return $this->$prop;
		}

		$method = "get_$prop";
		if(method_exists($this, $method)) {
			return $this->$method();
		}

		trigger_error("Unknown property/method $prop/$method");
	}


	/** ** ** ** ** CREATE & UPDATE ** ** ** ** **/

	/**
	 * Injects a new wp_post
	 * Called only from __construct()
	 * @return int|WP_Error
	 */
	private function create()
	{
		// insert the post, grab the ID
		$id = wp_insert_post( array( 'post_status'=>'publish', 'post_type' => self::POST_TYPE ) );
		return $id;
	}

	/**
	 * Updates post data and post meta
	 * @param array $data - key/value array of group properties
	 */
	public function update( $data )
	{
		$post_data = array(
			'ID' => $this->id,
		);

		foreach( $data as $key=>$value ) {

			if( property_exists($this, $key )) {
				// update self
				$this->$key = $value;

				// if the key belongs to post_data
				if(array_key_exists($key, $this->post_data_map)) {
					$post_key = $this->post_data_map[$key];
					$post_data[$post_key] = $value;
					continue;
				}

				// otherwise save in postmeta
				if(in_array($key, $this->meta_data_map)) {
					update_post_meta($this->id, 'peepso_group_cat_' . $key, $value);
					continue;
				}

			} else {
				trigger_error("Unknown property PeepSoGroup::$key", E_USER_NOTICE);
			}
		}

		if(count($post_data) > 1) {
			wp_update_post($post_data);
		}
	}


	public function delete($category) {

		wp_delete_post($category);

		return TRUE;
	}

	/** ** ** ** ** GETTERS & FORMATTING ** ** ** ** **/

	/**
	 * Utility - returns category URL
	 * @return string
	 */
	public function get_url()
	{
		return PeepSo::get_page('groups') . '?category=' . $this->id.'/';
	}
}
