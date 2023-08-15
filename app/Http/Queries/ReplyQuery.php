<?php

namespace App\Http\Queries;

use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Reply;

class ReplyQuery extends QueryBuilder
{
    public function __construct()
    {
        parent::__construct(Reply::query());

        $this->allowedIncludes('user', 'topic', 'topic.user');
    }
}
