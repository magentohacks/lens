/* global google, navigator */
/**
 * Google Autocomplete request processor
 */
define([
    'ko',
    'underscore',
    'mage/utils/misc'
], function (ko, _, utils) {
    'use strict';

    /**
     * @typedef {Object} AmSuggestion
     * @property {string} text
     * @property {Array<StringRange>} matches
     * @property {Place} place
     * @property {string} uid
     *
     * @typedef {Object} StringRange
     * @property {number} startOffset
     * @property {number} endOffset
     */

    return {
        includedCounties: [],
        request: null,
        suggestions: ko.observableArray([]),
        selectedPlace: ko.observable(null),
        isActive: ko.observable(false),
        isLoading: ko.observable(false),
        /**
         * @type {HTMLInputElement}
         */
        input: null,
        activeKeydownListener: null,

        /**
         * @param {HTMLInputElement} input
         * @param includedCounties
         */
        activate: function (input, includedCounties) {
            this.input = input;
            this.includedCounties = includedCounties;

            if (!this.request) {
                this.buildRequestCore().then(() => {
                    this.initInputBinding(input);
                });
            } else {
                this.initInputBinding(input);
            }

            this.isActive(true);
        },

        initInputBinding: function (input) {
            const inputText = input.value;

            this.doRequest(inputText);

            if (!this.activeKeydownListener) {
                this.activeKeydownListener = _.debounce(this.keydownListener.bind(this), 200);

                input.addEventListener(
                    'keydown',
                    this.activeKeydownListener
                );
            }
        },

        keydownListener: function (event) {
            if (event.key === 'Enter' || event.key === 'Escape') {
                return;
            }

            this.doRequest(this.input.value);
        },

        deactivate: function (input) {
            this.isActive(false);
            this.suggestions([]);
            input.removeEventListener(
                'keydown',
                this.activeKeydownListener
            );
            this.activeKeydownListener = null;
            this.input = null;
        },

        /**
         * @param {AmSuggestion|null} suggestion
         */
        selectSuggestion: function (suggestion) {
            this.isActive(false);

            if (suggestion) {
                this.selectedPlace(suggestion.place);
            } else {
                this.selectedPlace(null);
            }
        },

        buildRequestCore: async function () {
            const { AutocompleteSessionToken } = await google.maps.importLibrary('places');

            this.request = {
                input: '',
                includedPrimaryTypes: [ 'street_address', 'route' ],
                sessionToken: new AutocompleteSessionToken
            };

            if (this.includedCounties) {
                this.request.includedRegionCodes = this.includedCounties;
            }

            this.geolocate();
        },

        geolocate: function () {
            navigator?.geolocation.getCurrentPosition((position) => {
                this.request.locationBias = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
            });
        },

        /**
         * @param {string} inputText
         * @returns {Promise<*>}
         */
        doRequest: async function (inputText) {
            if (this.request.input === inputText) {
                this.isLoading(false);

                return;
            }

            if (!inputText) {
                this.suggestions([]);
                this.request.input = '';
                this.isLoading(false);

                return;
            }

            this.isActive(true);
            this.isLoading(true);
            const { AutocompleteSuggestion } = await google.maps.importLibrary('places');
            this.request.input = inputText;
            const { suggestions } =
                await AutocompleteSuggestion.fetchAutocompleteSuggestions(this.request);
            this.suggestions(suggestions.map(this.mapSuggestion.bind(this)));
            this.isLoading(false);

            return suggestions;
        },

        /**
         * @param {PlacePrediction} placePrediction
         * @returns {AmSuggestion}
         */
        mapSuggestion: function ({ placePrediction }) {
            return {
                text: placePrediction.text.text,
                matches: placePrediction.text.matches,
                place: placePrediction.toPlace(),
                uid: utils.uniqueid()
            };
        }
    };
});
