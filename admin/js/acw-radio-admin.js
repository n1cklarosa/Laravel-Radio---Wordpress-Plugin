(function ($) {
  "use strict";

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  console.log(wp_ajax);
  const url = wp_ajax.ajax_url;
  const nonce = wp_ajax._nonce;
  var dataJSON = {
    action: "prefix_ajax_first",
    whatever: 1234,
    nonce: "nonce-key",
  };

  const startUpdate = async () => {
    var dataJSON = {
      action: "prefix_ajax_first",
      whatever: 1234,
      nonce: wp_ajax._nonce,
    };
    const loadingDiv = document.getElementById("mrloading");
    const resultsDiv = document.getElementById("mrresults");
    loadingDiv.innerHTML = "<p>Loading..</p>";
    const data = new FormData();
    data.append("action", "prefix_ajax_first");
    data.append("_nonce", wp_ajax._nonce);

    const response = await fetch(`${wp_ajax.ajax_url}`, {
      method: "POST",
      credentials: "same-origin",
      body: data,
    });
    const results = await response.json();
    console.log(results);
    if (results.success === false) {
      loadingDiv.innerHTML = `<p>An error has occures..${results.message}</p>`;
      return;
    }
    loadingDiv.innerHTML =
      "<p>Done! The following programs have been updated</p>";

    results.data?.data?.programs?.forEach((element) => {
      console.log(element);
      resultsDiv.innerHTML = resultsDiv.innerHTML + `<p>${element.name}</p>`;
    });
  };

  document.addEventListener(
    "click",
    function (event) {
      // If the clicked element doesn't have the right selector, bail
      if (!event.target.matches(".reload-programs")) return;

      // Don't follow the link
      event.preventDefault();

      // Log the clicked element in the console
      startUpdate();
    },
    false
  );
})(jQuery);
