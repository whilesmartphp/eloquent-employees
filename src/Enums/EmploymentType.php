<?php

namespace Whilesmart\Employees\Enums;

enum EmploymentType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Contractor = 'contractor';
}
