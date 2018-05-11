<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    public function actingAs(UserContract $user, $driver = null)
    {
        return parent::actingAs($user, 'api');
    }

    protected function assertModelIs(Model $expected, Model $haystack)
    {
        $this->assertTrue($expected->is($haystack), "[" . class_basename($expected) . "] is not equal to [" . class_basename($haystack) . "]");
    }

    protected function assertContainsAllModels(Collection $models, Collection $responseModel)
    {
        $responseModel->each(function ($responseModel) use ($models) {
            $contains = $models->contains(function ($model) use ($responseModel) {
                if($responseModel instanceof Model) {
                    return $model->is($responseModel);
                }
                elseif ($responseModel instanceof Resource) {
                    return $model->is($responseModel->resource);
                }
                return false;
            });
            $this->assertTrue($contains, "Model [{$responseModel->getKey()}] is not in the collection");
        });
    }

    protected function assertModelIn(Model $model, Collection $collection)
    {
        $contains = false;

        $collection->each(function ($hay) use ($model, &$contains) {
            if($hay instanceof Model && $hay->is($model)) {
                $contains = true;
            }
            elseif ($hay instanceof Resource && $hay->resource->is($model)) {
                $contains = true;
            }
        });

        $this->assertTrue($contains);
    }
}
