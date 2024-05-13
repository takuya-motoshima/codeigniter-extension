/**
 * IPAddress or CIDR check function.
 */
export default () => ({
  validate: input => {
    let valid = false;
    if (input.value === '')
      valid = true;
    else
      valid = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/.test(input.value);
    return {valid};
  }
})