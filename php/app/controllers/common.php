<?php

class Common {

    private $curl;

    public function __construct(array $path) {
        // Set the target URL you want to proxy
        $targetUrl = $this->getTargetUrl($path);

        // Execute the proxy request
        $response = $this->executeProxyRequest($targetUrl);

        // Output the response to the client
        $this->outputResponse($response);
    }

    private function getTargetUrl(array $path) {
        return isset($path[1]) ? implode('/', array_slice($path, 1)) : exit('No target specified');
    }

    private function executeProxyRequest(string $targetUrl) {
        // Create a new cURL resource
        $this->curl = curl_init();

        // Set the options for the cURL request
        curl_setopt($this->curl, CURLOPT_URL, $targetUrl);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false); // Use this only for testing, not recommended in production

        $cookies = $_SERVER["HTTP_COOKIE"] ?? '';
        $headers = [];

        if ( !empty($cookies) ) $headers = ["Cookie: $cookies"];

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $headers = $this->addPostHeaders($headers, $cookies);
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->getPostPayload());
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL request
        $response = curl_exec($this->curl);

        // Check for errors
        if ( $response === false ) exit('Error: ' . curl_error($this->curl));

        return $response;
    }

    private function addPostHeaders(array $headers, string $cookies) {
        $contentType = $_SERVER['CONTENT_TYPE'];
        $headers[] = 'Content-Type: ' . $contentType;

        $cookieHeader = $this->findCookieHeader("MoodleSession", $cookies);
        if ( $cookieHeader !== false ) $headers[0] = "Cookie: $cookieHeader";

        return $headers;
    }

    private function findCookieHeader(string $cookieName, string $cookies) {
        foreach (explode(';', $cookies) as $cookie) {
            if (strpos($cookie, "$cookieName=") !== false) {
                return $cookie;
            }
        }
        return false;
    }

    private function getPostPayload() {
        return file_get_contents('php://input');
    }

    private function outputResponse(string $response) {
        // Get the response headers
        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $responseHeaders = substr($response, 0, $headerSize);

        // Output the response headers
        header('Content-Type: ' . curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE));
        $this->outputCookiesToClient($responseHeaders);

        // Output the response body content
        $responseBody = substr($response, $headerSize);
        echo $responseBody; ob_flush(); flush(); exit;
    }

    private function outputCookiesToClient(string $responseHeaders) {
        preg_match_all('/^Set-Cookie:\s*([^;]*)(;\s*secure)?/mi', $responseHeaders, $matches);
        if ( !empty($matches[1]) ) {
            $cookies = $matches[1];
            $cookieValues = [];

            // Filter and store the values of MoodleSession cookies
            foreach ($cookies as $cookie) {
                if (stripos($cookie, "MoodleSession=") !== false) {
                    $cookieValue = substr($cookie, strpos($cookie, "=") + 1);
                    $cookieValues[] = $cookieValue;
                } else {
                    header("Set-Cookie: $cookie; path=/", false);
                }
            }

            // Set the first occurrence of MoodleSession cookie
            if ( !empty($cookieValues) ) {
                $firstCookieValue = reset($cookieValues);
                header("Set-Cookie: MoodleSession=$firstCookieValue; path=/", false);
            }
        }
    }

}
