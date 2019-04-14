<?php

namespace Therour\Actionable\Params;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

abstract class AbstractParam
{
    /**
     * Original data comes to param.
     *
     * @var array
     */
    protected $originParam;

    /**
     * Filtered Param
     *
     * @var array
     */
    protected $filteredParam = [];

    /**
     * Modified param by each getters.
     *
     * @var array
     */
    protected $resultParam = [];

    /**
     * Create a new param instance.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->init($data);
        if (! empty($data)) {
            $this->create($data);
        }
    }

    /**
     * Initiating data.
     *
     * @return void
     */
    protected function init()
    {
        //
    }

    /**
     * initiating data in param
     *
     * @param array $data
     * @return self
     */
    public function create(array $data)
    {
        $this->originParam = $data;

        foreach ($data as $key => $value) {
            $property = Str::camel($key);
            if (property_exists($this, $property)) {
                $this->setProperty($property, $value);
                $this->filteredParam[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Run validation.
     *
     * @return void
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validate(): void
    {
        $validator = Validator::make(
            $this->filteredParam,
            static::rules(),
            static::customMessages()
        );

        $validator->validate();
    }

    /**
     * Defined rules.
     *
     * @return array
     */
    public static function rules()
    {
        return [];
    }

    /**
     * Defined custom messages in validation.
     *
     * @return array
     */
    public static function customMessages()
    {
        return [];
    }

    /**
     * Get original data.
     *
     * @return array
     */
    public function origin()
    {
        return $this->originParam;
    }

    /**
     * Get filtered and modified parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->toArray();
    }

    /**
     * Get an array of payload.
     *
     * @return array
     */
    public function toArray()
    {
        if ($this->resultParam != null) {
            return $this->resultParam;
        }

        $result = [];
        
        foreach ($this->filteredParam as $key => $value) {
            $methodName = 'get' . Str::studly($key);
            if (method_exists($this, $methodName)) {
                $value = $this->{$methodName}();
            }
            $result[$key] = $value;
        }

        return $this->resultParam = $result;
    }

    /**
     * Set value into private property.
     *
     * @param string $property
     * @param mixed $value
     * @return void
     */
    private function setProperty($key, $value): void
    {
        $property = new \ReflectionProperty(get_class($this), $key);
        $property->setAccessible(true);
        $property->setValue($this, $value);
    }
}