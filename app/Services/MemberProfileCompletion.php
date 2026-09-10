<?php

namespace App\Services;

class MemberProfileCompletion
{
    private const FIELDS = [
        'full_name',
        'email',
        'mobile_number',
        'alternate_number',
        'whatsapp_number',
        'birth_date_time',
        'height',
        'gender',
        'blood_group',
        'health_info',
        'birth_place',
        'religion',
        'mother_tongue',
        'cast',
        'sub_cast',
        'gotra',
        'manglik',
        'marital_status',
        'no_of_child',
        'about_my_education',
        'education',
        'any_other_qualifications',
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
        'address_living_in',
        'native_place',
        'family_type',
        'family_status',
        'father_name',
        'father_occupation',
        'mother_name',
        'mother_occupation',
        'no_of_brothers',
        'no_of_sisters',
        'married_brothers',
        'married_sisters',
        'family_income',
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
        'is_partner_manglik',
        'partner_occupation',
        'partner_state',
        'partner_city',
        'partner_diet',
        'is_partner_smoking',
        'is_partner_drinking',
        'about_my_partner',
    ];

    public function summary(object $member): array
    {
        $completed = 0;
        foreach (self::FIELDS as $field) {
            $value = $member->{$field} ?? null;
            if ($value !== null && trim((string) $value) !== '') {
                $completed++;
            }
        }

        $total = count(self::FIELDS);

        return [
            'completedFields' => $completed,
            'totalFields' => $total,
            'profileCompletion' => (int) round($completed / $total * 100),
        ];
    }

    public function percentage(object $member): int
    {
        return $this->summary($member)['profileCompletion'];
    }
}
