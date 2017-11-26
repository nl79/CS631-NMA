import axios from 'axios';
// Weathermap API key.
const API_KEY = '46dba4d5312dc549732344f6181c37f6';
const ROOT_URL = `http://api.openweathermap.org/data/2.5/forecast?mode=json&appid=${API_KEY}`;
export const FETCH_WEATHER = 'FETCH_WEATHER';

export function fetchWeather(args) {
  const url = `${ROOT_URL}&q=${args},US`;
  const request = axios.get(url);
  return {
    type: FETCH_WEATHER,
    // the middlware 'redux-promise' will unwrap the promise automaticaly
    // and supply the resolved object data.
    payload: request
  };
}