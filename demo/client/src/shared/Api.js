import axios from 'axios';

/**
 * REST client.
 */
export default class {
  /**
   * Construct REST client.
   */
  constructor(path, host = undefined) {
    // Client instance.
    this.client = axios.create({
      baseURL: `${host || location.origin}/api/${path.replace(/^\//, '')}`,
      timeout: 60000,
      // timeout: 5000,
      headers: {
        'accept': 'application/json',
        'Content-Type': 'application/json',
        // 'X-Requested-With': 'XMLHttpRequest'
      },
      responseType: 'json',
      // transformRequest: [
      //   (data, headers) => {
      //     return data;
      //   }
      // ],
      withCredentials: true
    });

    // Hook before sending a request.
    this.client.interceptors.request.use(config => {
      // if (config.data instanceof FormData)
      //   config.headers['Content-Type'] = 'multipart/form-data';
      return config;
    });
  }
}