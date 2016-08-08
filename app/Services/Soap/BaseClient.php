<?php

namespace WA\Services\Soap;

use Log;
use SoapClient;

/**
 * Class BaseClient.
 */
abstract class BaseClient
{
    protected $url;

    protected $client;

    protected $options = [];

    protected $errors = [];

  /**
   * Connect to the SOAP service.
   */
  public function connect()
  {
      try {
          return $this->client ?:
          $this->client = new SoapClient($this->url, ['trace' => true, 'exceptions' => true]);
      } catch (\Exception $e) {
          Log::error('There was an issue connection to the SOAP service '.$e->getMessage());
      }

      return false;
  }

  /**
   * Get the SOAP Client.
   *
   * @return Object of soap client
   */
  public function getClient()
  {
      return $this->client;
  }

  /**
   * Get the options on this client.
   *
   * @return array of client options
   */
  public function getOptions()
  {
      return $this->options;
  }

  /**
   * Get the error of there is any on the client.
   *
   * @return string error
   */
  public function getError()
  {
      return array_pop($this->errors);
  }
}
