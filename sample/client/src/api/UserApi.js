import Api from '~/shared/Api';

export default class extends Api {
  constructor() {
    super('/users');
  }

  async login(formData) {
    return this.client.post('login', formData);
  }

  async logout() {
    location.assign('/api/users/logout');
  }

  async createUser(formData) {
    return this.client.post('/', formData);
  }

  async getUser(userId) {
    return this.client.get(`/${userId}`);
  }

  async updateUser(userId, formData) {
    return this.client.put(`/${userId}`, formData);
  }

  // async updatePersonal(formData) {
  //   return this.client.put('update-personal', formData);
  // }

  // async deleteUser(userId) {
  //   return this.client.delete(`/${userId}`);
  // }

  // async sendPasswordResetEmail(clientNumber, formData) {
  //   return this.client.post(`/send-password-reset-email/${clientNumber}`, formData);
  // }

  // async resetPassword(formData) {
  //   return this.client.post('reset-password', formData);
  // }
}