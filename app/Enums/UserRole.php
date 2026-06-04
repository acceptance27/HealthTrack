<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Midwife = 'midwife';
    case Patient = 'patient';
}
