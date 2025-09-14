<?php

namespace App\Contracts;

use App\Models\Task;

interface TaskFactoryInterface
{
    public function create(array $data, int $userId): Task;
}
