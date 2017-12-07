
import axios from "axios";

let prefix = '/api/facilities';

export const FacilitiesService = {

    save(params) {
      let url = `${prefix}`;

      return axios.post(url, params);

    },

    get(id) {
      let url = `${prefix}/${id}`;
      return axios.get(url);
    },

    list(params) {
      let url = `${prefix}/list`;
      return axios.get(url, params);
    },

    types() {
      let url = `${prefix}/types`;
      return axios.get(url);
    }
}
