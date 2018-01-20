<?php

/**
 * Created by PhpStorm.
 * User: matthewboddy
 * Date: 24/11/15
 * Time: 2:54 PM
 */
class TaskRocketLicenseService {

    /**
     * Activation or Deactivation API secret key
     */
    private static $ValidationSecretKey = "563931a7aaebe3.67050558";

    /**
     * This is the URL where API query request will be sent to. This should be the URL of the site where you have installed the main license manager plugin. Get this value from the integration help page.
     */
    private static $LicenseServerURL = "https://taskrocket.info";

    /**
     * This is a value that will be recorded in the license manager data so you can identify licenses for this item/product.
     */
    private static $Reference = "Task Rocket";

    /**
     * @param $params
     * @return array|WP_Error
     */
    private static function QueryLicenseServer($params) {

        $queryParams = array_merge(
            array(
                'secret_key' => self::$ValidationSecretKey,
                'registered_domain' => $_SERVER['SERVER_NAME'],
                'item_reference' => urlencode(self::$Reference)
            ),
            $params
        );

        $query = add_query_arg(
            $queryParams,
            self::$LicenseServerURL
        );

        return wp_remote_get($query, array(
            'timeout' => 20,
            'sslverify' => false
        ));

    }

    private static function ParseResponse($response) {

        if (is_wp_error($response)) {

            return false;
        }

        return json_decode(
            wp_remote_retrieve_body(
                $response
            )
        );

    }

    public static function ActivateLicense($license) {

        $params = array(
            'slm_action' => 'slm_activate',
            'license_key' => $license,
        );

        $response = self::QueryLicenseServer($params);

        $data = self::ParseResponse($response);

        if ($data) {

            return $data;

        } else {

            _e( "Unexpected Error! The query returned with an error", "taskrocket" );

        }

    }

    public static function DeactivateLicense($license) {

        $params = array(
            'slm_action' => 'slm_deactivate',
            'license_key' => $license,
        );

        $response = self::QueryLicenseServer($params);

        $data = self::ParseResponse($response);

        if ($data) {

            return $data;

        } else {

            _e( "Unexpected Error! The query returned with an error", "taskrocket" );

        }

    }

    public static function CheckLicense($license, $forceRefresh = false) {

        $refreshInterval = 86400;

        $lastKeyQueried = get_option("license_server_last_key_queried");
        $lastQueried = get_option("license_server_last_queried");
        $currentTime = current_time("mysql");

        if (
            $forceRefresh OR
            !$lastQueried OR
            $lastKeyQueried != $license OR
            (strtotime($currentTime) - strtotime($lastQueried)) > $refreshInterval)
        {

            $params = array(
                'slm_action' => 'slm_check',
                'license_key' => $license,
            );

            $response = self::QueryLicenseServer($params);

            $data = self::ParseResponse($response);

            if (!$data) {
                _e( "Unexpected Error! The query returned with an error", "taskrocket" );
            }

            update_option("license_server_cached_response", $data);
            update_option("license_server_last_queried", $currentTime);

        }

        update_option("license_server_last_key_queried", $license);

        return get_option("license_server_cached_response");

    }

    public static function IsValidForInstall($license) {

        $response = TaskRocketLicenseService::CheckLicense($license);

        if (empty($response->registered_domains)) { // no domains have been registered yet.

            return true;

        } else {

            foreach ($response->registered_domains as $domain) {

                $siteUrl = parse_url(get_site_url());

                if ($siteUrl['host'] == $domain->registered_domain) {

                    return true;

                }

            }

        }

    }

}