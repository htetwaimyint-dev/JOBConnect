<?php

namespace App\Enums;

enum UserRole: string
{
    case JobSeeker = 'job_seeker';
    case Admin = 'admin';
    case Employer = 'employer';
}
