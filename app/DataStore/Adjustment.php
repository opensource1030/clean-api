<?php


namespace WA\DataStore;

/**
 * An Eloquent Model: 'WA\DataStore\Adjustment'.
 *
 * @property int $id
 * @property int $adjustableId
 * @property string $memberOf
 * @property bool $active
 * @property int $order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \WA\DataStore\Adjustable $adjustable
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\WA\DataStore\Adjustable[] $adjustables
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustment whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustment whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustment whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\Adjustment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Adjustment extends BaseDataStore
{
    protected $table = 'adjustments';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adjustables()
    {
        return $this->hasMany('WA\DataStore\Adjustable', 'adjustmentId');
    }
}
