
import axios from "axios";

let prefix = '/api/patients';

export const PatientService = {

    save(params) {
      let url = `${prefix}`;

      return axios.post(url, params);

    },

    get(id) {
      let url = `${prefix}/${id}`;
      return axios.get(url);
    },

    profile(id) {
      let url = `${prefix}/${id}/profile`;
      return axios.get(url);
    },

    list(params) {
      let url = `${prefix}/list`;
      return axios.get(url, params);
    },

    addCondition(patient, condition) {
      let url = `${prefix}/${patient}/condition`;
      return axios.post(url, {condition});
    },

    conditions(id) {
      let url = `${prefix}/${id}/conditions`;
      return axios.get(url);
    }
}
