/**
 * Returns the object type of the given payload
 *
 * @param {*} payload
 * @returns {string}
 */
 export function getType(payload) {
  return Object.prototype.toString.call(payload).slice(8, -1);
}

/**
 * Returns whether the payload is a plain JavaScript object (excluding special classes or objects with other prototypes)
 *
 * @param {*} payload
 * @returns {payload is PlainObject}
 */
export function isPlainObject(payload) {
  if (getType(payload) !== 'Object')
    return false;
  return payload.constructor === Object && Object.getPrototypeOf(payload) === Object.prototype;
}

/**
 * Returns whether the payload is a Symbol
 *
 * @param {*} payload
 * @returns {payload is symbol}
 */
export function isSymbol(payload) {
  return getType(payload) === 'Symbol';
}

/**
  * Returns true for asynchronous functions, false otherwise.
  * 
  * @param  {Function} value
  * @return {boolean}
  */
export function isAsyncFunction(value) {
  return value && value.constructor && value.constructor === Object.getPrototypeOf(async function(){}).constructor;
}
