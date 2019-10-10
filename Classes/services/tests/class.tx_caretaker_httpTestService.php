<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2009-2011 by n@work GmbH and networkteam GmbH
 *
 * All rights reserved
 *
 * This script is part of the Caretaker project. The Caretaker project
 * is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is a file of the caretaker project.
 * http://forge.typo3.org/projects/show/extension-caretaker
 *
 * Project sponsored by:
 * n@work GmbH - http://www.work.de
 * networkteam GmbH - http://www.networkteam.com/
 *
 * $Id$
 */

/**
 * Testservice to execute a HHTP request and to check the result.
 *
 * @author Martin Ficzel <martin@work.de>
 * @author Thomas Hempel <thomas@work.de>
 * @author Christopher Hlubek <hlubek@networkteam.com>
 * @author Tobias Liebig <liebig@networkteam.com>
 *
 */
class tx_caretaker_httpTestService extends tx_caretaker_TestServiceBase
{
    /**
     * Value Description
     *
     * @var string
     */
    protected $valueDescription = 'Milliseconds';

    /**
     * Service type description in human readable form.
     *
     * @var string
     */
    protected $typeDescription = 'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_service_description';

    /**
     * Template to display the test Configuration in human readable form.
     *
     * @var string
     */
    protected $configurationInfoTemplate = 'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_service_configuration';

