<? // the base class for all pages
class Site {
  // default values
  var $title  = 'SeiteLite';            // page title
  var $tpl    = 'html';                 // template name
  var $lang   = 'en';                   // language

  var $logo   = SL . 'assets/logo.svg'; // logo/icon

  // meta tags
  var $description  = 'Build lightweight websites easily with SiteLite.';
  var $robots       = 'index, follow';
}
