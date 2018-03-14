<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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


}
