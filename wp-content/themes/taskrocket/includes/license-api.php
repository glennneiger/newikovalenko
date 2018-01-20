<?php

require_once("license-service.php");

/**
 * Created by PhpStorm.
 * User: matthewboddy
 * Date: 17/12/15
 * Time: 1:50 PM
 */
class TaskRocketLicenseApi {

    /**
     *  Hook WordPress
     */
    public function __construct() {

        add_option('license_last_queried_message', "");

        add_filter('query_vars', array($this, 'add_query_vars'), 0);
        add_action('parse_request', array($this, 'sniff_requests'), 0);
        add_action('init', array($this, 'add_endpoint'), 0);

    }

    /**
     *  Add public query vars
     *	@param array $vars List of current public query vars
     *	@return array $vars
     */
    public function add_query_vars($vars) {

        $vars[] = '__api';
        $vars[] = 'key';
        return $vars;

    }

    /**
     *  Add API Endpoint
     *	This is where the magic happens - brush up on your regex skillz
     *	@return void
     */
    public function add_endpoint() {

        add_rewrite_rule('^api/license/?(.*)?/?','index.php?__api=1&key=$matches[1]','top');

    }

    /**
     *  Sniff Requests
     *	This is where we hijack all API requests
     * 	If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness
     *	@return die if API request
     */
    public function sniff_requests() {

        global $wp;

        if(isset($wp->query_vars['__api'])){
            $this->handle_request();
            exit;
        }

    }

    /**
     *  Handle Requests
     *	This is where we send off for an intense pug bomb package
     *	@return void
     */
    protected function handle_request() {

        global $wp;

        $key = $wp->query_vars['key'];

        if ($key) {

            $response = TaskRocketLicenseService::CheckLicense($key);

            if (isset($response->result) AND $response->result == "error") {

                $message = __( "Your license is invalid", "taskrocket" );
                update_option('license_last_queried_message', $message);

                $this->send_response('200 OK', array(
                    "status" => "Error",
                    "message" => $message
                ));

            }

            switch ($response->status) {
                case "pending":

                    $message = __( "Your license is still pending", "taskrocket" );
                    update_option('license_last_queried_message', $message);

                    $this->send_response('200 OK', array(
                        "status" => "Pending",
                        "message" => $message
                    ));

                    break;

                case "blocked":

                    $message = __( "Your license is invalid", "taskrocket" );
                    update_option('license_last_queried_message', $message);

                    $this->send_response('200 OK', array(
                        "status" => "Blocked",
                        "message" => $message
                    ));

                    break;

                case "expired":

                    $message = __( "Your license has expired", "taskrocket" );
                    update_option('license_last_queried_message', $message);

                    $this->send_response('200 OK', array(
                        "status" => "Expired",
                        "message" => $message
                    ));

                    break;

                case "active":

                    if (TaskRocketLicenseService::IsValidForInstall($key)) {

                        $message = __( "Your license is valid", "taskrocket" );
                        update_option('license_last_queried_message', $message);

                        $this->send_response('200 OK', array(
                            "status" => "Active",
                            "message" => $message
                        ));

                    } else {

                        if (count($response->registered_domains) >= $response->max_allowed_domains) {

                            $message = __( "Your license has already been activated the maximum number of times", "taskrocket" );
                            update_option('license_last_queried_message', $message);

                            $this->send_response('200 OK', array(
                                "status" => "Error",
                                "message" => $message
                            ));

                        } else {

                            TaskRocketLicenseService::ActivateLicense($key);

                            $message = __( "Your license is valid", "taskrocket" );
                            update_option('license_last_queried_message', $message);

                            $this->send_response('200 OK', array(
                                "status" => "Active",
                                "message" => $message
                            ));

                        }

                        $message = __( "Your license is invalid for this install", "taskrocket" );
                        update_option('license_last_queried_message', $message);

                        $this->send_response('200 OK', array(
                            "status" => "Error",
                            "message" => $message
                        ));

                    }

                    break;

            }

        }

        $this->send_response(__( "Something went wrong", "taskrocket" ));

    }

    /** Response Handler
     *	This sends a JSON response to the browser
     */
    protected function send_response($msg, $data = ''){

        $response['message'] = $msg;
        $response['data'] = $data;

        header("Access-Control-Allow-Origin: *");
        header('content-type: application/json; charset=utf-8');
        echo json_encode($response)."\n";
        exit;

    }

    public static function processLicense($key) {

        if (!$key) {
            update_option('license_last_queried_message', __( "Make sure you've entered in your license correctly", "taskrocket" ));
            return false;
        }

        $response = TaskRocketLicenseService::CheckLicense($key);

        if (isset($response->result) AND $response->result == "error") {

            update_option('license_last_queried_message', __( "Your license is invalid", "taskrocket" ));

        }

        switch ($response->status) {
            case "pending":

                update_option('license_last_queried_message', __( "Your license is still pending", "taskrocket" ));

                break;

            case "blocked":

                update_option('license_last_queried_message', __( "Your license is invalid", "taskrocket" ));

                break;

            case "expired":

                update_option('license_last_queried_message', __( "Your license has expired", "taskrocket" ));

                break;

            case "active":

                if (count($response->registered_domains) < $response->max_allowed_domains) {

                    TaskRocketLicenseService::ActivateLicense($key);

                }

                if (TaskRocketLicenseService::IsValidForInstall($key)) {

                    update_option('license_last_queried_message', __( "Your license is valid", "taskrocket" ));

                } else {

                    if (count($response->registered_domains) >= $response->max_allowed_domains) {

                        update_option('license_last_queried_message', __( "Your license has already been activated the maximum number of times", "taskrocket" ));

                    } else {

                        TaskRocketLicenseService::ActivateLicense($key);

                        update_option('license_last_queried_message', __( "Your license is valid", "taskrocket" ));

                    }

                }

                break;

        }

    }

    /**
     * @return bool
     */
    public static function GetCurrentLicenseKey() {

        $settings = get_option('taskrocket_settings');

        if (isset($settings["license_key"]) AND strlen($settings["license_key"])) {

            return $settings["license_key"];

        }

        return false;

    }

    /**
     * @return array|bool|mixed|object
     */
    public static function GetCurrentLicense() {

        $key = TaskRocketLicenseApi::GetCurrentLicenseKey();

        if ($key) {

            return TaskRocketLicenseService::CheckLicense($key);

        }

        return false;

    }

    /**
     * @return array|bool|mixed|object
     */
    public static function GetIsCurrentLicenseValidForInstall() {

        $key = TaskRocketLicenseApi::GetCurrentLicenseKey();
        return TaskRocketLicenseService::IsValidForInstall($key);

    }

}
new TaskRocketLicenseApi();