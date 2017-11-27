
import axios from "axios";

let prefix = '/api/patients';

export const PatientService = {

    save(params) {
      let url = `${prefix}`;

      return axios.post(url, params);

    },

    list(params) {
      let url = `${prefix}/list`;
      return axios.get(url, params);
    }
}
