/**
 * UNIX username check function.
 */
export default () => ({
  validate: input => {
    let valid = false;
    if (input.value === '')
      valid = true;
    else
      valid = /^[a-z_]([a-z0-9_-]{0,31}|[a-z0-9_-]{0,30}\$)$/.test(input.value);
    return {valid};
  }
})