    /**
     * (non-PHPdoc)
     *
     * @see caretaker/trunk/services/tx_caretaker_TestServiceBase#runTest()
     */
    public function runTest()
    {
        $timeWarning = $this->getTimeWarning();
        $timeError = $this->getTimeError();

        // request
        $requestQuery = $this->getRequestQuery();
        $requestMethod = $this->getRequestMethod();
        $requestData = $this->getRequestData();
        $requestUsername = $this->getRequestUsername();
        $requestPassword = $this->getRequestPassword();
        $requestPort = $this->getRequestPort();
        $requestUseragent = $this->getRequestUseragent();
        $requestReferer = $this->getRequestReferer();
        $requestProxy = $this->getRequestProxy();
        $requestProxyport = $this->getRequestProxyport();

        // response
        $expectedStatus = $this->getExpectedReturnCode();
        $expectedHeaders = $this->getExpectedHeaders();
        $expectedRegex = $this->getExpectedRegex();
        $expectedDateAge = $this->getExpectedDateAge();
        $expectedModifiedAge = $this->getExpectedModifiedAge();

        if (preg_match('/^https?:\/\/.*/', $requestQuery)) {
            $parsed_url = parse_url($requestQuery);
            $requestQuery = '?' . $parsed_url['query'];
        } else {
            $url = $this->getInstanceUrl();
            $parsed_url = parse_url($url);
        }

        if ($parsed_url['path'] == '' && strpos($requestQuery, '/') !== 0) {
            $parsed_url['path'] = '/';
        }

        if ($parsed_url['port'] && !$requestPort) {
            $requestPort = $parsed_url['port'];
        }

        if ($parsed_url['user'] && !$requestUsername) {
            $requestUsername = $parsed_url['user'];
        }

        if ($parsed_url['pass'] && !$requestPassword) {
            $requestPassword = $parsed_url['pass'];
        }

        $request_url = $parsed_url['scheme'] . '://' . $parsed_url['host'] . $parsed_url['path'] . $requestQuery;

        // no query
        if (!($expectedStatus && $request_url)) {
            return tx_caretaker_TestResult::create(tx_caretaker_Constants::state_undefined, 0, 'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_no_query');
        }

        // execute query
        list($time, $content, $info, $headers) = $this->executeCurlRequest($request_url, $timeError * 3, $requestPort, $requestMethod, $requestUsername, $requestPassword, $requestData, $requestProxy, $requestProxyport, $requestUseragent, $requestReferer);

        $submessages = array();

        // time-ERROR
        $resultState = tx_caretaker_Constants::state_ok;
        if ($timeError && $time > $timeError) {
            $resultState = tx_caretaker_Constants::state_error;
            $submessages[] = new tx_caretaker_ResultMessage(
                'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_time_error',
                array('time' => $time)
            );
        } // time-WARNING
        elseif ($timeWarning && $time > $timeWarning) {
            if ($resultState < tx_caretaker_Constants::state_warning) {
                $resultState = tx_caretaker_Constants::state_warning;
            }
            $submessages[] = new tx_caretaker_ResultMessage(
                'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_time_warning',
                array('time' => $time)
            );
        }

        // http-status check
        if (!in_array($info['http_code'], $expectedStatus)) {
            $resultState = $this->getErrorTypeOnFailure();
            $submessages[] = new tx_caretaker_ResultMessage(
                'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_state_error',
                array(
                    'state_returned' => $info['http_code'],
                    'state_expected' => implode(',', $expectedStatus),
                )
            );
        }

        // http-header check
        if (count($expectedHeaders) > 0) {
            $headerSuccess = true;
            foreach ($expectedHeaders as $headerName => $expectedValue) {
                $returnedValue = $headers[$headerName];
                if (!$returnedValue) {
                    $resultState = tx_caretaker_Constants::state_error;
                    $submessages[] = new tx_caretaker_ResultMessage(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_header_missing',
                        array('name' => $headerName)
                    );
                } else {
                    $partialSuccess = $this->checkSingleHeader($returnedValue, $expectedValue);
                    if ($partialSuccess == false) {
                        $headerSuccess = false;
                        $submessages[] = new tx_caretaker_ResultMessage(
                            'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_header_error',
                            array(
                                'name' => $headerName,
                                'expected' => $expectedValue,
                                'returned' => $returnedValue,
                            )
                        );
                    } else {
                        $submessages[] = new tx_caretaker_ResultMessage(
                            'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_header_ok',
                            array(
                                'name' => $headerName,
                                'expected' => $expectedValue,
                                'returned' => $returnedValue,
                            )
                        );
                    }
                }
            }
            if (!$headerSuccess) {
                $resultState = tx_caretaker_Constants::state_error;
            }
        }

        // regex  check
        if (count($expectedRegex) > 0) {
            $regexSuccess = true;
            foreach ($expectedRegex as $regex) {
                if (preg_match($regex, $content)) {
                    $submessages[] = new tx_caretaker_ResultMessage(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_regex_ok',
                        array(
                            'regex' => htmlspecialchars($regex),
                        )
                    );
                } else {
                    $regexSuccess = false;
                    $submessages[] = new tx_caretaker_ResultMessage(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_regex_error',
                        array(
                            'regex' => htmlspecialchars($regex),
                        )
                    );
                }
            }
            if (!$regexSuccess) {
                $resultState = tx_caretaker_Constants::state_error;
            }
        }

        // date header check
        if ($expectedDateAge) {
            $expectedDate = 'Age:<' . $expectedDateAge;
            $returnedDate = $headers['Date'];
            if (!$returnedDate) {
                $resultState = tx_caretaker_Constants::state_error;
                $submessages[] = new tx_caretaker_ResultMessage(
                    'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_header_missing',
                    array('name' => 'Date')
                );
            } else {
                $partialSuccess = $this->checkSingleHeader($returnedDate, $expectedDate);
                if (!$partialSuccess) {
                    $resultState = tx_caretaker_Constants::state_error;
                    $submessages[] = new tx_caretaker_ResultMessage(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_date_error',
                        array(
                            'plain' => $returnedDate,
                            'parsed' => strftime('%X %x', $this->parseHeaderDate($returnedDate)),
                            'max_age' => $expectedDateAge,
                        )
                    );
                }
            }
        }

        // modified header check
        if ($expectedModifiedAge) {
            $expectedDate = 'Age:<' . $expectedModifiedAge;
            $returnedDate = $headers['Last-Modified'];
            if (!$returnedDate) {
                $resultState = tx_caretaker_Constants::state_error;
                $submessages[] = new tx_caretaker_ResultMessage(
                    'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_header_missing',
                    array('name' => 'Last-Modified')
                );
            } else {
                $partialSuccess = $this->checkSingleHeader($returnedDate, $expectedDate);
                if (!$partialSuccess) {
                    $resultState = tx_caretaker_Constants::state_error;
                    $submessages[] = new tx_caretaker_ResultMessage(
                        'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_modified_error',
                        array(
                            'plain' => $returnedDate,
                            'parsed' => strftime('%X %x', $this->parseHeaderDate($returnedDate)),
                            'max_age' => $expectedModifiedAge,
                        )
                    );
                }
            }
        }

        $message = '';
        $values = array('url' => $request_url, 'time' => $time, 'state' => $info['http_code']);
        switch ($resultState) {
            case tx_caretaker_Constants::state_error:
                $message = 'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_error';
                break;
            case tx_caretaker_Constants::state_warning:
                $message = 'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_warning';
                break;
            case tx_caretaker_Constants::state_ok:
                $message = 'LLL:EXT:caretaker/Resources/Private/Language/locallang.xlf:http_ok';
                break;
        }

        // Return
        return tx_caretaker_TestResult::create($resultState, $time, new tx_caretaker_ResultMessage($message, $values), $submessages);
    }

