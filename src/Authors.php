<?php namespace Lean\Endpoints;

use Leean\AbstractEndpoint;
use Lean\Endpoints\Authors\Filter;

/**
 * Class that returns a collection of posts using dynamic arguments.
 *
 * @package Lean\Endpoints;
 */
class Authors extends AbstractEndpoint {

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
	 * Array that holds all the shared arguments used for query the site.
	 *
	 * @since 0.1.0
	 * @var array
	 */
	protected $args = [];

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
	 * Function inherint from the parant Abstract class that is called once the
	 * endpoint has been initiated and the method that returns the data delivered
	 * to the endpoint.
	 *
	 * @Override
	 *
	 * @since 0.1.0
	 *
	 * @param \WP_REST_Request $request The request object that mimics the request
	 *									made by the user.
	 * @return array The data to be delivered to the endpoint
	 */
	public function endpoint_callback( \WP_REST_Request $request ) {
		$this->args = $request->get_params();
		return $this->filter_data( $this->loop() );
	}

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

	/**
	 * Clean up and make sure we don't deliver post with password, privates and
	 * some other datat that might be sensible on the API. This medhod overrides
	 * the default mechanism inherint from the parent class.
	 *
	 * @Override
	 *
	 * @since 0.1.0
	 * @return array an array with the accepted arguments and options per each argument.
	 */
	public function endpoint_args() {
		return [];
	}
}
