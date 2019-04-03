/**
 * Formstack plugin for Craft CMS
 *
 * FormstackField Field JS
 *
 * @author    TrendyMinds
 * @copyright Copyright (c) 2019 TrendyMinds
 * @link      https://trendyminds.com
 * @package   Formstack
 * @since     2.0.0FormstackFormstackField
 */

;(function ( $, window, document, undefined ) {

  var pluginName = "FormstackFormstackField",
      defaults = {};

  // Plugin constructor
  function Plugin( element, options ) {
      this.element = element;

      this.options = $.extend( {}, defaults, options) ;

      this.$field = $(`#${this.options.namespace}`);
      this.$select = $(`#${this.options.namespace}-select`);

      this._defaults = defaults;
      this._name = pluginName;

      this.init();
  }

  Plugin.prototype = {
      init: function() {
          this.events();
      },

      events: function() {
        this.$select.on("change", this.handleSelect.bind(this));
      },

      handleSelect(ev) {
        this.fetchForm(ev.currentTarget.value);
      },

      fetchForm(formId) {
        const $field = this.$field;
        const $select = this.$select;

        $select.attr("disabled", "disabled");

        $field.attr("disabled", "disabled");
        $field.addClass("disabled");

        $.get("/actions/formstack/default/get-form", { id: formId }).then(function (data) {
          $select.attr("disabled", false);

          $field.attr("disabled", false);
          $field.removeClass("disabled");

          $field.val(data);
        });
      }
  };

  $.fn[pluginName] = function ( options ) {
      return this.each(function () {
          if (!$.data(this, "plugin_" + pluginName)) {
              $.data(this, "plugin_" + pluginName,
              new Plugin( this, options ));
          }
      });
  };

})( jQuery, window, document );
