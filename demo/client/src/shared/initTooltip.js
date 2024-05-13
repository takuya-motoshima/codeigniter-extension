/**
 * Initialize Bootstrap5 tooltip.
 */
export default (context, selector = '[data-bs-toggle="tooltip"]') => {
  if (context instanceof HTMLElement)
    context = $(context);
  else if (!(context instanceof $))
    throw new TypeError('Specify HTMLElement or jQuery object for context parameter');
  for(let tooltipTrigger of context.find(selector))
    new bootstrap.Tooltip(tooltipTrigger, {trigger: 'hover'});
}