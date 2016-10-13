<?php

/**
 * CompanySaml2TableSeeder - Insert info into database.
 *  
 * @author   Agustí Dosaiguas
 */

class CompanySaml2TableSeeder extends BaseTableSeeder
{
    protected $table = "company_saml2";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->deleteTable();

        $dataMicrosoft = [
            'entityId' => 'https://sts.windows.net/d6f6af8c-1dd5-4480-afe3-53aaa025a273/',
            'singleSignOnServiceUrl' => 'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2',
            'singleSignOnServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'singleLogoutServiceUrl' => 'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2',
            'singleLogoutServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'x509cert' => 'MIIDBTCCAe2gAwIBAgIQPLxWKJ0EEqNLJ1eIGhsS/jANBgkqhkiG9w0BAQsFADAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MB4XDTE2MDkwNTAwMDAwMFoXDTE4MDkwNjAwMDAwMFowLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKEXDfH9+M8nNOH9ZfhuIn3+UrkdUBNuOac3fHhKViTpufqgBl4EG8acO3iBJWMHhuX9PS/4Agik20fNG/7BnAdr7/0b0XduOhzmwrVIweLQ5JkuifrrdS0cF3WiJ6E99Vsm1eYYaTcH4xk5GNg2/sDhzT03xzXiPha74McLS50VgbqoBIh2sKVcC7E5/GFe5HYwENdC/UdI+89HwmSadeFO5Qxrua+VYk9WhtMUkOEjo8rdBZB06zOYpxA/Wn9Sx1RYjOrCBLvlrnXvvJtAqlhaCqWOSXT+/QWT5AetcvLBSi2t2kWpyj8/Qc1vnqez3vfW6qAyzTaA6TbSb+8jlzMCAwEAAaMhMB8wHQYDVR0OBBYEFJuS8ySZ1mYXPa4Sq1nSrl1G41rXMA0GCSqGSIb3DQEBCwUAA4IBAQBxf5BldsfSq05AAnco9NlToMPsXf46GbInCC/o2R+4WbwJ3uzZe+2/o86nI5gFcq/hGy/HXZXdsWj6py6fI0T5Av0GlhCxAuCmsMoyEMmoGdEnSL6cMfAA57lsAgDGVOB3OdzZoK3um1fpb0paXv1eColOIYsL9lY91Bk4P3E496IDAbkjCjiFzsiQerlmzXSHhvSjvas2g6VTQEwj8/9l4xZO1O3BhExdZHWAkUW1ZciTSB4Ite5bcAHWWBRqMUB7Da5Yj674SocHFhGM+9iM6xaJfMSYjlDFB2rNDSUv8ZLIyDpHB9Ry9N8p7znyixhpiWn0nPVqfX84LMckrgfs',
            'companyId' => 9
        ];

        $this->loadTable($dataMicrosoft);
    }
}