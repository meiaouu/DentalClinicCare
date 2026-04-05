<?php

namespace App\Enums;

enum RoleName: string
{
    case ADMIN = 'admin';
    case STAFF = 'staff';
    case DENTIST = 'dentist';
    case PATIENT = 'patient';
}
