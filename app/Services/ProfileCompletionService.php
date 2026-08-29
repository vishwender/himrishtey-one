<?php

namespace App\Services;

use App\Models\SiteMember;

class ProfileCompletionService
{
    protected array $profileFields = [
        'full_name',
        'email',
        'mobile_number',
        'birth_date_time',
        'height',
        'gender',
        'blood_group',
        'birth_place',
        'religion',
        'mother_tongue',
        'cast',
        'marital_status',

        'about_my_education',
        'education',
        'about_my_career',
        'employed_in',
        'occupation',
        'designation',
        'organization_name',
        'job_location',
        'annual_income',

        'country_living_in',
        'state_living_in',
        'city_living_in',
        'native_place',

        'family_type',
        'family_status',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'about_family',

        'diet',
        'is_drinking',
        'is_smoking',
        'about_me',
        'any_disability',

        'looking_for',
        'partner_age_from',
        'partner_age_to',
        'partner_country',
        'partner_religion',
        'partner_cast',
        'partner_height_from',
        'partner_height_to',
        'partner_education',
        'partner_mothertongue',
        'partner_annual_income_from',
        'partner_annual_income_to',
        'partner_occupation',
        'partner_state',
        'partner_city',
        'partner_diet',
        'is_partner_smoking',
        'is_partner_drinking',
        'about_my_partner',
    ];

    public function calculate(SiteMember $member): int
    {
        $totalFields = count($this->profileFields);

        if ($totalFields === 0) {
            return 0;
        }

        $completedFields = 0;

        foreach ($this->profileFields as $field) {

            $value = $member->{$field};

            if ($this->isFilled($value)) {
                $completedFields++;
            }
        }

        return (int) round(
            ($completedFields / $totalFields) * 100
        );
    }

    protected function isFilled(mixed $value): bool
    {
        if (is_null($value)) {
            return false;
        }

        if (is_string($value)) {
            return trim($value) !== '';
        }

        return true;
    }
}
