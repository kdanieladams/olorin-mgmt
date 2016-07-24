<?php namespace Olorin\Mgmt;

use Illuminate\Support\Facades\Facade;

class FormGroupFacade extends Facade {

    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'formGroup';
    }

}