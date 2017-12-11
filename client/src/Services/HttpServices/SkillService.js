
import axios from "axios";

let prefix = '/api/skills';

export const ConditionService = {

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
    }
}
