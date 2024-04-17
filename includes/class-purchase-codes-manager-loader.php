<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://https://roymo.es/
 * @since      1.0.0
 *
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Purchase_Codes_Manager
 * @subpackage Purchase_Codes_Manager/includes
 * @author     Rommel & Montgomery <dev@roymo.es>
 */
class Purchase_Codes_Manager_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $actions    The actions registered with WordPress to fire when the plugin loads.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      array    $filters    The filters registered with WordPress to fire when the plugin loads.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->actions = array();
		$this->filters = array();

	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress action that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the action is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since    1.0.0
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         Optional. The priority at which the function should be fired. Default is 10.
	 * @param    int                  $accepted_args    Optional. The number of arguments that should be passed to the $callback. Default is 1
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks into a single
	 * collection.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @param    array                $hooks            The collection of hooks that is being registered (that is, actions or filters).
	 * @param    string               $hook             The name of the WordPress filter that is being registered.
	 * @param    object               $component        A reference to the instance of the object on which the filter is defined.
	 * @param    string               $callback         The name of the function definition on the $component.
	 * @param    int                  $priority         The priority at which the function should be fired.
	 * @param    int                  $accepted_args    The number of arguments that should be passed to the $callback.
	 * @return   array                                  The collection of actions and filters registered with WordPress.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {

		$hooks[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args
		);

		return $hooks;

	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		/*Create api rest*/
		add_action('rest_api_init', function(){
			register_rest_route('purchase-manager/v1', '/purchases/', [
				'methods' => 'GET',
				'callback' => function(){
					global $wpdb;
					$table = $wpdb->prefix . 'purchases';
					$query = "SELECT * FROM $table";
					$result = $wpdb->get_results($wpdb->prepare($query));

					$res = new WP_REST_Response($result);
					$res->set_status(200);

					return ['req' => $res];
				}
			]);
			register_rest_route('purchase-manager/v1', '/purchases/', [
				'methods' => 'POST',
				'callback' => function($req){
					global $wpdb;
					$response['name'] = $req['name'];
					$response['email'] = $req['email'];
					$response['phone'] = $req['phone'];
					$response['location'] = $req['location'];
					$response['shop'] = $req['shop'];
					
					if(!$req['medium'] || $req['medium'] != '1'){
						$res = new WP_REST_Response(['message' => 'Unauthorized']);
						$res->set_status(401);
						return ['req' => $res];
					}else{
						$table = $wpdb->prefix . 'purchases';
						
						$name = $req['name'];
						$email = $req['email'];
						$phone = $req['phone'];
						$location = $req['location'];
						$shop = $req['shop'];
						$draw_code = generate_random_code();
						$date_created = date('Y-m-d H:i:s');
						$date_redeemed = '';
						$purchase_invoice = '';


						$wpdb->insert(
							$table,
							[
								'name' => $name,
								'email' => $email,
								'phone' => $phone,
								'location' => $location,
								'shop' => $shop,
								'date_created' => $date_created,
								'draw_code' => $draw_code
							]
						);

						//POST al excel de make
						$params = [
							'name' => $name,
							'email' => $email,
							'phone' => $phone,
							'location' => $location,
							'shop' => $shop,
							'date_created' => $date_created,
							'date_redeemed' => $date_redeemed,
							'purchase_invoice' => $purchase_invoice,
							'draw_code' => $draw_code
						];
						$url = 'https://hook.eu1.make.com/2q1mvv2v2vjh6befgwdhxyn49d4rwfn4';

						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

						curl_exec($ch);
						curl_close($ch);
						
						//Respuesta del post correcta
						$res = new WP_REST_Response($response);
						$res->set_status(201);

						//Envia el email con el codigo
						send_mail($email, $name, $draw_code, $shop);

						return ['req' => $res];
					}
				}
			]);

			register_rest_route('purchase-manager/v1', '/purchase/(?P<code>[a-zA-Z0-9-]+)', [
				'methods' => 'GET',
				'callback' => function($req){
					global $wpdb;
					$code = $req['code'];
					$table = $wpdb->prefix . 'purchases';

					$query = "SELECT * FROM $table WHERE draw_code = '$code'";
					$result = $wpdb->get_results($wpdb->prepare($query));

					$res = new WP_REST_Response($result);
					$res->set_status(200);

					return ['req' => $res];
				}
			]);

			register_rest_route('purchase-manager/v1', '/purchase/(?P<code>[a-zA-Z0-9-]+)', [
				'methods' => 'PUT',
				'callback' => function($req){
					global $wpdb;
					$code = $req['code'];
					$nonce = $req['nonce'];
					$table = $wpdb->prefix . 'purchases';
					$datetime = date('Y-m-d H:i:s');

					if($nonce != 'dH9CeGem3LvAxkDz3N'){
						$res = new WP_REST_Response(['message' => 'Unauthorized']);
						$res->set_status(401);
						return ['req' => $res];
					}

					$query_select = "SELECT * FROM $table WHERE draw_code = '$code'";
					$result = $wpdb->get_results($wpdb->prepare($query_select));

					if($result[0]->purchase_invoice){
						$res = new WP_REST_Response(['message' => 'La factura ya esta asociada a un codigo']);
						$res->set_status(203);

						return ['req' => $res];
					}elseif(count($result) === 0){
						$res = new WP_REST_Response(['message' => 'La factura ya esta asociada a un codigo']);
						$res->set_status(404);

						return ['req' => $res];
					}else{
						$invoice = $req['purchase_invoice'];

						$query_update = "UPDATE $table SET purchase_invoice = $invoice, date_redeemed = '$datetime' WHERE draw_code = '$code'";
						$result2 = $wpdb->query($wpdb->prepare($query_update));

						$result[0]->purchase_invoice = $invoice;
						$result[0]->date_redeemed = $datetime;

						$params = [
							'name' => $result[0]->name,
							'email' => $result[0]->email,
							'phone' => $result[0]->phone,
							'location' => $result[0]->location,
							'shop' => $result[0]->shop,
							'date_created' => $result[0]->date_created,
							'date_redeemed' => $result[0]->date_redeemed,
							'purchase_invoice' => $result[0]->purchase_invoice,
							'draw_code' => $result[0]->draw_code
						];
						$url = 'https://hook.eu1.make.com/2q1mvv2v2vjh6befgwdhxyn49d4rwfn4';

						$ch = curl_init($url);
						curl_setopt($ch, CURLOPT_POST, true);
						curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));

						curl_exec($ch);
						curl_close($ch);

						$res = new WP_REST_Response($result);
						$res->set_status(201);

						return ['req' => $res];
					}
				}
			]);

		});
		/** ---------- */
	}
}

