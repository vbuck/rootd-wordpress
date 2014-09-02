/**
 * Rootd admin modal application. 
 * Based on the work of aut0poietic.
 *
 * @see       https://github.com/aut0poietic/wp-admin-modal-example
 *
 * @package   Rootd_Adminmodal
 * @author    Rick Buczynski <me@rickbuczynski.com>
 * @copyright 2014 Rick Buczynski. All Rights Reserved.
 */

var rootdAdminmodal = {

    appView         : null,

    instance        : null,

    templateView    : null

};

(function($) {

    $(document).ready(function() {
        rootdAdminmodal.templateView = Backbone.View.extend({

            loadEvent: 'templates_loaded',

            templateCache: {},

            getTemplate: function(id) {
                if (this.templateCache[id] === undefined) {
                    this.templateCache[id] = _.template(this.$('#' + id).html());
                }

                return this.templateCache[id];
            },

            initialize: function() {
                //_.bindAll(this);
            },

            handleError: function() {
                return this;
            },

            handleResponse: function(data) {
                this.setElement(data);
                this.trigger(this.loadEvent);

                return this;
            },

            load: function(instanceId) {
                var data = {
                    action: 'templates_' + instanceId
                };

                $.get(ajaxurl, data, 'html')
                    .done(this.handleResponse.bind(this))
                    .fail(this.handleError.bind(this));

                return this;
            }

        });

        rootdAdminmodal.appView = Backbone.View.extend({

            id: 'rootd_adminmodal_container',

            events: {
                'click .rootd-adminmodal-close' : 'close'
            },

            templates: null,

            instanceId: 'rootd_adminmodal',

            ui: {
                glass   : null,
                header  : null,
                content : null,
                footer  : null
            },

            initialize: function(data) {
                //_.bindAll(this);
                this.templates = new rootdAdminmodal.templateView();

                if (typeof data.instanceId !== undefined) {
                    this.instanceId = data.instanceId;
                }

                this.templates
                    .on(this.templates.loadEvent, this.render.bind(this))
                    .load(this.instanceId);
            },

            render: function() {
                this.templates.off(this.templates.loadEvent);

                this.$el.attr('tabindex', '0')
                    .append(this.templates.getTemplate(this.instanceId)());

                this.ui.glass   = this.$('#rootd_adminmodal_glass');
                this.ui.header  = this.$('#rootd_adminmodal_header');
                this.ui.content = this.$('#rootd_adminmodal_content');
                this.ui.footer  = this.$('#rootd_adminmodal_footer');

                $(document).on('focusin', this.preserveFocus.bind(this));
                $(window).on('resize', this.resize.bind(this));
                $(document.body).append(this.$el);

                this.resize();

                this.trigger('adminmodal_render');

                return this;
            },

            close: function(event) {
                this.trigger('adminmodal_close');

                if (event) {
                    event.preventDefault();
                }

                this.undelegateEvents();

                $(document).off('focusin');
                $(window).off('resize');

                this.$el.removeClass('open');

                this.remove();

                rootdAdminmodal.instance = null;
            },

            open: function(event) {
                this.trigger('adminmodal_open');

                this.$el
                    .addClass('open')
                    .focus();

                return this;
            },

            preserveFocus: function(event) {
                if (this.$el[0] !== event.target && !this.$el.has(event.target).length) {
                    this.$el.focus();
                }

                return this;
            },

            resize: function(event) {
                this.ui.glass.css('line-height', $(window).height().toString() + 'px');

                return this;
            },

        });

    });


}(jQuery));