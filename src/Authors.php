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
	 * WP_Query Loop that has been triggered from the endpoint.
	 *
	 * @return array An array with the data associated with the request.
	 */
	protected function loop() {
		$data = [];

		$this->args['number'] = get_option( 'posts_per_page', 10 );

		$this->query = new \WP_User_Query( $this->args );
		if ( $this->query->total_users > 0 ) {
			foreach ( $this->query->results as $user ) {
				$data[] = $this->format_item( $user );
			}
		}

		return [
			'data' => $data,
			'pagination' => $this->get_pagination(),
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

	/**
	 * Returns the data related with the pagination, useful to
	 * iterate over the data in the FE on a infinite scroll or load more
	 * buttons since we know if there are more pages ahead.
	 *
	 * @return array The array with the formated data.
	 */
	protected function get_pagination() {
		$total = absint( $this->query->total_users );
		$meta = [
			'items' => $total,
			'pages' => 0,
		];
		if ( $total > 0 ) {
			$meta['pages'] = ceil( $this->query->total_users / $this->args['number'] );
		}
		return $meta;
	}
}
