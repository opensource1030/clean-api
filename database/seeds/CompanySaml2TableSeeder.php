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

        $dataFacebook = [
        'entityId' => 'http://simplesamlphp.dev/simplesaml/saml2/idp/metadata.php',
        'singleSignOnServiceUrl' => 'http://simplesamlphp.dev/simplesaml/saml2/idp/SSOService.php',
        'singleSignOnServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'singleLogoutServiceUrl' => 'http://simplesamlphp.dev/simplesaml/saml2/idp/SingleLogoutService.php',
        'singleLogoutServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        'x509cert' => 'MIIDXTCCAkWgAwIBAgIJAMtuJVQu8b68MA0GCSqGSIb3DQEBCwUAMEUxCzAJBgNVBAYTAkFVMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBXaWRnaXRzIFB0eSBMdGQwHhcNMTYwNzA1MDkwODM1WhcNMjYwNzA1MDkwODM1WjBFMQswCQYDVQQGEwJBVTETMBEGA1UECAwKU29tZS1TdGF0ZTEhMB8GA1UECgwYSW50ZXJuZXQgV2lkZ2l0cyBQdHkgTHRkMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAsPLmLoR69FYGOobPcQ8tmcBLwh1iPkL4QSZ8IisReUUENf9JfHRB8ie8+NQdsMJM9oqZ1OXTtHSgHfOk0bnC2r8qtx0SYbFVoZXw9YQofO6bxZBYWqWfJd8ADXo0fj47ywCVAwMhOBkSJy8NVK5rwXoo7DVOLeRS4Qu0mvsz8IK0wujUmoe+LNs7YEIbnsyDeSM6eqHgVuU9JQGhO20SMas+EdzU/E+o0PLaZlIo4CNpiC8DhbAIV+mLNxoVb7QHteNPDJEBRndURfaKiH8SKH2zuMN0Ay0AZUQzbYl/DZgm0WDyFfwqFhwBBg8sDMB4sV/vYSCbV2qd4CcFjyGtUQIDAQABo1AwTjAdBgNVHQ4EFgQUh6QbF4rPbOU+qVxjSRubWIk2jEswHwYDVR0jBBgwFoAUh6QbF4rPbOU+qVxjSRubWIk2jEswDAYDVR0TBAUwAwEB/zANBgkqhkiG9w0BAQsFAAOCAQEAIhX1+HSWLulEBKImMMels+GKNSIH7/RqOVvP9lE1DTaKJtjx9ZxbrqSIvSA1j6aiCOqMr0ok63Eg3kSSaPDxlaD9xVVIEGTTwZ01pvev5pbGzhpW2M0s6nNi0AGz8aCZGotugxLX7p4qPuuhnr5vnYu/MqhIEQrXhQsTizzQKfu6W4aojlRZNuiJEKJDALavu2l1Iby4eLUubFuBWAsPHpbIB+5QtYjjUVlGTnXKCrUubBj3GVIhdKtzPG10uyHNgT2wBcdj7T9tdPHT0XzkIjndHvhOJKqXDBcqYphXnrWlfqpLYUD3xwlHIbSohYSaiNRbcjaPwk6OWs/O/SBTPw==',
        'companyId' => 21
        ];

        $dataMicrosoft = [
            'entityId' => 'https://sts.windows.net/d6f6af8c-1dd5-4480-afe3-53aaa025a273/',
            'singleSignOnServiceUrl' => 'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2',
            'singleSignOnServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'singleLogoutServiceUrl' => 'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2',
            'singleLogoutServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'x509cert' => 'MIIC4jCCAcqgAwIBAgIQfQ29fkGSsb1J8n2KueDFtDANBgkqhkiG9w0BAQsFADAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MB4XDTE2MDQxNzAwMDAwMFoXDTE4MDQxNzAwMDAwMFowLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAL23Ba49fdxpus3qOXtv8ueCcePbCIEnL/tiTvp+jcTakGilZzJB3M/ktY9hX6RZ4KLcBjM2SClQmNUivEimTBX0U1N8L06GSE8H91tUKup/ofmm6qciU2qiHH4QNHepBADOTbEACoX78O363tUInJlPS1lVlGAGsi5okV+qN7ZLSauh+fKVM07cfw9A6a58es+bFvrojIqS1264GJjns+4baJCVYA4PMPsgxQsWTaOylbnlJC5MYTY2BpBn57dfLO2VtN+lqE5nWkJluAgoX/6OEyxOVchqWFpuyP/p1feQQb8Jc6JFVSs73in95eVFN3Oj5BsvgQdxPwoahZurD1sCAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAe5RxtMLU2i4/vN1YacncR3GkOlbRv82rll9cd5mtVmokAw7kwbFBFNo2vIVkun+n+VdJf+QRzmHGm3ABtKwz3DPr78y0qdVFA3h9P60hd3wqu2k5/Q8s9j1Kq3u9TIEoHlGJqNzjqO7khX6VcJ6BRLzoefBYavqoDSgJ3mkkYCNqTV2ZxDNks3obPg4yUkh5flULH14TqlFIOhXbsd775aPuMT+/tyqcc6xohU5NyYA63KtWG1BLDuF4LEF84oNPcY9i0n6IphEGgz20H7YcLRNjU55pDbWGdjE4X8ANb23kAc75RZn9EY4qYCiqeIAg3qEVKLnLUx0fNKMHmuedjg==',
            'companyId' => 9
        ];

        $dataMicrosoftMALO = [
            'entityId' => 'https://sts.windows.net/d6f6af8c-1dd5-4480-afe3-53aaa025a273/',
            'singleSignOnServiceUrl' => 'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2',
            'singleSignOnServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'singleLogoutServiceUrl' => 'https://login.microsoftonline.com/d6f6af8c-1dd5-4480-afe3-53aaa025a273/saml2',
            'singleLogoutServiceBinding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'x509cert' => 'MIIC4jCCAcqgAwIBAgIQQNXrmzhLN4VGlUXDYCRT3zANBgkqhkiG9w0BAQsFADAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MB4XDTE0MTAyODAwMDAwMFoXDTE2MTAyNzAwMDAwMFowLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALyKs/uPhEf7zVizjfcr/ISGFe9+yUOqwpel38zgutvLHmFD39E2hpPdQhcXn4c4dt1fU5KvkbcDdVbP8+e4TvNpJMy/nEB2V92zCQ/hhBjilwhF1ETe1TMmVjALs0KFvbxW9ZN3EdUVvxFvz/gvG29nQhl4QWKj3x8opr89lmq14Z7T0mzOV8kub+cgsOU/1bsKqrIqN1fMKKFhjKaetctdjYTfGzVQ0AJAzzbtg0/Q1wdYNAnhSDafygEv6kNiquk0r0RyasUUevEXs2LY3vSgKsKseI8ZZlQEMtE9/k/iAG7JNcEbVg53YTurNTrPnXJOU88mf3TToX14HpYsS1ECAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAfolx45w0i8CdAUjjeAaYdhG9+NDHxop0UvNOqlGqYJexqPLuvX8iyUaYxNGzZxFgGI3GpKfmQP2JQWQ1E5JtY/n8iNLOKRMwqkuxSCKJxZJq4Sl/m/Yv7TS1P5LNgAj8QLCypxsWrTAmq2HSpkeSk4JBtsYxX6uhbGM/K1sEktKybVTHu22/7TmRqWTmOUy9wQvMjJb2IXdMGLG3hVntN/WWcs5w8vbt1i8Kk6o19W2MjZ95JaECKjBDYRlhG1KmSBtrsKsCBQoBzwH/rXfksTO9JoUYLXiW0IppB7DhNH4PJ5hZI91R8rR0H3/bKkLSuDaKLWSqMhozdhXsIIKvJQ==',
            'companyId' => 9
        ];

        $this->loadTable($dataFacebook);
        $this->loadTable($dataMicrosoft);
    }
}