    /**
     * compare returned http headers with expected values
     *
     * @param string $headerValue
     * @param string $expectedValue
     * @return bool
     */
    protected function checkSingleHeader($headerValue, $expectedValue)
    {
        if (!$headerValue || strlen(trim($headerValue)) == 0) {
            return false;
        }

        // replace values in expectd headers
        if (strpos($expectedValue, '###') !== false) {
            $instanceUrl = $this->getInstanceUrl();
            $instanceUrlParts = explode('/', str_replace('://', '/', $instanceUrl));
            $instanceProtocol = array_shift($instanceUrlParts);
            $instanceHostname = array_shift($instanceUrlParts);
            $instanceQuery = implode('/', $instanceUrlParts);
            $requestQuery = $this->getRequestQuery();

            $expectedValue = str_replace('###INSTANCE_URL###', $instanceUrl, $expectedValue);
            $expectedValue = str_replace('###INSTANCE_PROTOCOL###', $instanceProtocol, $expectedValue);
            $expectedValue = str_replace('###INSTANCE_HOSTNAME###', $instanceHostname, $expectedValue);
            $expectedValue = str_replace('###INSTANCE_QUERY###', $instanceQuery, $expectedValue);
            $expectedValue = str_replace('###REQUEST_QUERY###', $requestQuery, $expectedValue);
        }

        $result = true;

        // = Value equals
        if (strpos($expectedValue, '=') === 0) {
            $result = ($headerValue == trim(substr($expectedValue, 1)));
        } // < Intval smaller than
        elseif (strpos($expectedValue, '<') === 0) {
            $result = (intval($headerValue) < intval(substr($expectedValue, 1)));
        } // > Intval bigger than
        elseif (strpos($expectedValue, '>') === 0) {
            $result = (intval($headerValue) > intval(substr($expectedValue, 1)));
        } // AGE<
        elseif (strpos($expectedValue, 'Age:<') === 0) {
            $age = intval(substr($expectedValue, 5));
            $headerTimestamp = $this->parseHeaderDate($headerValue);
            $result = ($headerTimestamp >= time() - $age);
        } // AGE>
        elseif (strpos($expectedValue, 'Age:>') === 0) {
            $age = intval(substr($expectedValue, 5));
            $headerTimestamp = $this->parseHeaderDate($headerValue);
            $result = ($headerTimestamp < time() - $age);
        } // default
        else {
            $result = ($headerValue == $expectedValue);
        }

        return $result;
    }

