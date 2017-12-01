import axios from "axios";

let prefix = '/api/staff';

export const StaffService = {

  save(params) {
    let url = `${prefix}`;

    return axios.post(url, params);

  },

  get(id) {
    let url = `${prefix}/${id}`;
    return axios.get(url);
  },



  inRole(params) {
    let url = `${prefix}/inRole`;
    return axios.get(url, {params});
  },

  list(params) {
    let url = `${prefix}/list`;
    return axios.get(url, params);
  },

  addSkill(patient, condition) {
    let url = `${prefix}/${patient}/skill`;
    return axios.post(url, {condition});
  },

  types() {
    let url = `${prefix}/types`;
    return axios.get(url);
  },

  skills(id) {
    let url = `${prefix}/${id}/skill`;
    return axios.get(url);
  }
}
