/**
 * Set the field to a ref object.
 */
function setField(ref, key, node) {
  const keys = key.split('.');
  key = null;
  while ((key = keys.shift()) !== undefined) {
    if (keys.length === 0)
      ref[key] = (key in ref) ? ref[key].add($(node)) : $(node);
    else {
      if (!(key in ref))
        ref[key] = {};
      ref = ref[key];
    }
  }
}

/**
 * Return elements with data-ref attribute.
 */
export default (context = 'body', ref = undefined, newField = undefined) => {
  // Check parameters.
  if($.type(context) === 'string' || context instanceof HTMLElement)
    context = $(context);
  else if (!(context instanceof $))
    throw new TypeError('The context parameter is invalid');

  // Add new fields to an existing reference.
  if (ref && newField)
    return void setField(ref, node.data('ref'), context.find(`[data-ref="${newField}"]`).get(0));

  // Create a new reference.
  ref = {};
  context.find('[data-ref]').each((_, node) => {
    setField(ref, node.dataset.ref, node);
  });
  return ref;
}