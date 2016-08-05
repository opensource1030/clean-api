<?php


namespace WA\DataStore;

/**
 * An Eloquent Model: 'WA\DataStore\Adjustable'.
 *
 * @property int $id
 * @property string $label
 * @property string $applicator
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Adjustment[] $adjustments
 * @property int $adjustmentId
 * @property bool $active
 * @property-read \WA\DataStore\Adjustment $adjustment
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereLabel($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereApplicator($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereAdjustmentId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustable whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Adjustable extends BaseDataStore
{
    protected $table = 'adjustables';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adjustment()
    {
        return $this->belongsTo('WA\DataStore\Adjustment', 'adjustmentId');
    }
}
