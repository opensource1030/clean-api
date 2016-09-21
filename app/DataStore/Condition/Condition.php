<?php

namespace WA\DataStore\Condition;

use WA\DataStore\BaseDataStore;
use WA\DataStore\Order\OrderTransformer;

/**
 * Class Order
 *
 * @package WA\DataStore\Order
 */
class Condition extends BaseDataStore
{
    protected  $table = 'package_conditions';

    protected $fillable = [
            'profileNameCondition',
            'profileNameValue',
            'profileEmailCondition',
            'profileEmailValue',
            'profilePositionCondition',
            'profilePositionValue',
            'profileLevelCondition',
            'profileLevelValue',
            'profileDivisionCondition',
            'profileDivisionValue',
            'profileCostCenterCondition',
            'profileCostCenterValue',
            'profileBudgetCondition',
            'profileBudgetValue',
            'locationItemsCountryACondition',
            'locationItemsCountryAValue',
            'locationItemsCountryBCondition',
            'locationItemsCountryBValue',
            'locationItemsCityCondition',
            'locationItemsCityValue',
            'locationItemsAdressCondition',
            'locationItemsAdressValue'];

    /**
     * Get all the owners for the order
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
  public function owner()
  {
      return $this->morphTo();
  }

    /**
     * Get the transformer instance
     *
     * @return OrderTransformer
     */
    public function getTransformer()
    {
        return new ConditionTransformer();
    }

}