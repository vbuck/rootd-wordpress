/**
 * Region form JavaScript helper.
 *
 * @todo      Support address verification, postal code lookup.
 *
 * @package   Rootd_Iso
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

(function($) {

    var rootdIsoRegionSelector = function() {

        this._objectCache = {},
        this._countries   = {},
        this._regions     = {},
        this._fields      = {
            country     : null,
            address     : null,
            region      : null,
            regionText  : null,
            city        : null,
            postcode    : null
        },

        this.initialize = function(
            country, 
            address, 
            region, 
            city, 
            postcode,
            defaultCountry,
            defaultRegion,
            countries,
            regions
        ) {
            this._fields            = {};
            this._fields.country    = $(country);
            this._fields.address    = $(address);
            this._fields.region     = $(region);
            this._fields.city       = $(city);
            this._fields.postcode   = $(postcode);

            this._countries = $.extend(this._countries, (countries || {}));
            this._regions   = $.extend(this._regions, (regions || {}));

            this._initEvents();

            this._initElements(defaultCountry, defaultRegion);
        },

        this._generateCountryOptions = function() {
            var options = [],
                option  = null;

            for (var code in this._countries) {
                option = document.createElement('option');

                option.value = code;
                option.label = this._countries[code];

                options.push(option);
            }

            return options;
        },

        this._generateRegionOptions = function(country) {
            var options = [],
                option  = null;

            for (var code in this._regions[country]) {
                option = document.createElement('option');

                option.value = code;
                option.label = this._regions[country][code];

                options.push(option);
            }

            return options;
        },

        this._initCountries = function() {
            this._fields.country
                .empty()
                .append(this._generateCountryOptions());

            return this;
        },

        this._initElements = function(defaultCountry, defaultRegion) {
            this._initCountries();

            if (defaultCountry) {
                this._fields.country.val(defaultCountry);
            }

            this._fields.region.attr('data-name', this._fields.region.attr('name'));

            this._initRegions();

            if (defaultRegion) {
                this._fields.region.val(defaultRegion);
            }

            var regionText              = document.createElement('input');
            regionText.type             = 'text';
            regionText.className        = this._fields.region.attr('class');
            regionText.name             = '';
            regionText.id               = this._fields.region.attr('id') + '_text';
            regionText.style.display    = 'none';

            this._fields.region.after(regionText);

            this._fields.regionText = $(regionText);
            this._fields.regionText.attr('data-name', '');

            this._updateRegion();

            return this;
        }

        this._initEvents = function() {
            this._fields.country.on('change', this._updateRegion.bind(this));

            return this;
        },

        this._initRegions = function() {
            this._fields.region
                .empty()
                .append(this._generateRegionOptions(this._fields.country.val()));

            return this;
        },

        this._setRegionInputType = function(type) {
            if (type == 'select') {
                this._fields.region
                    .attr('name', this._fields.region.attr('data-name'))
                    .show();

                this._fields.regionText
                    .attr('name', '')
                    .hide();
            } else {
                this._fields.regionText
                    .attr('name', this._fields.region.attr('data-name'))
                    .show();

                this._fields.region
                    .attr('name', '')
                    .hide();
            }

            return this;
        },

        this._updateRegion = function() {
            var country = this._fields.country.val(),
                options = null;

            if (typeof this._regions[country] != 'undefined') {
                if (typeof this._objectCache[country] != 'undefined') {
                    options = this._objectCache[country];
                } else {
                    options = this._generateRegionOptions(country);
                }

                this._fields.region
                    .empty()
                    .append(options);

                this._objectCache[country] = options;

                this._setRegionInputType('select');
            } else {
                this._setRegionInputType('text');
            }

            return this;
        }

        ;

        this.initialize.apply(this,arguments);
    }

    window.rootdIsoRegionSelector = rootdIsoRegionSelector;

}(jQuery));