<?php
namespace LMMS;

use Github\HttpClient\HttpClientInterface;
use Github\HttpClient\CachedHttpClient;

require_once($_SERVER['DOCUMENT_ROOT'].'/../vendor/autoload.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/feed/json_common.php');

// This CachedHttpClient subclass catches exceptions of requests and tries to return a cache object in case of failure.
class SafeCachedHttpClient extends \Github\HttpClient\CachedHttpClient
{
	public function request($path, $body = null, $httpMethod = 'GET', array $headers = array(), array $options = array())
	{
		$cache = $this->getCache();

		try {
			return parent::request($path, $body, $httpMethod, $headers, $options);
		} catch (Exception $e) {
			if ($cache->has($path)) {
				return $cache->get($path);
			} else {
				throw $e;
			}
		}
	}
}

class GitHubClient extends \Github\Client
{
	public function __construct(HttpClientInterface $httpClient = null)
	{
		parent::__construct($httpClient);
		$client_id = get_base64_secret('GITHUB_CLIENT_ID');
		$client_secret = get_base64_secret('GITHUB_CLIENT_SECRET');
		$this->authenticate($client_id, $client_secret, \Github\Client::AUTH_URL_CLIENT_ID);
	}
}