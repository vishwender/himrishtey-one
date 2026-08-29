<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $profile->full_name }} | {{ $site->name }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --share-primary: #7055e8;
            --share-ink: #1c2333;
            --share-muted: #697386;
            --share-border: #e5e9f2;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at 10% 0%, rgba(112, 85, 232, .14), transparent 30rem),
                #f5f7fb;
            color: var(--share-ink);
            font-family: 'DM Sans', sans-serif;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }

        .share-shell {
            width: min(960px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 2rem 0 3rem;
        }

        .share-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.5rem;
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .share-brand-mark {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: linear-gradient(135deg, #947cff, #6040ed);
            color: #fff;
        }

        .profile-card,
        .details-card {
            overflow: hidden;
            border: 1px solid rgba(229, 233, 242, .9);
            border-radius: 22px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 20px 55px rgba(31, 40, 70, .09);
        }

        .profile-hero {
            padding: 2rem;
            background: linear-gradient(135deg, rgba(112, 85, 232, .1), rgba(255, 255, 255, .2));
        }

        .profile-photo,
        .profile-placeholder {
            width: 150px;
            height: 150px;
            border: 5px solid #fff;
            border-radius: 22px;
            box-shadow: 0 12px 28px rgba(31, 40, 70, .14);
            object-fit: cover;
        }

        .profile-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ece9ff;
            color: var(--share-primary);
            font-size: 3rem;
        }

        .profile-id {
            display: inline-flex;
            padding: .4rem .7rem;
            border-radius: 999px;
            background: #ece9ff;
            color: #583ccf;
            font-size: .8rem;
            font-weight: 700;
        }

        .detail-item {
            height: 100%;
            padding: 1rem;
            border: 1px solid var(--share-border);
            border-radius: 14px;
            background: #fff;
        }

        .detail-label {
            margin-bottom: .28rem;
            color: var(--share-muted);
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .045em;
            text-transform: uppercase;
        }

        .detail-value {
            font-weight: 600;
        }

        .detail-section + .detail-section {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--share-border);
        }

        .section-heading {
            display: flex;
            align-items: center;
            gap: .7rem;
            margin-bottom: 1rem;
        }

        .section-heading i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: #ece9ff;
            color: var(--share-primary);
        }

        .profile-description {
            margin-top: 1rem;
            padding: 1rem;
            border-left: 3px solid var(--share-primary);
            border-radius: 0 12px 12px 0;
            background: #f8f7ff;
            color: var(--share-muted);
            white-space: pre-line;
        }

        .empty-section {
            padding: 1rem;
            border: 1px dashed #cfd4e2;
            border-radius: 12px;
            background: #fafbfe;
            color: var(--share-muted);
        }

        .privacy-note {
            color: var(--share-muted);
            font-size: .85rem;
        }

        @media (max-width: 575.98px) {
            .share-shell { padding-top: 1rem; }
            .profile-hero { padding: 1.4rem; text-align: center; }
            .profile-photo, .profile-placeholder { width: 125px; height: 125px; }
        }
    </style>