function generate_random_code(){
	global $wpdb;
	$characters = 'ABCDEFGHYJKLMNOPQRSTUVWXYZ';
	$numbers = '123456789';
	$table = $wpdb->prefix . 'purchases';

	$code = '';

	for($i = 0; $i < 5; $i++){
		$type = rand(0, 1);
		if($type === 0){
			$code = $code . $characters[rand(0, strlen($characters) - 1)];
		}else{
			$code = $code . $numbers[rand(0, strlen($numbers) - 1)];
		}
	}

	$exist = $wpdb->get_results($wpdb->prepare("SELECT draw_code FROM $table WHERE draw_code = $code"));

	if(count($exist) > 1){
		generate_random_code();
	}

	return $code;
}

function send_mail($email, $name, $code, $shop){
	$html = get_mail_content($name, $code, $shop);
	$message = $html ? $html : "Hola";

	wp_mail(
		$email,
		'dev@roymo.es',
		"$message",
		['Content-Type: text/html; charset=UTF-8'],
	);
}

function get_mail_content($name, $code, $shop){
	$mail = file_get_contents(plugin_dir_path(__FILE__) . '../admin/mail/html/email.html');
	$mail = str_replace('%draw_code%', $code, $mail);
	$mail = str_replace('%name%', $name, $mail);
	$mail = str_replace('%shop%', $shop, $mail);
	return $mail;
}
