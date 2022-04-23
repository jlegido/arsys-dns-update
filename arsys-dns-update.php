<?php

require_once('nusoap/src/nusoap.php');

class ArsysDnsUpdate {

    function __construct($domain, $password, $dns, $currentType, $newType) {
        $this->domain = $domain;
        $this->password = $password;
        $this->dns = $dns;
        $this->currentType = $currentType;
        $this->newType = $newType;
        $this->url = 'https://api.servidoresdns.net:54321/hosting/api/soap/index.php';
    }

    function _getIpFromDns($dns) {
        $function = 'InfoDNSZone';
        $params = array('domain' => $this->domain, 'dns' => $this->dns, 'type' => $this->currentType);
        $response = $this->_apiCall($function, $params);
        if (empty($response['res']['data'])) {
            print 'DNS record' . $dns . " does not exists yet\n";
            return False;
        }
        else {
            return $response['res']['data'][0]['value'];
        }
    }

    function _isChangedIp() {
        # There's a wildcard so if currentValue = 91.121.173.87 DNS does NOT really exists
	$currentValue = $this->_getIpFromDns($this->dns);
        $newValue = $this->_getCurrentIp();
        if ($currentValue != $newValue) {
            return True;
        }
        return False;
    }

    function modifyDNSEntry() {
        if ($this->_isChangedIp()) {
            print "IP Changed\n";
            $currentValue = $this->_getIpFromDns($this->dns);
            $newValue = $this->_getCurrentIp();
            $function = 'ModifyDNSEntry'; # <---*** FUNCION ***
            $params = array('domain' => $this->domain, 'dns' => $this->dns, 'currenttype' => $this->currentType, 'currentvalue' => $currentValue, 'newtype' => $this->newType, 'newvalue' => $newValue);
            $response = $this->_apiCall($function, $params);
            if ($response['errorCode'] == '-3') {
                print "DNS entry not found\n";
                $function = 'CreateDNSEntry';
                $params = array('domain' => $this->domain, 'dns' => $this->dns, 'type' => $this->currentType, 'value' => $newValue);
                $response = $this->_apiCall($function, $params);
                print "DNS created\n";
            }
            else {
                print "DNS updated\n";
                return $response;
            }
        }
        else {
            print "Same DNS, no action needed\n";
            return array();
        }
    }

    function _apiCall($function, $params) {
        $client = new soapclient($this->url, false);
        $client->setCredentials($this->domain, $this->password);
        $client->response_timeout = 1800;
        return $client->call($function, array('input' => $params));
    }

    function _getCurrentIp() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'ifconfig.me');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

}

?>
