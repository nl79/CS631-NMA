import axios from "axios";

let prefix = '/api/person';

export const PersonService = {

  save(params) {
    let url = `${prefix}`;
    return axios.post(url, params);
  },

  delete(id) {
    let url = `${prefix}/${id}`;
    return axios.delete(url);
  },

  get(id) {
    let url = `${prefix}/${id}`;
    return axios.get(url);
  },

  skills(id) {
    let url = `${prefix}/${id}/skills`;
    return axios.get(url);
  }
}
