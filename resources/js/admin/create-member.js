document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const form = document.getElementById('create-member-form');
    const countrySelect = document.getElementById('country_living_in');
    const stateSelect = document.getElementById('state_living_in');
    const citySelect = document.getElementById('city_living_in');

    const partnerCountry = document.getElementById('partner_country');
    const partnerState = document.getElementById('partner_state');
    const partnerCity = document.getElementById('partner_city');
    const disabilitySelect = document.getElementById('any_disability');
    const disabilityGroup = document.getElementById('disability_description_group');
    const disabilityDescription = document.getElementById('health_info');
    const brothersSelect = document.getElementById('no_of_brothers');
    const marriedBrothersSelect = document.getElementById('married_brothers');
    const marriedBrothersZero = document.getElementById('married_brothers_zero');
    const sistersSelect = document.getElementById('no_of_sisters');
    const marriedSistersSelect = document.getElementById('married_sisters');
    const marriedSistersZero = document.getElementById('married_sisters_zero');

    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');
    const copyPasswordButton = document.getElementById('copyPassword');
    const passwordCopyStatus = document.getElementById('passwordCopyStatus');

    function syncMarriedSiblingCount(totalSelect, marriedSelect, zeroInput) {
        if (!totalSelect || !marriedSelect || !zeroInput) return;

        const total = Number.parseInt(totalSelect.value, 10);
        const hasTotal = Number.isInteger(total);
        const hasNoSiblings = hasTotal && total === 0;

        marriedSelect.disabled = hasNoSiblings;
        zeroInput.disabled = !hasNoSiblings;
        zeroInput.name = hasNoSiblings ? marriedSelect.name : '';

        Array.from(marriedSelect.options).forEach(option => {
            if (option.value === '') return;
            option.disabled = hasTotal && Number(option.value) > total;
        });

        if (hasNoSiblings) {
            marriedSelect.value = '0';
        } else if (hasTotal && Number(marriedSelect.value) > total) {
            marriedSelect.value = String(total);
        }
    }

    brothersSelect?.addEventListener('change', () =>
        syncMarriedSiblingCount(brothersSelect, marriedBrothersSelect, marriedBrothersZero)
    );
    sistersSelect?.addEventListener('change', () =>
        syncMarriedSiblingCount(sistersSelect, marriedSistersSelect, marriedSistersZero)
    );
    syncMarriedSiblingCount(brothersSelect, marriedBrothersSelect, marriedBrothersZero);
    syncMarriedSiblingCount(sistersSelect, marriedSistersSelect, marriedSistersZero);

    togglePasswordButton?.addEventListener('click', function () {
        const passwordIsVisible = passwordInput.type === 'text';

        passwordInput.type = passwordIsVisible ? 'password' : 'text';
        this.setAttribute('aria-pressed', String(!passwordIsVisible));
        this.setAttribute(
            'aria-label',
            passwordIsVisible ? 'Show password' : 'Hide password'
        );
        this.title = passwordIsVisible ? 'Show password' : 'Hide password';
        this.querySelector('i')?.classList.toggle('bi-eye', passwordIsVisible);
        this.querySelector('i')?.classList.toggle('bi-eye-slash', !passwordIsVisible);
    });

    copyPasswordButton?.addEventListener('click', async function () {
        if (!passwordInput.value) {
            passwordCopyStatus.textContent = 'Enter a password before copying.';
            passwordCopyStatus.className = 'small mt-1 text-danger';

            return;
        }

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(passwordInput.value);
            } else {
                const originalType = passwordInput.type;

                passwordInput.type = 'text';
                passwordInput.select();
                document.execCommand('copy');
                passwordInput.setSelectionRange(0, 0);
                passwordInput.type = originalType;
            }

            passwordCopyStatus.textContent = 'Password copied.';
            passwordCopyStatus.className = 'small mt-1 text-success';
        } catch (error) {
            passwordCopyStatus.textContent = 'Unable to copy the password.';
            passwordCopyStatus.className = 'small mt-1 text-danger';
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Get selected option ID
    |--------------------------------------------------------------------------
    */

    function getSelectedId(select) {

        if (!select) {
            return null;
        }

        const selectedOption =
            select.options[select.selectedIndex];

        // Prefer data-id (numeric id) when available, otherwise fall back to the option value.
        return selectedOption?.dataset?.id || selectedOption?.value || null;
    }


    /*
    |--------------------------------------------------------------------------
    | Load States
    |--------------------------------------------------------------------------
    */

    function loadStates(countryId, stateSelect, citySelect) {

        if (!stateSelect || !citySelect) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | No country selected
        |--------------------------------------------------------------------------
        */

        if (!countryId) {

            stateSelect.innerHTML =
                '<option value="">Select Country First</option>';

            stateSelect.disabled = true;

            citySelect.innerHTML =
                '<option value="">Select State First</option>';

            citySelect.disabled = true;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        stateSelect.innerHTML =
            '<option value="">Loading states...</option>';

        stateSelect.disabled = true;

        citySelect.innerHTML =
            '<option value="">Select State First</option>';

        citySelect.disabled = true;


        /*
        |--------------------------------------------------------------------------
        | Fetch states
        |--------------------------------------------------------------------------
        */

        fetch(
            form.dataset.statesUrl.replace('__ID__', encodeURIComponent(countryId)),
            {
                method: 'GET',

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        )
        .then(response => {

            if (!response.ok) {
                throw new Error(
                    `Unable to load states. HTTP ${response.status}`
                );
            }

            return response.json();
        })
        .then(states => {

            console.log('States:', states);


            /*
            |--------------------------------------------------------------------------
            | Reset state options
            |--------------------------------------------------------------------------
            */

            stateSelect.innerHTML =
                '<option value="">Select State</option>';


            /*
            |--------------------------------------------------------------------------
            | Validate response
            |--------------------------------------------------------------------------
            */

            if (!Array.isArray(states)) {

                console.error(
                    'Invalid states response:',
                    states
                );

                stateSelect.innerHTML =
                    '<option value="">Unable to load states</option>';

                stateSelect.disabled = true;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | No states
            |--------------------------------------------------------------------------
            */

            if (states.length === 0) {

                stateSelect.innerHTML =
                    '<option value="">No states found</option>';

                stateSelect.disabled = true;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Add states
            |--------------------------------------------------------------------------
            */

            states.forEach(state => {

                const option =
                    document.createElement('option');

                /*
                | Your API should return:
                |
                | {
                |     id: 1,
                |     name: "Himachal Pradesh"
                | }
                */

                option.value = state.name;

                option.textContent = state.name;

                option.dataset.id = state.id;

                stateSelect.appendChild(option);
            });


            /*
            |--------------------------------------------------------------------------
            | Enable state
            |--------------------------------------------------------------------------
            */

            stateSelect.disabled = false;

        })
        .catch(error => {

            console.error(
                'State loading error:',
                error
            );

            stateSelect.innerHTML =
                '<option value="">Unable to load states</option>';

            stateSelect.disabled = true;

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Load Cities
    |--------------------------------------------------------------------------
    */

    function loadCities(stateId, citySelect) {

        if (!citySelect) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | No state selected
        |--------------------------------------------------------------------------
        */

        if (!stateId) {

            citySelect.innerHTML =
                '<option value="">Select State First</option>';

            citySelect.disabled = true;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        citySelect.innerHTML =
            '<option value="">Loading cities...</option>';

        citySelect.disabled = true;


        /*
        |--------------------------------------------------------------------------
        | Fetch cities
        |--------------------------------------------------------------------------
        */

        fetch(
            form.dataset.citiesUrl.replace('__ID__', encodeURIComponent(stateId)),
            {
                method: 'GET',

                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        )
        .then(response => {

            if (!response.ok) {
                throw new Error(
                    `Unable to load cities. HTTP ${response.status}`
                );
            }

            return response.json();
        })
        .then(cities => {

            console.log('Cities:', cities);


            /*
            |--------------------------------------------------------------------------
            | Reset city options
            |--------------------------------------------------------------------------
            */

            citySelect.innerHTML =
                '<option value="">Select City</option>';


            /*
            |--------------------------------------------------------------------------
            | Validate response
            |--------------------------------------------------------------------------
            */

            if (!Array.isArray(cities)) {

                console.error(
                    'Invalid cities response:',
                    cities
                );

                citySelect.innerHTML =
                    '<option value="">Unable to load cities</option>';

                citySelect.disabled = true;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | No cities
            |--------------------------------------------------------------------------
            */

            if (cities.length === 0) {

                citySelect.innerHTML =
                    '<option value="">No cities found</option>';

                citySelect.disabled = true;

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Add cities
            |--------------------------------------------------------------------------
            */

            cities.forEach(city => {

                const option =
                    document.createElement('option');

                /*
                | Your API should return:
                |
                | {
                |     id: 1,
                |     name: "Shimla"
                | }
                */

                option.value = city.name;

                option.textContent = city.name;

                option.dataset.id = city.id ?? '';

                citySelect.appendChild(option);
            });


            /*
            |--------------------------------------------------------------------------
            | Enable city
            |--------------------------------------------------------------------------
            */

            citySelect.disabled = false;

        })
        .catch(error => {

            console.error(
                'City loading error:',
                error
            );

            citySelect.innerHTML =
                '<option value="">Unable to load cities</option>';

            citySelect.disabled = true;

        });
    }


    /*
    |--------------------------------------------------------------------------
    | MEMBER LOCATION
    |--------------------------------------------------------------------------
    */


    /*
    | Country changed
    */

    countrySelect?.addEventListener(
        'change',
        function () {

            const countryId =
                getSelectedId(this);

            loadStates(
                countryId,
                stateSelect,
                citySelect
            );
        }
    );


    /*
    | State changed
    */

    stateSelect?.addEventListener(
        'change',
        function () {

            const stateId =
                getSelectedId(this);

            loadCities(
                stateId,
                citySelect
            );
        }
    );


    /*
    |--------------------------------------------------------------------------
    | PARTNER LOCATION
    |--------------------------------------------------------------------------
    */


    /*
    | Partner country changed
    */

    partnerCountry?.addEventListener(
        'change',
        function () {

            const countryId =
                getSelectedId(this);

            loadStates(
                countryId,
                partnerState,
                partnerCity
            );
        }
    );


    /*
    | Partner state changed
    */

    partnerState?.addEventListener(
        'change',
        function () {

            const stateId =
                getSelectedId(this);

            loadCities(
                stateId,
                partnerCity
            );
        }
    );

    function toggleDisabilityDescription() {
        const visible = disabilitySelect?.value === 'Yes';
        disabilityGroup?.classList.toggle('d-none', !visible);

        if (disabilityDescription) {
            disabilityDescription.required = visible;
            if (!visible) disabilityDescription.value = '';
        }
    }

    disabilitySelect?.addEventListener('change', toggleDisabilityDescription);
    toggleDisabilityDescription();

});
