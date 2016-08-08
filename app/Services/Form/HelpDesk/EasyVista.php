<?php

namespace WA\Services\Form\HelpDesk;

use Log;
use WA\Repositories\Location\LocationInterface;
use WA\Services\Soap\HelpDeskEasyVista as HelpDeskApi;

class EasyVista
{
    /**
     * @var HelpDeskApi
     */
    protected $helpDeskApi;

    public function __construct(HelpDeskApi $helpDeskApi)
    {
        $this->helpDeskApi = $helpDeskApi;
    }

    /**
     *  Create a new User
     *
     * @param array                  $data
     * @param LocationInterface|null $location
     *
     * @return bool
     */
    public function createUser(array $data = [], LocationInterface $location = null)
    {
        $locationInterface = $location ?: app()->make('WA\Repositories\Location\LocationInterface');

        if ((int)$data['input']['level'] === 0) {
            $data['input']['level'] = '';
        }

        $isCensus = (isset($data['input']['syncId'])) ? true : false;

        // for census updates, keep the default values
        //$internal_location_id = ($isCensus) ? null : $data['input']['defaultLocationId'];
        //Adding location Id for census update
        $internal_location_id = isset($data['input']['defaultLocationId']) ? $data['input']['defaultLocationId'] : null;
        $external_location_id = !empty($location = $locationInterface->byId($internal_location_id)) ? $location->externalId : '';
        if(empty($external_location_id))
        {
            $default_location = $locationInterface->byName('United States');
            $external_location_id = $default_location->externalId;
        }
        $external_department_id = (bool)$data['input']['evDepartmentId'] ? $data['input']['evDepartmentId'] : '';

        $xmlString = <<<XML
             <fields>
             <field name="LAST_NAME">{$data['input']['lastName']}, {$data['input']['firstName']}</field>
             <field name="LOCATION_ID">{$external_location_id}</field>
              <field name="LANGUAGE_ID">1</field>
             <field name="DEFAULT_DOMAIN_ID">{$data['input']['companyExternalId']}</field>
             <field name="DEPARTMENT_ID">{$external_department_id}</field>
             <field name="E_MAIL">{$data['input']['email']}</field>
             <field name="IDENTIFICATION">{$data['input']['identification']}</field>
             <field name="MANAGER_ID">{$data['input']['externalSupervisorId']}</field>
             <field name="VALIDATOR_ID">{$data['input']['approverId']}</field>
             <field name="COMMENT_EMPLOYEE"><![CDATA[{$data['input']['notes']}]]></field>
             <field name="VIP_LEVEL_ID">{$data['input']['level']}</field>
XML;
        $this->helpDeskApi->connect();

        if ($data['input']['notify'] == 0) {
            $xmlString .= '</fields>';
        } else {
            $xmlString .= "<field name='NOTIFICATION_TYPE_ID'>{$data['input']['notify']}</field></fields>";
        }

        if (!$response = $this->helpDeskApi->createUser($xmlString)) {
            Log::error("Could not add user: " . $xmlString . ' >> ' . $response);

            return false;
        }

        return true;
    }

