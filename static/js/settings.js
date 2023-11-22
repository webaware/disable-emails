(function (settings) {
  addHandler("disable-emails-mu-enable", "click", function () {
    if (window.confirm(settings.msg.mu_activate)) {
      mustUse('activate');
    }
  });
  addHandler("disable-emails-mu-disable", "click", function () {
    if (window.confirm(settings.msg.mu_deactivate)) {
      mustUse('deactivate');
    }
  });

  /**
   * add an event handler to element, if element is found
   * @param {String} selector
   * @param {String} event
   * @param {Function} handler
   */
  function addHandler(selector, event, handler) {
    const element = document.getElementById(selector);
    if (element) {
      element.addEventListener(event, handler, false);
    }
  }

  /**
   * reload page with request to enable / disable the must-use plugin
   * @param {String} action
   */
  function mustUse(action) {
    document.location = settings.mu_url + "&action=" + action;
  }
})(disable_emails_settings);
