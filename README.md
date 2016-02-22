# Complex field

## Goal

This module was written to avoid the mind-numbing tedium of writing custom field
types for Drupal. Eventually, I hope to improve this module to the point where
at least the API part of it can be included in Drupal core so that field developers
have an easy way to set up fields with complex values.

## Known issues

* This module should automatically generate widget forms from FieldWidget plugins,
  but that was not implemented in the interest of retaining my sanity. Creating
  instances of all of the FieldWidget plugins needed requires mocking a ton of
  dependencies to the point where they're relatively complete so that they can
  be passed to WidgetPluginManager to create the plugin instance. This was not
  something I wanted to do, so for the time being, implementers are required
  to bring-your-own-widget-form.
* This module should provide a way to configure the output of the values in the
  custom field (i.e. choose what FieldFormatter plugin you want to use for a
  given subelement). Same issue as the FieldWidget plugins ^.
* There are a lot of ugly hacks that need to be cleaned up, not to mention the
  blatant duplication of code with FieldWidget and FieldFormatter plugins. Generally,
  this module could use some cleanup and architectural review/remodeling.
* A UI would be the killer feature for this module. Being able to attach FieldType
  plugins via a UI when you create a Complex Field would be amazing. Bonus points
  if the Widget and Formatter were configurable from the UI as well. This would
  make complex_field a viable alternative to field_collection, in that an extra
  entity is not required to have subfields on a field.