    /**
     * Update a user
     *
     * @param array $data of user information
     *
     * @return bool
     */
    public function updateUser(array $data = [])
    {
        $locationInterface = app()->make('WA\Repositories\Location\LocationInterface');

        if (!isset($data['input']['level']) || (int)$data['input']['level'] === 0 ||
            (int)$data['input']['level'] === 3
        ) {
            $data['input']['level'] = ''; //empty
        }

        $isCensus = (isset($data['input']['syncId'])) ? true : false;


        $externalId = $data['employee']->identification;
        // Any other  updates:
        // the identification is the EV ID
        $lastName = $data['input']['lastName'];
        $firstName = $data['input']['firstName'];
        $name = $lastName . ', ' . $firstName;
        $data['input']['approverId'] = empty($data['input']['approverId']) ? '' : $data['input']['approverId'];

        $userInfo = array(
            'name' => $name,
            'identification' => $externalId,
            'supervisorId' => $data['input']['externalSupervisorId'],
            'evDepartmentId' => isset($data['input']['evDepartmentId']) ? $data['input']['evDepartmentId'] : null,
            'lastName' => $data['input']['lastName'],
            'firstName' => $data['input']['firstName'],
            'syncId' => isset($data['input']['syncId']) ? $data['input']['syncId'] : null ,
            'email' => isset($data['input']['email']) ? strtolower($data['input']['email']): null
        );

        $last_name = $this->formatName($data);

        // for census updates, keep the default values
        //$internal_location_id = ($isCensus) ? null : $data['input']['defaultLocationId'];
        //Adding location Id for census update
        $internal_location_id = isset($data['input']['defaultLocationId']) ? $data['input']['defaultLocationId'] : null;
        $external_location_id = !empty($location = $locationInterface->byId($internal_location_id)) ? $location->externalId : '';
        if(empty($external_location_id))
        {
            $default_location = $locationInterface->byName('United States');
            $external_location_id = $default_location->externalId;
        }
        $department_id = ($isCensus) ? null : $data['input']['evDepartmentId'];

        $xmlString = <<<XML
       <fields>
         <field name="last_name" xsi:type="xsd:string">{$last_name}</field>
         <field name="identification" xsi:type="xsd:string">{$externalId}</field>
         <field name="location_id" xsi:type="xsd:string">{$external_location_id}</field>
        <field name="language_id" xsi:type="xsd:string">1</field>
         <field name="vip_level_id" xsi:type="xsd:string">{$data['input']['level']}</field>
         <field name="default_domain_id" xsi:type="xsd:string">{$data['input']['companyId']}</field>
         <field name="manager_id" xsi:type="xsd:string">{$data['input']['externalSupervisorId']}</field>
          <field name="validator_id" xsi:type="xsd:string">{$data['input']['approverId']}</field>
          <field name="notification_type_id" xsi:type="xsd:string">{$data['input']['notify']}</field>
         <field name="department_id" xsi:type="xsd:string">{$department_id}</field>
         <field name="e_mail" xsi:type="xsd:string">{$data['input']['email']}</field>
           <field name="approved_to_validate" xsi:type="xsd:string">{$data['input']['isValidator']}</field>
        <field name="COMMENT_EMPLOYEE"><![CDATA[{$data['input']['notes']}]]></field>

         </fields>



XML;
        /*

 <field name="impact" xsi:type="xsd:string"></field>       <fields>
         <field name="last_name" xsi:type="xsd:string">Abassi, Nasro</field>
         <field name="identification" xsi:type="xsd:string">TFS-F9LYTPBPIW</field>
         <field name="location_id" xsi:type="xsd:string"></field>
        <field name="language_id" xsi:type="xsd:string">1</field>
         <field name="vip_level_id" xsi:type="xsd:string"></field>
         <field name="default_domain_id" xsi:type="xsd:string">16</field>
         <field name="manager_id" xsi:type="xsd:string"></field>
          <field name="validator_id" xsi:type="xsd:string"></field>
          <field name="notification_type_id" xsi:type="xsd:string"></field>
         <field name="department_id" xsi:type="xsd:string"></field>
         <field name="e_mail" xsi:type="xsd:string">mohamed.abassi@thermofisher.com</field>
           <field name="approved_to_validate" xsi:type="xsd:string"></field>
        <field name="COMMENT_EMPLOYEE"><![CDATA[]]></field>
         <field name="impact" xsi:type="xsd:string"></field>
         */


        $this->helpDeskApi->connect();

        if (!$response = $this->helpDeskApi->updateUser($xmlString, $userInfo)) {

            Log::error("Could not update user: " . $xmlString . ' >> ' . $response);

            return false;
        }

        return true;
    }

    /**
     * Formats the name for proper sending to the Webservice
     *
     * @param array $data
     *
     * @return string of formatted name
     */
    public function formatName(array $data)
    {
        $last_name = $data['input']['lastName'] . ", " . $data['input']['firstName'];

        //@FIXME
        // we will ideally get the company rules, and format according
        // for now, this works
        if ((int)$data['input']['companyId'] === 16) {
            $alternate_first_name = isset($data['input']['alternateFirstName']) ? $data['input']['alternateFirstName'] : "";

            if (!empty($alternate_first_name)) {
                $last_name = $data['input']['lastName'] . ", " . $alternate_first_name;
            }

        }


        return $last_name;
    }


}
