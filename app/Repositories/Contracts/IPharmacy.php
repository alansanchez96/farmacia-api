<?php

namespace App\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

interface IPharmacy
{
    public function getCollection(?Request $request);

    public function getNearestCollection(?Request $request);

    public function findById(int $id);

    public function store(?Request $request);
}
