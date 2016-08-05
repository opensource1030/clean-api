<?php

namespace WA\DataStore\UdlValuePathEmployees;

use League\Fractal\TransformerAbstract;

/**
 * Class UdlValuePathCreatorUserTransformer.
 */
class UdlValuePathEmployeesTransformer extends TransformerAbstract
{
    /**
     * @param UdlValuePathEmployees $udlValuePathEmployees
     *
     * @return array
     */
    public function transform(UdlValuePathEmployees $udlValuePathEmployees)
    {
        return [
            'id' => $udlValuePathEmployees->id,
            'creatorId' => $udlValuePathEmployees->creatorId,
            'userEmail'=> $udlValuePathEmployees->userEmail,
            'userFirstName' => $udlValuePathEmployees->userFirstName,
            'userLastName' => $udlValuePathEmployees->userLastName,
            'udlValuePathId' => $udlValuePathEmployees->udlValuePathId,

        ];
    }
}
