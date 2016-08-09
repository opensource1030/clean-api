<?php

namespace WA\DataStore\UdlValuePathUsers;

use League\Fractal\TransformerAbstract;

/**
 * Class UdlValuePathCreatorUserTransformer.
 */
class UdlValuePathUsersTransformer extends TransformerAbstract
{
    /**
     * @param UdlValuePathUsers $udlValuePathUsers
     *
     * @return array
     */
    public function transform(UdlValuePathUsers $udlValuePathUsers)
    {
        return [
            'id' => $udlValuePathUsers->id,
            'creatorId' => $udlValuePathUsers->creatorId,
            'userEmail'=> $udlValuePathUsers->userEmail,
            'userFirstName' => $udlValuePathUsers->userFirstName,
            'userLastName' => $udlValuePathUsers->userLastName,
            'udlValuePathId' => $udlValuePathUsers->udlValuePathId,

        ];
    }
}
