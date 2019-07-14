<?php

namespace blundell\glide\models;

use craft\base\Model;

class Settings extends Model
{
    public $signed = false;
    public $key = 'A-Unique-And-Random-Key--Replace-This';
    public $driver = 'gd';

    public function rules()
    {
        return [
            [['signed', 'key', 'driver'], 'required']
        ];
    }
}