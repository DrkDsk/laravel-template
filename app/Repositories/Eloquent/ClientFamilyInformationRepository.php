<?php

namespace App\Repositories\Eloquent;

use App\Models\ClientFamilyInformation;
use App\Repositories\Contract\ClientFamilyInformationRepositoryInterface;

class ClientFamilyInformationRepository extends BaseRepository implements ClientFamilyInformationRepositoryInterface
{
    public function __construct(ClientFamilyInformation $model)
    {
        parent::__construct($model);
    }
}
