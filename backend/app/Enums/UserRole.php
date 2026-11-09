<?php

namespace App\Enums;

enum UserRole: string
{
    case JOB_SEEKER = 'job_seeker';
    case ADMIN = 'admin';
    case EMPLOYER = 'employer';
}