    /**
     * @param string $datestring
     * @return string
     */
    public function parseHeaderDate($datestring)
    {
        $date = \DateTime::createFromFormat(\DateTime::RFC1123, $datestring);
        return $date->format('U');
    }

    /**
     * Get the maximal time before WARNING
     *
     * @return int
     */
    protected function getTimeWarning()
    {
        return intval($this->getConfigValue('max_time_warning'));
    }

    /**
     * Get the maximal time before ERROR
     *
     * @return int
     */
    protected function getTimeError()
    {
        return intval($this->getConfigValue('max_time_error'));
    }

    /**
     * Get the error type that is returned if the http status check is not successful
     *
     * @return int
     */
    protected function getErrorTypeOnFailure()
    {
        switch ($this->getConfigValue('error_type_on_failure', false, 'sResponse')) {
            case 'WARNING':
                return tx_caretaker_Constants::state_warning;
                break;
            case 'UNDEFINED':
                return tx_caretaker_Constants::state_undefined;
                break;
            default:
                return tx_caretaker_Constants::state_error;
                break;
        }
    }

    /**
     * Get the expected http status code from the test configuration
     *
     * @return array
     */
    protected function getExpectedReturnCode()
    {
        return explode(',', $this->getConfigValue('expected_status', false, 'sResponse'));
    }

    /**
     * Get the expected maximum age of the Last-Modified Header in Seconds
     *
     * @return int
     */
    protected function getExpectedDateAge()
    {
        return intval($this->getConfigValue('expected_date_age', false, 'sResponse'));
    }

    /**
     * Get the expected maximum age of the Date Header in Seconds
     *
     * @return int
     */
    protected function getExpectedModifiedAge()
    {
        return intval($this->getConfigValue('expected_modified_age', false, 'sResponse'));
    }

    /**
     * Get the expected headers from the test configuration
     *
     * @return array an associative Array with headers as key and expectec values as sting value
     */
    protected function getExpectedHeaders()
    {
        $expectedHeaders = array();
        $expectedHeadersConfiguration = $this->getConfigValue('expected_headers', false, 'sResponse');
        if ($expectedHeadersConfiguration) {
            $configurationLines = explode(chr(10), $expectedHeadersConfiguration);
            foreach ($configurationLines as $configurationLine) {
                list($headerName, $headerValue) = explode(':', $configurationLine, 2);
                $headerName = trim($headerName);
                $headerValue = trim($headerValue);
                if ($headerName && $headerValue) {
                    $expectedHeaders[$headerName] = $headerValue;
                }
            }
        }

        return $expectedHeaders;
    }

    /**
     * Get the expected headers from the test configuration
     *
     * @return array an associative Array with headers as key and expected values as string value
     */
    protected function getExpectedRegex()
    {
        $expectedRegex = array();
        $expectedRegexConfiguration = $this->getConfigValue('expected_regex', false, 'sResponse');
        if ($expectedRegexConfiguration) {
            $expectedRegex = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(chr(10), $expectedRegexConfiguration, true);
        }

        return $expectedRegex;
    }

    /**
     * Get the url path and query for the request
     *
     * @return string
     */
    protected function getRequestQuery()
    {
        return $this->getConfigValue('request_query');
    }

    /**
     * Get the HTTP-Method for the Request
     *
     * @return string
     */
    protected function getRequestMethod()
    {
        return $this->getConfigValue('request_method', false, 'sRequest');
    }

    /**
     * Get the port for the HTTP-request
     *
     * @return string
     */
    protected function getRequestPort()
    {
        return $this->getConfigValue('request_port', false, 'sRequest');
    }

    /**
     * Get the username for the HTTP-request
     *
     * @return string
     */
    protected function getRequestUsername()
    {
        return $this->getConfigValue('request_username', false, 'sRequest');
    }

    /**
     * Get the password for the HTTP-request
     *
     * @return string
     */
    protected function getRequestPassword()
    {
        return $this->getConfigValue('request_password', false, 'sRequest');
    }