</head>
<body>
    @php
        $sections = [
            'Basic Information' => [
                'icon' => 'bi-person-vcard',
                'details' => [
                    'Profile Created For' => $profile->profile_created_for ?? null,
                    'Age' => $profile->age ? $profile->age . ' years' : null,
                    'Gender' => $profile->gender ?? null,
                    'Height' => $profile->height ?? null,
                    'Marital Status' => $profile->marital_status ?? null,
                    'Religion' => $profile->religion ?? null,
                    'Community' => $profile->cast ?? null,
                    'Sub-community' => $profile->sub_cast ?? null,
                    'Mother Tongue' => $profile->mother_tongue ?? null,
                    'Manglik' => $profile->manglik ?? null,
                    'Diet' => $profile->diet ?? null,
                ],
                'description' => $profile->about_me ?? null,
            ],
            'Education & Career' => [
                'icon' => 'bi-mortarboard',
                'details' => [
                    'Education' => $profile->education ?? null,
                    'Other Qualifications' => $profile->any_other_qualifications ?? null,
                    'Employed In' => $profile->employed_in ?? null,
                    'Occupation' => $profile->occupation ?? null,
                    'Designation' => $profile->designation ?? null,
                    'Organization' => $profile->organization_name ?? null,
                    'Job Location' => $profile->job_location ?? null,
                    'Annual Income' => $profile->annual_income ?? null,
                ],
                'description' => collect([
                    $profile->about_my_education ?? null,
                    $profile->about_my_career ?? null,
                ])->filter()->implode("\n\n"),
            ],
            'Location' => [
                'icon' => 'bi-geo-alt',
                'details' => [
                    'Country' => $profile->country_living_in ?? null,
                    'State' => $profile->state_living_in ?? null,
                    'City' => $profile->city_living_in ?? null,
                    'Native Place' => $profile->native_place ?? null,
                ],
                'description' => null,
            ],
            'Family Information' => [
                'icon' => 'bi-people',
                'details' => [
                    'Family Type' => $profile->family_type ?? null,
                    'Family Status' => $profile->family_status ?? null,
                    'Family Income' => $profile->family_income ?? null,
                    "Father's Name" => $profile->father_name ?? null,
                    "Father's Occupation" => $profile->father_occupation ?? null,
                    "Mother's Name" => $profile->mother_name ?? null,
                    "Mother's Occupation" => $profile->mother_occupation ?? null,
                    'Brothers' => $profile->no_of_brothers ?? null,
                    'Married Brothers' => $profile->married_brothers ?? null,
                    'Sisters' => $profile->no_of_sisters ?? null,
                    'Married Sisters' => $profile->married_sisters ?? null,
                ],
                'description' => $profile->about_family ?? null,
            ],
        ];

        foreach ($sections as &$section) {
            $section['details'] = array_filter(
                $section['details'],
                fn ($value) => filled($value)
            );
        }

        unset($section);
    @endphp

    <main class="share-shell">
        <div class="share-brand">
            <span class="share-brand-mark"><i class="bi bi-heart-fill"></i></span>
            <span>{{ $site->name }}</span>
        </div>

        <article class="profile-card">
            <section class="profile-hero">
                <div class="row align-items-center g-4">
                    <div class="col-sm-auto">
                        @if($profile->photo_url)
                            <img
                                src="{{ $profile->photo_url }}"
                                alt="{{ $profile->full_name }}"
                                class="profile-photo">
                        @else
                            <div class="profile-placeholder" aria-label="No profile photo">
                                <i class="bi bi-person"></i>
                            </div>
                        @endif
                    </div>

                    <div class="col">
                        <span class="profile-id mb-3">{{ $profile->profile_id }}</span>
                        <h1 class="h2 mb-2">{{ $profile->full_name }}</h1>
                        <p class="text-secondary mb-0">
                            {{ collect([
                                $profile->age ? $profile->age . ' years' : null,
                                $profile->occupation ?? null,
                                $profile->city_living_in ?? null,
                            ])->filter()->implode(' · ') }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="p-4 p-md-5">
                @foreach($sections as $title => $section)
                    @if(
                        $title === 'Family Information' ||
                        count($section['details']) ||
                        filled($section['description'])
                    )
                        <section class="detail-section">
                            <div class="section-heading">
                                <i class="bi {{ $section['icon'] }}"></i>
                                <h2 class="h5 mb-0">{{ $title }}</h2>
                            </div>

                            @if(count($section['details']))
                                <div class="row g-3">
                                    @foreach($section['details'] as $label => $value)
                                        <div class="col-sm-6 col-lg-4">
                                            <div class="detail-item">
                                                <div class="detail-label">{{ $label }}</div>
                                                <div class="detail-value">{{ $value }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($title === 'Family Information')
                                <div class="empty-section">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Family details have not been provided for this profile.
                                </div>
                            @endif

                            @if(filled($section['description']))
                                <div class="profile-description">{{ $section['description'] }}</div>
                            @endif
                        </section>
                    @endif
                @endforeach
            </section>
        </article>

        <div class="privacy-note text-center mt-3">
            <i class="bi bi-shield-check me-1"></i>
            Contact details are hidden for privacy. This shared link expires automatically.
        </div>
    </main>
</body>
</html>
