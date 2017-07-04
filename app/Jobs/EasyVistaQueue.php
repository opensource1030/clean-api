<?php

namespace WA\Jobs;

class EasyVistaQueue extends Job
{
    protected $values;

    /**
     * EasyVistaQueue constructor.
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        \Log::debug("EasyVistaQueue@handle - values: " .print_r($this->values, true));

        $post_data = array(
            'Catalog_GUID' => '',
            'Catalog_Code' => $this->values['packageAC'],
            'AssetID' => '',
            'AssetTag' => '',
            'ASSET_NAME' => '',
            'Urgency_ID' => '2',
            'Severity_ID' => '41',
            'External_reference' => '',
            'Phone' => '',
            'Requestor_Identification' => '',
            'Requestor_Mail' => $this->values['email'],
            'Requestor_Name' => '',
            'Location_ID' => '',
            'Location_Code' => '',
            'Department_ID' => '',
            'Department_Code' => '',
            'Recipient_ID' => '',
            'Recipient_Identification' => '',
            'Recipient_Mail' => $this->values['email'],
            'Recipient_Name' => '',
            'Origin' => '2',
            'Description' => 'An order has been created for :' . $this->values['description'],
            'ParentRequest' => '',
            'CI_ID' => '',
            'CI_ASSET_TAG' => '',
            'CI_NAME' => '',
            'SUBMIT_DATE' => ''
        );

        $client = new \Guzzle\Http\Client('https://wa.easyvista.com/api/v1/50005/');
        $uri = 'requests';
        $code = base64_encode(env('EV_API_LOGIN') . ':' . env('EV_API_PASSWORD'));
        $request = $client->post($uri, array(
            'content-type' => 'application/json',
            'Authorization' => 'Basic anN0ZWVsZTp3MXJlbGVzcw=='// . $code
        ));
 
        $data = json_encode(['requests' => [$post_data]]);
        $request->setBody($data);
        $response = $request->send();

        \Log::debug("EasyVistaQueue@handle - EV Request has been sent.");
    }
}
