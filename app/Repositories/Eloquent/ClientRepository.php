<?php

namespace App\Repositories\Eloquent;

use App\Models\Client;
use App\Repositories\Contract\ClientRepositoryInterface;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    public function __construct(Client $model)
    {
        parent::__construct($model);
    }
}
