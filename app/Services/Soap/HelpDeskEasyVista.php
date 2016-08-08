<?php

namespace WA\Services\Soap;

use Log;
use WA\Exceptions\DatabaseLogger;

/**
 * Class HelpDeskEasyVista.
 */
class HelpDeskEasyVista extends BaseClient
{
    protected $url = 'https://wa.easyvista.com/WebService/SmoBridge.php?wsdl';

    protected $options = [];

    /**
     * Creates a new update using defined XML params.
     *
     * @param string $xmlString the required user information (formatted as: <fields> <field
     *                          name="*DB_FIELD_NAME*">*VALUE*</field><fields>)
     * @param array  $params
     *
     * @return bool
     */
    public function createUser($xmlString, array $params = [])
    {
        $this->options['params'] = $params;

        $account = env('EV_API_ACCOUNT');
        $login = env('EV_API_LOGIN');
        $pwd = env('EV_API_PASSWORD');

        try {
            $response = $this->client->EZV_CreateUserXML(
                env('EV_API_ACCOUNT'),
                env('EV_API_LOGIN'),
                env('EV_API_PASSWORD'), $xmlString);

            if ((int)$response <= 0) {
                throw new \Exception('Creation of User in Help desk failed, with Error Code:  ' . $response);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('There was an issue: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Creates a new update using defined XML params.
     * !Note: Per the EasyVista Documentation:
     * If the employee number is completed, it is used in conjunction with the name.
     * Neither the *name of the employee* nor *their reference number* (CLEAN ID) is modifiable via this web service.
     *
     * @param string $xmlString the required user information (formatted as: <fields> <field
     *                          name="*DB_FIELD_NAME*">*VALUE*</field><fields>)
     * @param array  $params
     *
     * @return bool
     */
    public function updateUser($xmlString, array $params = [])
    {
        $this->options['params'] = $params;

        try {

            $response = $this->client->EZV_ModifyUserXML(
                env('EV_API_ACCOUNT'),
                env('EV_API_LOGIN'),
                env('EV_API_PASSWORD'), $xmlString
            );

            if ((int)$response <= 0) {
                throw new \Exception('Update of User in EasyVista failed, with Error Code:  ' . $response);
            }

            return true;

        } catch (\Exception $e) {
            $message = "Update failed in EasyVista with Error Code " . $response;

            $meta = [
                'identification' => isset($params['identification']) ? $params['identification'] : null,
                'name' => isset($params['name']) ? $params['name'] : null,
                'email' => isset($params['email']) ? $params['email'] : null,
                'firstName' => isset($params['firstName']) ? $params['firstName'] : null ,
                'lastName' => isset($params['lastName']) ? $params['lastName'] : null,
                'syncId' => isset($params['syncId']) ? $params['syncId'] : null
            ];

            Log::error('There was an issue: ' . $e->getMessage());
            $this->errors[] = 'There was an issue: ' . $e->getMessage();

            new DatabaseLogger($message,'employee',$meta);

            return false;
        }


    }
}
