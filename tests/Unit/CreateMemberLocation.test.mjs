import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { test } from 'node:test';
import { runInNewContext } from 'node:vm';

const script = readFileSync(new URL('../../resources/js/admin/create-member.js', import.meta.url), 'utf8');

for (const base of ['https://example.test', 'https://example.test/himadmin/public']) {
    test(`member and partner locations use the application's URLs at ${base}`, async () => {
        const elements = {};
        const requests = [];

        for (const id of ['country_living_in', 'state_living_in', 'city_living_in', 'partner_country', 'partner_state', 'partner_city']) {
            elements[id] = {
                options: [{ value: 'India', dataset: { id: '1' } }],
                selectedIndex: 0,
                listeners: {},
                addEventListener(event, callback) { this.listeners[event] = callback; },
                appendChild(option) { this.options.push(option); },
            };
        }

        elements['create-member-form'] = {
            dataset: {
                statesUrl: `${base}/admin/members/location/states/__ID__`,
                citiesUrl: `${base}/admin/members/location/cities/__ID__`,
            },
        };

        runInNewContext(script, {
            document: {
                addEventListener(event, callback) { callback(); },
                getElementById(id) { return elements[id] ?? null; },
                createElement() { return { dataset: {} }; },
            },
            console: { log() {}, error() {} },
            fetch: async (url) => {
                requests.push(url);
                return { ok: true, json: async () => [{ id: 4006, name: 'Himachal Pradesh' }] };
            },
        });

        for (const [country, state, city] of [
            ['country_living_in', 'state_living_in', 'city_living_in'],
            ['partner_country', 'partner_state', 'partner_city'],
        ]) {
            elements[country].listeners.change.call(elements[country]);
            await new Promise(setImmediate);
            assert.equal(requests.at(-1), `${base}/admin/members/location/states/1`);
            assert.equal(elements[state].disabled, false);
            assert.equal(elements[state].options.at(-1).value, 'Himachal Pradesh');

            elements[state].selectedIndex = elements[state].options.length - 1;
            elements[state].listeners.change.call(elements[state]);
            await new Promise(setImmediate);
            assert.equal(requests.at(-1), `${base}/admin/members/location/cities/4006`);
            assert.equal(elements[city].disabled, false);
        }
    });
}
