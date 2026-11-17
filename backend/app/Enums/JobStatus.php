<?php

namespace App\Enums;

enum JobStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Published = 'published';
    case Rejected = 'rejected';
    case Closed = 'closed';
}
