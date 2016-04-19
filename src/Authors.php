<?php namespace Lean\Endpoints;

use Leean\AbstractCollectionEndpoint;
use Lean\Endpoints\Authors\Filter;

/**
 * Class that returns a collection of posts using dynamic arguments.
 *
 * @package Lean\Endpoints;
 */
class Authors extends AbstractCollectionEndpoint {

	/**
	 * Path of the new endpoint.
	 *
	 * @Override
	 *
	 * @since 0.1.0
	 * @var String
	 */
	protected $endpoint = '/authors';

	/**
	 * Object that holds the current queried object on the site.
	 *
	 * @since 0.1.0
	 * @var \WP_Query
	 */
	protected $query = null;

	/**
	 * Flag used to carry the value of the filter and avoid to call the function
	 * N times inside of the loop.
	 *
	 * @since 0.1.0
	 * @var bool
	 */
	protected $format_item = false;

	/**
	 * WP_Query Loop that has been triggered from the endpoint.
	 *
	 * @return array An array with the data associated with the request.
	 */
	protected function loop() {
		global $wpdb;

		// Default number of users to display per page.
		if ( ! isset( $this->args['number'] ) ) {
			$this->args['number'] = get_option( 'posts_per_page', 10 );
		}

		// By default we show non-admins.
		$this->args['meta_key'] = $wpdb->prefix . 'user_level';
		$this->args['meta_compare'] = '<';
		$this->args['meta_value'] = '10';

		// Loop through the data.
		$data = [];
		$this->query = new \WP_User_Query(
			apply_filters( Filter::QUERY_ARGS, $this->args )
		);
		if ( $this->query->total_users > 0 ) {
			foreach ( $this->query->results as $user ) {
				$data[] = $this->format_item( $user );
			}
		}

		return [
			'data' => $data,
			'pagination' => $this->get_pagination(
				$this->query->total_users,
				ceil( $this->query->total_users / $this->args['number'] )
			),
		];
	}

	/**
	 * This function allow to format every item that is returned to the endpoint
	 * the filter sends 3 params to the user so can be more easy to manipulate the
	 * data based on certain params.
	 *
	 * @param object $user User object.
	 * @return array The formated data from every item.
	 */
	protected function format_item( $user ) {

		$item = $user->data;
		$item->roles = $user->roles;

		return apply_filters( Filter::ITEM_FORMAT, $item, $user, $this->args );
	}
}
