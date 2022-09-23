/**
 * Initialize the function to copy to the clipboard.
 */
export default context => {
  // Check parameters.
  if (context instanceof $)
    context = context.get(0);
  else if(!(context instanceof HTMLElement))
    throw new TypeError('The context parameter is invalid');
  for (let button of context.querySelectorAll('[data-clipboard-target]')) {
    const clipboard = new ClipboardJS(button);
    clipboard.on('success', evnt => {
      const button = evnt.trigger;
      const target = context.querySelector(button.dataset.clipboardTarget);
      let checkIcon = button.querySelector('.bi.bi-check');
      const svgIcon = button.querySelector('.svg-icon');

      // Exit check icon when already showing
      if (checkIcon)
        return;

      // Create check icon
      checkIcon = document.createElement('i');
      checkIcon.classList.add('bi');
      checkIcon.classList.add('bi-check');
      checkIcon.classList.add('fs-2x');

      // Append check icon
      button.appendChild(checkIcon);

      // Highlight target
      const classes = ['text-success', 'fw-boldest'];
      target.classList.add(...classes);

      // Highlight button
      button.classList.add('btn-success');

      // Hide copy icon
      svgIcon.classList.add('d-none');

      // Revert button label after 3 seconds
      setTimeout(()  => {
        // Remove check icon
        svgIcon.classList.remove('d-none');

        // Revert icon
        button.removeChild(checkIcon);

        // Remove target highlight
        target.classList.remove(...classes);

        // Remove button highlight
        button.classList.remove('btn-success');
      }, 3000);
    });
  }
}