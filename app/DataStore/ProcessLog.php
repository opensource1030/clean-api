<?php


namespace WA\DataStore;

use Culpa\Traits\Blameable;
use Culpa\Traits\CreatedBy;

/**
 * An Eloquent Model: 'WA\DataStore\ProcessLog'.
 *
 * @property int $id
 * @property string $message
 * @property string $description
 * @property string $processHandler
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $created_by_id
 * @property-read \ $model $createdBy
 *
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereProcessHandler($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereCreatedById($value)
 *
 * @property string $context
 * @property string $metadata
 *
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereContext($value)
 * @method static \Illuminate\Database\Query\Builder|\WA\DataStore\ProcessLog whereMetadata($value)
 */
class ProcessLog extends BaseDataStore
{
    use Blameable, CreatedBy;
    
    public $timestamps = true;
    protected $tableName = 'process_logs';
    protected $fillable = ['message', 'description', 'processHandler'];
    protected $blameable = ['created' => 'created_by_id'];


}