    /**
     *
     * @return string
     */
    protected function getRequestData()
    {
        return $this->getConfigValue('request_data', false, 'sRequest');
    }

    /**
     * @return string
     */
    protected function getRequestUseragent()
    {
        return $this->getConfigValue('request_useragent', '', 'sRequest');
    }

    /**
     * @return string
     */
    protected function getRequestReferer()
    {
        return $this->getConfigValue('request_referer', '', 'sRequest');
    }

    /**
     * Get the Proxy for the HTTP-request
     *
     * @return string
     */
    protected function getRequestProxy()
    {
        return $this->getConfigValue('request_proxy', false, 'sProxy');
    }

    /**
     * Get the Proxy for the HTTP-request
     *
     * @return string
     */
    protected function getRequestProxyport()
    {
        return $this->getConfigValue('request_proxyport', false, 'sProxy');
    }

    /**
     *
     * @return string
     */
    protected function getInstanceUrl()
    {
        return $this->instance->getUrl();
    }

    /**
     *
     * @return string
     */
    protected function getInstanceHostname()
    {
        return $this->instance->getUrl();
    }

    /**
     *
     * @param string $request_url
     * @param int $timeout curl http-timeout
     * @param mixed $request_port curl request http-port
     * @param string $request_method
     * @param string $request_username
     * @param string $request_password
     * @param string $request_data
     * @param bool|string $request_proxy
     * @param bool|string $request_proxyport
     * @param string $request_useragent
     * @param string $request_referer
     * @return array time in seconds and status information im associatie arrays
     */
    protected function executeCurlRequest($request_url, $timeout = 0, $request_port = false, $request_method = 'GET', $request_username = '', $request_password = '', $request_data = '', $request_proxy = false, $request_proxyport = false, $request_useragent = '', $request_referer = '')
    {
        $curl = curl_init();

        // url & timeout
        curl_setopt($curl, CURLOPT_URL, $request_url);
        if ($timeout > 0) {
            curl_setopt($curl, CURLOPT_TIMEOUT, (int)ceil($timeout / 1000));
        }

        // username & password
        if ($request_username && $request_password) {
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($curl, CURLOPT_USERPWD, $request_username . ':' . $request_password);
        }

        // user agent
        if (!empty($request_useragent)) {
            curl_setopt($curl, CURLOPT_USERAGENT, $request_useragent);
        }

        // referer
        if (!empty($request_referer)) {
            curl_setopt($curl, CURLOPT_REFERER, $request_referer);
        }

        // port
        if ($request_port && $request_port != '') {
            curl_setopt($curl, CURLOPT_PORT, $request_port);
        }

        // proxy server
        if ($request_proxy && $request_proxyport) {
            curl_setopt($curl, CURLOPT_PROXY, $request_proxy);
            curl_setopt($curl, CURLOPT_PROXYPORT, $request_proxyport);
        }

        // add instance curl options
        $instanceCurlOptions = $this->instance->getCurlOptions();
        if (is_array($instanceCurlOptions)) {
            foreach ($instanceCurlOptions as $key => $value) {
                curl_setopt($curl, $key, $value);
            }
        }

        // handle request method
        switch ($request_method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($$request_data)));
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($$request_data)));
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request_data);
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // execute request
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        list($headerText, $content) = preg_split('/\n[\s]*\n/', $response, 2);

        // split headers
        $headers = array();
        if ($headerText) {
            $headerLines = explode(chr(10), $headerText);
            foreach ($headerLines as $headerLine) {
                list($headerName, $headerValue) = explode(':', $headerLine, 2);
                $headerName = trim($headerName);
                $headerValue = trim($headerValue);
                if ($headerName && $headerValue) {
                    $headers[$headerName] = $headerValue;
                }
            }
        }

        $time = $info['total_time'] * 1000;

        return array($time, $content, $info, $headers);
    }
}
