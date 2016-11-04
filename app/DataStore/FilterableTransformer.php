<?php

namespace WA\DataStore;

use Illuminate\Support\Str;
use League\Fractal\Resource\Collection as ResourceCollection;
use League\Fractal\TransformerAbstract;
use WA\Helpers\Traits\Criteria;

abstract class FilterableTransformer extends TransformerAbstract
{
    use Criteria;

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'include')) {
            return $this->dynamicInclude($method, $parameters);
        }
        $className = static::class;
        throw new \BadMethodCallException("Call to undefined method {$className}::{$method}()");
    }

    public function dynamicInclude($method, $parameters)
    {
        $this->criteria = $this->getRequestCriteria();
        $finder = strtolower(substr($method, 7));
        $resource = $parameters[0];
        $model = title_case(str_singular($finder));
        $transformer = "\\WA\\DataStore\\${model}\\${model}Transformer";

        if (!class_exists($transformer)) {
            throw new \BadMethodCallException("Unable to create $transformer");
        }
        $include = $this->applyCriteria($resource->$finder(), $this->criteria);
        return new ResourceCollection($include->get(), new $transformer(), $finder);

    }
}
