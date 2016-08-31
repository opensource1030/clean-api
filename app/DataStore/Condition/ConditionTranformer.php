<?php

namespace WA\DataStore\Order;

use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\Resource\Item as ResourceItem;
use League\Fractal\TransformerAbstract;

/**
 * Class OrderTransformer
 *
 */
class OrderTransformer extends TransformerAbstract
{

    /**
     * @param Order $order
     *
     * @return array
     */
    public function transform(Order $order)
    {
        return [

            'id' => (int)$order->id,
            'profileNameCondition' => $order->profileNameCondition,
            'profileNameValue' => $order->profileNameValue,
            'profileEmailCondition' => $order->profileEmailCondition,
            'profileEmailValue' => $order->profileEmailValue,
            'profilePositionCondition' => $order->profilePositionCondition,
            'profilePositionValue' => $order->profilePositionValue,
            'profileLevelCondition' => $order->profileLevelCondition,
            'profileLevelValue' => $order->profileLevelValue,
            'profileDivisionCondition' => $order->profileDivisionCondition,
            'profileDivisionValue' => $order->profileDivisionValue,
            'profileCostCenterCondition' => $order->profileCostCenterCondition,
            'profileCostCenterValue' => $order->profileCostCenterValue,
            'profileBudgetCondition' => $order->profileBudgetCondition,
            'profileBudgetValue' => $order->profileBudgetValue,
            'locationItemsCountryACondition' => $order->locationItemsCountryACondition,
            'locationItemsCountryAValue' => $order->locationItemsCountryAValue,
            'locationItemsCountryBCondition' => $order->locationItemsCountryBCondition,
            'locationItemsCountryBValue' => $order->locationItemsCountryBValue,
            'locationItemsCityCondition' => $order->locationItemsCityCondition,
            'locationItemsCityValue' => $order->locationItemsCityValue,
            'locationItemsAdressCondition' => $order->locationItemsAdressCondition,
            'locationItemsAdressValue' => $order->locationItemsAdressValue,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
        ];
    }
}