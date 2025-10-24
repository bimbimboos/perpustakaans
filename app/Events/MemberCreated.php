<?php
namespace App\Events;

use App\Models\Members;
use Illuminate\Foundation\Events\Dispatchable;

class MemberCreated
{
use Dispatchable;

public $members;

public function __construct(Members $members)
{
$this->members = $members;
}
}
