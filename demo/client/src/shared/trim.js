/**
 * Remove spaces before and after a string.
 */
export default (str, toLower = false) => {
  if (str == null)
    return str;
  str =  str.replace(/^[\s　]+|[\s　]+$/g, '');
  if (toLower)
    str = str.toLowerCase();
  return str;
}